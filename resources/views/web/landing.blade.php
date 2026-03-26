@extends('web.layouts.main')

@push('head')
    <style>
        .plot-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .plot-card-img:hover {
            transform: scale(1.05);
        }
    </style>
@endpush

@section('content')
    @php
        $siteSettings = site_setting();
        $landingHeroImageUrl = get_landing_hero_image_url();
        $landingHeroImageAlt = $siteSettings->landing_hero_image_alt ?: 'Modern indoor market with organized wooden stalls';
        $listingService = app(App\Services\PublicPlotListingQuery::class);
    @endphp

    <!-- Modern Split Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="flex-1 text-left lg:pr-8">
                    <div class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-sm font-semibold leading-6 text-primary ring-1 ring-inset ring-primary/20 mb-6">
                        <span>{{ __('web.landing.hero_badge') }}</span>
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-black text-slate-900 dark:text-slate-50 leading-[1.1] tracking-tight mb-6">
                        {{ __('web.landing.hero_title') }}
                    </h1>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 max-w-xl">
                        {{ __('web.landing.hero_subtitle') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('lahan.index') }}" class="bg-primary hover:bg-primary/90 text-slate-900 px-8 py-4 rounded-xl text-lg font-bold shadow-lg transition-transform active:scale-95 text-center">
                            {{ __('web.landing.browse_stalls') }}
                        </a>
                        <a href="#pilih-lahan" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-8 py-4 rounded-xl text-lg font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-center">
                            {{ __('web.landing.view_map') }}
                        </a>
                    </div>
                </div>
                <div class="flex-1 w-full relative">
                    <div class="aspect-[4/3] rounded-3xl overflow-hidden shadow-2xl relative z-10">
                        <img class="w-full h-full object-cover" alt="{{ $landingHeroImageAlt }}" src="{{ $landingHeroImageUrl }}" />
                    </div>
                    <div class="absolute -bottom-6 -left-6 bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-xl z-20 hidden md:block border border-slate-100 dark:border-slate-700">
                        <div class="flex items-center gap-4">
                            <div class="size-12 rounded-full bg-primary/20 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">trending_up</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold">1.2k+ Active Vendors</p>
                                <p class="text-xs text-slate-500">Joining this month</p>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -top-10 -right-10 size-64 bg-primary/10 rounded-full blur-3xl -z-10"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter Bar — links to /lahan with preset filters -->
    <section class="relative z-30 -mt-8 px-4 sm:px-6 lg:px-8">
        <form action="{{ route('lahan.index') }}" method="GET" id="home-filter-form">
            <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 flex flex-wrap items-center gap-3">
                <!-- Region -->
                <div class="flex-1 min-w-[200px]">
                    <select name="region" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 hover:border-primary transition-colors group text-sm font-medium cursor-pointer appearance-none">
                        <option value="">{{ __('web.landing.filter_all_regions') }}</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region }}">{{ $region }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Size -->
                <div class="flex-1 min-w-[200px]">
                    <select name="size" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 hover:border-primary transition-colors group text-sm font-medium cursor-pointer appearance-none">
                        <option value="">{{ __('web.landing.filter_stall_size') }}</option>
                        <option value="small">&lt; 4 m²</option>
                        <option value="medium">4–9 m²</option>
                        <option value="large">&gt; 9 m²</option>
                    </select>
                </div>
                <!-- Price -->
                <div class="flex-1 min-w-[200px]">
                    <select name="price" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 hover:border-primary transition-colors group text-sm font-medium cursor-pointer appearance-none">
                        <option value="">{{ __('web.landing.filter_price_range') }}</option>
                        <option value="under_1m">&lt; Rp 1 JT/bln</option>
                        <option value="1m_to_2m">Rp 1–2 JT/bln</option>
                        <option value="over_2m">&gt; Rp 2 JT/bln</option>
                    </select>
                </div>
                <button type="submit" class="bg-slate-900 dark:bg-primary dark:text-slate-900 text-white px-8 py-3 rounded-xl font-bold flex items-center gap-2 hover:opacity-90 transition-opacity">
                    <span class="material-symbols-outlined text-xl">tune</span>
                    {{ __('web.landing.filter_apply') }}
                </button>
            </div>
        </form>
    </section>

    <!-- Listings Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-black tracking-tight mb-2">{{ __('web.landing.section_listings') }}</h2>
                    <p class="text-slate-500">
                        {{ __('web.landing.showing_stalls', ['shown' => $previewPlots->count(), 'total' => $previewPlotsTotal]) }}
                    </p>
                </div>
                <a class="hidden sm:flex items-center gap-2 text-sm font-bold text-primary group" href="{{ route('lahan.index') }}">
                    {{ __('web.landing.see_all') }}
                    <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>

            @if ($previewPlots->isEmpty())
                <div class="text-center py-16">
                    <span class="material-symbols-outlined text-6xl text-slate-300 mb-4">storefront</span>
                    <p class="text-slate-500 text-lg">{{ __('web.landing.empty_no_stalls') }}</p>
                    <a class="mt-4 inline-flex items-center gap-2 text-primary font-bold" href="{{ route('lahan.index') }}">{{ __('web.landing.empty_browse_all') }} →</a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($previewPlots as $plot)
                        <a href="{{ route('lahan.show', $plot) }}"
                           class="group bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all block">
                            <div class="relative aspect-video overflow-hidden">
                                @php
                                    $img = App\Services\PublicPlotListingQuery::class;
                                    $imgService = app($img);
                                    $cardImg = $imgService->primaryImageUrl($plot);
                                @endphp
                                <img class="plot-card-img" src="{{ $cardImg }}" alt="{{ $plot->name }}" />
                                <div class="absolute top-3 left-3 bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm px-3 py-1 rounded-lg">
                                    <span class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-widest">{{ __('web.landing.status_available') }}</span>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold mb-1 group-hover:text-primary transition-colors">{{ $plot->name }}</h3>
                                        <p class="text-slate-500 text-sm flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">location_on</span>
                                            {{ $plot->market->city ?? 'Unknown' }}
                                        </p>
                                    </div>
                                    <div class="bg-primary/10 text-primary p-2 rounded-lg">
                                        <span class="material-symbols-outlined">favorite</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-slate-400 text-lg">aspect_ratio</span>
                                        <span class="text-sm font-semibold">
                                            {{ $plot->length }}×{{ $plot->width }}m
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-slate-400 text-lg">bolt</span>
                                        <span class="text-sm font-semibold">{{ __('web.landing.status_ready') }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-slate-500 font-bold uppercase tracking-tight">{{ __('web.landing.price_label') }}</p>
                                        <p class="text-lg font-black text-slate-900 dark:text-slate-100">
                                            {{ $imgService->formatPrice($plot->base_price_monthly) }}
                                            <span class="text-sm font-normal text-slate-500">{{ __('web.landing.per_month') }}</span>
                                        </p>
                                    </div>
                                    <span class="bg-primary hover:bg-primary/90 text-slate-900 px-4 py-2.5 rounded-lg font-bold transition-all shadow-sm text-sm">
                                        {{ __('web.landing.rent_now') }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- How To Choose Teaser -->
    <section id="pilih-lahan" class="bg-slate-900 py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-12 relative z-10">
            <div class="flex-1">
                <h2 class="text-3xl lg:text-5xl font-black text-white mb-6">{{ __('web.landing.section_map_title') }}</h2>
                <p class="text-slate-400 text-lg mb-8">{{ __('web.landing.section_map_desc') }}</p>
                <div class="space-y-4 mb-8">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-1">check_circle</span>
                        <div>
                            <h4 class="text-white font-bold">{{ __('web.landing.map_feature_realtime') }}</h4>
                            <p class="text-slate-500 text-sm">{{ __('web.landing.map_feature_realtime_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-1">check_circle</span>
                        <div>
                            <h4 class="text-white font-bold">{{ __('web.landing.map_feature_heatmap') }}</h4>
                            <p class="text-slate-500 text-sm">{{ __('web.landing.map_feature_heatmap_desc') }}</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('lahan.index') }}" class="inline-flex bg-primary text-slate-900 px-8 py-4 rounded-xl font-bold items-center gap-2">
                    <span class="material-symbols-outlined">map</span>
                    {{ __('web.landing.open_map') }}
                </a>
            </div>
            <div class="flex-1 w-full">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse ($previewPlots->take(4) as $previewPlot)
                        <a
                            href="{{ route('lahan.show', $previewPlot) }}"
                            class="rounded-2xl bg-slate-800 border border-slate-700 p-4 shadow-xl hover:border-primary/70 transition-colors"
                        >
                            <p class="text-sm text-slate-400 mb-2">{{ $previewPlot->market->city ?? 'Unknown' }}</p>
                            <h4 class="text-white font-bold leading-tight mb-1">{{ $previewPlot->name }}</h4>
                            <p class="text-primary text-sm font-semibold">
                                {{ $listingService->formatPrice($previewPlot->base_price_monthly) }}
                                <span class="text-slate-400 font-medium">{{ __('web.landing.per_month') }}</span>
                            </p>
                        </a>
                    @empty
                        <div class="rounded-2xl bg-slate-800 border border-slate-700 p-5">
                            <p class="text-slate-300 font-semibold mb-1">{{ __('web.landing.empty_no_stalls') }}</p>
                            <p class="text-sm text-slate-500">{{ __('web.landing.empty_browse_all') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="absolute top-0 right-0 size-96 bg-primary/10 blur-[120px] rounded-full"></div>
    </section>
@endsection
