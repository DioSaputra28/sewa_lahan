<?php

use App\Filament\User\Resources\Invoices\Pages\ViewInvoice;
use App\Models\Area;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Market;
use App\Models\PaymentAttempt;
use App\Models\Plot;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

it('shows only invoices that belong to the authenticated customer', function () {
    $context = seedUserInvoiceContext();

    $otherInvoice = createInvoiceForCustomer($context['otherCustomer'], $context['plot'], [
        'invoice_number' => 'INV-OTHER-001',
        'status' => 'paid',
    ]);

    $ownInvoice = createInvoiceForCustomer($context['customer'], $context['plot'], [
        'invoice_number' => 'INV-OWN-001',
        'status' => 'pending',
    ]);

    actingAs($context['customer']);

    $response = get('/user/invoices');

    $response->assertSuccessful();
    $response->assertSee($ownInvoice->invoice_number);
    $response->assertDontSee($otherInvoice->invoice_number);
});

it('blocks customers from opening invoices that do not belong to them', function () {
    $context = seedUserInvoiceContext();

    $otherInvoice = createInvoiceForCustomer($context['otherCustomer'], $context['plot'], [
        'invoice_number' => 'INV-LOCKED-001',
    ]);

    actingAs($context['customer']);

    get('/user/invoices/'.$otherInvoice->id)->assertNotFound();
});

it('renders invoice detail with booking context and invoice items', function () {
    $context = seedUserInvoiceContext();

    $invoice = createInvoiceForCustomer($context['customer'], $context['plot'], [
        'invoice_number' => 'INV-DETAIL-001',
        'status' => 'pending',
    ]);

    PaymentAttempt::query()->create([
        'invoice_id' => $invoice->id,
        'user_id' => $context['customer']->id,
        'provider' => 'pakasir',
        'provider_project_slug' => 'demo-project',
        'provider_order_id' => $invoice->invoice_number,
        'payment_method' => 'qris',
        'request_amount' => $invoice->total_amount,
        'fee' => 2500,
        'total_payment' => $invoice->total_amount + 2500,
        'payment_number' => 'QR-DETAIL-001',
        'checkout_url' => 'https://checkout.example.test/inv-detail-001',
        'status' => 'pending',
        'expired_at' => now()->addHour(),
        'requested_at' => now(),
    ]);

    actingAs($context['customer']);

    $response = get('/user/invoices/'.$invoice->id);

    $response->assertSuccessful();
    $response->assertSee($invoice->invoice_number);
    $response->assertSee('Sewa '.$context['plot']->name);
    $response->assertSee('Pasar Invoice User');
    $response->assertSee('Blok Invoice');
    $response->assertSee('QR-DETAIL-001');
});

it('shows continue payment action only when the latest payment attempt is still active', function () {
    $context = seedUserInvoiceContext();

    $invoice = createInvoiceForCustomer($context['customer'], $context['plot'], [
        'invoice_number' => 'INV-CONTINUE-001',
        'status' => 'pending',
    ]);

    PaymentAttempt::query()->create([
        'invoice_id' => $invoice->id,
        'user_id' => $context['customer']->id,
        'provider' => 'pakasir',
        'provider_project_slug' => 'demo-project',
        'provider_order_id' => $invoice->invoice_number,
        'payment_method' => 'qris',
        'request_amount' => $invoice->total_amount,
        'fee' => 2500,
        'total_payment' => $invoice->total_amount + 2500,
        'payment_number' => 'QR-CONTINUE-001',
        'checkout_url' => 'https://checkout.example.test/inv-continue-001',
        'status' => 'pending',
        'expired_at' => now()->addHour(),
        'requested_at' => now(),
    ]);

    actingAs($context['customer']);
    useUserPanel();

    Livewire::test(ViewInvoice::class, ['record' => $invoice->getRouteKey()])
        ->assertActionVisible('continuePayment')
        ->assertActionHasUrl('continuePayment', 'https://checkout.example.test/inv-continue-001')
        ->assertActionHidden('createPaymentAttempt');
});

it('shows retry payment action only when the latest payment attempt is retryable', function () {
    $context = seedUserInvoiceContext();

    $invoice = createInvoiceForCustomer($context['customer'], $context['plot'], [
        'invoice_number' => 'INV-RETRY-001',
        'status' => 'expired',
    ]);

    PaymentAttempt::query()->create([
        'invoice_id' => $invoice->id,
        'user_id' => $context['customer']->id,
        'provider' => 'pakasir',
        'provider_project_slug' => 'demo-project',
        'provider_order_id' => $invoice->invoice_number,
        'payment_method' => 'qris',
        'request_amount' => $invoice->total_amount,
        'fee' => 2500,
        'total_payment' => $invoice->total_amount + 2500,
        'payment_number' => 'QR-RETRY-001',
        'checkout_url' => 'https://checkout.example.test/inv-retry-001',
        'status' => 'expired',
        'expired_at' => now()->subMinute(),
        'requested_at' => now()->subHour(),
    ]);

    actingAs($context['customer']);
    useUserPanel();

    Livewire::test(ViewInvoice::class, ['record' => $invoice->getRouteKey()])
        ->assertActionHidden('continuePayment')
        ->assertActionVisible('createPaymentAttempt');
});

