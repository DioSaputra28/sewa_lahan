<?php

namespace App\Filament\User\Resources\Bookings\Pages;

use App\Filament\User\Resources\Bookings\BookingResource;
use App\Models\Plot;
use Filament\Resources\Pages\Page;

class BrowsePlots extends Page
{
    protected static string $resource = BookingResource::class;

    protected string $view = 'filament.user.resources.bookings.pages.browse-plots';

    public function getTitle(): string
    {
        return 'Cari Lahan';
    }

    public function getPlots()
    {
        return Plot::query()
            ->with(['market', 'area', 'images'])
            ->where('status', 'available')
            ->orderBy('name')
            ->get();
    }
}
