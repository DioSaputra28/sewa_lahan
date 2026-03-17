<?php

namespace App\Filament\Resources\Markets;

use App\Filament\Resources\Markets\Pages\ManageMarkets;
use App\Models\Market;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MarketResource extends Resource
{
    protected static ?string $model = Market::class;

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi pasar')
                    ->description('Lengkapi data pasar agar mudah dikelola dan dipilih saat membuat area atau lahan.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama pasar')
                            ->placeholder('Contoh: Pasar Induk Kebumen')
                            ->helperText('Nama ini akan tampil di daftar pasar dan pilihan lokasi lain di panel admin.')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('city')
                            ->label('Kota')
                            ->placeholder('Contoh: Kebumen')
                            ->helperText('Isi dengan nama kota atau kabupaten tempat pasar berada.')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('maps_url')
                            ->label('Link maps')
                            ->placeholder('https://maps.google.com/...')
                            ->helperText('Masukkan tautan Google Maps agar lokasi pasar mudah dibuka kembali.')
                            ->url()
                            ->maxLength(2048),
                        Select::make('status')
                            ->label('Status pasar')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Nonaktif',
                            ])
                            ->default('active')
                            ->native(false)
                            ->helperText('Pasar aktif dapat dipakai untuk pengelolaan area dan lahan.')
                            ->required(),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->placeholder('Tulis alamat lengkap pasar')
                            ->helperText('Alamat lengkap membantu admin memastikan lokasi pasar dengan tepat.')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Deskripsi tambahan')
                            ->placeholder('Tambahkan catatan umum tentang pasar bila diperlukan')
                            ->helperText('Kolom ini opsional dan bisa dipakai untuk catatan tambahan internal.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->grow()
                    ->maxWidth(Width::Full),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama pasar')
                    ->searchable(),
                TextColumn::make('city')
                    ->label('Kota')
                    ->searchable(),
                TextColumn::make('maps_url')
                    ->label('Link maps')
                    ->limit(30)
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                        default => ucfirst($state),
                    }),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->modalWidth(Width::FourExtraLarge),
                DeleteAction::make()
                    ->label('Hapus')
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus terpilih'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMarkets::route('/'),
        ];
    }
}
