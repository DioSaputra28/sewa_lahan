<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->danger()
            ->title('Form user belum valid.')
            ->body('Periksa kembali perubahan data user sebelum menyimpan.')
            ->send();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $currentUser = Auth::user();
        $record = $this->getRecord();

        if ($currentUser && $record->is($currentUser)) {
            $selectedRoles = collect($data['roles'] ?? [])->map(fn ($roleId): int => (int) $roleId);
            $currentAdminRoleId = $record->roles()->where('name', 'admin')->value('roles.id');

            if ($currentAdminRoleId && ! $selectedRoles->contains($currentAdminRoleId)) {
                Notification::make()
                    ->danger()
                    ->title('Akses admin tidak boleh dicabut.')
                    ->body('Kamu tidak bisa menghapus role admin dari akun yang sedang dipakai login.')
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'User berhasil diperbarui.';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus')
                ->visible(fn (): bool => ! $this->getRecord()->is(Auth::user())),
        ];
    }
}
