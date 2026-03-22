<?php

namespace App\Filament\Widgets;

use App\Models\BookingRequest;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;

class BookingTrendChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = [
        'default' => 1,
        'xl' => 1,
    ];

    protected ?string $heading = 'Tren Booking';

    protected ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $period = $this->pageFilters['period'] ?? 'last_30_days';
        $marketId = $this->pageFilters['market'] ?? null;

        [$startDate, $endDate] = $this->getDateRange($period);

        $query = BookingRequest::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($marketId) {
            $query->whereHas('plot', function ($q) use ($marketId) {
                $q->where('market_id', $marketId);
            });
        }

        $bookings = $query->get()
            ->groupBy(fn ($booking) => $booking->created_at->toDateString())
            ->map(fn (Collection $dayBookings) => $dayBookings->count());

        $labels = [];
        $data = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateString = $current->toDateString();
            $labels[] = $current->format('d M');
            $data[] = $bookings->get($dateString, 0);
            $current->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Permintaan Booking',
                    'data' => $data,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.4,
                    'fill' => true,
                    'pointRadius' => 0,
                    'pointHoverRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getDateRange(string $period): array
    {
        $today = Carbon::today();

        return match ($period) {
            'last_7_days' => [$today->copy()->subDays(6), $today],
            'last_30_days' => [$today->copy()->subDays(29), $today],
            'last_90_days' => [$today->copy()->subDays(89), $today],
            'this_month' => [$today->copy()->startOfMonth(), $today],
            'last_month' => [
                $today->copy()->subMonth()->startOfMonth(),
                $today->copy()->subMonth()->endOfMonth(),
            ],
            'this_year' => [$today->copy()->startOfYear(), $today],
            default => [$today->copy()->subDays(29), $today],
        };
    }

    protected function getType(): string
    {
        return 'line';
    }
}
