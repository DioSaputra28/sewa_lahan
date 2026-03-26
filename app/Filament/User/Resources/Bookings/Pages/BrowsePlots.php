<?php

namespace App\Filament\User\Resources\Bookings\Pages;

use App\Filament\User\Resources\Bookings\BookingResource;
use App\Models\Plot;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;

class BrowsePlots extends Page
{
    protected static string $resource = BookingResource::class;

    protected string $view = 'filament.user.resources.bookings.pages.browse-plots';

    public string $search = '';

    public function getTitle(): string
    {
        return 'Cari Lahan';
    }

    public function getPlots()
    {
        $search = trim($this->search);

        return Plot::query()
            ->with(['market', 'area', 'images'])
            ->where('status', 'available')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $nestedQuery) use ($search): void {
                    $nestedQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhereHas('market', function (Builder $marketQuery) use ($search): void {
                            $marketQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('city', 'like', "%{$search}%");
                        })
                        ->orWhereHas('area', fn (Builder $areaQuery): Builder => $areaQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('name')
            ->get();
    }
}
