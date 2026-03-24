<?php

namespace App\Filament\User\Resources\Invoices\Pages;

use App\Filament\User\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    public function getTitle(): string
    {
        return 'Invoice Saya';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
