<?php

namespace App\Filament\User\Auth\Responses;

use Filament\Auth\Http\Responses\Contracts\RegistrationResponse as RegistrationResponseContract;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class RegistrationOtpResponse implements RegistrationResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        return redirect()->route(
            Filament::getCurrentOrDefaultPanel()->generateRouteName('auth.verify-otp'),
            ['token' => session('registration_otp_token')]
        );
    }
}
