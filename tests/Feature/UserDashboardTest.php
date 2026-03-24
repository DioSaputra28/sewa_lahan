<?php

use App\Models\Area;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Market;
use App\Models\Plot;
use App\Models\Role;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('renders the user dashboard with built in widget sections and quick actions', function () {
    $context = seedUserDashboardContext();

    actingAs($context['customer']);

    get('/user')
        ->assertSuccessful()
        ->assertSee('Ringkasan Kontrak Aktif')
        ->assertSee('Invoice yang Perlu Diselesaikan')
        ->assertSee('Booking yang Masih Diproses')
        ->assertSee('Ringkasan Akun')
        ->assertSee('Cari Lahan')
        ->assertSee('Lanjutkan Pembayaran')
        ->assertSee('Ajukan Perpanjangan')
        ->assertSee('Lihat Invoice');
});

it('shows empty state guidance on the user dashboard when there is no active lease', function () {
    $context = seedUserDashboardContext(withActiveLease: false);

    actingAs($context['customer']);

    get('/user')
        ->assertSuccessful()
        ->assertSee('Belum Ada Kontrak Aktif')
        ->assertSee('Cari Lahan');
});

function seedUserDashboardContext(bool $withActiveLease = true): array
{
    $customerRole = Role::query()->firstOrCreate(['name' => 'customer']);

    $customer = User::factory()->create([
        'status' => 'active',
        'email_verified_at' => now(),
    ]);
    $customer->roles()->syncWithoutDetaching([$customerRole->id]);

    $market = Market::query()->create([
        'name' => 'Pasar Dashboard User',
        'address' => 'Jl. Dashboard No. 1',
        'city' => 'Kebumen',
        'status' => 'active',
    ]);

    $area = Area::query()->create([
        'market_id' => $market->id,
        'name' => 'Blok Dashboard',
        'status' => 'active',
    ]);

    $plot = Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => $area->id,
        'name' => 'Lahan Dashboard',
        'type' => 'lapak',
        'length' => 5,
        'width' => 5,
        'area_square_meters' => 25,
        'base_price_monthly' => 12000000,
        'base_price_yearly' => 120000000,
        'status' => 'occupied',
    ]);

    BookingRequest::query()->create([
        'user_id' => $customer->id,
        'plot_id' => $plot->id,
        'term_type' => 'monthly',
        'duration' => 1,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonth()->subDay()->toDateString(),
        'quoted_price' => 12000000,
        'final_price' => 12000000,
        'status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    BookingRequest::query()->create([
        'user_id' => $customer->id,
        'plot_id' => $plot->id,
        'term_type' => 'monthly',
        'duration' => 2,
        'start_date' => now()->addMonth()->toDateString(),
        'end_date' => now()->addMonths(3)->subDay()->toDateString(),
        'quoted_price' => 24000000,
        'final_price' => 24000000,
        'status' => 'approved',
        'payment_status' => 'unpaid',
    ]);

    Invoice::query()->create([
        'booking_request_id' => BookingRequest::query()->where('user_id', $customer->id)->latest('id')->firstOrFail()->id,
        'user_id' => $customer->id,
        'invoice_number' => 'INV-DASHBOARD-001',
        'issue_date' => now()->toDateString(),
        'due_date' => now()->addDay()->toDateString(),
        'subtotal' => 24000000,
        'discount_amount' => 0,
        'penalty_amount' => 0,
        'total_amount' => 24000000,
        'status' => 'unpaid',
    ]);

    if ($withActiveLease) {
        Lease::query()->create([
            'booking_request_id' => BookingRequest::query()->where('user_id', $customer->id)->firstOrFail()->id,
            'tenant_id' => $customer->id,
            'plot_id' => $plot->id,
            'invoice_id' => null,
            'lease_number' => 'LEASE-DASHBOARD-001',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonth()->subDay()->toDateString(),
            'term_type' => 'monthly',
            'duration' => 1,
            'agreed_price' => 12000000,
            'deposit_amount' => 0,
            'status' => 'active',
            'activated_at' => now()->subMonth(),
        ]);
    }

    return compact('customer', 'market', 'area', 'plot');
}
