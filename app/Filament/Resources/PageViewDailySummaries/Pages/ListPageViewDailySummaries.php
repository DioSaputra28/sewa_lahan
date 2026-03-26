<?php

namespace App\Filament\Resources\PageViewDailySummaries\Pages;

use App\Filament\Resources\PageViewDailySummaries\PageViewDailySummaryResource;
use App\Filament\Widgets\PageViewsStatsWidget;
use App\Models\PageViewDailySummary;
use App\Models\PageViewEvent;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

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
        return [
            Action::make('refreshRollup')
                ->label('Refresh Summary')
                ->icon('heroicon-o-arrow-path')
                ->action(function (): void {
                    Artisan::call('analytics:rollup-page-views', ['--date' => now()->toDateString()]);
                    Artisan::call('analytics:rollup-page-views', ['--date' => now()->subDay()->toDateString()]);

                    Notification::make()
                        ->title('Summary analytics berhasil di-refresh.')
                        ->success()
                        ->send();

                    $this->resetTable();
                }),
            Action::make('debugStatus')
                ->label('Debug Status')
                ->icon('heroicon-o-bug-ant')
                ->visible(fn (): bool => (bool) config('analytics.page_views.debug', false))
                ->modalHeading('Debug Tracking Halaman')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup')
                ->infolist([
                    TextEntry::make('app_env')
                        ->label('Environment')
                        ->state(fn (): string => app()->environment()),
                    TextEntry::make('tracking_enabled')
                        ->label('Tracking enabled')
                        ->state(fn (): string => config('analytics.page_views.enabled', true) ? 'yes' : 'no'),
                    TextEntry::make('debug_mode')
                        ->label('Debug mode')
                        ->state(fn (): string => config('analytics.page_views.debug', false) ? 'yes' : 'no'),
                    TextEntry::make('excluded_envs')
                        ->label('Excluded envs')
                        ->state(fn (): string => json_encode(config('analytics.page_views.excluded_environments', [])) ?: '[]'),
                    TextEntry::make('raw_today')
                        ->label('Raw events today')
                        ->state(fn (): string => number_format(PageViewEvent::query()->whereDate('visited_at', now()->toDateString())->count())),
                    TextEntry::make('summary_today')
                        ->label('Summary rows today')
                        ->state(fn (): string => number_format(PageViewDailySummary::query()->whereDate('date', now()->toDateString())->count())),
                    TextEntry::make('latest_event')
                        ->label('Latest raw event')
                        ->state(function (): string {
                            $latest = PageViewEvent::query()->latest('id')->first();

                            if (! $latest) {
                                return 'no-data';
                            }

                            return 'id='.$latest->id.' route='.$latest->route_name.' path='.$latest->path.' at='.$latest->visited_at?->toDateTimeString();
                        }),
                ]),
        ];
    }
}
