<?php

namespace App\Console\Commands;

use App\Actions\ActivityLogs\WriteOperationalActivityLog;
use App\Models\BookingRequest;
use App\Models\BookingStatusEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExpirePendingBookingsCommand extends Command
{
    protected $signature = 'booking:expire-overdue';

    protected $description = 'Expire approved bookings that were not paid before their payment due date.';

    public function handle(WriteOperationalActivityLog $writeOperationalActivityLog): int
    {
        BookingRequest::query()
            ->where('status', 'approved')
            ->where('payment_status', '!=', 'paid')
            ->whereNotNull('payment_due_at')
            ->where('payment_due_at', '<', now())
            ->orderBy('id')
            ->chunkById(100, function ($bookingRequests) use ($writeOperationalActivityLog): void {
                foreach ($bookingRequests as $bookingRequest) {
                    $this->expireBookingRequest($bookingRequest->id, $writeOperationalActivityLog);
                }
            });

        return self::SUCCESS;
    }

    protected function expireBookingRequest(int $bookingRequestId, WriteOperationalActivityLog $writeOperationalActivityLog): void
    {
        DB::transaction(function () use ($bookingRequestId, $writeOperationalActivityLog): void {
            $bookingRequest = BookingRequest::query()
                ->with(['user', 'plot.market', 'plot.area'])
                ->whereKey($bookingRequestId)
                ->lockForUpdate()
                ->first();

            if (! $bookingRequest instanceof BookingRequest) {
                return;
            }

            if ($bookingRequest->status !== 'approved') {
                return;
            }

            if ($bookingRequest->payment_status === 'paid') {
                return;
            }

            if (! $bookingRequest->payment_due_at instanceof Carbon || $bookingRequest->payment_due_at->greaterThanOrEqualTo(now())) {
                return;
            }

            $invoice = $bookingRequest->latestInvoiceRecord();

            if ($invoice) {
                $invoice = $bookingRequest->invoices()
                    ->whereKey($invoice->id)
                    ->lockForUpdate()
                    ->first();
            }

            $beforeBooking = $writeOperationalActivityLog->snapshot($bookingRequest, [
                'id',
                'status',
                'payment_status',
                'approved_by',
                'approved_at',
                'payment_due_at',
                'final_price',
            ]);

            $beforeInvoice = $invoice
                ? $writeOperationalActivityLog->snapshot($invoice, [
                    'id',
                    'invoice_number',
                    'status',
                    'due_date',
                    'total_amount',
                ])
                : null;

            $bookingRequest->update([
                'status' => 'expired',
                'payment_status' => 'expired',
            ]);

            if ($invoice && $invoice->status !== 'paid') {
                $invoice->update([
                    'status' => 'expired',
                ]);
            }

            BookingStatusEvent::query()->create([
                'booking_request_id' => $bookingRequest->id,
                'status' => 'expired',
                'changed_by' => null,
                'notes' => 'Booking expired automatically because payment due date passed.',
            ]);

            $writeOperationalActivityLog->handle(
                null,
                $bookingRequest,
                'expire-booking',
                'Booking expired automatically because payment due date passed.',
                [
                    'booking' => [
                        'before' => $beforeBooking,
                        'after' => $writeOperationalActivityLog->snapshot($bookingRequest->fresh(), [
                            'id',
                            'status',
                            'payment_status',
                            'approved_by',
                            'approved_at',
                            'payment_due_at',
                            'final_price',
                        ]),
                    ],
                    'invoice' => $invoice
                        ? [
                            'before' => $beforeInvoice,
                            'after' => $writeOperationalActivityLog->snapshot($invoice->fresh(), [
                                'id',
                                'invoice_number',
                                'status',
                                'due_date',
                                'total_amount',
                            ]),
                        ]
                        : null,
                ],
            );
        });
    }
}
