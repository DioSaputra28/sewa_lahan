<?php

use App\Models\Market;
use App\Models\Plot;

it('filters lahan by search query on plot name and market city', function () {
    $jakartaMarket = Market::query()->create([
        'name' => 'Pasar Jakarta',
        'address' => 'Jl. Jakarta No. 1',
        'city' => 'Jakarta',
        'status' => 'active',
    ]);
    $bandungMarket = Market::query()->create([
        'name' => 'Pasar Bandung',
        'address' => 'Jl. Bandung No. 1',
        'city' => 'Bandung',
        'status' => 'active',
    ]);

    Plot::query()->create([
        'market_id' => $jakartaMarket->id,
        'area_id' => null,
        'name' => 'Kios Sembako Utama',
        'type' => 'kiosk',
        'length' => 3,
        'width' => 3,
        'area_square_meters' => 9,
        'base_price_monthly' => 1500000,
        'base_price_yearly' => 18000000,
        'status' => 'available',
    ]);
    Plot::query()->create([
        'market_id' => $bandungMarket->id,
        'area_id' => null,
        'name' => 'Lapak Sayur Segar',
        'type' => 'stall',
        'length' => 2,
        'width' => 2,
        'area_square_meters' => 4,
        'base_price_monthly' => 800000,
        'base_price_yearly' => 9000000,
        'status' => 'available',
    ]);

    $this->get(route('lahan.index', ['q' => 'sembako']))
        ->assertSuccessful()
        ->assertSee('Kios Sembako Utama')
        ->assertDontSee('Lapak Sayur Segar');

    $this->get(route('lahan.index', ['q' => 'bandung']))
        ->assertSuccessful()
        ->assertSee('Lapak Sayur Segar')
        ->assertDontSee('Kios Sembako Utama');
});

it('filters lahan by price range and area range', function () {
    $market = Market::query()->create([
        'name' => 'Pasar Range',
        'address' => 'Jl. Range No. 1',
        'city' => 'Depok',
        'status' => 'active',
    ]);

    Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => null,
        'name' => 'Kios Mini',
        'type' => 'kiosk',
        'length' => 2,
        'width' => 2,
        'area_square_meters' => 4,
        'base_price_monthly' => 700000,
        'base_price_yearly' => 8000000,
        'status' => 'available',
    ]);
    Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => null,
        'name' => 'Kios Tengah',
        'type' => 'kiosk',
        'length' => 3,
        'width' => 3,
        'area_square_meters' => 9,
        'base_price_monthly' => 1500000,
        'base_price_yearly' => 18000000,
        'status' => 'available',
    ]);
    Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => null,
        'name' => 'Kios Besar',
        'type' => 'kiosk',
        'length' => 5,
        'width' => 4,
        'area_square_meters' => 20,
        'base_price_monthly' => 3000000,
        'base_price_yearly' => 34000000,
        'status' => 'available',
    ]);

    $this->get(route('lahan.index', ['price_min' => 1000000, 'price_max' => 2000000]))
        ->assertSuccessful()
        ->assertSee('Kios Tengah')
        ->assertDontSee('Kios Mini')
        ->assertDontSee('Kios Besar');

    $this->get(route('lahan.index', ['area_min' => 8, 'area_max' => 12]))
        ->assertSuccessful()
        ->assertSee('Kios Tengah')
        ->assertDontSee('Kios Mini')
        ->assertDontSee('Kios Besar');
});
