<?php

namespace App\Filament\User\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Profile extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $navigationLabel = 'Profil';

    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.user.pages.profile';

    public string $name = '';

    public string $email = '';

    public ?string $phone = null;

    public ?string $current_password = null;

    public ?string $password = null;

    public ?string $password_confirmation = null;

    public function mount(): void
    {
        $user = $this->getUser();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', Password::default(), 'same:password_confirmation'],
            'password_confirmation' => ['nullable', 'required_with:password'],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.same' => 'Konfirmasi password harus sama dengan password baru.',
        ]);

        $user = $this->getUser();

        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? null;

        if (filled($validated['password'] ?? null)) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $this->current_password = null;
        $this->password = null;
        $this->password_confirmation = null;

        Notification::make()
            ->success()
            ->title('Profil berhasil diperbarui.')
            ->body('Perubahan akunmu sudah tersimpan.')
            ->send();
    }

    public function getTitle(): string
    {
        return 'Profil Akun';
    }

    public function getUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    public function getAccountSummary(): array
    {
        $user = $this->getUser();

        return [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?: '-',
            'status' => match ($user->status) {
                'active' => 'Aktif',
                'inactive' => 'Nonaktif',
                'blocked' => 'Diblokir',
                default => ucfirst((string) $user->status),
            },
            'email_verified_at' => $user->email_verified_at?->format('d M Y H:i') ?? 'Belum diverifikasi',
            'member_since' => $user->created_at?->format('d M Y') ?? '-',
        ];
    }
}
