<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BookingRequests\BookingRequestResource;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Leases\LeaseResource;
use App\Filament\Resources\Payments\PaymentResource;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class AdminDashboardStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $marketId = $this->pageFilters['market'] ?? null;
        $period = $this->pageFilters['period'] ?? 'last_30_days';

        return [
            Stat::make('Permintaan Booking', number_format($this->getBookingPendingCount($marketId, $period)))
                ->icon(Heroicon::OutlinedCalendar)
                ->color('warning')
                ->url(BookingRequestResource::getUrl('index', [
                    'tableFilters' => [
                        'status' => ['value' => 'pending'],
                    ],
                ])),

            Stat::make('Invoice Belum Lunas', number_format($this->getInvoiceUnpaidCount($marketId, $period)))
                ->icon(Heroicon::OutlinedDocumentText)
                ->color('warning')
                ->url(InvoiceResource::getUrl('index', [
                    'tableFilters' => [
                        'status' => ['value' => 'unpaid'],
                    ],
                ])),

            Stat::make('Pembayaran Tertunda', number_format($this->getPaymentPendingCount($marketId, $period)))
                ->icon(Heroicon::OutlinedCreditCard)
                ->color('warning')
                ->url(PaymentResource::getUrl('index', [
                    'tableFilters' => [
                        'status' => ['value' => 'pending'],
                    ],
                ])),

            Stat::make('Lease Aktif', number_format($this->getActiveLeaseCount($marketId)))
                ->icon(Heroicon::OutlinedKey)
                ->color('success')
                ->url(LeaseResource::getUrl('index', [
                    'tableFilters' => [
                        'status' => ['value' => 'active'],
                    ],
                ])),
        ];
    }

    protected function getBookingPendingCount(?int $marketId, string $period): int
    {
        $query = BookingRequest::query()
            ->where('status', 'pending');

        if ($marketId) {
            $query->whereHas('plot', fn (Builder $q) => $q->where('market_id', $marketId));
        }

        [$startDate, $endDate] = $this->getDateRange($period);
        $query->whereBetween('created_at', [$startDate, $endDate]);

        return $query->count();
    }

    protected function getInvoiceUnpaidCount(?int $marketId, string $period): int
    {
        $query = Invoice::query()
            ->where('status', 'unpaid');

        if ($marketId) {
            $query->whereHas('bookingRequest.plot', fn (Builder $q) => $q->where('market_id', $marketId));
        }

        [$startDate, $endDate] = $this->getDateRange($period);
        $query->whereBetween('created_at', [$startDate, $endDate]);

        return $query->count();
    }

    protected function getPaymentPendingCount(?int $marketId, string $period): int
    {
        $query = Payment::query()
            ->where('status', 'pending');

        if ($marketId) {
            $query->whereHas('invoice.bookingRequest.plot', fn (Builder $q) => $q->where('market_id', $marketId));
        }

        [$startDate, $endDate] = $this->getDateRange($period);
        $query->whereBetween('created_at', [$startDate, $endDate]);

        return $query->count();
    }

    protected function getActiveLeaseCount(?int $marketId): int
    {
        $query = Lease::query()
            ->where('status', 'active');

        if ($marketId) {
            $query->whereHas('plot', fn (Builder $q) => $q->where('market_id', $marketId));
        }

        return $query->count();
    }

    protected function getDateRange(string $period): array
    {
        $today = Carbon::today();

        return match ($period) {
            'last_7_days' => [$today->copy()->subDays(6)->startOfDay(), $today->copy()->endOfDay()],
            'last_30_days' => [$today->copy()->subDays(29)->startOfDay(), $today->copy()->endOfDay()],
            'last_90_days' => [$today->copy()->subDays(89)->startOfDay(), $today->copy()->endOfDay()],
            'this_month' => [$today->copy()->startOfMonth(), $today->copy()->endOfDay()],
            'last_month' => [
                $today->copy()->subMonth()->startOfMonth(),
                $today->copy()->subMonth()->endOfMonth(),
            ],
            'this_year' => [$today->copy()->startOfYear(), $today->copy()->endOfDay()],
            default => [$today->copy()->subDays(29)->startOfDay(), $today->copy()->endOfDay()],
        };
    }
}
