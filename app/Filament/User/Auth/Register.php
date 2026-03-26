<?php

namespace App\Filament\User\Auth;

use App\Filament\User\Auth\Responses\RegistrationOtpResponse;
use App\Models\Role;
use App\Models\User;
use App\Services\RegistrationOtpService;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Register extends \Filament\Auth\Pages\Register
{
    use WithRateLimiting;

    protected static string $layout = 'components.filament.user.auth-layout';

    protected string $view = 'filament.user.auth.register';

    protected Width|string|null $maxContentWidth = Width::Full;

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function (): Model {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        app(RegistrationOtpService::class)->issue($user);

        Notification::make()
            ->success()
            ->title('Registrasi berhasil.')
            ->body('Kode OTP sudah dikirim ke email kamu.')
            ->send();

        session()->put('registration_otp_token', Crypt::encryptString((string) $user->getKey()));

        return app(RegistrationOtpResponse::class);
    }

    protected function handleRegistration(array $data): Model
    {
        /** @var Model&User $user */
        $user = parent::handleRegistration($data);

        $customerRole = Role::query()->firstOrCreate(['name' => 'customer']);

        $user->update([
            'status' => 'inactive',
            'email_verified_at' => null,
        ]);

        $user->roles()->syncWithoutDetaching([$customerRole->id]);

        return $user;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPhoneFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->label('Nomor telepon')
            ->tel()
            ->required()
            ->maxLength(255);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Alamat email')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(table: User::class, column: 'email')
            ->validationMessages([
                'required' => 'Alamat email wajib diisi.',
                'email' => 'Format email tidak valid.',
                'unique' => 'Email ini sudah terdaftar.',
            ]);
    }

    public function getSubheading(): string|Htmlable|null
    {
        return null;
    }
}
