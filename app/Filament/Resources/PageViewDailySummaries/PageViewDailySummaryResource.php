<?php

namespace App\Filament\Resources\PageViewDailySummaries;

use App\Filament\Resources\PageViewDailySummaries\Pages\ListPageViewDailySummaries;
use App\Filament\Resources\PageViewDailySummaries\Tables\PageViewDailySummariesTable;
use App\Models\PageViewDailySummary;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class PageViewDailySummaryResource extends Resource
{
    protected static ?string $model = PageViewDailySummary::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Tracking Halaman';

    protected static ?string $slug = 'page-view-analytics';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 30;

    protected static ?string $modelLabel = 'ringkasan kunjungan';

    protected static ?string $pluralModelLabel = 'ringkasan kunjungan';

    public static function table(Table $table): Table
    {
        return PageViewDailySummariesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPageViewDailySummaries::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
