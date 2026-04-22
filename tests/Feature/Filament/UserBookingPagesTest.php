<?php

use App\Actions\Bookings\CreateUserBookingRequest;
use App\Models\Area;
use App\Models\BookingRequest;
use App\Models\Market;
use App\Models\Plot;
use App\Models\PlotImage;
use App\Models\Role;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

it('redirects guests from the user panel to the user login page', function () {
    get('/user')
        ->assertRedirect('/user/login');
});

it('allows customer users to access the user booking pages', function () {
    $context = seedUserBookingContext();

    actingAs($context['customer']);

    get('/user/bookings')->assertSuccessful();
    get('/user/bookings/browse-plots')->assertSuccessful();
    get('/user/bookings/plots/'.$context['plot']->id)->assertSuccessful();
    get('/user/bookings/create?plot='.$context['plot']->id)->assertSuccessful();
});

it('shows available term types on the booking create page based on plot prices', function () {
    $context = seedUserBookingContext();

    actingAs($context['customer']);

    $response = get('/user/bookings/create?plot='.$context['plot']->id);

    $response->assertSuccessful();
    $response->assertSee('Bulanan');
    $response->assertSee('Tahunan');
});

it('shows complete plot detail sections on the user plot page', function () {
    $context = seedUserBookingContext();

    actingAs($context['customer']);

    get('/user/bookings/plots/'.$context['plot']->id)
        ->assertSuccessful()
        ->assertSee('Detail Lokasi')
        ->assertSee('Detail Lahan')
        ->assertSee('Tentang Lapak Ini')
        ->assertSee('Fasilitas')
        ->assertSee('Lapak Serupa di Sekitar');
});

it('uses custom action labels on the booking create page', function () {
    $context = seedUserBookingContext();

    actingAs($context['customer']);

    $response = get('/user/bookings/create?plot='.$context['plot']->id);

    $response->assertSuccessful();
    $response->assertSee('Ajukan Booking');
    $response->assertSee('Kembali');
    $response->assertDontSee('Create another');
});

it('shows only current user bookings in the user panel', function () {
    $context = seedUserBookingContext();

    BookingRequest::query()->create([
        'user_id' => $context['otherCustomer']->id,
        'plot_id' => $context['plot']->id,
        'term_type' => 'monthly',
        'duration' => 2,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonths(2)->subDay()->toDateString(),
        'quoted_price' => 20000000,
        'status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    $ownBooking = BookingRequest::query()->create([
        'user_id' => $context['customer']->id,
        'plot_id' => $context['plot']->id,
        'term_type' => 'monthly',
        'duration' => 1,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonth()->subDay()->toDateString(),
        'quoted_price' => 10000000,
        'status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    actingAs($context['customer']);

    $response = get('/user/bookings');

    $response->assertSuccessful();
    $response->assertSee((string) $ownBooking->id);
    $response->assertDontSee('20000000');
});

it('creates booking request from selected plot with calculated dates and quoted price', function () {
    $context = seedUserBookingContext();

    $action = app(CreateUserBookingRequest::class);

    $booking = $action->handle(
        user: $context['customer'],
        plot: $context['plot'],
        termType: 'monthly',
        duration: 3,
        startDate: now()->startOfDay(),
        notes: 'Mohon diproses cepat',
    );

    expect($booking->user_id)->toBe($context['customer']->id)
        ->and($booking->plot_id)->toBe($context['plot']->id)
        ->and($booking->quoted_price)->toBe(30000000)
        ->and($booking->status)->toBe('pending');

    assertDatabaseHas('booking_requests', [
        'id' => $booking->id,
        'user_id' => $context['customer']->id,
        'plot_id' => $context['plot']->id,
        'term_type' => 'monthly',
        'duration' => 3,
        'quoted_price' => 30000000,
        'status' => 'pending',
        'payment_status' => 'unpaid',
    ]);
});

function seedUserBookingContext(): array
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
        'name' => 'Pasar User Demo',
        'address' => 'Jl. User No. 1',
        'city' => 'Kebumen',
        'status' => 'active',
    ]);

    $area = Area::query()->create([
        'market_id' => $market->id,
        'name' => 'Blok User',
        'status' => 'active',
    ]);

    $plot = Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => $area->id,
        'name' => 'Lahan User Demo',
        'type' => 'lapak',
        'length' => 5,
        'width' => 5,
        'area_square_meters' => 25,
        'base_price_monthly' => 10000000,
        'base_price_yearly' => 100000000,
        'status' => 'available',
        'description' => 'Lahan demo untuk pengujian panel user.',
    ]);

    PlotImage::query()->create([
        'plot_id' => $plot->id,
        'image_path' => 'plots/user-demo-1.jpg',
        'is_primary' => true,
        'sort_order' => 1,
    ]);

    PlotImage::query()->create([
        'plot_id' => $plot->id,
        'image_path' => 'plots/user-demo-2.jpg',
        'is_primary' => false,
        'sort_order' => 2,
    ]);

    return compact('customer', 'otherCustomer', 'market', 'area', 'plot');
}
