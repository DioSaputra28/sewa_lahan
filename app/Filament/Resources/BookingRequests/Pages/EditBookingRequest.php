<?php

namespace App\Filament\Resources\BookingRequests\Pages;

use App\Actions\ActivityLogs\WriteOperationalActivityLog;
use App\Filament\Resources\BookingRequests\BookingRequestResource;
use App\Models\BookingRequest;
use App\Models\BookingStatusEvent;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditBookingRequest extends EditRecord
{
    protected static string $resource = BookingRequestResource::class;

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->danger()
            ->title('Form booking belum valid.')
            ->body('Periksa kembali data review admin sebelum melanjutkan.')
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading('Approve booking')
                ->modalDescription('Booking akan disetujui dan invoice akan dibuat otomatis untuk customer.')
                ->modalSubmitActionLabel('Ya, approve')
                ->visible(fn (): bool => $this->getRecord()->status === 'pending')
                ->action(fn (): mixed => $this->approveBooking()),
            Action::make('reject')
                ->label('Reject')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->visible(fn (): bool => $this->getRecord()->status === 'pending')
                ->schema([
                    Textarea::make('rejection_reason')
                        ->label('Alasan penolakan')
                        ->placeholder('Tulis alasan kenapa booking ditolak')
                        ->helperText('Alasan penolakan wajib diisi agar mudah ditelusuri kembali.')
                        ->required()
                        ->rows(4),
                ])
                ->action(fn (array $data): mixed => $this->rejectBooking($data)),
        ];
    }

    protected function approveBooking(): mixed
    {
        $record = $this->getRecord();
        $data = $this->form->getState();
        $adminId = Auth::id();

        if (! $adminId) {
            Notification::make()
                ->danger()
                ->title('Sesi admin tidak valid.')
                ->body('Silakan muat ulang halaman lalu coba lagi.')
                ->send();

            return null;
        }

        if (blank($data['final_price'] ?? null) || blank($data['payment_due_at'] ?? null)) {
            Notification::make()
                ->danger()
                ->title('Data review belum lengkap.')
                ->body('Harga final dan batas waktu pembayaran wajib diisi sebelum approve.')
                ->send();

            return null;
        }

        if ($record->invoices()->exists()) {
            Notification::make()
                ->warning()
                ->title('Invoice sudah pernah dibuat.')
                ->body('Booking ini sudah memiliki invoice, jadi tidak bisa di-approve lagi.')
                ->send();

            return null;
        }

        $beforeBooking = app(WriteOperationalActivityLog::class)->snapshot($record, [
            'id',
            'status',
            'payment_status',
            'approved_by',
            'approved_at',
            'payment_due_at',
            'final_price',
        ]);

        DB::transaction(function () use ($adminId, $data, $record): void {
            $record->update([
                'final_price' => $data['final_price'],
                'payment_due_at' => $data['payment_due_at'],
                'expires_at' => $data['payment_due_at'],
                'notes' => $data['notes'] ?? null,
                'status' => 'approved',
                'payment_status' => 'unpaid',
                'approved_by' => $adminId,
                'approved_at' => now(),
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);

            BookingStatusEvent::query()->create([
                'booking_request_id' => $record->id,
                'status' => 'approved',
                'changed_by' => $adminId,
                'notes' => 'Booking approved oleh admin.',
            ]);

            $invoice = Invoice::query()->create([
                'booking_request_id' => $record->id,
                'user_id' => $record->user_id,
                'invoice_number' => $this->generateInvoiceNumber($record),
                'issue_date' => now()->toDateString(),
                'due_date' => $record->payment_due_at?->toDateString() ?? now()->toDateString(),
                'subtotal' => (int) $record->final_price,
                'discount_amount' => 0,
                'penalty_amount' => 0,
                'total_amount' => (int) $record->final_price,
                'status' => 'unpaid',
            ]);

            InvoiceItem::query()->create([
                'invoice_id' => $invoice->id,
                'type' => 'rent',
                'description' => $this->buildInvoiceItemDescription($record),
                'qty' => 1,
                'unit_price' => (int) $record->final_price,
                'total' => (int) $record->final_price,
            ]);

            app(WriteOperationalActivityLog::class)->handle(
                $adminId,
                $record->fresh(),
                'approve-booking',
                'Booking approved and invoice created.',
                [
                    'booking_request' => [
                        'before' => $beforeBooking,
                        'after' => app(WriteOperationalActivityLog::class)->snapshot($record->fresh(), [
                            'id',
                            'status',
                            'payment_status',
                            'approved_by',
                            'approved_at',
                            'payment_due_at',
                            'final_price',
                        ]),
                    ],
                    'invoice' => [
                        'after' => app(WriteOperationalActivityLog::class)->snapshot($invoice, [
                            'id',
                            'invoice_number',
                            'status',
                            'due_date',
                            'total_amount',
                        ]),
                    ],
                ],
            );
        });

        Notification::make()
            ->success()
            ->title('Booking berhasil di-approve.')
            ->body('Invoice otomatis sudah dibuat untuk customer.')
            ->send();

        $this->redirect(BookingRequestResource::getUrl('index'));

        return null;
    }

    protected function rejectBooking(array $data): mixed
    {
        $record = $this->getRecord();
        $formData = $this->form->getState();
        $adminId = Auth::id();

        if (! $adminId) {
            Notification::make()
                ->danger()
                ->title('Sesi admin tidak valid.')
                ->body('Silakan muat ulang halaman lalu coba lagi.')
                ->send();

            return null;
        }

        $beforeBooking = app(WriteOperationalActivityLog::class)->snapshot($record, [
            'id',
            'status',
            'payment_status',
            'rejected_at',
            'rejection_reason',
        ]);

        DB::transaction(function () use ($adminId, $data, $formData, $record): void {
            $record->update([
                'notes' => $formData['notes'] ?? null,
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => $data['rejection_reason'],
            ]);

            BookingStatusEvent::query()->create([
                'booking_request_id' => $record->id,
                'status' => 'rejected',
                'changed_by' => $adminId,
                'notes' => $data['rejection_reason'],
            ]);

            app(WriteOperationalActivityLog::class)->handle(
                $adminId,
                $record->fresh(),
                'reject-booking',
                'Booking rejected by admin.',
                [
                    'booking_request' => [
                        'before' => $beforeBooking,
                        'after' => app(WriteOperationalActivityLog::class)->snapshot($record->fresh(), [
                            'id',
                            'status',
                            'payment_status',
                            'rejected_at',
                            'rejection_reason',
                        ]),
                    ],
                ],
            );
        });

        Notification::make()
            ->success()
            ->title('Booking berhasil ditolak.')
            ->body('Alasan penolakan sudah tersimpan di histori booking.')
            ->send();

        $this->redirect(BookingRequestResource::getUrl('index'));

        return null;
    }

    protected function generateInvoiceNumber(BookingRequest $record): string
    {
        return 'INV-'.now()->format('Ymd').'-'.str_pad((string) $record->id, 5, '0', STR_PAD_LEFT);
    }

    protected function buildInvoiceItemDescription(BookingRequest $record): string
    {
        $termLabel = match ($record->term_type) {
            'monthly' => 'bulanan',
            'yearly' => 'tahunan',
            default => $record->term_type,
        };

        return "Sewa {$record->plot?->name} untuk {$record->duration} periode ({$termLabel})";
    }
}
