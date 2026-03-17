<?php

namespace App\Filament\User\Resources\Leases\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('lease_number')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('lease_number')
                    ->label('Nomor lease')
                    ->searchable()
                    ->sortable(),
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
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'active' => 'Aktif',
                        'ended' => 'Berakhir',
                        'cancelled' => 'Dibatalkan',
                        default => $state ? ucfirst($state) : '-',
                    }),
                TextColumn::make('activated_at')
                    ->label('Aktif pada')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Aktif',
                        'ended' => 'Berakhir',
                        'cancelled' => 'Dibatalkan',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Lihat detail'),
            ]);
    }
}
