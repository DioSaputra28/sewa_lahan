<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Plot;
use App\Services\PublicPlotListingQuery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LahanController extends Controller
{
    public function __construct(
        private PublicPlotListingQuery $listingQuery
    ) {}

    /**
     * Listing page with optional filters.
     */
    public function index(Request $request): View
    {
        $query = $this->listingQuery->baseQuery();

        $this->listingQuery->applySearch($query, $request->query('q'));
        $this->listingQuery->applyRegion($query, $request->query('region'));
        $this->listingQuery->applySize($query, $request->query('size'));
        $this->listingQuery->applyPrice($query, $request->query('price'));
        $this->listingQuery->applyPriceRange(
            $query,
            $request->query('price_min'),
            $request->query('price_max')
        );
        $this->listingQuery->applyAreaRange(
            $query,
            $request->query('area_min'),
            $request->query('area_max')
        );

        // Sort options
        $sort = $request->query('sort', 'newest');
        $query = match ($sort) {
            'price_asc' => $query->orderBy('base_price_monthly', 'asc'),
            'price_desc' => $query->orderBy('base_price_monthly', 'desc'),
            'size_desc' => $query->orderBy('area_square_meters', 'desc'),
            default => $query->orderByDesc('id'),
        };

        $perPage = 12;
        $plots = $query->paginate($perPage)->withQueryString();

        $regions = $this->listingQuery->availableRegions();
        $priceBuckets = PublicPlotListingQuery::PRICE_BUCKETS;
        $sizeBuckets = PublicPlotListingQuery::SIZE_BUCKETS;

        return view('web.lahan.index', [
            'plots' => $plots,
            'regions' => $regions,
            'priceBuckets' => $priceBuckets,
            'sizeBuckets' => $sizeBuckets,
            'filters' => [
                'region' => $request->query('region'),
                'size' => $request->query('size'),
                'price' => $request->query('price'),
                'q' => $request->query('q'),
                'price_min' => $request->query('price_min'),
                'price_max' => $request->query('price_max'),
                'area_min' => $request->query('area_min'),
                'area_max' => $request->query('area_max'),
                'sort' => $sort,
            ],
        ]);
    }

    /**
     * Dynamic plot detail page.
     */
    public function show(Plot $plot): View
    {
        // Guard: only show available plots with active market
        abort_unless(
            $plot->status === 'available'
            && $plot->market?->status === 'active'
            && ($plot->area_id === null || $plot->area?->status === 'active'),
            404
        );

        // Related: same market, available, exclude current
        $related = $this->listingQuery->baseQuery()
            ->where('market_id', $plot->market_id)
            ->where('id', '!=', $plot->id)
            ->limit(4)
            ->get();

        $primaryImage = $this->listingQuery->primaryImageUrl($plot);
        $allImages = $this->listingQuery->allImageUrls($plot);

        return view('web.lahan.single', [
            'plot' => $plot,
            'related' => $related,
            'primaryImage' => $primaryImage,
            'allImages' => $allImages,
        ]);
    }
}
