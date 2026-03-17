<?php

namespace App\Filament\User\Resources\Bookings\Pages;

use App\Filament\User\Resources\Bookings\BookingResource;
use Filament\Resources\Pages\Page;

class ListPlots extends Page
{
    protected static string $resource = BookingResource::class;

    protected string $view = 'filament.user.resources.bookings.pages.list-plots';
}
