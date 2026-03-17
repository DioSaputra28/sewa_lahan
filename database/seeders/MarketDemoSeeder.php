<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Market;
use App\Models\Plot;
use App\Models\PlotImage;
use Illuminate\Database\Seeder;

class MarketDemoSeeder extends Seeder
{
    public function run(): void
    {
        $markets = [
            [
                'name' => 'Pasar Induk Kebumen',
                'address' => 'Jl. Veteran No. 10, Kebumen',
                'city' => 'Kebumen',
                'maps_url' => 'https://maps.google.com/?q=Pasar+Induk+Kebumen',
                'status' => 'active',
                'description' => 'Pasar utama untuk kebutuhan harian dan grosir.',
                'areas' => [
                    [
                        'name' => 'Blok A',
                        'status' => 'active',
                        'description' => 'Area depan dekat pintu utama.',
                        'plots' => [
                            ['name' => 'Lahan A-01', 'type' => 'lapak', 'length' => 5, 'width' => 5.2, 'area_square_meters' => 26, 'floor_level' => '1F', 'location_note' => 'Dekat pintu barat', 'base_price_monthly' => 15000000, 'base_price_yearly' => 165000000, 'status' => 'available', 'description' => 'Lapak strategis untuk freezer dan bahan segar.'],
                            ['name' => 'Lahan A-02', 'type' => 'kios', 'length' => 4, 'width' => 5, 'area_square_meters' => 20, 'floor_level' => '1F', 'location_note' => 'Samping area parkir', 'base_price_monthly' => 12000000, 'base_price_yearly' => 132000000, 'status' => 'available', 'description' => 'Kios dengan akses bongkar muat mudah.'],
                        ],
                    ],
                    [
                        'name' => 'Blok B',
                        'status' => 'active',
                        'description' => 'Area tengah dengan arus pembeli stabil.',
                        'plots' => [
                            ['name' => 'Lahan B-01', 'type' => 'lapak', 'length' => 4.5, 'width' => 5, 'area_square_meters' => 22.5, 'floor_level' => '1F', 'location_note' => 'Dekat jalur sayur', 'base_price_monthly' => 10000000, 'base_price_yearly' => 110000000, 'status' => 'occupied', 'description' => 'Lapak aktif dengan pengunjung stabil.'],
                            ['name' => 'Lahan B-02', 'type' => 'lahan', 'length' => 6, 'width' => 5, 'area_square_meters' => 30, 'floor_level' => '1F', 'location_note' => 'Sudut blok B', 'base_price_monthly' => 13000000, 'base_price_yearly' => 143000000, 'status' => 'available', 'description' => 'Lahan cukup luas untuk penyimpanan dan display.'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Pasar Pagi Nusantara',
                'address' => 'Jl. Merdeka No. 7, Purwokerto',
                'city' => 'Purwokerto',
                'maps_url' => 'https://maps.google.com/?q=Pasar+Pagi+Nusantara',
                'status' => 'active',
                'description' => 'Pasar modern dengan area sewa bulanan dan tahunan.',
                'areas' => [
                    [
                        'name' => 'Blok C',
                        'status' => 'active',
                        'description' => 'Area sayur dan kebutuhan segar.',
                        'plots' => [
                            ['name' => 'Lahan C-01', 'type' => 'lapak', 'length' => 5, 'width' => 4, 'area_square_meters' => 20, 'floor_level' => 'Ground', 'location_note' => 'Pojok kiri', 'base_price_monthly' => 9000000, 'base_price_yearly' => 99000000, 'status' => 'available', 'description' => 'Cocok untuk jual hasil panen harian.'],
                            ['name' => 'Lahan C-02', 'type' => 'kios', 'length' => 4.5, 'width' => 4.5, 'area_square_meters' => 20.25, 'floor_level' => 'Ground', 'location_note' => 'Dekat loading dock', 'base_price_monthly' => 11000000, 'base_price_yearly' => 121000000, 'status' => 'available', 'description' => 'Kios modern dengan akses barang mudah.'],
                        ],
                    ],
                    [
                        'name' => 'Blok D',
                        'status' => 'active',
                        'description' => 'Area sembako dan bahan kering.',
                        'plots' => [
                            ['name' => 'Lahan D-01', 'type' => 'lahan', 'length' => 5.5, 'width' => 5, 'area_square_meters' => 27.5, 'floor_level' => 'Ground', 'location_note' => 'Tengah blok D', 'base_price_monthly' => 9500000, 'base_price_yearly' => 104500000, 'status' => 'available', 'description' => 'Lahan dengan area display cukup lebar.'],
                            ['name' => 'Lahan D-02', 'type' => 'lapak', 'length' => 4, 'width' => 4, 'area_square_meters' => 16, 'floor_level' => 'Ground', 'location_note' => 'Samping pintu samping', 'base_price_monthly' => 8000000, 'base_price_yearly' => 88000000, 'status' => 'inactive', 'description' => 'Sementara nonaktif untuk renovasi area.'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($markets as $marketData) {
            $areas = $marketData['areas'];
            unset($marketData['areas']);

            $market = Market::query()->updateOrCreate(
                ['name' => $marketData['name']],
                $marketData,
            );

            foreach ($areas as $areaData) {
                $plots = $areaData['plots'];
                unset($areaData['plots']);

                $area = Area::query()->updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $areaData['name'],
                    ],
                    [
                        'market_id' => $market->id,
                        ...$areaData,
                    ],
                );

                foreach ($plots as $plotData) {
                    $plot = Plot::query()->updateOrCreate(
                        [
                            'market_id' => $market->id,
                            'area_id' => $area->id,
                            'name' => $plotData['name'],
                        ],
                        [
                            'market_id' => $market->id,
                            'area_id' => $area->id,
                            ...$plotData,
                        ],
                    );

                    PlotImage::query()->updateOrCreate(
                        [
                            'plot_id' => $plot->id,
                            'sort_order' => 1,
                        ],
                        [
                            'plot_id' => $plot->id,
                            'image_path' => 'plots/demo-'.$plot->id.'-1.jpg',
                            'is_primary' => true,
                            'sort_order' => 1,
                        ],
                    );

                    PlotImage::query()->updateOrCreate(
                        [
                            'plot_id' => $plot->id,
                            'sort_order' => 2,
                        ],
                        [
                            'plot_id' => $plot->id,
                            'image_path' => 'plots/demo-'.$plot->id.'-2.jpg',
                            'is_primary' => false,
                            'sort_order' => 2,
                        ],
                    );
                }
            }
        }
    }
}
