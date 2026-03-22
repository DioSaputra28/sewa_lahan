<?php

use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LahanController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::view('/about', 'web.about')->name('about');
Route::get('/lahan', [LahanController::class, 'index'])->name('lahan.index');
Route::get('/lahan/{plot}', [LahanController::class, 'show'])->name('lahan.show');
Route::view('/contact', 'web.contact')->name('contact');

Route::post('/webhooks/pakasir', PaymentWebhookController::class)->name('webhooks.pakasir');
