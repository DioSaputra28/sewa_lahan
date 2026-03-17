<?php

use App\Filament\User\Resources\Bookings\Schemas\BookingForm;
use App\Models\Area;
use App\Models\Market;
use App\Models\Plot;

it('builds term type options from plot id without relying on the request query', function () {
    $market = Market::query()->create([
        'name' => 'Pasar Term Type',
        'address' => 'Jl. Term Type No. 1',
        'city' => 'Kebumen',
        'status' => 'active',
    ]);

    $area = Area::query()->create([
        'market_id' => $market->id,
        'name' => 'Blok Form',
        'status' => 'active',
    ]);

    $plot = Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => $area->id,
        'name' => 'Lahan Form',
        'type' => 'lapak',
        'length' => 5,
        'width' => 5,
        'area_square_meters' => 25,
        'base_price_monthly' => 15000000,
        'base_price_yearly' => 165000000,
        'status' => 'available',
    ]);

    expect(BookingForm::getTermTypeOptions($plot->id))->toBe([
        'monthly' => 'Bulanan',
        'yearly' => 'Tahunan',
    ]);
});

it('returns an empty term type option list when no plot id is provided', function () {
    expect(BookingForm::getTermTypeOptions(null))->toBe([]);
});
