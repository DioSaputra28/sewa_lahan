<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->danger()
            ->title('Form user belum valid.')
            ->body('Periksa kembali data user, password, dan role yang dipilih.')
            ->send();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'User berhasil ditambahkan.';
    }
}
