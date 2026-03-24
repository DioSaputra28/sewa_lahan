<?php

namespace App\Filament\User\Resources\Invoices\Pages;

use App\Actions\Payments\CreatePakasirPaymentAttempt;
use App\Filament\User\Resources\Invoices\InvoiceResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use RuntimeException;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    public function getTitle(): string
    {
        return 'Detail Invoice';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('continuePayment')
                ->label('Lanjutkan pembayaran')
                ->color('warning')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->visible(fn (): bool => $this->getRecord()->canContinuePayment())
                ->url(fn (): ?string => $this->getRecord()->latestPaymentAttemptRecord()?->checkout_url)
                ->openUrlInNewTab(),
            Action::make('createPaymentAttempt')
                ->label('Buat link pembayaran baru')
                ->color('primary')
                ->icon('heroicon-o-credit-card')
                ->visible(fn (): bool => $this->getRecord()->canCreatePaymentAttempt())
                ->action(function (CreatePakasirPaymentAttempt $createPakasirPaymentAttempt): void {
                    if (! $this->getRecord()->canCreatePaymentAttempt()) {
                        throw new RuntimeException('Link pembayaran baru belum bisa dibuat untuk invoice ini.');
                    }

                    $createPakasirPaymentAttempt->handle($this->getRecord());

                    $this->record = $this->getRecord()->fresh();
                    $this->fillForm();

                    Notification::make()
                        ->success()
                        ->title('Link pembayaran baru berhasil dibuat.')
                        ->body('Lanjutkan pembayaran melalui halaman Pakasir untuk memilih metode bayar.')
                        ->send();
                }),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
