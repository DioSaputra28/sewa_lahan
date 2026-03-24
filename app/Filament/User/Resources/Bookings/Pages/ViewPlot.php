<?php

namespace App\Filament\User\Resources\Bookings\Pages;

use App\Filament\User\Resources\Bookings\BookingResource;
use App\Models\Plot;
use Filament\Resources\Pages\Page;

class ViewPlot extends Page
{
    protected static string $resource = BookingResource::class;

    protected string $view = 'filament.user.resources.bookings.pages.view-plot';

    public Plot $plot;

    public function mount(Plot $plot): void
    {
        abort_if($plot->status !== 'available', 404);

        $this->plot = $plot->load(['market', 'area', 'images']);
    }

    public function getTitle(): string
    {
        return 'Detail Lahan';
    }
}
