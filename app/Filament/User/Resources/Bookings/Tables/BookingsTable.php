<?php

namespace App\Filament\User\Resources\Bookings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('plot.name')
                    ->label('Lahan')
                    ->searchable(),
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
                    ->suffix(' periode'),
                TextColumn::make('quoted_price')
                    ->label('Harga awal')
                    ->formatStateUsing(fn ($state): string => 'Rp '.number_format((int) ($state ?? 0), 0, ',', '.')),
                TextColumn::make('status')
                    ->label('Status booking')
                    ->badge(),
                TextColumn::make('payment_status')
                    ->label('Status bayar')
                    ->badge(),
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
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('payment_status')
                    ->label('Status pembayaran')
                    ->options([
                        'unpaid' => 'Belum dibayar',
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Gagal',
                        'expired' => 'Expired',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Lihat detail'),
            ])
            ->toolbarActions([]);
    }
}
