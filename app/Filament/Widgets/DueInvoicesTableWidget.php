<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class DueInvoicesTableWidget extends TableWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = [
        'default' => 1,
        'xl' => 1,
    ];

    protected function getHeading(): string
    {
        return 'Invoice Jatuh Tempo';
    }

    protected function applyFilters(Builder $query): Builder
    {
        $query->where('status', 'unpaid');

        $query->where(function (Builder $q): Builder {
            return $q
                ->whereDate('due_date', '<=', Carbon::now()->addDays(7))
                ->orWhereDate('due_date', '<', Carbon::now());
        });

        if ($marketId = $this->pageFilters['market'] ?? null) {
            $query->whereHas('bookingRequest.plot', function (Builder $q) use ($marketId): Builder {
                return $q->where('market_id', $marketId);
            });
        }

        if ($period = $this->pageFilters['period'] ?? null) {
            [$startDate, $endDate] = $this->getDateRange($period);
            $query->where(function (Builder $q) use ($startDate, $endDate): Builder {
                return $q->whereBetween('issue_date', [$startDate, $endDate])
                    ->orWhereBetween('due_date', [$startDate, $endDate]);
            });
        }

        return $query;
    }

    protected function getDateRange(string $period): array
    {
        $today = Carbon::today();

        return match ($period) {
            'last_7_days' => [$today->copy()->subDays(6), $today],
            'last_30_days' => [$today->copy()->subDays(29), $today],
            'last_90_days' => [$today->copy()->subDays(89), $today],
            'this_month' => [$today->copy()->startOfMonth(), $today],
            'last_month' => [
                $today->copy()->subMonth()->startOfMonth(),
                $today->copy()->subMonth()->endOfMonth(),
            ],
            'this_year' => [$today->copy()->startOfYear(), $today],
            default => [$today->copy()->subDays(29), $today],
        };
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => $this->applyFilters(
                    Invoice::query()->with([
                        'user',
                        'bookingRequest',
                        'bookingRequest.plot',
                        'bookingRequest.plot.market',
                    ]),
                )->orderBy('due_date', 'asc')->limit(8),
            )
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->sortable()
                    ->wrap(),
                TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->wrap(),
                TextColumn::make('plot')
                    ->label('Lapak')
                    ->formatStateUsing(fn (Invoice $record): string => $record->bookingRequest?->plot?->name ?? '-')
                    ->limit(24)
                    ->wrap(),
                TextColumn::make('total_amount')
                    ->label('Jumlah')
                    ->formatStateUsing(fn (int $state): string => 'Rp '.number_format($state / 1000, 0, ',', '.').'k')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->formatStateUsing(function ($state): string {
                        $months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                        $date = Carbon::parse($state);

                        return $date->format('d').' '.$months[(int) $date->format('n')];
                    })
                    ->color(fn (Invoice $record): string => $record->due_date->isPast() ? 'warning' : 'gray')
                    ->sortable(),
                TextColumn::make('badge')
                    ->label('')
                    ->formatStateUsing(fn (Invoice $record): string => $record->due_date->isPast() ? 'Telat' : 'Maks 7 hari')
                    ->badge()
                    ->color(fn (Invoice $record): string => $record->due_date->isPast() ? 'danger' : 'warning'),
            ])
            ->recordUrl(fn (Invoice $record): string => InvoiceResource::getUrl('edit', ['record' => $record]))
            ->headerActions([
                Action::make('view_all')
                    ->label('Lihat Semua')
                    ->url(InvoiceResource::getUrl('index', [
                        'tableFilters' => [
                            'status' => ['value' => 'unpaid'],
                        ],
                    ]))
                    ->icon('heroicon-m-arrow-trending-down')
                    ->size('sm'),
            ])
            ->paginated(false);
    }
}
