<?php

use App\Actions\Payments\CreatePakasirPaymentAttempt;
use App\Actions\Payments\SyncPakasirPaymentStatus;
use App\Models\Area;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Lease;
use App\Models\Market;
use App\Models\Payment;
use App\Models\PaymentAttempt;
use App\Models\Plot;
use App\Models\Role;
use App\Models\User;
use App\Notifications\SendAdminPaymentCompletedNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

it('creates a pakasir hosted payment link from an invoice', function () {
    $context = seedPaymentContext();

    $action = app(CreatePakasirPaymentAttempt::class);

    $payment = $action->handle($context['invoice']);

    expect($payment)->toBeInstanceOf(Payment::class)
        ->and($payment->provider)->toBe('pakasir')
        ->and($payment->provider_payment_method)->toBeNull()
        ->and($payment->provider_order_id)->toBe($context['invoice']->invoice_number);

    assertDatabaseHas('payment_attempts', [
        'invoice_id' => $context['invoice']->id,
        'provider' => 'pakasir',
        'provider_order_id' => $context['invoice']->invoice_number,
        'checkout_url' => expectedPakasirCheckoutUrl($context['invoice']->invoice_number, $context['invoice']->total_amount),
        'payment_method' => null,
    ]);
});

it('syncs pakasir payment status and creates lease when transaction is completed', function () {
    $context = seedPaymentContext([
        'term_type' => 'monthly',
        'duration' => 3,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonths(3)->subDay()->toDateString(),
    ]);

    $payment = Payment::query()->create([
        'invoice_id' => $context['invoice']->id,
        'user_id' => $context['customer']->id,
        'provider' => 'pakasir',
        'provider_project_slug' => 'demo-project',
        'provider_order_id' => $context['invoice']->invoice_number,
        'provider_status' => 'pending',
        'provider_payment_method' => null,
        'amount' => $context['invoice']->total_amount,
        'status' => 'pending',
    ]);

    PaymentAttempt::query()->create([
        'invoice_id' => $context['invoice']->id,
        'user_id' => $context['customer']->id,
        'provider' => 'pakasir',
        'provider_project_slug' => 'demo-project',
        'provider_order_id' => $context['invoice']->invoice_number,
        'payment_method' => null,
        'request_amount' => $context['invoice']->total_amount,
        'checkout_url' => expectedPakasirCheckoutUrl($context['invoice']->invoice_number, $context['invoice']->total_amount),
        'status' => 'pending',
        'requested_at' => now(),
    ]);

    Http::fake([
        'app.pakasir.com/api/transactiondetail*' => Http::response([
            'transaction' => [
                'amount' => $context['invoice']->total_amount,
                'order_id' => $context['invoice']->invoice_number,
                'project' => 'demo-project',
                'status' => 'completed',
                'payment_method' => 'qris',
                'payment_number' => 'QRIS-DEMO-001',
                'completed_at' => now()->toIso8601String(),
            ],
        ]),
    ]);

    $action = app(SyncPakasirPaymentStatus::class);

    $action->handle($payment);

    $payment->refresh();
    $context['invoice']->refresh();
    $context['booking']->refresh();

    expect($payment->status)->toBe('paid')
        ->and($context['invoice']->status)->toBe('paid')
        ->and($context['booking']->payment_status)->toBe('paid');

    assertDatabaseHas('leases', [
        'booking_request_id' => $context['booking']->id,
        'invoice_id' => $context['invoice']->id,
        'status' => 'active',
    ]);

    expect(Lease::query()->where('booking_request_id', $context['booking']->id)->first()?->periods()->count())
        ->toBe(3);
});

