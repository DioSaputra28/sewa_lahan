<?php

namespace App\Notifications;

use App\Filament\User\Resources\Bookings\BookingResource;
use App\Filament\User\Resources\Leases\LeaseResource;
use App\Models\Lease;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendLeaseEndedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Lease $lease,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $lease = $this->lease->loadMissing(['plot.market']);
        $plotName = $lease->plot?->name ?? 'lahan kamu';
        $marketName = $lease->plot?->market?->name ?? 'pasar terkait';
        $endDateLabel = $lease->end_date?->format('d M Y') ?? '-';

        return (new MailMessage)
            ->subject('Kontrak lahan kamu telah berakhir')
            ->greeting('Halo '.$notifiable->name.',')
            ->line('Kontrak '.$lease->lease_number.' untuk '.$plotName.' telah berakhir.')
            ->line('Pasar: '.$marketName.'. Masa kontrak berakhir pada '.$endDateLabel.'.')
            ->action('Lihat Kontrak Saya', LeaseResource::getUrl(panel: 'user'))
            ->line('Kamu bisa ajukan perpanjangan atau cari lahan baru jika masih membutuhkan tempat usaha.')
            ->action('Cari Lahan Baru', BookingResource::getUrl('browse', panel: 'user'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'lease_id' => $this->lease->id,
            'lease_number' => $this->lease->lease_number,
            'end_date' => $this->lease->end_date?->toDateString(),
        ];
    }
}
