<?php

use App\Filament\Resources\BookingRequests\Pages\EditBookingRequest;
use App\Models\Area;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\Market;
use App\Models\Plot;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

it('rejects booking without requiring approval fields and resets payment review fields', function () {
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
        'name' => 'Pasar Reject',
        'address' => 'Jl. Reject No. 1',
        'city' => 'Kebumen',
        'status' => 'active',
    ]);

    $area = Area::query()->create([
        'market_id' => $market->id,
        'name' => 'Blok Reject',
        'status' => 'active',
    ]);

    $plot = Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => $area->id,
        'name' => 'Lahan Reject',
        'type' => 'lapak',
        'length' => 4,
        'width' => 4,
        'area_square_meters' => 16,
        'base_price_monthly' => 5000000,
        'base_price_yearly' => 50000000,
        'status' => 'available',
    ]);

    $bookingRequest = BookingRequest::query()->create([
        'user_id' => $customer->id,
        'plot_id' => $plot->id,
        'term_type' => 'monthly',
        'duration' => 1,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonth()->subDay()->toDateString(),
        'quoted_price' => 5000000,
        'final_price' => null,
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'payment_due_at' => null,
        'expires_at' => null,
        'notes' => 'Catatan awal',
    ]);

    \Pest\Laravel\actingAs($admin);
    Filament::setCurrentPanel('admin');
    Filament::bootCurrentPanel();

    Livewire::test(EditBookingRequest::class, ['record' => $bookingRequest->getRouteKey()])
        ->set('data.notes', 'Catatan reject admin')
        ->callAction('reject', data: [
            'rejection_reason' => 'Dokumen belum lengkap.',
        ])
        ->assertHasNoActionErrors();

    expect($bookingRequest->fresh())
        ->status->toBe('rejected')
        ->payment_status->toBe('cancelled')
        ->final_price->toBeNull()
        ->payment_due_at->toBeNull()
        ->expires_at->toBeNull()
        ->rejection_reason->toBe('Dokumen belum lengkap.')
        ->notes->toBe('Catatan reject admin');
});

it('does not approve pending booking when approval fields are missing', function () {
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
        'name' => 'Pasar Approve Guard',
        'address' => 'Jl. Approve No. 1',
        'city' => 'Kebumen',
        'status' => 'active',
    ]);

    $area = Area::query()->create([
        'market_id' => $market->id,
        'name' => 'Blok Approve',
        'status' => 'active',
    ]);

    $plot = Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => $area->id,
        'name' => 'Lahan Approve Guard',
        'type' => 'lapak',
        'length' => 3,
        'width' => 3,
        'area_square_meters' => 9,
        'base_price_monthly' => 3000000,
        'base_price_yearly' => 30000000,
        'status' => 'available',
    ]);

    $bookingRequest = BookingRequest::query()->create([
        'user_id' => $customer->id,
        'plot_id' => $plot->id,
        'term_type' => 'monthly',
        'duration' => 1,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonth()->subDay()->toDateString(),
        'quoted_price' => 3000000,
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'final_price' => null,
        'payment_due_at' => null,
    ]);

    \Pest\Laravel\actingAs($admin);
    Filament::setCurrentPanel('admin');
    Filament::bootCurrentPanel();

    Livewire::test(EditBookingRequest::class, ['record' => $bookingRequest->getRouteKey()])
        ->callAction('approve');

    expect($bookingRequest->fresh())
        ->status->toBe('pending')
        ->payment_status->toBe('unpaid');

    expect(Invoice::query()->where('booking_request_id', $bookingRequest->id)->exists())
        ->toBeFalse();
});
