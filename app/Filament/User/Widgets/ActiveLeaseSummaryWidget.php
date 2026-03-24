<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Resources\Bookings\BookingResource;
use App\Filament\User\Resources\Leases\LeaseResource;
use App\Models\Lease;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ActiveLeaseSummaryWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected ?string $heading = 'Ringkasan Kontrak Aktif';

    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = 3;

    protected function getStats(): array
    {
        $lease = Lease::query()
            ->where('tenant_id', Auth::id())
            ->where('status', 'active')
            ->with('plot.market')
            ->first();

        if (! $lease) {
            return [
                Stat::make('Status', 'Belum Ada Kontrak Aktif')
                    ->description('Cari lahan yang tersedia untuk mulai mengajukan booking.')
                    ->icon('heroicon-o-building-storefront')
                    ->color('gray')
                    ->url(BookingResource::getUrl('browse')),
                Stat::make('Lahan', '-')
                    ->description('Belum ada lahan aktif')
                    ->icon('heroicon-o-map')
                    ->color('gray'),
                Stat::make('Berakhir', '-')
                    ->description('Belum ada masa kontrak berjalan')
                    ->icon('heroicon-o-calendar')
                    ->color('gray'),
            ];
        }

        return [
            Stat::make('Status', 'Aktif')
                ->description('Kelola kontrak aktifmu dari halaman kontrak.')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->url(LeaseResource::getUrl('index')),
            Stat::make('Lahan', $lease->plot?->name ?? '-')
                ->description($lease->plot?->market?->name ?? 'Tanpa pasar')
                ->icon('heroicon-o-building-storefront')
                ->color('primary')
                ->url(LeaseResource::getUrl('view', ['record' => $lease])),
            Stat::make('Berakhir', $lease->end_date?->format('d M Y') ?? '-')
                ->description('Masa aktif kontrak saat ini')
                ->icon('heroicon-o-calendar')
                ->color('warning')
                ->url(LeaseResource::getUrl('view', ['record' => $lease])),
        ];
    }
}
