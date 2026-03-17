<?php

namespace App\Filament\User\Resources\Leases;

use App\Filament\User\Resources\Leases\Pages\ListLeases;
use App\Filament\User\Resources\Leases\Pages\ViewLease;
use App\Filament\User\Resources\Leases\Schemas\LeaseForm;
use App\Filament\User\Resources\Leases\Tables\LeasesTable;
use App\Models\Lease;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LeaseResource extends Resource
{
    protected static ?string $model = Lease::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'lease_number';

    protected static ?string $navigationLabel = 'Leases';

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
            'view' => ViewLease::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', Auth::id())
            ->with([
                'plot.market',
                'invoice',
                'periods',
                'bookingRequest',
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }
}
