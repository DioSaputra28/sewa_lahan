<?php

namespace App\Actions\Bookings;

use App\Models\BookingRequest;
use App\Models\BookingStatusEvent;
use App\Models\Plot;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateUserBookingRequest
{
    public function handle(
        User $user,
        Plot $plot,
        string $termType,
        int $duration,
        DateTimeInterface|string $startDate,
        ?string $notes = null,
    ): BookingRequest {
        if ($plot->status !== 'available') {
            throw new RuntimeException('Lahan ini belum tersedia untuk diajukan.');
        }

        $price = match ($termType) {
            'monthly' => $plot->base_price_monthly,
            'yearly' => $plot->base_price_yearly,
            default => null,
        };

        if (! $price) {
            throw new RuntimeException('Metode sewa yang dipilih tidak tersedia untuk lahan ini.');
        }

        $startDate = $startDate instanceof Carbon
            ? $startDate->copy()
            : Carbon::parse($startDate);

        $endDate = match ($termType) {
            'monthly' => $startDate->copy()->addMonths($duration)->subDay(),
            'yearly' => $startDate->copy()->addYears($duration)->subDay(),
            default => throw new RuntimeException('Tipe sewa tidak valid.'),
        };

        return DB::transaction(function () use ($duration, $endDate, $notes, $plot, $price, $startDate, $termType, $user): BookingRequest {
            $bookingRequest = BookingRequest::query()->create([
                'user_id' => $user->id,
                'plot_id' => $plot->id,
                'term_type' => $termType,
                'duration' => $duration,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'quoted_price' => $price * $duration,
                'final_price' => null,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => $notes,
            ]);

            BookingStatusEvent::query()->create([
                'booking_request_id' => $bookingRequest->id,
                'status' => 'pending',
                'changed_by' => null,
                'notes' => 'Booking diajukan oleh customer.',
            ]);

            return $bookingRequest;
        });
    }
}
