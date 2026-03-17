<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Models\Area;
use App\Models\Market;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('provider_order_id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('invoice.invoice_number')
                    ->label('Nomor invoice')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('provider')
                    ->label('Provider')
                    ->searchable(),
                TextColumn::make('provider_payment_method')
                    ->label('Metode bayar')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn (int $state): string => 'Rp '.number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status payment')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Gagal',
                        'expired' => 'Kadaluarsa',
                        'cancelled' => 'Dibatalkan',
                        default => ucfirst($state),
                    }),
                TextColumn::make('provider_status')
                    ->label('Status provider')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('paid_at')
                    ->label('Paid at')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status payment')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Gagal',
                        'expired' => 'Kadaluarsa',
                        'cancelled' => 'Dibatalkan',
                    ]),
                SelectFilter::make('provider')
                    ->label('Provider')
                    ->options([
                        'pakasir' => 'Pakasir',
                    ]),
                SelectFilter::make('provider_payment_method')
                    ->label('Metode bayar')
                    ->options([
                        'qris' => 'QRIS',
                        'virtual_account' => 'Virtual Account',
                        'bank_transfer' => 'Bank Transfer',
                    ]),
                SelectFilter::make('market_id')
                    ->label('Pasar')
                    ->options(Market::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, $value): Builder => $query->whereHas('invoice.bookingRequest.plot.market', fn (Builder $query): Builder => $query->whereKey($value)),
                    )),
                SelectFilter::make('area_id')
                    ->label('Area')
                    ->options(Area::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, $value): Builder => $query->whereHas('invoice.bookingRequest.plot.area', fn (Builder $query): Builder => $query->whereKey($value)),
                    )),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Lihat detail'),
            ])
            ->toolbarActions([]);
    }
}
