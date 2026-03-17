<?php

use App\Models\User;
use Database\Seeders\CustomerUserSeeder;
use Database\Seeders\RoleSeeder;

it('creates the default customer login account', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(CustomerUserSeeder::class);

    $user = User::query()->where('email', 'user@gmail.com')->first();

    expect($user)->not->toBeNull()
        ->and($user?->name)->toBe('user')
        ->and($user?->status)->toBe('active')
        ->and($user?->phone)->toBeNull()
        ->and($user?->email_verified_at)->not->toBeNull()
        ->and($user?->roles()->where('name', 'customer')->exists())->toBeTrue()
        ->and($user?->canAccessPanel(filament()->getPanel('user')))->toBeTrue();
});
