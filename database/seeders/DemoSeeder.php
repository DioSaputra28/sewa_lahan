<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\GuardsAgainstProductionSeeding;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    use GuardsAgainstProductionSeeding;

    public function run(): void
    {
        $this->guardAgainstProductionSeeding(static::class);

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

