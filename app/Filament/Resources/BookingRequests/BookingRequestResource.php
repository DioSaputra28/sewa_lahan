<?php

namespace App\Filament\Resources\BookingRequests;

use App\Filament\Resources\BookingRequests\Pages\EditBookingRequest;
use App\Filament\Resources\BookingRequests\Pages\ListBookingRequests;
use App\Filament\Resources\BookingRequests\Schemas\BookingRequestForm;
use App\Filament\Resources\BookingRequests\Tables\BookingRequestsTable;
use App\Models\BookingRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BookingRequestResource extends Resource
{
    protected static ?string $model = BookingRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'Booking Request';

    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'booking request';

    protected static ?string $pluralModelLabel = 'booking requests';

    public static function form(Schema $schema): Schema
    {
        return BookingRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookingRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookingRequests::route('/'),
            'edit' => EditBookingRequest::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
