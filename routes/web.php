<?php

use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LahanController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::view('/about', 'web.about')->name('about');
Route::get('/lahan', [LahanController::class, 'index'])->name('lahan.index');
Route::get('/lahan/{plot}', [LahanController::class, 'show'])->name('lahan.show');
Route::view('/contact', 'web.contact')->name('contact');

Route::post('/webhooks/pakasir', PaymentWebhookController::class)->name('webhooks.pakasir');

Route::post('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'], true)) {
        session(['locale' => $locale]);

        return redirect()->back()->cookie(
            cookie()->forever('locale', $locale)
        );
    }

    return redirect()->back();
})->name('locale.switch')->withoutMiddleware(ValidateCsrfToken::class);
