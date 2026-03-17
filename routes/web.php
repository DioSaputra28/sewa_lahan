<?php

use App\Http\Controllers\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::post('/webhooks/pakasir', PaymentWebhookController::class)->name('webhooks.pakasir');
