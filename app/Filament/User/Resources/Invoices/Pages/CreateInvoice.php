<?php

namespace App\Filament\User\Resources\Invoices\Pages;

use App\Filament\User\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
}
