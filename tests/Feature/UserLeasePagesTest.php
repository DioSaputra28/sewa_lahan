<?php

use App\Actions\Leases\CreateLeaseFromPaidBooking;
use App\Filament\User\Resources\Leases\Pages\ViewLease;
use App\Models\Area;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\LeasePeriod;
use App\Models\Market;
use App\Models\Plot;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

it('shows only leases that belong to the authenticated tenant', function () {
    $context = seedUserLeaseContext();

    $otherLease = createLeaseForTenant($context['otherCustomer'], $context['plot'], [
        'lease_number' => 'LEASE-OTHER-001',
    ]);

    $ownLease = createLeaseForTenant($context['customer'], $context['plot'], [
        'lease_number' => 'LEASE-OWN-001',
    ]);

    actingAs($context['customer']);

    $response = get('/user/leases');

    $response->assertSuccessful();
    $response->assertSee($ownLease->lease_number);
    $response->assertDontSee($otherLease->lease_number);
});

it('blocks a tenant from opening another tenants lease', function () {
    $context = seedUserLeaseContext();

    $otherLease = createLeaseForTenant($context['otherCustomer'], $context['plot']);

    actingAs($context['customer']);

    get('/user/leases/'.$otherLease->id)->assertNotFound();
});

it('renders lease detail with contract info invoice reference and periods', function () {
    $context = seedUserLeaseContext();

    $lease = createLeaseForTenant($context['customer'], $context['plot'], [
        'lease_number' => 'LEASE-DETAIL-001',
    ]);

    LeasePeriod::query()->create([
        'lease_id' => $lease->id,
        'period_no' => 1,
        'period_start' => $lease->start_date,
        'period_end' => $lease->end_date,
        'due_date' => $lease->start_date,
        'amount' => $lease->agreed_price,
        'status' => 'paid',
    ]);

    actingAs($context['customer']);

    $response = get('/user/leases/'.$lease->id);

    $response->assertSuccessful();
    $response->assertSee('LEASE-DETAIL-001');
    $response->assertSee($context['plot']->name);
    $response->assertSee('INV-LEASE-DETAIL-001');
    $response->assertSee('Periode ke');
});

it('shows renewal action only for active leases without unresolved renewals', function () {
    $context = seedUserLeaseContext();

    $lease = createLeaseForTenant($context['customer'], $context['plot'], [
        'status' => 'active',
        'lease_number' => 'LEASE-ACTIVE-001',
    ]);

    actingAs($context['customer']);
    useUserPanel();

    Livewire::test(ViewLease::class, ['record' => $lease->getRouteKey()])
        ->assertActionVisible('requestRenewal');

    BookingRequest::query()->create([
        'user_id' => $context['customer']->id,
        'plot_id' => $context['plot']->id,
        'term_type' => $lease->term_type,
        'duration' => 2,
        'start_date' => now()->addDay()->toDateString(),
        'end_date' => now()->addMonths(2)->toDateString(),
        'quoted_price' => 24000000,
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'renewal_of_lease_id' => $lease->id,
    ]);

    Livewire::test(ViewLease::class, ['record' => $lease->getRouteKey()])
        ->assertActionHidden('requestRenewal');
});

it('creates a renewal booking request from the lease detail modal', function () {
    $context = seedUserLeaseContext();

    $lease = createLeaseForTenant($context['customer'], $context['plot'], [
        'status' => 'active',
        'lease_number' => 'LEASE-RENEW-001',
        'term_type' => 'monthly',
        'duration' => 1,
    ]);

    actingAs($context['customer']);
    useUserPanel();

    Livewire::test(ViewLease::class, ['record' => $lease->getRouteKey()])
        ->callAction('requestRenewal', data: [
            'duration' => 3,
            'start_date' => $lease->end_date->copy()->addDay()->toDateString(),
        ])
        ->assertHasNoActionErrors()
        ->assertActionHidden('requestRenewal');

    $renewalBooking = BookingRequest::query()
        ->where('user_id', $context['customer']->id)
        ->where('plot_id', $context['plot']->id)
        ->where('renewal_of_lease_id', $lease->id)
        ->latest('id')
        ->first();

    expect($renewalBooking)->not->toBeNull()
        ->and($renewalBooking?->term_type)->toBe('monthly')
        ->and($renewalBooking?->duration)->toBe(3)
        ->and($renewalBooking?->status)->toBe('pending')
        ->and($renewalBooking?->payment_status)->toBe('unpaid')
        ->and($renewalBooking?->renewal_of_lease_id)->toBe($lease->id)
        ->and($renewalBooking?->notes)->toBeNull();
});

