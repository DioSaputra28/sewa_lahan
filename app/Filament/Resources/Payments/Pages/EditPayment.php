<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Actions\Payments\SyncPakasirPaymentStatus;
use App\Filament\Resources\Payments\PaymentResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('recheckStatus')
                ->label('Cek ulang status')
                ->color('warning')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalHeading('Cek ulang status payment')
                ->modalDescription('Action ini nantinya akan menyinkronkan ulang status payment dari gateway tanpa mengubah data manual.')
                ->modalSubmitActionLabel('Lanjut cek ulang')
                ->action(function (SyncPakasirPaymentStatus $syncPakasirPaymentStatus): void {
                    $syncPakasirPaymentStatus->handle($this->getRecord());

                    Notification::make()
                        ->success()
                        ->title('Status payment berhasil diperiksa ulang.')
                        ->body('Data payment telah disinkronkan ulang dari Pakasir.')
                        ->send();
                }),
        ];
    }
}
