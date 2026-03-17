<?php

namespace App\Filament\User\Resources\Bookings\Pages;

use App\Actions\Bookings\CreateUserBookingRequest;
use App\Filament\User\Resources\Bookings\BookingResource;
use App\Models\BookingRequest;
use App\Models\Plot;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    public function mount(): void
    {
        abort_unless(request()->integer('plot'), 404);

        $plot = Plot::query()->findOrFail(request()->integer('plot'));

        abort_if($plot->status !== 'available', 404);

        parent::mount();
    }

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->danger()
            ->title('Form booking belum valid.')
            ->body('Periksa kembali tipe sewa, durasi, dan tanggal mulai booking.')
            ->send();
    }

    protected function handleRecordCreation(array $data): BookingRequest
    {
        $plot = Plot::query()->findOrFail((int) $data['plot_id']);

        return app(CreateUserBookingRequest::class)->handle(
            user: Auth::user(),
            plot: $plot,
            termType: $data['term_type'],
            duration: (int) $data['duration'],
            startDate: $data['start_date'],
            notes: $data['notes'] ?? null,
        );
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Booking berhasil diajukan.';
    }
}
