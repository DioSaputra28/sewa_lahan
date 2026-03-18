<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Models\PaymentAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendInvoicePaymentLink extends Notification
{
    use Queueable;

    public function __construct(
        protected Invoice $invoice,
        protected PaymentAttempt $paymentAttempt,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $bookingRequest = $this->invoice->bookingRequest;
        $plotName = $bookingRequest?->plot?->name ?? 'lahan pilihanmu';
        $paymentDueAt = $bookingRequest?->payment_due_at;
        $dueLabel = $paymentDueAt?->format('d M Y H:i') ?? $this->invoice->due_date?->format('d M Y');

        return (new MailMessage)
            ->subject('Pengajuan booking diterima dan invoice siap dibayar')
            ->greeting('Halo '.$notifiable->name.',')
            ->line('Pengajuan booking kamu untuk '.$plotName.' sudah diterima.')
            ->line('Invoice '.$this->invoice->invoice_number.' telah dibuat dengan total pembayaran Rp '.number_format((int) $this->invoice->total_amount, 0, ',', '.').'.')
            ->action('Bayar Sekarang', (string) $this->paymentAttempt->checkout_url)
            ->line('Link pembayaran ini berlaku sampai '.$dueLabel.'.')
            ->line('Jika melewati batas waktu tersebut, invoice akan kedaluwarsa dan tidak dapat diproses lagi.');
    }
}
