<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\Concerns\GuardsAgainstProductionSeeding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    use GuardsAgainstProductionSeeding;

    public function run(): void
    {
        $this->guardAgainstProductionSeeding(static::class);

        $adminRole = Role::query()->firstOrCreate(['name' => 'admin']);

        $adminUser = User::query()->updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('admin'),
                'status' => 'active',
            ],
        );

        $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}
