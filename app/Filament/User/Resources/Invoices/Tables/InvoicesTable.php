<?php

namespace App\Filament\User\Resources\Invoices\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
                    ->searchable(),
                TextColumn::make('bookingRequest.plot.name')
                    ->label('Lahan')
                    ->searchable(),
                TextColumn::make('bookingRequest.id')
                    ->label('Booking')
                    ->formatStateUsing(fn (?int $state): string => $state ? '#'.$state : '-')
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn (int $state): string => 'Rp '.number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Jatuh tempo')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status invoice')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unpaid' => 'Belum dibayar',
                        'pending' => 'Pending',
                        'paid' => 'Sudah dibayar',
                        'expired' => 'Kadaluarsa',
                        'cancelled' => 'Dibatalkan',
                        default => ucfirst($state),
                    }),
                TextColumn::make('payment_summary')
                    ->label('Status pembayaran')
                    ->badge()
                    ->state(function ($record): string {
                        $latestAttempt = $record->latestPaymentAttemptRecord();

                        if (! $latestAttempt) {
                            return 'Belum ada payment';
                        }

                        return match ($latestAttempt->status) {
                            'pending' => $latestAttempt->isExpired() ? 'Kadaluarsa' : 'Menunggu pembayaran',
                            'completed' => 'Sudah dibayar',
                            'failed' => 'Gagal',
                            'cancelled' => 'Dibatalkan',
                            'expired' => 'Kadaluarsa',
                            default => ucfirst($latestAttempt->status),
                        };
                    }),
                TextColumn::make('paid_at')
                    ->label('Dibayar pada')
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status invoice')
                    ->options([
                        'unpaid' => 'Belum dibayar',
                        'pending' => 'Pending',
                        'paid' => 'Sudah dibayar',
                        'expired' => 'Kadaluarsa',
                        'cancelled' => 'Dibatalkan',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Lihat detail'),
            ]);
    }
}
