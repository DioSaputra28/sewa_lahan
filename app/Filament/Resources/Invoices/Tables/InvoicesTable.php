<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Models\Area;
use App\Models\Market;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_number')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Nomor invoice')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bookingRequest.plot.name')
                    ->label('Lahan')
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn (int $state): string => 'Rp '.number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Jatuh tempo')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unpaid' => 'Belum dibayar',
                        'pending' => 'Pending',
                        'paid' => 'Sudah dibayar',
                        'expired' => 'Kadaluarsa',
                        'cancelled' => 'Dibatalkan',
                        default => ucfirst($state),
                    }),
                TextColumn::make('paid_at')
                    ->label('Dibayar pada')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'unpaid' => 'Belum dibayar',
                        'pending' => 'Pending',
                        'paid' => 'Sudah dibayar',
                        'expired' => 'Kadaluarsa',
                        'cancelled' => 'Dibatalkan',
                    ]),
                SelectFilter::make('market_id')
                    ->label('Pasar')
                    ->options(Market::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, $value): Builder => $query->whereHas('bookingRequest.plot.market', fn (Builder $query): Builder => $query->whereKey($value)),
                    )),
                SelectFilter::make('area_id')
                    ->label('Area')
                    ->options(Area::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, $value): Builder => $query->whereHas('bookingRequest.plot.area', fn (Builder $query): Builder => $query->whereKey($value)),
                    )),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Kelola'),
            ])
            ->toolbarActions([]);
    }
}
