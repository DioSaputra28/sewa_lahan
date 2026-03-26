<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendAdminPaymentCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Payment $payment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $payment = $this->payment->loadMissing(['invoice.bookingRequest.plot', 'user']);
        $invoice = $payment->invoice;
        $bookingRequest = $invoice?->bookingRequest;
        $customer = $payment->user;
        $customerEmail = $customer?->email ?? '-';
        $customerPhone = $customer?->phone ?? '-';
        $plotName = $bookingRequest?->plot?->name ?? '-';
        $paidAtLabel = $payment->paid_at?->format('d M Y H:i') ?? now()->format('d M Y H:i');
        $amountLabel = number_format((int) $payment->amount, 0, ',', '.');
        $whatsAppUrl = $this->whatsAppUrl($customer?->phone);

        $mailMessage = (new MailMessage)
            ->from((string) config('mail.from.address', 'hello@example.com'), 'System')
            ->subject('Pembayaran customer berhasil diproses')
            ->greeting('Halo Admin,')
            ->line('Pembayaran berhasil diterima untuk invoice '.($invoice?->invoice_number ?? '-').'.')
            ->line('Customer: '.($customer?->name ?? '-'))
            ->line('Email customer: '.$customerEmail)
            ->line('No. telepon customer: '.$customerPhone)
            ->line('Lahan: '.$plotName)
            ->line('Total pembayaran: Rp '.$amountLabel)
            ->line('Waktu pembayaran: '.$paidAtLabel);

        if (filled($whatsAppUrl)) {
            $mailMessage->action('Hubungi via WhatsApp', $whatsAppUrl);
        }

        return $mailMessage;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'invoice_number' => $this->payment->invoice?->invoice_number,
            'customer_email' => $this->payment->user?->email,
            'customer_phone' => $this->payment->user?->phone,
        ];
    }

    protected function whatsAppUrl(?string $phone): ?string
    {
        if (! filled($phone)) {
            return null;
        }

        $normalized = preg_replace('/\D+/', '', (string) $phone);

        if (! filled($normalized)) {
            return null;
        }

        if (str_starts_with($normalized, '0')) {
            $normalized = '62'.substr($normalized, 1);
        }

        return 'https://wa.me/'.$normalized;
    }
}
