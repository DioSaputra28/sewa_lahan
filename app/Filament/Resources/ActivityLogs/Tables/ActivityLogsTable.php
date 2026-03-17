<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('actor.name')
                    ->label('Admin')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->searchable(),
                TextColumn::make('target_type')
                    ->label('Target type')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('target_id')
                    ->label('Target ID')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Waktu kejadian')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('actor_id')
                    ->label('Admin')
                    ->options(User::query()->whereHas('roles', fn ($query) => $query->where('name', 'admin'))->orderBy('name')->pluck('name', 'id')->all()),
                SelectFilter::make('action')
                    ->label('Action')
                    ->options([
                        'approve-booking' => 'approve-booking',
                        'reject-booking' => 'reject-booking',
                        'review-invoice' => 'review-invoice',
                        'review-payment' => 'review-payment',
                        'activate-lease' => 'activate-lease',
                    ]),
                SelectFilter::make('target_type')
                    ->label('Target type')
                    ->options([
                        'App\\Models\\BookingRequest' => 'BookingRequest',
                        'App\\Models\\Invoice' => 'Invoice',
                        'App\\Models\\Payment' => 'Payment',
                        'App\\Models\\Lease' => 'Lease',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Lihat detail'),
            ])
            ->toolbarActions([]);
    }
}
