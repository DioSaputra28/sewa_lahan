<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;

class PaymentTrendChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = [
        'default' => 1,
        'xl' => 1,
    ];

    protected ?string $maxHeight = '200px';

    public function getHeading(): string|Htmlable|null
    {
        $period = $this->pageFilters['period'] ?? 'last_30_days';

        return match ($period) {
            'last_7_days' => 'Tren Pembayaran 7 Hari',
            'last_30_days' => 'Tren Pembayaran',
            'last_90_days' => 'Tren Pembayaran 90 Hari',
            'this_month' => 'Tren Pembayaran Bulan Ini',
            'last_month' => 'Tren Pembayaran Bulan Lalu',
            'this_year' => 'Tren Pembayaran Tahun Ini',
            default => 'Tren Pembayaran',
        };
    }

    protected function getData(): array
    {
        $period = $this->pageFilters['period'] ?? 'last_30_days';
        $marketId = $this->pageFilters['market'] ?? null;

        [$startDate, $endDate] = $this->getDateRange($period);

        $query = Payment::query()
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'paid']);

        if ($marketId) {
            $query->whereHas('invoice.bookingRequest.plot', function ($q) use ($marketId) {
                $q->where('market_id', $marketId);
            });
        }

        $payments = $query->get()
            ->groupBy(fn ($payment) => $payment->paid_at->toDateString())
            ->map(fn (Collection $dayPayments) => $dayPayments->sum('amount') / 1000);

        $labels = [];
        $data = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateString = $current->toDateString();
            $labels[] = $current->format('d M');
            $data[] = round($payments->get($dateString, 0), 0);
            $current->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pembayaran (Ribuan Rp)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                    'borderColor' => '#10b981',
                    'borderRadius' => 4,
                    'borderWidth' => 0,
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
        return 'bar';
    }
}
