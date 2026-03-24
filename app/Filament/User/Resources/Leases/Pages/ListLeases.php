<?php

namespace App\Filament\User\Resources\Leases\Pages;

use App\Filament\User\Resources\Leases\LeaseResource;
use Filament\Resources\Pages\ListRecords;

class ListLeases extends ListRecords
{
    protected static string $resource = LeaseResource::class;

    public function getTitle(): string
    {
        return 'Kontrak Saya';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
