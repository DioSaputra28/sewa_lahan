<?php

namespace App\Filament\User\Resources\Leases\Pages;

use App\Actions\Leases\CreateLeaseRenewalRequest;
use App\Filament\User\Resources\Leases\LeaseResource;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use RuntimeException;

class ViewLease extends ViewRecord
{
    protected static string $resource = LeaseResource::class;

    public function getTitle(): string
    {
        return 'Detail Kontrak';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('requestRenewal')
                ->label('Ajukan Perpanjangan')
                ->color('primary')
                ->icon('heroicon-o-arrow-path')
                ->visible(fn (): bool => $this->getRecord()->status === 'active' && ! $this->getRecord()->hasUnresolvedRenewalRequest())
                ->schema([
                    TextInput::make('duration')
                        ->label('Durasi perpanjangan')
                        ->suffix(' periode')
                        ->numeric()
                        ->minValue(1)
                        ->required(),
                    DatePicker::make('start_date')
                        ->label('Tanggal mulai perpanjangan')
                        ->minDate(fn () => $this->getRecord()->end_date?->copy()->addDay())
                        ->required(),
                ])
                ->action(function (array $data, CreateLeaseRenewalRequest $createLeaseRenewalRequest): void {
                    $bookingRequest = $createLeaseRenewalRequest->handle(
                        lease: $this->getRecord(),
                        duration: (int) $data['duration'],
                        startDate: $data['start_date'],
                    );

                    if (! $bookingRequest->exists) {
                        throw new RuntimeException('Pengajuan perpanjangan gagal dibuat.');
                    }

                    Notification::make()
                        ->success()
                        ->title('Perpanjangan berhasil diajukan.')
                        ->body('Pengajuan perpanjangan telah masuk ke admin untuk direview lebih lanjut.')
                        ->send();
                }),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
