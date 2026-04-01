<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $adminName = trim((string) config('seeding.admin.name'));
            $adminEmail = trim((string) config('seeding.admin.email'));
            $adminPassword = trim((string) config('seeding.admin.password'));

            $this->validateRequiredAdminConfig($adminName, $adminEmail, $adminPassword);

            $adminRole = Role::query()->firstOrCreate([
                'name' => 'admin',
            ]);

            Role::query()->firstOrCreate([
                'name' => 'customer',
            ]);

            $adminUser = User::query()
                ->where('email', $adminEmail)
                ->first();

            if (! $adminUser instanceof User) {
                $adminUser = User::query()->create([
                    'name' => $adminName,
                    'email' => $adminEmail,
                    'password' => Hash::make($adminPassword),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);
            }

            $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
        });
    }

    protected function validateRequiredAdminConfig(string $adminName, string $adminEmail, string $adminPassword): void
    {
        $missingKeys = [];

        if ($adminName === '') {
            $missingKeys[] = 'ADMIN_NAME';
        }

        if ($adminEmail === '') {
            $missingKeys[] = 'ADMIN_EMAIL';
        }

        if ($adminPassword === '') {
            $missingKeys[] = 'ADMIN_PASSWORD';
        }

        if ($missingKeys !== []) {
            throw new RuntimeException('Konfigurasi seeding admin belum lengkap: '.implode(', ', $missingKeys).'.');
        }
    }
}

