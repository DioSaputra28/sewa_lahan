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
            transition: transform 0.7s ease, opacity 200ms ease;
        }
        .hero-img:hover {
            transform: scale(1.03);
        }
        .hero-img.fading {
            opacity: 0;
        }
        .gallery-thumb {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            cursor: pointer;
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
        <li><a class="hover:text-slate-900 transition-colors" href="{{ route('home') }}">{{ __('web.single.breadcrumb_home') }}</a></li>
        <li><span class="material-symbols-outlined text-base">chevron_right</span></li>
        <li><a class="hover:text-slate-900 transition-colors" href="{{ route('lahan.index') }}">{{ __('web.single.breadcrumb_lahan') }}</a></li>
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
            <div class="relative rounded-3xl overflow-hidden bg-slate-100 mb-4 group" data-gallery-root>
                <img id="main-gallery-image" class="hero-img" src="{{ $primaryImage }}" alt="{{ $plot->name }}" />
                <div class="absolute top-4 left-4 flex gap-2">
                    <span class="bg-white/90 backdrop-blur-sm text-slate-900 text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full">
                        {{ __('web.single.badge_available_now') }}
                    </span>
                    <span class="bg-primary/10 text-primary text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full border border-primary/30">
                        {{ __('web.single.badge_premium') }}
                    </span>
                </div>
                <button class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm p-2.5 rounded-full hover:bg-white transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-slate-400 hover:text-red-500 transition-colors">favorite</span>
                </button>
            </div>

            {{-- Thumbnail Strip --}}
            <div class="grid grid-cols-4 gap-3" data-gallery-thumbs>
                @foreach ($allImages as $index => $imgUrl)
                    <button
                        type="button"
                        class="rounded-xl overflow-hidden transition-all duration-150 {{ $index === 0 ? 'ring-2 ring-slate-900 ring-offset-2 opacity-100' : 'opacity-60 hover:opacity-100' }}"
                        data-gallery-src="{{ $imgUrl }}"
                        data-gallery-alt="{{ $plot->name }} - Image {{ $index + 1 }}"
                        data-gallery-index="{{ $index }}"
                        aria-label="{{ __('web.single.aria_thumbnail', ['index' => $index + 1]) }}"
                        aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                    >
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
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">{{ __('web.single.label_premium_stall') }}</p>
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
                            {{ __('web.single.label_monthly') }}
                        </button>
                        <button id="toggle-yearly" class="flex-1 py-2 text-sm font-bold rounded-lg text-slate-500 hover:text-slate-700 transition-all">
                            {{ __('web.single.label_yearly') }} <span class="text-xs font-normal text-slate-400">{{ __('web.single.label_discount') }}</span>
                        </button>
                    </div>

                    {{-- Price Display --}}
                    <div class="mb-1">
                        <span id="price-display" class="text-4xl font-black text-slate-900">{{ $monthlyPrice }}</span>
                        <span class="text-slate-400 text-sm">/<span id="price-period">{{ __('web.single.label_per_month') }}</span></span>
                    </div>
                    <p id="price-sub" class="text-xs text-slate-400 mb-6">{{ __('web.single.label_per_year') }} {{ $yearlyPrice }}</p>

                    <button class="w-full py-4 bg-primary hover:bg-primary/90 text-slate-900 font-bold text-base rounded-xl shadow-md transition-all active:scale-[0.98]">
                        {{ __('web.single.btn_rent_now') }}
                    </button>
                    <p class="text-center text-xs text-slate-400 mt-3">{{ __('web.single.disclaimer_no_fees') }}</p>
                </div>

                {{-- Location Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-slate-400 mb-4">{{ __('web.single.location_title') }}</h3>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 mt-0.5">store</span>
                            <div>
                                <p class="text-xs text-slate-400">{{ __('web.single.label_market') }}</p>
                                <p class="text-sm font-semibold text-slate-900">{{ $marketName }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 mt-0.5">grid_view</span>
                            <div>
                                <p class="text-xs text-slate-400">{{ __('web.single.label_area_zone') }}</p>
                                <p class="text-sm font-semibold text-slate-900">{{ $areaName }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 mt-0.5">place</span>
                            <div>
                                <p class="text-xs text-slate-400">{{ __('web.single.label_city') }}</p>
                                <p class="text-sm font-semibold text-slate-900">{{ $marketCity }}</p>
                            </div>
                        </div>
                        @if ($marketAddress)
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 mt-0.5">pin_drop</span>
                            <div>
                                <p class="text-xs text-slate-400">{{ __('web.single.label_address') }}</p>
                                <p class="text-sm font-semibold text-slate-900">{{ $marketAddress }}</p>
                            </div>
                        </div>
                        @endif
                        @if ($mapsUrl && $mapsUrl !== '#')
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 mt-0.5">map</span>
                            <div>
                                <p class="text-xs text-slate-400">{{ __('web.single.label_maps') }}</p>
                                <a class="text-sm font-semibold text-primary hover:underline" href="{{ $mapsUrl }}" target="_blank" rel="noopener">{{ __('web.single.link_google_maps') }} →</a>
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
        <h2 class="text-lg font-black text-slate-900 mb-6">{{ __('web.single.specs_title') }}</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">straighten</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">{{ __('web.single.label_dimension') }}</p>
                <p class="text-base font-black text-slate-900">{{ $dimension }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">square_foot</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">{{ __('web.single.label_total_area') }}</p>
                <p class="text-base font-black text-slate-900">{{ $areaSize }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">layers</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">{{ __('web.single.label_floor') }}</p>
                <p class="text-base font-black text-slate-900">{{ $floorLabel }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">category</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">{{ __('web.single.label_type') }}</p>
                <p class="text-base font-black text-slate-900">{{ $typeLabel }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">bolt</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">{{ __('web.single.label_status') }}</p>
                <p class="text-base font-black text-primary">{{ __('web.single.status_available') }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">near_me</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">{{ __('web.single.label_position') }}</p>
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
                <h2 class="text-2xl font-black text-slate-900 mb-5">{{ __('web.single.about_title') }}</h2>
                <div class="prose prose-slate max-w-none space-y-4 text-slate-600 leading-relaxed">
                    @if ($plot->description)
                        <p>{{ $plot->description }}</p>
                    @else
                        <p>{{ __('web.single.about_empty') }}</p>
                    @endif
                </div>
            </div>

            {{-- Amenities placeholder --}}
            <div>
                <h2 class="text-lg font-black text-slate-900 mb-5">{{ __('web.single.facilities_title') }}</h2>
                <ul class="space-y-3">
                    @php
                        $amenities = [
                            ['icon' => 'electrical_services', 'key' => 'facility_electricity'],
                            ['icon' => 'wifi', 'key' => 'facility_wifi'],
                            ['icon' => 'local_parking', 'key' => 'facility_parking'],
                            ['icon' => 'local_gas_station', 'key' => 'facility_loading'],
                            ['icon' => 'security', 'key' => 'facility_security'],
                            ['icon' => 'cleaning_services', 'key' => 'facility_cleaning'],
                            ['icon' => 'water_drop', 'key' => 'facility_water'],
                            ['icon' => 'cash', 'key' => 'facility_payment'],
                        ];
                    @endphp
                    @foreach ($amenities as $a)
                    <li class="flex items-center gap-3">
                        <span class="bg-primary/10 text-primary p-1.5 rounded-lg">
                            <span class="material-symbols-outlined text-base">{{ $a['icon'] }}</span>
                        </span>
                        <span class="text-sm font-medium text-slate-700">{{ __('web.single.' . $a['key']) }}</span>
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
                    <h2 class="text-2xl font-black text-slate-900 mb-1">{{ __('web.single.related_title') }}</h2>
                    <p class="text-slate-500 text-sm">{{ __('web.single.related_subtitle', ['market' => $marketName]) }}</p>
                </div>
                <a class="text-sm font-bold text-primary hover:underline flex items-center gap-1" href="{{ route('lahan.index', ['region' => $marketCity]) }}">
                    {{ __('web.single.related_view_all') }}
                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                </a>
            </div>

            @if ($related->isEmpty())
                <p class="text-slate-500 text-center py-8">{{ __('web.single.related_empty') }}</p>
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
                                    {{ __('web.single.status_available') }}
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
                                        <p class="text-xs text-slate-400">{{ __('web.single.label_size') }}</p>
                                        <p class="text-sm font-bold text-slate-700">{{ $relSize }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-black text-primary">{{ $relPrice }}</p>
                                        <p class="text-[10px] text-slate-400">{{ __('web.single.label_per_month') }}</p>
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
    // ─── Price Toggle ────────────────────────────────────────────────────────────
    function initPriceToggle() {
        const btnMonthly = document.getElementById('toggle-monthly');
        const btnYearly = document.getElementById('toggle-yearly');
        const priceDisplay = document.getElementById('price-display');
        const pricePeriod = document.getElementById('price-period');
        const priceSub = document.getElementById('price-sub');
        const monthlyPrice = {{ Js::from($monthlyPrice) }};
        const yearlyPrice = {{ Js::from($yearlyPrice) }};
        const labelPerMonth = {{ Js::from(__('web.single.label_per_month')) }};
        const labelPerYear = {{ Js::from(__('web.single.label_per_year')) }};
        const billedAnnually = {{ Js::from(__('web.single.price_billed_annually')) }};

        btnYearly.addEventListener('click', () => {
            btnYearly.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
            btnYearly.classList.remove('text-slate-500');
            btnMonthly.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
            btnMonthly.classList.add('text-slate-500');
            priceDisplay.textContent = yearlyPrice;
            pricePeriod.textContent = 'year';
            priceSub.textContent = billedAnnually;
        });

        btnMonthly.addEventListener('click', () => {
            btnMonthly.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
            btnMonthly.classList.remove('text-slate-500');
            btnYearly.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
            btnYearly.classList.add('text-slate-500');
            priceDisplay.textContent = monthlyPrice;
            pricePeriod.textContent = 'month';
            priceSub.textContent = labelPerYear + ' ' + yearlyPrice;
        });
    }

	    // ─── Image Gallery ─────────────────────────────────────────────────────────
	    function initImageGallery() {
	        const mainImage = document.getElementById('main-gallery-image');
	        const thumbsContainer = document.querySelector('[data-gallery-thumbs]');
	        if (!mainImage || !thumbsContainer) {
	            return;
	        }

	        const thumbs = Array.from(thumbsContainer.querySelectorAll('button[data-gallery-src]'));
	        if (thumbs.length === 0) {
	            return;
	        }

	        const setActiveThumb = (activeThumb) => {
	            thumbs.forEach((thumb) => {
	                const isActive = thumb === activeThumb;

	                thumb.setAttribute('aria-current', isActive ? 'true' : 'false');
	                thumb.classList.toggle('ring-2', isActive);
	                thumb.classList.toggle('ring-slate-900', isActive);
	                thumb.classList.toggle('ring-offset-2', isActive);
	                thumb.classList.toggle('opacity-100', isActive);
	                thumb.classList.toggle('opacity-60', ! isActive);
	            });
	        };

	        setActiveThumb(thumbs.find((t) => t.getAttribute('aria-current') === 'true') ?? thumbs[0]);

	        const fadeOut = () => new Promise((resolve) => {
	            let resolved = false;
	            const finalize = () => {
	                if (resolved) {
	                    return;
	                }
	                resolved = true;
	                resolve();
	            };

	            const onEnd = (event) => {
	                if (event.propertyName !== 'opacity') {
	                    return;
	                }
	                mainImage.removeEventListener('transitionend', onEnd);
	                finalize();
	            };

	            mainImage.addEventListener('transitionend', onEnd);
	            // Fallback if transitionend doesn't fire for any reason.
	            window.setTimeout(() => {
	                mainImage.removeEventListener('transitionend', onEnd);
	                finalize();
	            }, 250);

	            mainImage.classList.add('fading');
	        });

	        const preload = (src) => new Promise((resolve) => {
	            const img = new Image();
	            img.onload = () => resolve(true);
	            img.onerror = () => resolve(false);
	            img.src = src;
	        });

	        thumbsContainer.addEventListener('click', async (event) => {
	            const clicked = event.target.closest('button[data-gallery-src]');
	            if (!clicked || !thumbsContainer.contains(clicked)) {
	                return;
	            }

	            const newSrc = clicked.dataset.gallerySrc;
	            const newAlt = clicked.dataset.galleryAlt || mainImage.getAttribute('alt') || '';

	            if (!newSrc) {
	                return;
	            }

	            setActiveThumb(clicked);

	            // Compare against the attribute to avoid absolute URL normalization differences.
	            if (mainImage.getAttribute('src') === newSrc) {
	                mainImage.setAttribute('alt', newAlt);
	                return;
	            }

	            await fadeOut();
	            await preload(newSrc);

	            mainImage.setAttribute('src', newSrc);
	            mainImage.setAttribute('alt', newAlt);
	            requestAnimationFrame(() => mainImage.classList.remove('fading'));
	        });
	    }

    // ─── Init ────────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        initPriceToggle();
        initImageGallery();
    });
</script>
@endsection
