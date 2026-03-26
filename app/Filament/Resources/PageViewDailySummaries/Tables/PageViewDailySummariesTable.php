<?php

namespace App\Filament\Resources\PageViewDailySummaries\Tables;

use App\Models\Plot;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PageViewDailySummariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('page_key')
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('page_key')
                    ->label('Halaman')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::pageLabel($state))
                    ->searchable(),
                TextColumn::make('route_name')
                    ->label('Route')
                    ->searchable(),
                TextColumn::make('plot.name')
                    ->label('Lahan')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('total_views')
                    ->label('Total View')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unique_visitors')
                    ->label('Unique Visitor')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('period')
                    ->label('Periode')
                    ->options([
                        'last_7_days' => '7 hari terakhir',
                        'last_30_days' => '30 hari terakhir',
                        'last_90_days' => '90 hari terakhir',
                        'this_month' => 'Bulan ini',
                    ])
                    ->default('last_30_days')
                    ->query(function (Builder $query, array $data): Builder {
                        [$from, $until] = self::resolveDateRange($data['value'] ?? 'last_30_days');

                        return $query->whereBetween('date', [$from->toDateString(), $until->toDateString()]);
                    }),
                SelectFilter::make('page_key')
                    ->label('Halaman')
                    ->options(self::pageOptions()),
                SelectFilter::make('plot_id')
                    ->label('Lahan')
                    ->options(Plot::query()->orderBy('name')->pluck('name', 'id')->all()),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function resolveDateRange(string $period): array
    {
        $today = Carbon::today();

        return match ($period) {
            'last_7_days' => [$today->copy()->subDays(6), $today->copy()],
            'last_90_days' => [$today->copy()->subDays(89), $today->copy()],
            'this_month' => [$today->copy()->startOfMonth(), $today->copy()],
            default => [$today->copy()->subDays(29), $today->copy()],
        };
    }

    public static function pageOptions(): array
    {
        $routes = config('analytics.page_views.tracked_routes', []);

        if (! is_array($routes)) {
            return [];
        }

        return collect($routes)
            ->filter(fn (mixed $label, mixed $key): bool => is_string($key) && is_string($label))
            ->mapWithKeys(fn (string $label, string $key): array => [$key => $label])
            ->all();
    }

    public static function pageLabel(string $pageKey): string
    {
        return self::pageOptions()[$pageKey] ?? $pageKey;
    }
}
