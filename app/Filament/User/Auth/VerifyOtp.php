<?php

namespace App\Filament\User\Auth;

use App\Models\User;
use App\Services\RegistrationOtpService;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class VerifyOtp extends SimplePage
{
    protected static bool $shouldRegisterNavigation = false;

    protected static string $layout = 'components.filament.user.auth-layout';

    protected string $view = 'filament.user.auth.verify-otp';

    protected Width|string|null $maxContentWidth = Width::Full;

    public string $token = '';

    public string $otp_code = '';

    public function mount(string $token): void
    {
        $this->token = $token;

        $this->resolvePendingUser();
    }

    public function verify(): mixed
    {
        app(RegistrationOtpService::class)->verify(
            user: $this->resolvePendingUser(),
            code: $this->otp_code,
        );

        Notification::make()
            ->success()
            ->title('Verifikasi berhasil.')
            ->body('Akunmu sudah aktif. Silakan login.')
            ->send();

        return redirect()->to(filament()->getLoginUrl());
    }

    public function resend(): void
    {
        app(RegistrationOtpService::class)->resend($this->resolvePendingUser());

        $this->reset('otp_code');

        Notification::make()
            ->success()
            ->title('OTP berhasil dikirim ulang.')
            ->body('Silakan cek email kamu untuk kode yang baru.')
            ->send();
    }

    public function getHeading(): string|Htmlable
    {
        return 'Verifikasi OTP';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Masukkan 6 digit kode OTP yang sudah dikirim ke email kamu.';
    }

    public function getPendingUser(): User
    {
        return $this->resolvePendingUser();
    }

    protected function resolvePendingUser(): User
    {
        try {
            $userId = (int) Crypt::decryptString($this->token);
        } catch (\Throwable $exception) {
            abort(404);
        }

        $user = User::query()->findOrFail($userId);

        if ($user->status !== 'inactive' || filled($user->email_verified_at)) {
            throw ValidationException::withMessages([
                'otp_code' => 'Akun ini sudah terverifikasi atau tidak memerlukan OTP lagi.',
            ]);
        }

        return $user;
    }
}
