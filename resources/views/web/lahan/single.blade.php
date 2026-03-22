@extends('web.layouts.main')

@push('head')
    <style>
        :root {
            --color-primary: #47eb7e;
        }
        .hero-img {
            width: 100%;
            aspect-ratio: 16/10;
            object-fit: cover;
            transition: transform 0.7s ease;
        }
        .hero-img:hover {
            transform: scale(1.03);
        }
        .gallery-thumb {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
@php
    $imgService = app(\App\Services\PublicPlotListingQuery::class);
    $plot = $plot; // explicit for clarity
    $monthlyPrice = $imgService->formatPriceFull($plot->base_price_monthly);
    $yearlyPrice  = $imgService->formatPriceFull($plot->base_price_yearly);
    $dimension = $plot->length . ' × ' . $plot->width . ' m';
    $areaSize = rtrim(rtrim(number_format($plot->area_square_meters, 2, '.', ''), '0'), '.') . ' m²';
    $floorLabel = $plot->floor_level ?? '—';
    $typeLabel = $plot->type ?? '—';
    $marketName = $plot->market->name ?? '—';
    $marketCity = $plot->market->city ?? '—';
    $marketAddress = $plot->market->address ?? '—';
    $areaName = $plot->area?->name ?? '—';
    $mapsUrl = $plot->market->maps_url ?? '#';
    $breadcrumbName = $plot->name;
@endphp

{{-- Breadcrumb --}}
<nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6" aria-label="Breadcrumb">
    <ol class="flex items-center gap-2 text-sm text-slate-500">
        <li><a class="hover:text-slate-900 transition-colors" href="{{ route('home') }}">Home</a></li>
        <li><span class="material-symbols-outlined text-base">chevron_right</span></li>
        <li><a class="hover:text-slate-900 transition-colors" href="{{ route('lahan.index') }}">Lahan</a></li>
        <li><span class="material-symbols-outlined text-base">chevron_right</span></li>
        <li class="text-slate-900 font-medium truncate max-w-[200px]">{{ $breadcrumbName }}</li>
    </ol>
</nav>

{{-- Hero: Image Gallery + Booking Sidebar --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- Left: Image Gallery --}}
        <div class="flex-1">
            {{-- Main Image --}}
            <div class="relative rounded-3xl overflow-hidden bg-slate-100 mb-4 group">
                <img class="hero-img" src="{{ $primaryImage }}" alt="{{ $plot->name }}" />
                <div class="absolute top-4 left-4 flex gap-2">
                    <span class="bg-white/90 backdrop-blur-sm text-slate-900 text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full">
                        Available Now
                    </span>
                    <span class="bg-primary/10 text-primary text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full border border-primary/30">
                        Premium Spot
                    </span>
                </div>
                <button class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm p-2.5 rounded-full hover:bg-white transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-slate-400 hover:text-red-500 transition-colors">favorite</span>
                </button>
            </div>

            {{-- Thumbnail Strip --}}
            <div class="grid grid-cols-4 gap-3">
                @foreach ($allImages as $index => $imgUrl)
                    <button class="rounded-xl overflow-hidden {{ $index === 0 ? 'ring-2 ring-slate-900 ring-offset-2' : 'opacity-60 hover:opacity-100 transition-opacity' }}">
                        <img class="gallery-thumb" src="{{ $imgUrl }}" alt="{{ $plot->name }} - Image {{ $index + 1 }}" />
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Right: Booking Sidebar (sticky on desktop) --}}
        <aside class="w-full lg:w-96 flex-shrink-0">
            <div class="lg:sticky lg:top-28 space-y-5">

                {{-- Plot Title Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Premium Stall</p>
                            <h1 class="text-2xl lg:text-3xl font-black text-slate-900 leading-tight">{{ $plot->name }}</h1>
                            <p class="text-slate-500 flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined text-sm text-slate-400">location_on</span>
                                {{ $marketName }} — {{ $areaName }}
                            </p>
                        </div>
                    </div>

                    {{-- Quick Specs Row --}}
                    <div class="flex flex-wrap gap-2 mt-4">
                        <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                            <span class="material-symbols-outlined text-sm">square_foot</span> {{ $areaSize }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                            <span class="material-symbols-outlined text-sm">layers</span> Floor {{ $floorLabel }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                            <span class="material-symbols-outlined text-sm">category</span> {{ $typeLabel }}
                        </span>
                    </div>
                </div>

                {{-- Pricing Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    {{-- Billing Toggle --}}
                    <div class="flex bg-slate-100 rounded-xl p-1 mb-5">
                        <button id="toggle-monthly" class="flex-1 py-2 text-sm font-bold rounded-lg bg-white text-slate-900 shadow-sm transition-all">
                            Monthly
                        </button>
                        <button id="toggle-yearly" class="flex-1 py-2 text-sm font-bold rounded-lg text-slate-500 hover:text-slate-700 transition-all">
                            Yearly <span class="text-xs font-normal text-slate-400">-5%</span>
                        </button>
                    </div>

                    {{-- Price Display --}}
                    <div class="mb-1">
                        <span id="price-display" class="text-4xl font-black text-slate-900">{{ $monthlyPrice }}</span>
                        <span class="text-slate-400 text-sm">/<span id="price-period">month</span></span>
                    </div>
                    <p id="price-sub" class="text-xs text-slate-400 mb-6">{{ $yearlyPrice }}/year if paid annually</p>

                    <button class="w-full py-4 bg-primary hover:bg-primary/90 text-slate-900 font-bold text-base rounded-xl shadow-md transition-all active:scale-[0.98]">
                        Rent Now
                    </button>
                    <p class="text-center text-xs text-slate-400 mt-3">No hidden fees. Cancel anytime.</p>
                </div>

                {{-- Location Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-slate-400 mb-4">Location Details</h3>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 mt-0.5">store</span>
                            <div>
                                <p class="text-xs text-slate-400">Market</p>
                                <p class="text-sm font-semibold text-slate-900">{{ $marketName }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 mt-0.5">grid_view</span>
                            <div>
                                <p class="text-xs text-slate-400">Area / Zone</p>
                                <p class="text-sm font-semibold text-slate-900">{{ $areaName }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 mt-0.5">place</span>
                            <div>
                                <p class="text-xs text-slate-400">City</p>
                                <p class="text-sm font-semibold text-slate-900">{{ $marketCity }}</p>
                            </div>
                        </div>
                        @if ($marketAddress)
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 mt-0.5">pin_drop</span>
                            <div>
                                <p class="text-xs text-slate-400">Address</p>
                                <p class="text-sm font-semibold text-slate-900">{{ $marketAddress }}</p>
                            </div>
                        </div>
                        @endif
                        @if ($mapsUrl && $mapsUrl !== '#')
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 mt-0.5">map</span>
                            <div>
                                <p class="text-xs text-slate-400">Maps</p>
                                <a class="text-sm font-semibold text-primary hover:underline" href="{{ $mapsUrl }}" target="_blank" rel="noopener">View on Google Maps →</a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </aside>
    </div>
</section>

{{-- Plot Specifications --}}
<section class="bg-white border-y border-slate-200 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-lg font-black text-slate-900 mb-6">Detail Lahan</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">straighten</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Dimension</p>
                <p class="text-base font-black text-slate-900">{{ $dimension }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">square_foot</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Total Area</p>
                <p class="text-base font-black text-slate-900">{{ $areaSize }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">layers</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Floor</p>
                <p class="text-base font-black text-slate-900">{{ $floorLabel }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">category</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Type</p>
                <p class="text-base font-black text-slate-900">{{ $typeLabel }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">bolt</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Status</p>
                <p class="text-base font-black text-primary">Available</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">near_me</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Position</p>
                <p class="text-base font-black text-slate-900">{{ $plot->location_note ?? $plot->name }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Description + Amenities --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid lg:grid-cols-3 gap-12">
        {{-- Description --}}
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-black text-slate-900 mb-5">About This Stall</h2>
            <div class="prose prose-slate max-w-none space-y-4 text-slate-600 leading-relaxed">
                @if ($plot->description)
                    <p>{{ $plot->description }}</p>
                @else
                    <p>No description available for this stall yet. Contact us for more information.</p>
                @endif
            </div>
        </div>

        {{-- Amenities placeholder --}}
        <div>
            <h2 class="text-lg font-black text-slate-900 mb-5">Fasilitas</h2>
            <ul class="space-y-3">
                @php
                    $amenities = [
                        ['icon' => 'electrical_services', 'label' => '230V Electrical Outlet'],
                        ['icon' => 'wifi', 'label' => 'Public WiFi Zone'],
                        ['icon' => 'local_parking', 'label' => 'Motorcycle Parking'],
                        ['icon' => 'local_gas_station', 'label' => 'Dedicated Loading Area'],
                        ['icon' => 'security', 'label' => '24/7 Security Guard'],
                        ['icon' => 'cleaning_services', 'label' => 'Daily Cleaning Service'],
                        ['icon' => 'water_drop', 'label' => 'Clean Water Supply'],
                        ['icon' => 'cash', 'label' => 'ATM & Digital Payment'],
                    ];
                @endphp
                @foreach ($amenities as $a)
                <li class="flex items-center gap-3">
                    <span class="bg-primary/10 text-primary p-1.5 rounded-lg">
                        <span class="material-symbols-outlined text-base">{{ $a['icon'] }}</span>
                    </span>
                    <span class="text-sm font-medium text-slate-700">{{ $a['label'] }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- Related Listings --}}
<section class="bg-slate-50 border-t border-slate-200 py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-black text-slate-900 mb-1">Similar Stalls Nearby</h2>
                <p class="text-slate-500 text-sm">Other available units in {{ $marketName }}</p>
            </div>
            <a class="text-sm font-bold text-primary hover:underline flex items-center gap-1" href="{{ route('lahan.index', ['region' => $marketCity]) }}">
                View All
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
        </div>

        @if ($related->isEmpty())
            <p class="text-slate-500 text-center py-8">No similar stalls available at the moment.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($related as $relPlot)
                    @php
                        $relImg = $imgService->primaryImageUrl($relPlot);
                        $relPrice = $imgService->formatPrice($relPlot->base_price_monthly);
                        $relSize = $relPlot->length . ' × ' . $relPlot->width . ' m';
                    @endphp
                    <a href="{{ route('lahan.show', $relPlot) }}"
                       class="bg-white rounded-2xl border border-slate-200 overflow-hidden group hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer block">
                        <div class="relative h-40 overflow-hidden">
                            <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="{{ $relImg }}" alt="{{ $relPlot->name }}" />
                            <span class="absolute top-3 left-3 text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded-full bg-slate-100 text-slate-700">
                                Available
                            </span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-sm text-slate-900 mb-1 group-hover:text-primary transition-colors leading-tight">{{ $relPlot->name }}</h3>
                            <p class="text-xs text-slate-400 flex items-center gap-0.5 mb-3">
                                <span class="material-symbols-outlined text-xs">location_on</span>
                                {{ $relPlot->market->city ?? '' }}
                            </p>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-400">Size</p>
                                    <p class="text-sm font-bold text-slate-700">{{ $relSize }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black text-primary">{{ $relPrice }}</p>
                                    <p class="text-[10px] text-slate-400">/month</p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

<script>
    // Price toggle
    const btnMonthly = document.getElementById('toggle-monthly');
    const btnYearly = document.getElementById('toggle-yearly');
    const priceDisplay = document.getElementById('price-display');
    const pricePeriod = document.getElementById('price-period');
    const priceSub = document.getElementById('price-sub');

    const monthlyPrice = {{ Js::from($monthlyPrice) }};
    const yearlyPrice = {{ Js::from($yearlyPrice) }};

    btnYearly.addEventListener('click', () => {
        btnYearly.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
        btnYearly.classList.remove('text-slate-500');
        btnMonthly.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
        btnMonthly.classList.add('text-slate-500');
        priceDisplay.textContent = yearlyPrice;
        pricePeriod.textContent = 'year';
        priceSub.textContent = 'Billed annually — save 5%';
    });

    btnMonthly.addEventListener('click', () => {
        btnMonthly.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
        btnMonthly.classList.remove('text-slate-500');
        btnYearly.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
        btnYearly.classList.add('text-slate-500');
        priceDisplay.textContent = monthlyPrice;
        pricePeriod.textContent = 'month';
        priceSub.textContent = yearlyPrice + '/year if paid annually';
    });
</script>
@endsection
