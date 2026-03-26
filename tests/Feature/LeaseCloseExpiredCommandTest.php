<?php

use App\Models\Area;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Market;
use App\Models\Plot;
use App\Models\User;
use App\Notifications\SendLeaseEndedNotification;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('marks active leases as ended when contract end date has passed and sends notification email', function () {
    Notification::fake();

    $context = seedLeaseExpiryContext();

    $expiredLease = createLeaseForTenant($context['tenant'], $context['plot'], [
        'lease_number' => 'LEASE-EXPIRED-001',
        'end_date' => now()->subDay()->toDateString(),
        'status' => 'active',
    ]);

    $todayLease = createLeaseForTenant($context['tenant'], $context['plot'], [
        'lease_number' => 'LEASE-TODAY-001',
        'end_date' => now()->toDateString(),
        'status' => 'active',
    ]);

    artisan('lease:close-expired')->assertExitCode(0);

    assertDatabaseHas('leases', [
        'id' => $expiredLease->id,
        'status' => 'ended',
    ]);

    assertDatabaseHas('leases', [
        'id' => $todayLease->id,
        'status' => 'active',
    ]);

    Notification::assertSentTo(
        $context['tenant'],
        SendLeaseEndedNotification::class,
        function (SendLeaseEndedNotification $notification, array $channels) use ($context, $expiredLease): bool {
            $payload = $notification->toArray($context['tenant']);

            return in_array('mail', $channels, true)
                && ($payload['lease_number'] ?? null) === $expiredLease->lease_number
                && ($payload['end_date'] ?? null) === $expiredLease->end_date?->toDateString();
        },
    );

    assertDatabaseHas('activity_logs', [
        'target_type' => Lease::class,
        'target_id' => $expiredLease->id,
        'action' => 'end-lease',
    ]);
});

it('is idempotent and does not update leases that are already ended', function () {
    Notification::fake();

    $context = seedLeaseExpiryContext();

    $alreadyEndedLease = createLeaseForTenant($context['tenant'], $context['plot'], [
        'lease_number' => 'LEASE-ENDED-001',
        'end_date' => now()->subDays(3)->toDateString(),
        'status' => 'ended',
    ]);

    artisan('lease:close-expired')->assertExitCode(0);
    artisan('lease:close-expired')->assertExitCode(0);

    assertDatabaseHas('leases', [
        'id' => $alreadyEndedLease->id,
        'status' => 'ended',
    ]);

    assertDatabaseMissing('activity_logs', [
        'target_type' => Lease::class,
        'target_id' => $alreadyEndedLease->id,
        'action' => 'end-lease',
    ]);

    Notification::assertNothingSent();
});

it('does not change renewal booking requests while ending expired leases', function () {
    Notification::fake();

    $context = seedLeaseExpiryContext();

    $expiredLease = createLeaseForTenant($context['tenant'], $context['plot'], [
        'lease_number' => 'LEASE-RENEW-SOURCE-001',
        'end_date' => now()->subDay()->toDateString(),
        'status' => 'active',
    ]);

    $renewalBooking = BookingRequest::query()->create([
        'user_id' => $context['tenant']->id,
        'plot_id' => $context['plot']->id,
        'renewal_of_lease_id' => $expiredLease->id,
        'term_type' => 'monthly',
        'duration' => 2,
        'start_date' => now()->addDay()->toDateString(),
        'end_date' => now()->addMonths(2)->subDay()->toDateString(),
        'quoted_price' => 24000000,
        'final_price' => 24000000,
        'status' => 'approved',
        'payment_status' => 'unpaid',
        'payment_due_at' => now()->addDays(2),
    ]);

    artisan('lease:close-expired')->assertExitCode(0);

    assertDatabaseHas('leases', [
        'id' => $expiredLease->id,
        'status' => 'ended',
    ]);

    assertDatabaseHas('booking_requests', [
        'id' => $renewalBooking->id,
        'status' => 'approved',
        'payment_status' => 'unpaid',
        'renewal_of_lease_id' => $expiredLease->id,
    ]);
});

function seedLeaseExpiryContext(): array
{
    $tenant = User::factory()->create([
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    $market = Market::query()->create([
        'name' => 'Pasar Lease Expiry',
        'address' => 'Jl. Expiry No. 1',
        'city' => 'Kebumen',
        'status' => 'active',
    ]);

    $area = Area::query()->create([
        'market_id' => $market->id,
        'name' => 'Blok Expiry',
        'status' => 'active',
    ]);

    $plot = Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => $area->id,
        'name' => 'Lahan Expiry Demo',
        'type' => 'lapak',
        'length' => 5,
        'width' => 5,
        'area_square_meters' => 25,
        'base_price_monthly' => 12000000,
        'base_price_yearly' => 120000000,
        'status' => 'occupied',
    ]);

    return compact('tenant', 'plot');
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
        'end_date' => now()->subDay()->toDateString(),
        'term_type' => 'monthly',
        'duration' => 1,
        'agreed_price' => 12000000,
        'deposit_amount' => 0,
        'status' => 'active',
        'activated_at' => now()->subMonth(),
        'renewal_of_lease_id' => null,
    ], $leaseOverrides));
}
