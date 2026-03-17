<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            CustomerUserSeeder::class,
            CustomerDemoSeeder::class,
            MarketDemoSeeder::class,
            CustomerUserTransactionSeeder::class,
            BookingFlowDemoSeeder::class,
            InvoiceDemoSeeder::class,
            PaymentDemoSeeder::class,
            LeaseDemoSeeder::class,
            ActivityLogDemoSeeder::class,
        ]);
    }
}
