<?php

namespace App\Filament\User\Resources\Leases\Pages;

use App\Filament\User\Resources\Leases\LeaseResource;
use Filament\Resources\Pages\ListRecords;

class ListLeases extends ListRecords
{
    protected static string $resource = LeaseResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
