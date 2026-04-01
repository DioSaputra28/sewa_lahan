<?php

namespace App\Services;

use App\Models\Plot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class PublicPlotListingQuery
{
    /**
     * Price bucket thresholds (in IDR).
     */
    public const PRICE_BUCKETS = [
        'under_1m' => ['label' => '< Rp 1 JT', 'max' => 1_000_000],
        '1m_to_2m' => ['label' => 'Rp 1–2 JT', 'min' => 1_000_000, 'max' => 2_000_000],
        'over_2m' => ['label' => '> Rp 2 JT',  'min' => 2_000_000],
    ];

    /**
     * Size bucket thresholds (in square meters).
     */
    public const SIZE_BUCKETS = [
        'small' => ['label' => '< 4 m²',    'max' => 4],
        'medium' => ['label' => '4–9 m²',    'min' => 4, 'max' => 9],
        'large' => ['label' => '> 9 m²',    'min' => 9],
    ];

    /**
     * Build the base public query with all visibility guards and eager loading.
     */
    public function baseQuery(): Builder
    {
        return Plot::query()
            ->with([
                'market',
                'area',
                'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('sort_order'),
            ])
            ->whereHas('market', fn ($q) => $q->where('status', 'active'))
            ->where(function (Builder $q) {
                $q->whereNull('area_id')
                    ->orWhereHas('area', fn ($q) => $q->where('status', 'active'));
            })
            ->where('status', 'available');
    }

    /**
     * Apply region (city) filter.
     */
    public function applyRegion(Builder $query, ?string $region): Builder
    {
        if ($region && $region !== 'all') {
            $query->whereHas('market', fn ($q) => $q->where('city', $region));
        }

        return $query;
    }

    /**
     * Apply search by plot name, market name, and market city.
     */
    public function applySearch(Builder $query, ?string $search): Builder
    {
        $search = is_string($search) ? trim($search) : '';

        if ($search === '') {
            return $query;
        }

        $query->where(function (Builder $nestedQuery) use ($search): void {
            $nestedQuery->where('name', 'like', "%{$search}%")
                ->orWhereHas('market', function (Builder $marketQuery) use ($search): void {
                    $marketQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
        });

        return $query;
    }

    /**
     * Apply size bucket filter.
     */
    public function applySize(Builder $query, ?string $size): Builder
    {
        if (! $size || $size === 'all') {
            return $query;
        }

        $bucket = self::SIZE_BUCKETS[$size] ?? null;
        if (! $bucket) {
            return $query;
        }

        if (isset($bucket['min'])) {
            $query->where('area_square_meters', '>=', $bucket['min']);
        }
        if (isset($bucket['max'])) {
            $query->where('area_square_meters', '<=', $bucket['max']);
        }

        return $query;
    }

    /**
     * Apply price bucket filter.
     */
    public function applyPrice(Builder $query, ?string $price): Builder
    {
        if (! $price || $price === 'all') {
            return $query;
        }

        $bucket = self::PRICE_BUCKETS[$price] ?? null;
        if (! $bucket) {
            return $query;
        }

        if (isset($bucket['min'])) {
            $query->where('base_price_monthly', '>=', $bucket['min']);
        }
        if (isset($bucket['max'])) {
            $query->where('base_price_monthly', '<=', $bucket['max']);
        }

        return $query;
    }

    /**
     * Apply price range filter.
     */
    public function applyPriceRange(Builder $query, mixed $minimumPrice, mixed $maximumPrice): Builder
    {
        if (is_numeric($minimumPrice)) {
            $query->where('base_price_monthly', '>=', (int) $minimumPrice);
        }

        if (is_numeric($maximumPrice)) {
            $query->where('base_price_monthly', '<=', (int) $maximumPrice);
        }

        return $query;
    }

    /**
     * Apply area range filter.
     */
    public function applyAreaRange(Builder $query, mixed $minimumArea, mixed $maximumArea): Builder
    {
        if (is_numeric($minimumArea)) {
            $query->where('area_square_meters', '>=', (float) $minimumArea);
        }

        if (is_numeric($maximumArea)) {
            $query->where('area_square_meters', '<=', (float) $maximumArea);
        }

        return $query;
    }

    /**
     * Get all active regions (cities) from markets that have available plots.
     */
    public function availableRegions(): array
    {
        return Plot::query()
            ->with('market')
            ->whereHas('market', fn ($q) => $q->where('status', 'active'))
            ->where('status', 'available')
            ->get()
            ->pluck('market.city')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Resolve image URL with fallback.
     * Supports external URLs (http://, https://) and local storage paths.
     */
    public function resolveImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return $path;
        }

        $normalizedPath = ltrim($path, '/');

        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        $publicDisk = Storage::disk('public');
        if ($publicDisk->exists($normalizedPath)) {
            return $publicDisk->url($normalizedPath);
        }

        if (! Storage::exists($normalizedPath)) {
            return null;
        }

        return Storage::url($normalizedPath);
    }

    /**
     * Get primary image URL for a plot.
     */
    public function primaryImageUrl(Plot $plot): ?string
    {
        $primary = $plot->images->first(fn ($img) => $img->is_primary)
            ?? $plot->images->first();

        if (! $primary) {
            return $this->placeholderImage();
        }

        $url = $this->resolveImageUrl($primary->image_path);

        return $url ?? $this->placeholderImage();
    }

    /**
     * Get all image URLs for a plot.
     */
    public function allImageUrls(Plot $plot): array
    {
        if ($plot->images->isEmpty()) {
            return array_fill(0, 4, $this->placeholderImage());
        }

        $urls = $plot->images
            ->map(fn ($img) => $this->resolveImageUrl($img->image_path))
            ->filter()
            ->values()
            ->all();

        // Pad to 4 with placeholder
        while (count($urls) < 4) {
            $urls[] = $this->placeholderImage();
        }

        return $urls;
    }

    /**
     * Format price in IDR shorthand.
     */
    public function formatPrice(int $amount): string
    {
        if ($amount >= 1_000_000) {
            return 'Rp '.number_format($amount / 1_000_000, 1).' JT';
        }

        return 'Rp '.number_format($amount / 1_000, 0).' RB';
    }

    /**
     * Format full price with commas.
     */
    public function formatPriceFull(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }

    /**
     * Placeholder image URL (SVG data URI).
     */
    public function placeholderImage(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600"><rect fill="#e5e7eb" width="800" height="600"/><text x="400" y="300" font-family="sans-serif" font-size="24" fill="#9ca3af" text-anchor="middle" dominant-baseline="middle">No Image</text></svg>';

        return 'data:image/svg+xml,'.rawurlencode($svg);
    }
}