it('links a newly created lease back to the source lease for renewals', function () {
    $context = seedUserLeaseContext();

    $sourceLease = createLeaseForTenant($context['customer'], $context['plot'], [
        'lease_number' => 'LEASE-SOURCE-001',
        'status' => 'active',
    ]);

    $renewalBooking = BookingRequest::query()->create([
        'user_id' => $context['customer']->id,
        'plot_id' => $context['plot']->id,
        'term_type' => $sourceLease->term_type,
        'duration' => 2,
        'start_date' => now()->addDay()->toDateString(),
        'end_date' => now()->addMonths(2)->subDay()->toDateString(),
        'quoted_price' => 24000000,
        'final_price' => 24000000,
        'status' => 'approved',
        'payment_status' => 'paid',
        'approved_at' => now(),
        'renewal_of_lease_id' => $sourceLease->id,
    ]);

    $invoice = Invoice::query()->create([
        'booking_request_id' => $renewalBooking->id,
        'user_id' => $context['customer']->id,
        'invoice_number' => 'INV-RENEW-LINK-001',
        'issue_date' => now()->toDateString(),
        'due_date' => now()->addDay()->toDateString(),
        'subtotal' => 24000000,
        'discount_amount' => 0,
        'penalty_amount' => 0,
        'total_amount' => 24000000,
        'status' => 'paid',
        'paid_at' => now(),
    ]);

    $lease = app(CreateLeaseFromPaidBooking::class)->handle($renewalBooking, $invoice);

    assertDatabaseHas('leases', [
        'id' => $lease->id,
        'renewal_of_lease_id' => $sourceLease->id,
    ]);
});

function seedUserLeaseContext(): array
{
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
        'name' => 'Pasar Lease User',
        'address' => 'Jl. Lease No. 1',
        'city' => 'Kebumen',
        'status' => 'active',
    ]);

    $area = Area::query()->create([
        'market_id' => $market->id,
        'name' => 'Blok Lease',
        'status' => 'active',
    ]);

    $plot = Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => $area->id,
        'name' => 'Lahan Lease Demo',
        'type' => 'lapak',
        'length' => 5,
        'width' => 5,
        'area_square_meters' => 25,
        'base_price_monthly' => 12000000,
        'base_price_yearly' => 120000000,
        'status' => 'occupied',
        'description' => 'Lahan demo untuk pengujian lease user.',
    ]);

    return compact('customer', 'otherCustomer', 'market', 'area', 'plot');
}

function createLeaseForTenant(User $tenant, Plot $plot, array $leaseOverrides = []): Lease
{
    $booking = BookingRequest::query()->create([
        'user_id' => $tenant->id,
        'plot_id' => $plot->id,
        'term_type' => 'monthly',
        'duration' => 1,
        'start_date' => now()->subMonth()->toDateString(),
        'end_date' => now()->subDay()->toDateString(),
        'quoted_price' => 12000000,
        'final_price' => 12000000,
        'status' => 'approved',
        'payment_status' => 'paid',
        'approved_at' => now()->subMonths(2),
    ]);

    $invoiceNumber = $leaseOverrides['invoice_number'] ?? ('INV-'.($leaseOverrides['lease_number'] ?? strtoupper(str()->random(8))));

    $invoice = Invoice::query()->create([
        'booking_request_id' => $booking->id,
        'user_id' => $tenant->id,
        'invoice_number' => $invoiceNumber,
        'issue_date' => now()->subMonths(2)->toDateString(),
        'due_date' => now()->subMonths(2)->addDay()->toDateString(),
        'subtotal' => 12000000,
        'discount_amount' => 0,
        'penalty_amount' => 0,
        'total_amount' => 12000000,
        'status' => 'paid',
        'paid_at' => now()->subMonths(2),
    ]);

    return Lease::query()->create(array_merge([
        'booking_request_id' => $booking->id,
        'tenant_id' => $tenant->id,
        'plot_id' => $plot->id,
        'invoice_id' => $invoice->id,
        'lease_number' => 'LEASE-'.strtoupper(str()->random(8)),
        'start_date' => now()->subMonth()->toDateString(),
        'end_date' => now()->addMonth()->subDay()->toDateString(),
        'term_type' => 'monthly',
        'duration' => 1,
        'agreed_price' => 12000000,
        'deposit_amount' => 0,
        'status' => 'active',
        'activated_at' => now()->subMonth(),
        'renewal_of_lease_id' => null,
    ], $leaseOverrides));
}

function useUserPanel(): void
{
    Filament::setCurrentPanel('user');
    Filament::bootCurrentPanel();
}
