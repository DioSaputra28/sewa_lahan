<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerUserSeeder extends Seeder
{
    public function run(): void
    {
        $customerRole = Role::query()->firstOrCreate(['name' => 'customer']);

        $customerUser = User::query()->updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'user',
                'password' => Hash::make('user'),
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        );

        $customerUser->roles()->syncWithoutDetaching([$customerRole->id]);
    }
}
