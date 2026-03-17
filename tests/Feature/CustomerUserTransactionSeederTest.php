<?php

use App\Models\ActivityLog;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\LeasePeriod;
use App\Models\Payment;
use App\Models\PaymentAttempt;
use App\Models\PaymentEvent;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\CustomerUserSeeder;
use Database\Seeders\CustomerUserTransactionSeeder;
use Database\Seeders\MarketDemoSeeder;
use Database\Seeders\RoleSeeder;

it('creates a rich transaction history for the default customer account', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(AdminUserSeeder::class);
    $this->seed(CustomerUserSeeder::class);
    $this->seed(MarketDemoSeeder::class);
    $this->seed(CustomerUserTransactionSeeder::class);

    $user = User::query()->where('email', 'user@gmail.com')->firstOrFail();

    expect(BookingRequest::query()->where('user_id', $user->id)->count())->toBe(30)
        ->and(BookingRequest::query()->where('user_id', $user->id)->where('status', 'pending')->count())->toBe(5)
        ->and(BookingRequest::query()->where('user_id', $user->id)->where('status', 'rejected')->count())->toBe(5)
        ->and(BookingRequest::query()->where('user_id', $user->id)->where('status', 'expired')->count())->toBe(4)
        ->and(BookingRequest::query()->where('user_id', $user->id)->where('status', 'approved')->count())->toBe(16)
        ->and(BookingRequest::query()->where('user_id', $user->id)->where('payment_status', 'paid')->count())->toBe(6)
        ->and(BookingRequest::query()->where('user_id', $user->id)->where('payment_status', 'expired')->count())->toBe(4);

    expect(Invoice::query()->where('user_id', $user->id)->count())->toBe(20)
        ->and(Invoice::query()->where('user_id', $user->id)->where('status', 'unpaid')->count())->toBe(4)
        ->and(Invoice::query()->where('user_id', $user->id)->where('status', 'pending')->count())->toBe(6)
        ->and(Invoice::query()->where('user_id', $user->id)->where('status', 'expired')->count())->toBe(4)
        ->and(Invoice::query()->where('user_id', $user->id)->where('status', 'paid')->count())->toBe(6);

    expect(Payment::query()->where('user_id', $user->id)->count())->toBe(16)
        ->and(PaymentAttempt::query()->where('user_id', $user->id)->count())->toBe(16)
        ->and(PaymentEvent::query()->whereIn('invoice_id', Invoice::query()->where('user_id', $user->id)->pluck('id'))->count())->toBe(16);

    expect(Lease::query()->where('tenant_id', $user->id)->count())->toBe(6)
        ->and(LeasePeriod::query()->whereIn('lease_id', Lease::query()->where('tenant_id', $user->id)->pluck('id'))->count())->toBeGreaterThanOrEqual(6)
        ->and(ActivityLog::query()->where('target_type', BookingRequest::class)->whereIn('target_id', BookingRequest::query()->where('user_id', $user->id)->pluck('id'))->count())->toBe(25);
});
