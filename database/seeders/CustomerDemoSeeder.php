<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\Concerns\GuardsAgainstProductionSeeding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerDemoSeeder extends Seeder
{
    use GuardsAgainstProductionSeeding;

    public function run(): void
    {
        $this->guardAgainstProductionSeeding(static::class);

        $customerRole = Role::query()->where('name', 'customer')->firstOrFail();

        $customers = [
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'phone' => '081111111111'],
            ['name' => 'Siti Aminah', 'email' => 'siti@example.com', 'phone' => '082222222222'],
            ['name' => 'Rudi Hartono', 'email' => 'rudi@example.com', 'phone' => '083333333333'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@example.com', 'phone' => '084444444444'],
            ['name' => 'Agus Saputra', 'email' => 'agus@example.com', 'phone' => '085555555555'],
            ['name' => 'Nina Marlina', 'email' => 'nina@example.com', 'phone' => '086666666666'],
        ];

        foreach ($customers as $customer) {
            $user = User::query()->updateOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'phone' => $customer['phone'],
                    'password' => Hash::make('password'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
            );

            $user->roles()->syncWithoutDetaching([$customerRole->id]);
        }
    }
}
