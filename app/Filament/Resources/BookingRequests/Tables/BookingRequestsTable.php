<?php

namespace App\Filament\Resources\BookingRequests\Tables;

use App\Models\Area;
use App\Models\Market;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('plot.name')
                    ->label('Lahan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('term_type')
                    ->label('Tipe sewa')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'monthly' => 'Bulanan',
                        'yearly' => 'Tahunan',
                        default => $state ? ucfirst($state) : '-',
                    }),
                TextColumn::make('duration')
                    ->label('Durasi')
                    ->suffix(' periode')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('final_price')
                    ->label('Harga final')
                    ->formatStateUsing(fn ($state, $record): string => 'Rp '.number_format((int) ($state ?? $record->quoted_price ?? 0), 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status booking')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                        'expired' => 'Expired',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                        'expired' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('payment_status')
                    ->label('Status bayar')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unpaid' => 'Belum dibayar',
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Gagal',
                        'cancelled' => 'Dibatalkan',
                        'expired' => 'Expired',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'unpaid' => 'warning',
                        'pending' => 'info',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        'expired' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status booking')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                        'expired' => 'Expired',
                    ]),
                SelectFilter::make('payment_status')
                    ->label('Status pembayaran')
                    ->options([
                        'unpaid' => 'Belum dibayar',
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Gagal',
                        'cancelled' => 'Dibatalkan',
                        'expired' => 'Expired',
                    ]),
                SelectFilter::make('market_id')
                    ->label('Pasar')
                    ->options(Market::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, $value): Builder => $query->whereHas('plot.market', fn (Builder $query): Builder => $query->whereKey($value)),
                    )),
                SelectFilter::make('area_id')
                    ->label('Area')
                    ->options(Area::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, $value): Builder => $query->whereHas('plot.area', fn (Builder $query): Builder => $query->whereKey($value)),
                    )),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Review'),
            ])
            ->toolbarActions([]);
    }
}
