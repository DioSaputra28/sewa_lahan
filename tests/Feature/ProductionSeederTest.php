<?php

use App\Models\BookingRequest;
use App\Models\Market;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Database\Seeders\ProductionSeeder;
use Illuminate\Support\Facades\Hash;

it('creates required roles and admin user for production seeding', function () {
    config()->set('seeding.admin.name', 'Prod Admin');
    config()->set('seeding.admin.email', 'prod-admin@example.com');
    config()->set('seeding.admin.password', 'SuperSecure123!');

    $this->seed(ProductionSeeder::class);

    $adminUser = User::query()->where('email', 'prod-admin@example.com')->first();

    expect($adminUser)->not->toBeNull()
        ->and($adminUser?->name)->toBe('Prod Admin')
        ->and($adminUser?->status)->toBe('active')
        ->and($adminUser?->email_verified_at)->not->toBeNull()
        ->and(Hash::check('SuperSecure123!', (string) $adminUser?->password))->toBeTrue()
        ->and(Role::query()->where('name', 'admin')->exists())->toBeTrue()
        ->and(Role::query()->where('name', 'customer')->exists())->toBeTrue()
        ->and($adminUser?->roles()->where('name', 'admin')->exists())->toBeTrue();
});

it('fails fast when required admin seeding config is missing and keeps database unchanged', function () {
    config()->set('seeding.admin.name', 'Prod Admin');
    config()->set('seeding.admin.email', 'prod-admin@example.com');
    config()->set('seeding.admin.password', '');

    expect(fn () => $this->seed(ProductionSeeder::class))
        ->toThrow(\RuntimeException::class);

    $this->assertDatabaseCount('roles', 0);
    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('role_user', 0);
});

it('does not overwrite existing admin profile while ensuring admin role attachment', function () {
    config()->set('seeding.admin.name', 'Replacement Name');
    config()->set('seeding.admin.email', 'existing-admin@example.com');
    config()->set('seeding.admin.password', 'ReplacementPassword123!');

    $existingUser = User::query()->create([
        'name' => 'Existing Admin',
        'email' => 'existing-admin@example.com',
        'password' => Hash::make('CurrentPassword123!'),
        'status' => 'blocked',
        'email_verified_at' => null,
    ]);

    $this->seed(ProductionSeeder::class);

    $existingUser->refresh();

    expect($existingUser->name)->toBe('Existing Admin')
        ->and($existingUser->status)->toBe('blocked')
        ->and($existingUser->email_verified_at)->toBeNull()
        ->and(Hash::check('CurrentPassword123!', $existingUser->password))->toBeTrue()
        ->and($existingUser->roles()->where('name', 'admin')->exists())->toBeTrue()
        ->and(Role::query()->where('name', 'customer')->exists())->toBeTrue();
});

it('does not create product or transaction data when running production seeder', function () {
    config()->set('seeding.admin.name', 'Prod Admin');
    config()->set('seeding.admin.email', 'prod-admin@example.com');
    config()->set('seeding.admin.password', 'SuperSecure123!');

    $this->seed(ProductionSeeder::class);

    $this->assertDatabaseCount('markets', 0);
    $this->assertDatabaseCount('plots', 0);
    $this->assertDatabaseCount('booking_requests', 0);
    $this->assertDatabaseCount('invoices', 0);
    $this->assertDatabaseCount('payments', 0);
    $this->assertDatabaseCount('leases', 0);

    expect(Market::query()->exists())->toBeFalse()
        ->and(BookingRequest::query()->exists())->toBeFalse();
});

it('blocks demo seeder execution when app env is production', function () {
    config()->set('app.env', 'production');

    expect(fn () => $this->seed(DemoSeeder::class))
        ->toThrow(\RuntimeException::class);

    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('roles', 0);
});
