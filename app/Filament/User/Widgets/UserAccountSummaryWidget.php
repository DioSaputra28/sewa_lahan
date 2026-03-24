<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Resources\Bookings\BookingResource;
use App\Filament\User\Resources\Invoices\InvoiceResource;
use App\Filament\User\Resources\Leases\LeaseResource;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\Lease;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class UserAccountSummaryWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected ?string $heading = 'Ringkasan Akun';

    protected function getStats(): array
    {
        $userId = Auth::id();

        return [
            Stat::make('Total Booking', $this->getBookingCount($userId))
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary')
                ->url(BookingResource::getUrl('index')),

            Stat::make('Total Invoice', $this->getInvoiceCount($userId))
                ->icon('heroicon-o-document-currency-dollar')
                ->color('primary')
                ->url(InvoiceResource::getUrl('index')),

            Stat::make('Total Kontrak', $this->getLeaseCount($userId))
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->url(LeaseResource::getUrl('index')),
        ];
    }

    protected function getBookingCount(int $userId): int
    {
        return BookingRequest::query()
            ->where('user_id', $userId)
            ->count();
    }

    protected function getInvoiceCount(int $userId): int
    {
        return Invoice::query()
            ->where('user_id', $userId)
            ->count();
    }

    protected function getLeaseCount(int $userId): int
    {
        return Lease::query()
            ->where('tenant_id', $userId)
            ->count();
    }
}
