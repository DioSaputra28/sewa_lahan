<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Resources\Bookings\BookingResource;
use App\Filament\User\Resources\Invoices\InvoiceResource;
use App\Filament\User\Resources\Leases\LeaseResource;
use App\Filament\User\Widgets\ActiveLeaseSummaryWidget;
use App\Filament\User\Widgets\PendingInvoicesCardsWidget;
use App\Filament\User\Widgets\RecentBookingsCardsWidget;
use App\Filament\User\Widgets\UserAccountSummaryWidget;
use App\Models\Lease;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Beranda';

    public function getTitle(): string|Htmlable
    {
        return 'Beranda';
    }

    public function getHeaderWidgets(): array
    {
        return [
            ActiveLeaseSummaryWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('browsePlots')
                ->label('Cari Lahan')
                ->icon('heroicon-o-magnifying-glass')
                ->color('primary')
                ->url(BookingResource::getUrl('browse')),
            Action::make('continuePayment')
                ->label('Lanjutkan Pembayaran')
                ->icon('heroicon-o-credit-card')
                ->color('warning')
                ->url(InvoiceResource::getUrl('index')),
            Action::make('requestRenewal')
                ->label('Ajukan Perpanjangan')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->visible(fn (): bool => $this->hasRenewableLease())
                ->url(LeaseResource::getUrl('index')),
            Action::make('viewInvoices')
                ->label('Lihat Invoice')
                ->icon('heroicon-o-document-currency-dollar')
                ->color('gray')
                ->url(InvoiceResource::getUrl('index')),
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            PendingInvoicesCardsWidget::class,
            RecentBookingsCardsWidget::class,
            UserAccountSummaryWidget::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'xl' => 2,
        ];
    }

    /**
     * @return array<class-string<Widget>>
     */
    public function getWidgets(): array
    {
        return [];
    }

    protected function hasRenewableLease(): bool
    {
        return Lease::query()
            ->where('tenant_id', Auth::id())
            ->where('status', 'active')
            ->get()
            ->contains(fn (Lease $lease): bool => ! $lease->hasUnresolvedRenewalRequest());
    }
}
