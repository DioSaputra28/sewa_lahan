<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Models\User;
use App\Notifications\SendRegistrationOtp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class RegistrationOtpService
{
    public const EXPIRY_MINUTES = 10;

    public const RESEND_COOLDOWN_SECONDS = 60;

    public const MAX_ATTEMPTS = 5;

    public function issue(User $user): OtpVerification
    {
        return DB::transaction(function () use ($user): OtpVerification {
            OtpVerification::query()
                ->where('user_id', $user->id)
                ->whereNull('verified_at')
                ->delete();

            $otp = OtpVerification::query()->create([
                'user_id' => $user->id,
                'otp_code' => $this->generateCode(),
                'attempt_count' => 0,
                'expired_at' => now()->addMinutes(static::EXPIRY_MINUTES),
                'verified_at' => null,
            ]);

            Notification::send($user, new SendRegistrationOtp($otp));

            return $otp;
        });
    }

    public function resend(User $user): OtpVerification
    {
        $otp = $this->latestPendingOtp($user);

        if ($otp && $otp->created_at && $otp->created_at->diffInSeconds(now()) < static::RESEND_COOLDOWN_SECONDS) {
            throw ValidationException::withMessages([
                'otp_code' => 'OTP baru bisa dikirim ulang setelah 60 detik.',
            ]);
        }

        return $this->issue($user);
    }

    public function verify(User $user, string $code): OtpVerification
    {
        $otp = $this->latestPendingOtp($user);

        if (! $otp) {
            throw ValidationException::withMessages([
                'otp_code' => 'OTP tidak ditemukan. Silakan kirim ulang kode baru.',
            ]);
        }

        if ($otp->expired_at->isPast()) {
            throw ValidationException::withMessages([
                'otp_code' => 'OTP sudah kadaluarsa. Silakan kirim ulang kode baru.',
            ]);
        }

        if ($otp->attempt_count >= static::MAX_ATTEMPTS) {
            $otp->update([
                'expired_at' => now()->subSecond(),
            ]);

            throw ValidationException::withMessages([
                'otp_code' => 'OTP sudah hangus karena terlalu banyak percobaan. Silakan kirim ulang kode baru.',
            ]);
        }

        if ($otp->otp_code !== $code) {
            $attemptCount = $otp->attempt_count + 1;

            $otp->update([
                'attempt_count' => $attemptCount,
                'expired_at' => $attemptCount >= static::MAX_ATTEMPTS ? now()->subSecond() : $otp->expired_at,
            ]);

            throw ValidationException::withMessages([
                'otp_code' => $attemptCount >= static::MAX_ATTEMPTS
                    ? 'OTP sudah hangus karena terlalu banyak percobaan. Silakan kirim ulang kode baru.'
                    : 'Kode OTP yang kamu masukkan tidak sesuai.',
            ]);
        }

        $otp->update([
            'verified_at' => now(),
        ]);

        $user->status = 'active';
        $user->email_verified_at = now();
        $user->save();

        return $otp->refresh();
    }

    public function latestPendingOtp(User $user): ?OtpVerification
    {
        return OtpVerification::query()
            ->where('user_id', $user->id)
            ->whereNull('verified_at')
            ->latest('id')
            ->first();
    }

    protected function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