it('handles pakasir webhook and updates local payment state', function () {
    $context = seedPaymentContext();

    $payment = Payment::query()->create([
        'invoice_id' => $context['invoice']->id,
        'user_id' => $context['customer']->id,
        'provider' => 'pakasir',
        'provider_project_slug' => 'demo-project',
        'provider_order_id' => $context['invoice']->invoice_number,
        'provider_status' => 'pending',
        'provider_payment_method' => null,
        'amount' => $context['invoice']->total_amount,
        'status' => 'pending',
    ]);

    PaymentAttempt::query()->create([
        'invoice_id' => $context['invoice']->id,
        'user_id' => $context['customer']->id,
        'provider' => 'pakasir',
        'provider_project_slug' => 'demo-project',
        'provider_order_id' => $context['invoice']->invoice_number,
        'payment_method' => null,
        'request_amount' => $context['invoice']->total_amount,
        'checkout_url' => expectedPakasirCheckoutUrl($context['invoice']->invoice_number, $context['invoice']->total_amount),
        'status' => 'pending',
        'requested_at' => now(),
    ]);

    Http::fake([
        'app.pakasir.com/api/transactiondetail*' => Http::response([
            'transaction' => [
                'amount' => $context['invoice']->total_amount,
                'order_id' => $context['invoice']->invoice_number,
                'project' => 'demo-project',
                'status' => 'completed',
                'payment_method' => 'qris',
                'payment_number' => 'QRIS-DEMO-002',
                'completed_at' => now()->toIso8601String(),
            ],
        ]),
    ]);

    postJson('/webhooks/pakasir', [
        'amount' => $context['invoice']->total_amount,
        'order_id' => $context['invoice']->invoice_number,
        'project' => 'demo-project',
        'status' => 'completed',
        'payment_method' => 'qris',
        'completed_at' => now()->toIso8601String(),
    ])->assertSuccessful();

    assertDatabaseHas('payment_events', [
        'payment_id' => $payment->id,
        'event_source' => 'webhook',
        'provider_order_id' => $context['invoice']->invoice_number,
    ]);

    assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => 'paid',
    ]);
});

it('accepts pakasir webhook requests without csrf token', function () {
    $context = seedPaymentContext();

    $payment = Payment::query()->create([
        'invoice_id' => $context['invoice']->id,
        'user_id' => $context['customer']->id,
        'provider' => 'pakasir',
        'provider_project_slug' => 'demo-project',
        'provider_order_id' => $context['invoice']->invoice_number,
        'provider_status' => 'pending',
        'provider_payment_method' => null,
        'amount' => $context['invoice']->total_amount,
        'status' => 'pending',
    ]);

    PaymentAttempt::query()->create([
        'invoice_id' => $context['invoice']->id,
        'user_id' => $context['customer']->id,
        'provider' => 'pakasir',
        'provider_project_slug' => 'demo-project',
        'provider_order_id' => $context['invoice']->invoice_number,
        'payment_method' => null,
        'request_amount' => $context['invoice']->total_amount,
        'checkout_url' => expectedPakasirCheckoutUrl($context['invoice']->invoice_number, $context['invoice']->total_amount),
        'status' => 'pending',
        'requested_at' => now(),
    ]);

    Http::fake([
        'app.pakasir.com/api/transactiondetail*' => Http::response([
            'transaction' => [
                'amount' => $context['invoice']->total_amount,
                'order_id' => $context['invoice']->invoice_number,
                'project' => 'demo-project',
                'status' => 'completed',
                'payment_method' => 'qris',
                'payment_number' => 'QRIS-DEMO-003',
                'completed_at' => now()->toIso8601String(),
            ],
        ]),
    ]);

    $this->withMiddleware()
        ->postJson('/webhooks/pakasir', [
            'amount' => $context['invoice']->total_amount,
            'order_id' => $context['invoice']->invoice_number,
            'project' => 'demo-project',
            'status' => 'completed',
            'payment_method' => 'qris',
            'completed_at' => now()->toIso8601String(),
        ])
        ->assertSuccessful();

    assertDatabaseHas('payment_events', [
        'payment_id' => $payment->id,
        'event_source' => 'webhook',
        'provider_order_id' => $context['invoice']->invoice_number,
    ]);
});