it('creates a new payment link from the customer invoice detail page', function () {
    $context = seedUserInvoiceContext();

    $invoice = createInvoiceForCustomer($context['customer'], $context['plot'], [
        'invoice_number' => 'INV-RETRY-ACTION-001',
        'status' => 'expired',
    ]);

    PaymentAttempt::query()->create([
        'invoice_id' => $invoice->id,
        'user_id' => $context['customer']->id,
        'provider' => 'pakasir',
        'provider_project_slug' => 'demo-project',
        'provider_order_id' => $invoice->invoice_number,
        'payment_method' => 'qris',
        'request_amount' => $invoice->total_amount,
        'fee' => 2500,
        'total_payment' => $invoice->total_amount + 2500,
        'payment_number' => 'QR-OLD-001',
        'checkout_url' => 'https://checkout.example.test/inv-old-001',
        'status' => 'expired',
        'expired_at' => now()->subMinute(),
        'requested_at' => now()->subHour(),
    ]);

    actingAs($context['customer']);
    useUserPanel();

    Livewire::test(ViewInvoice::class, ['record' => $invoice->getRouteKey()])
        ->callAction('createPaymentAttempt')
        ->assertHasNoActionErrors()
        ->assertActionVisible('continuePayment')
        ->assertActionHasUrl('continuePayment', 'https://app.pakasir.com/pay/demo-project/12000000?order_id=INV-RETRY-ACTION-001')
        ->assertActionHidden('createPaymentAttempt');

    assertDatabaseHas('payment_attempts', [
        'invoice_id' => $invoice->id,
        'provider_order_id' => $invoice->invoice_number,
        'payment_method' => null,
        'payment_number' => null,
        'checkout_url' => 'https://app.pakasir.com/pay/demo-project/12000000?order_id=INV-RETRY-ACTION-001',
        'status' => 'pending',
    ]);
});

it('does not allow a paid invoice to generate a new payment attempt', function () {
    $context = seedUserInvoiceContext();

    $invoice = createInvoiceForCustomer($context['customer'], $context['plot'], [
        'invoice_number' => 'INV-PAID-001',
        'status' => 'paid',
        'paid_at' => now(),
    ]);

    actingAs($context['customer']);
    useUserPanel();

    Livewire::test(ViewInvoice::class, ['record' => $invoice->getRouteKey()])
        ->assertActionHidden('createPaymentAttempt')
        ->assertActionHidden('continuePayment');
});

function seedUserInvoiceContext(): array
{
    config()->set('services.pakasir.project_slug', 'demo-project');
    config()->set('services.pakasir.api_key', 'demo-key');
    config()->set('services.pakasir.sandbox', true);
    config()->set('services.pakasir.base_url', 'https://app.pakasir.com');

    $customerRole = Role::query()->firstOrCreate(['name' => 'customer']);

    $customer = User::factory()->create([
        'status' => 'active',
        'email_verified_at' => now(),
    ]);
    $customer->roles()->syncWithoutDetaching([$customerRole->id]);

    $otherCustomer = User::factory()->create([
        'status' => 'active',
        'email_verified_at' => now(),
    ]);
    $otherCustomer->roles()->syncWithoutDetaching([$customerRole->id]);

    $market = Market::query()->create([
        'name' => 'Pasar Invoice User',
        'address' => 'Jl. Invoice No. 1',
        'city' => 'Kebumen',
        'status' => 'active',
    ]);

    $area = Area::query()->create([
        'market_id' => $market->id,
        'name' => 'Blok Invoice',
        'status' => 'active',
    ]);

    $plot = Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => $area->id,
        'name' => 'Lahan Invoice Demo',
        'type' => 'lapak',
        'length' => 4,
        'width' => 6,
        'area_square_meters' => 24,
        'base_price_monthly' => 12000000,
        'base_price_yearly' => 120000000,
        'status' => 'available',
        'description' => 'Lahan demo untuk pengujian invoice user.',
    ]);

    return compact('customer', 'otherCustomer', 'market', 'area', 'plot');
}

function createInvoiceForCustomer(User $customer, Plot $plot, array $invoiceOverrides = []): Invoice
{
    $booking = BookingRequest::query()->create([
        'user_id' => $customer->id,
        'plot_id' => $plot->id,
        'term_type' => 'monthly',
        'duration' => 1,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonth()->subDay()->toDateString(),
        'quoted_price' => 12000000,
        'final_price' => 12000000,
        'status' => 'approved',
        'payment_status' => 'unpaid',
        'payment_due_at' => now()->addDay(),
        'expires_at' => now()->addDay(),
    ]);

    $invoice = Invoice::query()->create(array_merge([
        'booking_request_id' => $booking->id,
        'user_id' => $customer->id,
        'invoice_number' => 'INV-'.str()->upper(str()->random(10)),
        'issue_date' => now()->toDateString(),
        'due_date' => now()->addDay()->toDateString(),
        'subtotal' => 12000000,
        'discount_amount' => 0,
        'penalty_amount' => 0,
        'total_amount' => 12000000,
        'status' => 'unpaid',
        'paid_at' => null,
    ], $invoiceOverrides));

    InvoiceItem::query()->create([
        'invoice_id' => $invoice->id,
        'type' => 'rent',
        'description' => 'Sewa '.$plot->name,
        'qty' => 1,
        'unit_price' => 12000000,
        'total' => 12000000,
    ]);

    return $invoice;
}

function useUserPanel(): void
{
    Filament::setCurrentPanel('user');
    Filament::bootCurrentPanel();
}
