<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PageViewDailySummaries\Tables\PageViewDailySummariesTable;
use App\Models\PageViewDailySummary;
use App\Models\Plot;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class PageViewsStatsWidget extends StatsOverviewWidget
{
    public ?array $tableFilters = [];

    protected function getStats(): array
    {
        $period = data_get($this->tableFilters, 'period.value', 'last_30_days');
        $pageKey = data_get($this->tableFilters, 'page_key.value');
        $plotId = data_get($this->tableFilters, 'plot_id.value');

        $query = PageViewDailySummary::query();
        $query = $this->applyFilters($query, (string) $period, $pageKey, $plotId);

        $totalViews = (clone $query)->sum('total_views');
        $uniqueVisitors = (clone $query)->sum('unique_visitors');

        $topPage = (clone $query)
            ->selectRaw('page_key, SUM(total_views) as views')
            ->groupBy('page_key')
            ->orderByDesc('views')
            ->first();

        $topPlot = (clone $query)
            ->whereNotNull('plot_id')
            ->selectRaw('plot_id, SUM(total_views) as views')
            ->groupBy('plot_id')
            ->orderByDesc('views')
            ->first();

        $topPageLabel = $topPage
            ? PageViewDailySummariesTable::pageLabel((string) $topPage->page_key)
            : '—';

        $topPlotLabel = '—';
        if ($topPlot?->plot_id) {
            $plotName = Plot::query()->whereKey((int) $topPlot->plot_id)->value('name');
            if (is_string($plotName) && $plotName !== '') {
                $topPlotLabel = $plotName;
            }
        }

        return [
            Stat::make('Total Views', number_format((int) $totalViews)),
            Stat::make('Unique Visitors', number_format((int) $uniqueVisitors)),
            Stat::make('Top Page', $topPageLabel),
            Stat::make('Top Plot', $topPlotLabel),
        ];
    }

    protected function applyFilters(Builder $query, string $period, mixed $pageKey, mixed $plotId): Builder
    {
        [$from, $until] = PageViewDailySummariesTable::resolveDateRange($period);

        $query->whereBetween('date', [$from->toDateString(), $until->toDateString()]);

        if (is_string($pageKey) && $pageKey !== '') {
            $query->where('page_key', $pageKey);
        }

        if (is_numeric($plotId)) {
            $query->where('plot_id', (int) $plotId);
        }

        return $query;
    }
}
