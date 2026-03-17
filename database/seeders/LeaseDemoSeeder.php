<?php

namespace Database\Seeders;

use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\LeasePeriod;
use Illuminate\Database\Seeder;

class LeaseDemoSeeder extends Seeder
{
    public function run(): void
    {
        $paidBooking = BookingRequest::query()
            ->where('payment_status', 'paid')
            ->first();

        if (! $paidBooking) {
            return;
        }

        $invoice = Invoice::query()
            ->where('booking_request_id', $paidBooking->id)
            ->first();

        $lease = Lease::query()->updateOrCreate(
            [
                'booking_request_id' => $paidBooking->id,
            ],
            [
                'booking_request_id' => $paidBooking->id,
                'tenant_id' => $paidBooking->user_id,
                'plot_id' => $paidBooking->plot_id,
                'invoice_id' => $invoice?->id,
                'lease_number' => 'LEASE-'.str_pad((string) $paidBooking->id, 5, '0', STR_PAD_LEFT),
                'start_date' => $paidBooking->start_date,
                'end_date' => $paidBooking->end_date,
                'term_type' => $paidBooking->term_type,
                'duration' => $paidBooking->duration,
                'agreed_price' => (int) ($paidBooking->final_price ?? $paidBooking->quoted_price ?? 0),
                'deposit_amount' => 0,
                'status' => 'active',
                'activated_at' => now()->subDay(),
                'renewal_of_lease_id' => null,
            ],
        );

        $periodCount = $paidBooking->term_type === 'monthly' ? $paidBooking->duration : 1;

        for ($period = 1; $period <= $periodCount; $period++) {
            $periodStart = $paidBooking->term_type === 'monthly'
                ? $paidBooking->start_date->copy()->addMonths($period - 1)
                : $paidBooking->start_date->copy();

            $periodEnd = $paidBooking->term_type === 'monthly'
                ? $periodStart->copy()->addMonth()->subDay()
                : $paidBooking->end_date->copy();

            LeasePeriod::query()->updateOrCreate(
                [
                    'lease_id' => $lease->id,
                    'period_no' => $period,
                ],
                [
                    'lease_id' => $lease->id,
                    'period_no' => $period,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'due_date' => $periodStart,
                    'amount' => $paidBooking->term_type === 'monthly'
                        ? (int) round($lease->agreed_price / max($paidBooking->duration, 1))
                        : $lease->agreed_price,
                    'status' => $period === 1 ? 'paid' : 'pending',
                ],
            );
        }
    }
}
