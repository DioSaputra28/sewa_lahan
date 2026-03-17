<?php

namespace App\Notifications;

use App\Models\OtpVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendRegistrationOtp extends Notification
{
    use Queueable;

    public function __construct(
        protected OtpVerification $otpVerification,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Kode OTP Registrasi Akun')
            ->greeting('Halo '.$notifiable->name.',')
            ->line('Gunakan kode OTP berikut untuk mengaktifkan akunmu:')
            ->line($this->otpVerification->otp_code)
            ->line('Kode ini berlaku selama 10 menit.')
            ->line('Jika kamu tidak merasa membuat akun, abaikan email ini.');
    }
}
