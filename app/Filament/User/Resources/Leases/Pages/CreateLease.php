<?php

namespace App\Filament\User\Resources\Leases\Pages;

use App\Filament\User\Resources\Leases\LeaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLease extends CreateRecord
{
    protected static string $resource = LeaseResource::class;
}
