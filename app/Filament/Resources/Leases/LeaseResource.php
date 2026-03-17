<?php

namespace App\Filament\Resources\Leases;

use App\Filament\Resources\Leases\Pages\EditLease;
use App\Filament\Resources\Leases\Pages\ListLeases;
use App\Filament\Resources\Leases\Schemas\LeaseForm;
use App\Filament\Resources\Leases\Tables\LeasesTable;
use App\Models\Lease;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LeaseResource extends Resource
{
    protected static ?string $model = Lease::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'lease_number';

    protected static ?string $navigationLabel = 'Lease';

    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 40;

    protected static ?string $modelLabel = 'lease';

    protected static ?string $pluralModelLabel = 'leases';

    public static function form(Schema $schema): Schema
    {
        return LeaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeasesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeases::route('/'),
            'edit' => EditLease::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
