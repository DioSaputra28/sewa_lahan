<?php

namespace App\Actions\Leases;

use App\Actions\ActivityLogs\WriteOperationalActivityLog;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\LeasePeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CreateLeaseFromPaidBooking
{
    public function __construct(
        protected WriteOperationalActivityLog $activityLog,
    ) {}

    public function handle(BookingRequest $bookingRequest, ?Invoice $invoice = null): Lease
    {
        if ($bookingRequest->lease) {
            return $bookingRequest->lease;
        }

        return DB::transaction(function () use ($bookingRequest, $invoice): Lease {
            $invoice ??= $bookingRequest->invoices()->latest('id')->first();

            $lease = Lease::query()->create([
                'booking_request_id' => $bookingRequest->id,
                'tenant_id' => $bookingRequest->user_id,
                'plot_id' => $bookingRequest->plot_id,
                'invoice_id' => $invoice?->id,
                'lease_number' => 'LEASE-'.now()->format('Ymd').'-'.str_pad((string) $bookingRequest->id, 5, '0', STR_PAD_LEFT),
                'start_date' => $bookingRequest->start_date,
                'end_date' => $bookingRequest->end_date,
                'term_type' => $bookingRequest->term_type,
                'duration' => $bookingRequest->duration,
                'agreed_price' => (int) ($bookingRequest->final_price ?? $bookingRequest->quoted_price ?? 0),
                'deposit_amount' => 0,
                'status' => 'active',
                'activated_at' => now(),
                'renewal_of_lease_id' => $bookingRequest->renewalSourceLeaseId(),
            ]);

            $duration = max((int) $bookingRequest->duration, 1);
            $totalAmount = (int) $lease->agreed_price;
            $baseAmount = intdiv($totalAmount, $duration);
            $remainder = $totalAmount - ($baseAmount * $duration);
            $startDate = $bookingRequest->start_date instanceof Carbon
                ? $bookingRequest->start_date->copy()
                : Carbon::parse($bookingRequest->start_date);

            for ($periodNumber = 1; $periodNumber <= $duration; $periodNumber++) {
                $periodStart = $bookingRequest->term_type === 'monthly'
                    ? $startDate->copy()->addMonths($periodNumber - 1)
                    : $startDate->copy()->addYears($periodNumber - 1);

                $periodEnd = $bookingRequest->term_type === 'monthly'
                    ? $periodStart->copy()->addMonth()->subDay()
                    : $periodStart->copy()->addYear()->subDay();

                LeasePeriod::query()->create([
                    'lease_id' => $lease->id,
                    'period_no' => $periodNumber,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'due_date' => $periodStart,
                    'amount' => $baseAmount + ($periodNumber === $duration ? $remainder : 0),
                    'status' => 'paid',
                ]);
            }

            $this->activityLog->handle(
                null,
                $lease,
                'activate-lease',
                'Lease activated after payment succeeded.',
                [
                    'lease' => $this->activityLog->snapshot($lease, [
                        'id',
                        'lease_number',
                        'booking_request_id',
                        'invoice_id',
                        'plot_id',
                        'tenant_id',
                        'status',
                        'term_type',
                        'duration',
                        'start_date',
                        'end_date',
                        'agreed_price',
                    ]),
                    'booking_request' => $this->activityLog->snapshot($bookingRequest, [
                        'id',
                        'status',
                        'payment_status',
                        'final_price',
                        'approved_at',
                        'payment_due_at',
                    ]),
                    'invoice' => $invoice
                        ? $this->activityLog->snapshot($invoice, [
                            'id',
                            'invoice_number',
                            'status',
                            'due_date',
                            'total_amount',
                        ])
                        : null,
                    'period_count' => $lease->periods()->count(),
                ],
            );

            return $lease->refresh();
        });
    }
}
