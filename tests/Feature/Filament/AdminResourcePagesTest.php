<?php

use App\Models\ActivityLog;
use App\Models\Area;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Lease;
use App\Models\Market;
use App\Models\Payment;
use App\Models\Plot;
use App\Models\Role;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('allows admin users to access master data and booking review resources', function () {
    $role = Role::query()->create([
        'name' => 'admin',
    ]);

    $admin = User::factory()->create();
    $admin->roles()->attach($role);

    $customer = User::factory()->create();

    $market = Market::query()->create([
        'name' => 'Pasar Uji',
        'address' => 'Jl. Contoh No. 1',
        'city' => 'Kebumen',
        'status' => 'active',
    ]);

    $area = Area::query()->create([
        'market_id' => $market->id,
        'name' => 'Blok A',
        'status' => 'active',
    ]);

    $plot = Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => $area->id,
        'name' => 'Freezer',
        'type' => 'lapak',
        'length' => 5,
        'width' => 5,
        'area_square_meters' => 25,
        'base_price_monthly' => 1000000,
        'base_price_yearly' => 10000000,
        'status' => 'available',
    ]);

    $bookingRequest = BookingRequest::query()->create([
        'user_id' => $customer->id,
        'plot_id' => $plot->id,
        'term_type' => 'monthly',
        'duration' => 12,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addYear()->toDateString(),
        'quoted_price' => 12000000,
        'status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    $invoice = Invoice::query()->create([
        'booking_request_id' => $bookingRequest->id,
        'user_id' => $customer->id,
        'invoice_number' => 'INV-TEST-00001',
        'issue_date' => now()->toDateString(),
        'due_date' => now()->addDays(3)->toDateString(),
        'subtotal' => 12000000,
        'discount_amount' => 0,
        'penalty_amount' => 0,
        'total_amount' => 12000000,
        'status' => 'unpaid',
    ]);

    InvoiceItem::query()->create([
        'invoice_id' => $invoice->id,
        'type' => 'rent',
        'description' => 'Sewa awal',
        'qty' => 1,
        'unit_price' => 12000000,
        'total' => 12000000,
    ]);

    $payment = Payment::query()->create([
        'invoice_id' => $invoice->id,
        'user_id' => $customer->id,
        'provider' => 'pakasir',
        'provider_order_id' => 'ORD-TEST-00001',
        'provider_status' => 'pending',
        'provider_payment_method' => 'qris',
        'amount' => 12000000,
        'status' => 'pending',
    ]);

    $lease = Lease::query()->create([
        'booking_request_id' => $bookingRequest->id,
        'tenant_id' => $customer->id,
        'plot_id' => $plot->id,
        'invoice_id' => $invoice->id,
        'lease_number' => 'LEASE-TEST-00001',
        'start_date' => now()->toDateString(),
        'end_date' => now()->addYear()->toDateString(),
        'term_type' => 'yearly',
        'duration' => 1,
        'agreed_price' => 12000000,
        'deposit_amount' => 0,
        'status' => 'active',
        'activated_at' => now(),
    ]);

    $activityLog = ActivityLog::query()->create([
        'actor_id' => $admin->id,
        'target_type' => BookingRequest::class,
        'target_id' => $bookingRequest->id,
        'action' => 'approve-booking',
        'description' => 'Admin menyetujui booking untuk customer.',
        'properties' => json_encode(['status' => 'approved'], JSON_THROW_ON_ERROR),
    ]);

    actingAs($admin);

    get('/admin/markets')
        ->assertSuccessful();

    get('/admin/areas')
        ->assertSuccessful();

    get('/admin/plots')
        ->assertSuccessful();

    get('/admin/plots/create')
        ->assertSuccessful();

    get('/admin/booking-requests')
        ->assertSuccessful();

    get('/admin/booking-requests/'.$bookingRequest->id.'/edit')
        ->assertSuccessful();

    get('/admin/booking-requests/create')
        ->assertNotFound();

    get('/admin/invoices')
        ->assertSuccessful();

    get('/admin/invoices/'.$invoice->id.'/edit')
        ->assertSuccessful();

    get('/admin/invoices/create')
        ->assertNotFound();

    get('/admin/payments')
        ->assertSuccessful();

    get('/admin/payments/'.$payment->id.'/edit')
        ->assertSuccessful();

    get('/admin/payments/create')
        ->assertNotFound();

    get('/admin/leases')
        ->assertSuccessful();

    get('/admin/leases/'.$lease->id.'/edit')
        ->assertSuccessful();

    get('/admin/leases/create')
        ->assertNotFound();

    get('/admin/users')
        ->assertSuccessful();

    get('/admin/users/create')
        ->assertSuccessful();

    get('/admin/users/'.$customer->id.'/edit')
        ->assertSuccessful();

    get('/admin/activity-logs')
        ->assertSuccessful();

    get('/admin/activity-logs/'.$activityLog->id.'/edit')
        ->assertSuccessful();

    get('/admin/activity-logs/create')
        ->assertNotFound();
});

it('blocks non admin users from accessing the admin panel', function () {
    $user = User::factory()->create();

    actingAs($user);

    get('/admin/markets')
        ->assertForbidden();
});
