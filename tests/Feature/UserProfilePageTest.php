<?php

use App\Filament\User\Pages\Profile;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('allows an authenticated customer to open the profile page', function () {
    $user = seedProfileUser();

    actingAs($user);

    get('/user/profile')
        ->assertSuccessful()
        ->assertSee($user->name)
        ->assertSee($user->email);
});

it('shows account summary values for the logged in user', function () {
    $user = seedProfileUser([
        'name' => 'Siti Profile',
        'email' => 'siti-profile@example.com',
        'phone' => '081234567890',
    ]);

    actingAs($user);

    get('/user/profile')
        ->assertSuccessful()
        ->assertSee('Siti Profile')
        ->assertSee('siti-profile@example.com')
        ->assertSee('081234567890')
        ->assertSee('Aktif');
});

it('updates name and phone without changing email', function () {
    $user = seedProfileUser([
        'email' => 'user-profile@example.com',
    ]);

    actingAs($user);
    useUserPanel();

    Livewire::test(Profile::class)
        ->set('name', 'Nama Baru')
        ->set('phone', '089999999999')
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toBe('Nama Baru')
        ->and($user->phone)->toBe('089999999999')
        ->and($user->email)->toBe('user-profile@example.com');
});

it('does not allow email to be changed through the profile page', function () {
    $user = seedProfileUser([
        'email' => 'fixed-email@example.com',
    ]);

    actingAs($user);
    useUserPanel();

    Livewire::test(Profile::class)
        ->set('email', 'changed@example.com')
        ->call('save')
        ->assertHasNoErrors();

    expect($user->fresh()->email)->toBe('fixed-email@example.com');
});

it('fails password change when current password is invalid', function () {
    $user = seedProfileUser();
    $originalPasswordHash = $user->password;

    actingAs($user);
    useUserPanel();

    Livewire::test(Profile::class)
        ->set('current_password', 'wrong-password')
        ->set('password', 'NewPassword123!')
        ->set('password_confirmation', 'NewPassword123!')
        ->call('save')
        ->assertHasErrors(['current_password']);

    expect($user->fresh()->password)->toBe($originalPasswordHash);
});

it('fails password change when password confirmation does not match', function () {
    $user = seedProfileUser();

    actingAs($user);
    useUserPanel();

    Livewire::test(Profile::class)
        ->set('current_password', 'password')
        ->set('password', 'NewPassword123!')
        ->set('password_confirmation', 'DifferentPassword123!')
        ->call('save')
        ->assertHasErrors(['password']);
});

it('changes password when current password and confirmation are valid', function () {
    $user = seedProfileUser();

    actingAs($user);
    useUserPanel();

    Livewire::test(Profile::class)
        ->set('current_password', 'password')
        ->set('password', 'NewPassword123!')
        ->set('password_confirmation', 'NewPassword123!')
        ->call('save')
        ->assertHasNoErrors();

    expect(Hash::check('NewPassword123!', $user->fresh()->password))->toBeTrue();
});

it('does not change password when profile is updated without password fields', function () {
    $user = seedProfileUser();
    $originalPasswordHash = $user->password;

    actingAs($user);
    useUserPanel();

    Livewire::test(Profile::class)
        ->set('name', 'Nama Tanpa Ganti Password')
        ->call('save')
        ->assertHasNoErrors();

    expect($user->fresh()->password)->toBe($originalPasswordHash);
});

function seedProfileUser(array $overrides = []): User
{
    $customerRole = Role::query()->firstOrCreate(['name' => 'customer']);

    $user = User::factory()->create(array_merge([
        'name' => 'User Profile',
        'email' => 'user@example.com',
        'phone' => '081111111111',
        'status' => 'active',
        'email_verified_at' => now(),
    ], $overrides));

    $user->roles()->syncWithoutDetaching([$customerRole->id]);

    return $user;
}

function useUserPanel(): void
{
    Filament::setCurrentPanel('user');
    Filament::bootCurrentPanel();
}
