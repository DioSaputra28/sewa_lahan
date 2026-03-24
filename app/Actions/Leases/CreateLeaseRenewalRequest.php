<?php

namespace App\Actions\Leases;

use App\Models\BookingRequest;
use App\Models\BookingStatusEvent;
use App\Models\Lease;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateLeaseRenewalRequest
{
    public function handle(
        Lease $lease,
        int $duration,
        DateTimeInterface|string $startDate,
    ): BookingRequest {
        if ($lease->status !== 'active') {
            throw new RuntimeException('Perpanjangan hanya bisa diajukan dari lease yang aktif.');
        }

        if ($lease->hasUnresolvedRenewalRequest()) {
            throw new RuntimeException('Lease ini masih memiliki pengajuan perpanjangan yang sedang diproses.');
        }

        $startDate = $startDate instanceof Carbon
            ? $startDate->copy()
            : Carbon::parse($startDate);

        $minimumStartDate = $lease->end_date instanceof Carbon
            ? $lease->end_date->copy()->addDay()
            : Carbon::parse($lease->end_date)->addDay();

        if ($startDate->lt($minimumStartDate)) {
            throw new RuntimeException('Tanggal mulai perpanjangan harus setelah lease aktif berakhir.');
        }

        $plot = $lease->plot;

        if (! $plot) {
            throw new RuntimeException('Data lahan untuk lease ini tidak ditemukan.');
        }

        $basePrice = match ($lease->term_type) {
            'monthly' => $plot->base_price_monthly,
            'yearly' => $plot->base_price_yearly,
            default => null,
        };

        if (! $basePrice) {
            throw new RuntimeException('Harga dasar untuk tipe sewa lease ini tidak tersedia.');
        }

        $endDate = match ($lease->term_type) {
            'monthly' => $startDate->copy()->addMonths($duration)->subDay(),
            'yearly' => $startDate->copy()->addYears($duration)->subDay(),
            default => throw new RuntimeException('Tipe sewa lease tidak valid.'),
        };

        return DB::transaction(function () use ($basePrice, $duration, $endDate, $lease, $startDate): BookingRequest {
            $bookingRequest = BookingRequest::query()->create([
                'user_id' => $lease->tenant_id,
                'plot_id' => $lease->plot_id,
                'renewal_of_lease_id' => $lease->id,
                'term_type' => $lease->term_type,
                'duration' => $duration,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'quoted_price' => (int) $basePrice * $duration,
                'final_price' => null,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => null,
            ]);

            BookingStatusEvent::query()->create([
                'booking_request_id' => $bookingRequest->id,
                'status' => 'pending',
                'changed_by' => null,
                'notes' => 'Renewal diajukan oleh tenant dari lease aktif.',
            ]);

            return $bookingRequest;
        });
    }
}
