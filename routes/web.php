<?php

use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'web.landing')->name('home');
Route::view('/about', 'web.about')->name('about');
Route::view('/lahan', 'web.lahan.index')->name('lahan.index');
Route::view('/lahan/single', 'web.lahan.single')->name('lahan.single');
Route::view('/contact', 'web.contact')->name('contact');

Route::post('/webhooks/pakasir', PaymentWebhookController::class)->name('webhooks.pakasir');
