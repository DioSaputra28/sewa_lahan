<?php

namespace App\Filament\User\Resources\Bookings\Pages;

use App\Filament\User\Resources\Bookings\BookingResource;
use App\Models\Plot;
use App\Services\PublicPlotListingQuery;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class ViewPlot extends Page
{
    protected static string $resource = BookingResource::class;

    protected string $view = 'filament.user.resources.bookings.pages.view-plot';

    public Plot $plot;

    public ?string $primaryImage = null;

    /**
     * @var array<int, string>
     */
    public array $allImages = [];

    /**
     * @var Collection<int, Plot>
     */
    public Collection $relatedPlots;

    public function mount(Plot $plot): void
    {
        abort_if($plot->status !== 'available', 404);

        $this->plot = $plot->load(['market', 'area', 'images']);

        $listingQuery = app(PublicPlotListingQuery::class);
        $this->primaryImage = $listingQuery->primaryImageUrl($this->plot);
        $this->allImages = $listingQuery->allImageUrls($this->plot);
        $this->relatedPlots = $listingQuery->baseQuery()
            ->where('market_id', $this->plot->market_id)
            ->whereKeyNot($this->plot->id)
            ->limit(4)
            ->get();
    }

    public function getTitle(): string
    {
        return 'Detail Lahan';
    }
}
