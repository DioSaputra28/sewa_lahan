<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'customer'] as $roleName) {
            Role::query()->updateOrCreate(
                ['name' => $roleName],
                ['name' => $roleName],
            );
        }
    }
}
