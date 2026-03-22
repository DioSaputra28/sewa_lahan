<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PublicPlotListingQuery;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private PublicPlotListingQuery $listingQuery
    ) {}

    public function __invoke(): View
    {
        $query = $this->listingQuery->baseQuery();

        // Preview: latest 6 available plots for home showcase
        $previewPlots = $query->latest()->limit(6)->get();

        $regions = $this->listingQuery->availableRegions();

        return view('web.landing', [
            'previewPlots' => $previewPlots,
            'regions' => $regions,
        ]);
    }
}
