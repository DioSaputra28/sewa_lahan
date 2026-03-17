<?php

use App\Filament\User\Resources\Bookings\Schemas\BookingForm;
use App\Models\Area;
use App\Models\Market;
use App\Models\Plot;

it('calculates quoted price preview from plot term type and duration', function () {
    $market = Market::query()->create([
        'name' => 'Pasar Preview',
        'address' => 'Jl. Preview No. 1',
        'city' => 'Kebumen',
        'status' => 'active',
    ]);

    $area = Area::query()->create([
        'market_id' => $market->id,
        'name' => 'Blok Preview',
        'status' => 'active',
    ]);

    $plot = Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => $area->id,
        'name' => 'Lahan Preview',
        'type' => 'lapak',
        'length' => 5,
        'width' => 5,
        'area_square_meters' => 25,
        'base_price_monthly' => 15000000,
        'base_price_yearly' => 165000000,
        'status' => 'available',
    ]);

    expect(BookingForm::getQuotedPricePreview($plot->id, 'monthly', 3))->toBe('45.000.000')
        ->and(BookingForm::getQuotedPricePreview($plot->id, 'yearly', 2))->toBe('330.000.000');
});

it('returns a dash for quoted price preview when the input is incomplete', function () {
    expect(BookingForm::getQuotedPricePreview(null, 'monthly', 3))->toBe('-')
        ->and(BookingForm::getQuotedPricePreview(1, null, 3))->toBe('-')
        ->and(BookingForm::getQuotedPricePreview(1, 'monthly', null))->toBe('-');
});
