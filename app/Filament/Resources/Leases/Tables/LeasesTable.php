<?php

namespace App\Filament\Resources\Leases\Tables;

use App\Models\Area;
use App\Models\Market;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                TextColumn::make('tenant.name')
                    ->label('Tenant')
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
                    ->placeholder('-')
                    ->toggleable(),
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
                SelectFilter::make('tenant_id')
                    ->label('Tenant')
                    ->options(User::query()->orderBy('name')->pluck('name', 'id')->all()),
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
                    ->label('Lihat detail'),
            ])
            ->toolbarActions([]);
    }
}
