<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminDashboardStatsWidget;
use App\Filament\Widgets\BookingTrendChartWidget;
use App\Filament\Widgets\DueInvoicesTableWidget;
use App\Filament\Widgets\PaymentTrendChartWidget;
use App\Filament\Widgets\PendingBookingRequestsTableWidget;
use App\Models\Market;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Schemas\Components\Section;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersAction;

    protected function getHeaderWidgets(): array
    {
        return [
            AdminDashboardStatsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getFooterWidgets(): array
    {
        return [
            PendingBookingRequestsTableWidget::class,
            DueInvoicesTableWidget::class,
            BookingTrendChartWidget::class,
            PaymentTrendChartWidget::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'xl' => 1,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->modalHeading('Filter Dashboard')
                ->modalWidth('sm')
                ->schema([
                    Section::make()
                        ->schema([
                            Select::make('market')
                                ->label('Pasar')
                                ->options(fn () => Market::query()->pluck('name', 'id'))
                                ->placeholder('Semua Pasar')
                                ->native(false),
                            Select::make('period')
                                ->label('Periode')
                                ->options([
                                    'last_7_days' => '7 Hari Terakhir',
                                    'last_30_days' => '30 Hari Terakhir',
                                    'last_90_days' => '90 Hari Terakhir',
                                    'this_month' => 'Bulan Ini',
                                    'last_month' => 'Bulan Lalu',
                                    'this_year' => 'Tahun Ini',
                                ])
                                ->default('last_30_days')
                                ->native(false),
                        ])
                        ->columns(1),
                ]),
        ];
    }
}
