<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BookingRequests\BookingRequestResource;
use App\Models\BookingRequest;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingBookingRequestsTableWidget extends TableWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = [
        'default' => 1,
        'xl' => 1,
    ];

    protected function getHeading(): string
    {
        return 'Permintaan Booking Menunggu Tinjauan';
    }

    protected function applyFilters(Builder $query): Builder
    {
        $query->where('status', 'pending');

        $query->where(function (Builder $q): Builder {
            return $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });

        if ($marketId = $this->pageFilters['market'] ?? null) {
            $query->whereHas('plot.market', function (Builder $q) use ($marketId): Builder {
                return $q->whereKey($marketId);
            });
        }

        if ($period = $this->pageFilters['period'] ?? null) {
            $periodStart = match ($period) {
                'last_7_days' => Carbon::now()->subDays(6)->startOfDay(),
                'last_30_days' => Carbon::now()->subDays(29)->startOfDay(),
                'last_90_days' => Carbon::now()->subDays(89)->startOfDay(),
                'this_month' => Carbon::now()->startOfMonth(),
                'last_month' => Carbon::now()->subMonth()->startOfMonth(),
                'this_year' => Carbon::now()->startOfYear(),
                default => null,
            };

            if ($periodStart) {
                $query->where('created_at', '>=', $periodStart);
            }
        }

        return $query;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => $this->applyFilters(
                    BookingRequest::query()->with(['user', 'plot.market']),
                )->orderBy('created_at', 'asc')->limit(8),
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->sortable()
                    ->wrap(),
                TextColumn::make('plot.name')
                    ->label('Lapak')
                    ->formatStateUsing(fn ($record): string => $record->plot->name.' - '.$record->plot->market->name)
                    ->limit(24)
                    ->wrap(),
                TextColumn::make('term')
                    ->label('Jangka Waktu')
                    ->formatStateUsing(fn (BookingRequest $record): string => ucfirst($record->term_type).' · '.$record->duration.' bln'),
                TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->formatStateUsing(function ($state): string {
                        return Carbon::parse($state)->locale('id')->diffForHumans(['parts' => 2, 'join' => ', ']);
                    }),
            ])
            ->recordUrl(fn (BookingRequest $record): string => BookingRequestResource::getUrl('edit', ['record' => $record]))
            ->headerActions([
                Action::make('view_all')
                    ->label('Lihat Semua')
                    ->url(BookingRequestResource::getUrl('index', [
                        'tableFilters' => [
                            'status' => ['value' => 'pending'],
                        ],
                    ]))
                    ->icon('heroicon-m-arrow-trending-down')
                    ->size('sm'),
            ])
            ->paginated(false);
    }
}
