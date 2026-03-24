<?php

namespace App\Filament\User\Resources\Bookings\Pages;

use App\Filament\User\Resources\Bookings\BookingResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    public function getTitle(): string
    {
        return 'Booking Saya';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('browsePlots')
                ->label('Cari lahan')
                ->url(BookingResource::getUrl('browse')),
        ];
    }
}
