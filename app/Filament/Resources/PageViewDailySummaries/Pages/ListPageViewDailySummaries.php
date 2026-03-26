<?php

namespace App\Filament\Resources\PageViewDailySummaries\Pages;

use App\Filament\Resources\PageViewDailySummaries\PageViewDailySummaryResource;
use App\Filament\Widgets\PageViewsStatsWidget;
use Filament\Resources\Pages\ListRecords;

class ListPageViewDailySummaries extends ListRecords
{
    protected static string $resource = PageViewDailySummaryResource::class;

    public function getHeaderWidgets(): array
    {
        return [
            PageViewsStatsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getHeaderWidgetsData(): array
    {
        return [
            'tableFilters' => $this->tableFilters,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
