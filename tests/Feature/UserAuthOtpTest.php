<?php

use App\Filament\User\Auth\Login;
use App\Filament\User\Auth\Register;
use App\Filament\User\Auth\VerifyOtp;
use App\Models\OtpVerification;
use App\Models\Role;
use App\Models\User;
use App\Notifications\SendRegistrationOtp;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\get;

it('renders the custom user login and register pages', function () {
    get('/user/login')
        ->assertSuccessful()
        ->assertSee('PasarSpace')
        ->assertSee('Welcome back');

    get('/user/register')
        ->assertSuccessful()
        ->assertSee('PasarSpace')
        ->assertSee('Create your account');
});

it('registers a customer user in inactive state and sends otp email', function () {
    Role::query()->firstOrCreate(['name' => 'customer']);
    Notification::fake();
    useUserPanel();

    Livewire::test(Register::class)
        ->set('data.name', 'User OTP')
        ->set('data.email', 'otp-user@example.com')
        ->set('data.phone', '081234567890')
        ->set('data.password', 'NewPassword123!')
        ->set('data.passwordConfirmation', 'NewPassword123!')
        ->call('register')
        ->assertRedirect();

    $user = User::query()->where('email', 'otp-user@example.com')->firstOrFail();

    expect($user->status)->toBe('inactive')
        ->and($user->email_verified_at)->toBeNull()
        ->and($user->roles()->where('name', 'customer')->exists())->toBeTrue()
        ->and(OtpVerification::query()->where('user_id', $user->id)->exists())->toBeTrue();

    Notification::assertSentTo($user, SendRegistrationOtp::class);
});

it('renders the otp verification page for an unverified user token', function () {
    $user = seedPendingOtpUser();

    get('/user/verify-otp/'.rawurlencode(Crypt::encryptString((string) $user->id)))
        ->assertSuccessful()
        ->assertSee('Verifikasi OTP')
        ->assertSee($user->email);
});

it('shows the resend cooldown indicator on the otp verification page', function () {
    $user = seedPendingOtpUser();
    useUserPanel();

    Livewire::test(VerifyOtp::class, ['token' => Crypt::encryptString((string) $user->id)])
        ->assertSee('Kirim ulang dalam 01:00');
});

it('activates the account when the submitted otp is valid', function () {
    $user = seedPendingOtpUser();
    $otp = OtpVerification::query()->where('user_id', $user->id)->latest('id')->firstOrFail();
    useUserPanel();

    Livewire::test(VerifyOtp::class, ['token' => Crypt::encryptString((string) $user->id)])
        ->set('otp_code', $otp->otp_code)
        ->call('verify')
        ->assertRedirect('/user/login');

    $user->refresh();
    $otp->refresh();

    expect($user->status)->toBe('active')
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($otp->verified_at)->not->toBeNull();
});

it('increments otp attempt count when the submitted otp is invalid', function () {
    $user = seedPendingOtpUser();
    $otp = OtpVerification::query()->where('user_id', $user->id)->latest('id')->firstOrFail();
    useUserPanel();

    Livewire::test(VerifyOtp::class, ['token' => Crypt::encryptString((string) $user->id)])
        ->set('otp_code', '000000')
        ->call('verify')
        ->assertHasErrors(['otp_code']);

    expect($otp->fresh()->attempt_count)->toBe(1);
});

it('expires the otp after five invalid attempts and requires resend', function () {
    $user = seedPendingOtpUser();
    $otp = OtpVerification::query()->where('user_id', $user->id)->latest('id')->firstOrFail();
    useUserPanel();

    $component = Livewire::test(VerifyOtp::class, ['token' => Crypt::encryptString((string) $user->id)]);

    foreach (range(1, 5) as $attempt) {
        $component
            ->set('otp_code', '000000')
            ->call('verify');
    }

    expect($otp->fresh()->attempt_count)->toBe(5)
        ->and($otp->fresh()->expired_at->isPast())->toBeTrue();
});

it('blocks resend before sixty seconds and allows it after cooldown', function () {
    $user = seedPendingOtpUser();
    $otp = OtpVerification::query()->where('user_id', $user->id)->latest('id')->firstOrFail();
    Notification::fake();
    useUserPanel();

    Livewire::test(VerifyOtp::class, ['token' => Crypt::encryptString((string) $user->id)])
        ->call('resend')
        ->assertHasErrors(['otp_code']);

    $this->travel(61)->seconds();

    Livewire::test(VerifyOtp::class, ['token' => Crypt::encryptString((string) $user->id)])
        ->call('resend')
        ->assertHasNoErrors();

    Notification::assertSentTo($user, SendRegistrationOtp::class, 1);
});

it('resets the resend cooldown indicator after a successful resend', function () {
    $user = seedPendingOtpUser();
    Notification::fake();
    useUserPanel();

    $this->travel(61)->seconds();

    Livewire::test(VerifyOtp::class, ['token' => Crypt::encryptString((string) $user->id)])
        ->call('resend')
        ->assertHasNoErrors()
        ->assertSee('Kirim ulang dalam 01:00');
});

it('rejects login for inactive and unverified users', function () {
    $user = seedPendingOtpUser();
    useUserPanel();

    Livewire::test(Login::class)
        ->set('data.email', $user->email)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertHasErrors(['data.email']);
});

it('allows login after otp verification succeeds', function () {
    $user = seedPendingOtpUser([
        'status' => 'active',
        'email_verified_at' => now(),
    ]);
    useUserPanel();

    Livewire::test(Login::class)
        ->set('data.email', $user->email)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertHasNoErrors();

    expect(Filament::auth()->check())->toBeTrue();
});

function seedPendingOtpUser(array $overrides = []): User
{
    Role::query()->firstOrCreate(['name' => 'customer']);

    $user = User::factory()->create(array_merge([
        'name' => 'Pending OTP User',
        'email' => 'pending-otp@example.com',
        'status' => 'inactive',
        'phone' => '081111111111',
        'email_verified_at' => null,
    ], $overrides));

    $user->roles()->syncWithoutDetaching([
        Role::query()->where('name', 'customer')->firstOrFail()->id,
    ]);

    OtpVerification::query()->create([
        'user_id' => $user->id,
        'otp_code' => '123456',
        'attempt_count' => 0,
        'expired_at' => now()->addMinutes(10),
        'verified_at' => null,
    ]);

    return $user;
}

function useUserPanel(): void
{
    Filament::setCurrentPanel('user');
    Filament::bootCurrentPanel();
}
