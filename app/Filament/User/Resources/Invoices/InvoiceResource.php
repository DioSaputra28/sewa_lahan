<?php

namespace App\Filament\User\Resources\Invoices;

use App\Filament\User\Resources\Invoices\Pages\ListInvoices;
use App\Filament\User\Resources\Invoices\Pages\ViewInvoice;
use App\Filament\User\Resources\Invoices\Schemas\InvoiceForm;
use App\Filament\User\Resources\Invoices\Tables\InvoicesTable;
use App\Models\Invoice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCurrencyDollar;

    protected static ?string $recordTitleAttribute = 'invoice_number';

    protected static ?string $navigationLabel = 'Invoice Saya';

    protected static ?string $modelLabel = 'invoice';

    protected static ?string $pluralModelLabel = 'invoice';

    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'view' => ViewInvoice::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id())
            ->with([
                'bookingRequest.plot.market',
                'bookingRequest.plot.area',
                'items',
                'latestPaymentAttempt',
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
