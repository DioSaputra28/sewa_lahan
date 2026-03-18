<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Actions\ActivityLogs\WriteOperationalActivityLog;
use App\Actions\Payments\CreatePakasirPaymentAttempt;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->danger()
            ->title('Form invoice belum valid.')
            ->body('Periksa kembali item invoice, jatuh tempo, diskon, dan penalti.')
            ->send();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['subtotal'] = $this->getRecord()->subtotal;
        $data['total_amount'] = $this->getRecord()->total_amount;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->getRecord()->paymentAttempts()->exists()) {
            Notification::make()
                ->warning()
                ->title('Invoice terkunci.')
                ->body('Invoice tidak bisa diubah karena link pembayaran sudah dibuat.')
                ->send();

            $this->halt();
        }

        $subtotal = collect($data['items'] ?? [])->sum(fn (array $item): int => (int) ($item['total'] ?? 0));
        $discountAmount = (int) ($data['discount_amount'] ?? 0);
        $penaltyAmount = (int) ($data['penalty_amount'] ?? 0);

        $data['subtotal'] = $subtotal;
        $data['total_amount'] = max($subtotal - $discountAmount + $penaltyAmount, 0);

        unset($data['items']);

        return $data;
    }

    protected function handleRecordUpdate($record, array $data): Invoice
    {
        $activityLog = app(WriteOperationalActivityLog::class);
        $before = $activityLog->snapshot($record, [
            'id',
            'due_date',
            'subtotal',
            'discount_amount',
            'penalty_amount',
            'total_amount',
            'status',
        ]);

        return DB::transaction(function () use ($activityLog, $before, $data, $record): Invoice {
            $record->update($data);

            $invoice = $record->refresh();

            $activityLog->handle(
                Auth::id(),
                $invoice,
                'update-invoice',
                'Invoice updated from admin panel.',
                [
                    'invoice' => [
                        'before' => $before,
                        'after' => $activityLog->snapshot($invoice, [
                            'id',
                            'due_date',
                            'subtotal',
                            'discount_amount',
                            'penalty_amount',
                            'total_amount',
                            'status',
                        ]),
                    ],
                ],
            );

            return $invoice;
        });
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Invoice berhasil diperbarui.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createPaymentAttempt')
                ->label('Buat link pembayaran')
                ->color('warning')
                ->icon('heroicon-o-credit-card')
                ->visible(fn (): bool => $this->getRecord()->status !== 'paid')
                ->action(function (CreatePakasirPaymentAttempt $createPakasirPaymentAttempt): void {
                    $createPakasirPaymentAttempt->handle($this->getRecord());

                    Notification::make()
                        ->success()
                        ->title('Link pembayaran berhasil dibuat.')
                        ->body('Invoice sekarang memiliki link pembayaran Pakasir yang bisa dibuka oleh customer.')
                        ->send();
                }),
        ];
    }
}
