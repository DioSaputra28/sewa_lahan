<?php

use App\Filament\Resources\Invoices\Pages\EditInvoice;
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

it('allows admin to create a hosted payment link without choosing a method', function () {
    config()->set('services.pakasir.project_slug', 'demo-project');
    config()->set('services.pakasir.api_key', 'demo-key');
    config()->set('services.pakasir.sandbox', true);
    config()->set('services.pakasir.base_url', 'https://app.pakasir.com');

    $adminRole = Role::query()->firstOrCreate(['name' => 'admin']);
    $admin = User::factory()->create([
        'status' => 'active',
        'email_verified_at' => now(),
    ]);
    $admin->roles()->syncWithoutDetaching([$adminRole->id]);

    $customer = User::factory()->create([
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    $market = Market::query()->create([
        'name' => 'Pasar Link Admin',
        'address' => 'Jl. Admin No. 1',
        'city' => 'Kebumen',
        'status' => 'active',
    ]);

    $area = Area::query()->create([
        'market_id' => $market->id,
        'name' => 'Blok Admin',
        'status' => 'active',
    ]);

    $plot = Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => $area->id,
        'name' => 'Lahan Link Admin',
        'type' => 'lapak',
        'length' => 5,
        'width' => 5,
        'area_square_meters' => 25,
        'base_price_monthly' => 12000000,
        'base_price_yearly' => 120000000,
        'status' => 'available',
    ]);

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

    $invoice = Invoice::query()->create([
        'booking_request_id' => $booking->id,
        'user_id' => $customer->id,
        'invoice_number' => 'INV-ADMIN-LINK-001',
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
        'description' => 'Sewa lahan admin',
        'qty' => 1,
        'unit_price' => 12000000,
        'total' => 12000000,
    ]);

    actingAs($admin);
    Filament::setCurrentPanel('admin');
    Filament::bootCurrentPanel();

    Livewire::test(EditInvoice::class, ['record' => $invoice->getRouteKey()])
        ->assertActionExists('createPaymentAttempt')
        ->callAction('createPaymentAttempt')
        ->assertHasNoActionErrors();

    assertDatabaseHas('payment_attempts', [
        'invoice_id' => $invoice->id,
        'provider_order_id' => $invoice->invoice_number,
        'checkout_url' => 'https://app.pakasir.com/pay/demo-project/12000000?order_id=INV-ADMIN-LINK-001',
        'payment_method' => null,
    ]);

    expect(PaymentAttempt::query()->where('invoice_id', $invoice->id)->first()?->checkout_url)
        ->toBe('https://app.pakasir.com/pay/demo-project/12000000?order_id=INV-ADMIN-LINK-001');
});
