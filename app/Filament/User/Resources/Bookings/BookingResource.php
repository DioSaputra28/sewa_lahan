<?php

namespace App\Filament\User\Resources\Bookings;

use App\Filament\User\Resources\Bookings\Pages\BrowsePlots;
use App\Filament\User\Resources\Bookings\Pages\CreateBooking;
use App\Filament\User\Resources\Bookings\Pages\EditBooking;
use App\Filament\User\Resources\Bookings\Pages\ListBookings;
use App\Filament\User\Resources\Bookings\Pages\ViewPlot;
use App\Filament\User\Resources\Bookings\Schemas\BookingForm;
use App\Filament\User\Resources\Bookings\Tables\BookingsTable;
use App\Models\BookingRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BookingResource extends Resource
{
    protected static ?string $model = BookingRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'My Bookings';

    public static function form(Schema $schema): Schema
    {
        return BookingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookings::route('/'),
            'browse' => BrowsePlots::route('/browse-plots'),
            'plot' => ViewPlot::route('/plots/{plot}'),
            'create' => CreateBooking::route('/create'),
            'edit' => EditBooking::route('/{record}/edit'),
        ];
    }
}