it('sends payment success email to all admins with customer contact details and whatsapp link', function () {
    Notification::fake();

    $context = seedPaymentContext();
    $context['customer']->update([
        'phone' => '081234567890',
    ]);

    $adminRole = Role::query()->firstOrCreate(['name' => 'admin']);
    $firstAdmin = User::factory()->create([
        'status' => 'active',
        'email_verified_at' => now(),
    ]);
    $firstAdmin->roles()->syncWithoutDetaching([$adminRole->id]);

    $secondAdmin = User::factory()->create([
        'status' => 'active',
        'email_verified_at' => now(),
    ]);
    $secondAdmin->roles()->syncWithoutDetaching([$adminRole->id]);

    $payment = Payment::query()->create([
        'invoice_id' => $context['invoice']->id,
        'user_id' => $context['customer']->id,
        'provider' => 'pakasir',
        'provider_project_slug' => 'demo-project',
        'provider_order_id' => $context['invoice']->invoice_number,
        'provider_status' => 'pending',
        'provider_payment_method' => null,
        'amount' => $context['invoice']->total_amount,
        'status' => 'pending',
    ]);

    PaymentAttempt::query()->create([
        'invoice_id' => $context['invoice']->id,
        'user_id' => $context['customer']->id,
        'provider' => 'pakasir',
        'provider_project_slug' => 'demo-project',
        'provider_order_id' => $context['invoice']->invoice_number,
        'payment_method' => null,
        'request_amount' => $context['invoice']->total_amount,
        'checkout_url' => expectedPakasirCheckoutUrl($context['invoice']->invoice_number, $context['invoice']->total_amount),
        'status' => 'pending',
        'requested_at' => now(),
    ]);

    Http::fake([
        'app.pakasir.com/api/transactiondetail*' => Http::response([
            'transaction' => [
                'amount' => $context['invoice']->total_amount,
                'order_id' => $context['invoice']->invoice_number,
                'project' => 'demo-project',
                'status' => 'completed',
                'payment_method' => 'qris',
                'payment_number' => 'QRIS-DEMO-004',
                'completed_at' => now()->toIso8601String(),
            ],
        ]),
    ]);

    app(SyncPakasirPaymentStatus::class)->handle($payment);

    Notification::assertSentTo(
        [$firstAdmin, $secondAdmin],
        SendAdminPaymentCompletedNotification::class,
        function (SendAdminPaymentCompletedNotification $notification, array $channels) use ($context, $firstAdmin): bool {
            $payload = $notification->toArray($firstAdmin);
            $mailMessage = $notification->toMail($firstAdmin);

            return in_array('mail', $channels, true)
                && ($payload['customer_email'] ?? null) === $context['customer']->email
                && ($payload['customer_phone'] ?? null) === '081234567890'
                && (string) $mailMessage->actionUrl === 'https://wa.me/6281234567890';
        },
    );
});

function seedPaymentContext(array $bookingOverrides = []): array
{
    config()->set('services.pakasir.project_slug', 'demo-project');
    config()->set('services.pakasir.api_key', 'demo-key');
    config()->set('services.pakasir.sandbox', true);
    config()->set('services.pakasir.base_url', 'https://app.pakasir.com');

    $customer = User::factory()->create([
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    $market = Market::query()->create([
        'name' => 'Pasar Demo Integrasi',
        'address' => 'Jl. Integrasi No. 1',
        'city' => 'Kebumen',
        'status' => 'active',
    ]);

    $area = Area::query()->create([
        'market_id' => $market->id,
        'name' => 'Blok Integrasi',
        'status' => 'active',
    ]);

    $plot = Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => $area->id,
        'name' => 'Lahan Integrasi',
        'type' => 'lapak',
        'length' => 5,
        'width' => 5,
        'area_square_meters' => 25,
        'base_price_monthly' => 10000000,
        'base_price_yearly' => null,
        'status' => 'available',
    ]);

    $booking = BookingRequest::query()->create(array_merge([
        'user_id' => $customer->id,
        'plot_id' => $plot->id,
        'term_type' => 'yearly',
        'duration' => 1,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addYear()->toDateString(),
        'quoted_price' => 12000000,
        'final_price' => 12000000,
        'status' => 'approved',
        'payment_status' => 'unpaid',
        'payment_due_at' => now()->addDay(),
        'expires_at' => now()->addDay(),
    ], $bookingOverrides));

    $invoice = Invoice::query()->create([
        'booking_request_id' => $booking->id,
        'user_id' => $customer->id,
        'invoice_number' => 'INV-'.now()->format('YmdHis').'-'.$booking->id,
        'issue_date' => now()->toDateString(),
        'due_date' => now()->addDay()->toDateString(),
        'subtotal' => 12000000,
        'discount_amount' => 0,
        'penalty_amount' => 0,
        'total_amount' => 12000000,
        'status' => 'unpaid',
    ]);

    InvoiceItem::query()->create([
        'invoice_id' => $invoice->id,
        'type' => 'rent',
        'description' => 'Sewa demo integrasi',
        'qty' => 1,
        'unit_price' => 12000000,
        'total' => 12000000,
    ]);

    return compact('customer', 'market', 'area', 'plot', 'booking', 'invoice');
}

function expectedPakasirCheckoutUrl(string $invoiceNumber, int $amount): string
{
    return 'https://app.pakasir.com/pay/demo-project/'.$amount.'?'.http_build_query([
        'order_id' => $invoiceNumber,
        'redirect' => route('filament.user.pages.dashboard'),
    ]);
}
