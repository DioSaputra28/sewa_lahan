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
    <!-- Modern Split Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="flex-1 text-left lg:pr-8">
                    <div class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-sm font-semibold leading-6 text-primary ring-1 ring-inset ring-primary/20 mb-6">
                        <span>Now available in 12 major cities</span>
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-black text-slate-900 dark:text-slate-50 leading-[1.1] tracking-tight mb-6">
                        Secure Your <span class="text-primary">Prime Market</span> Stall Today
                    </h1>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 max-w-xl">
                        Empower your business with high-traffic locations in the heart of local communities. Affordable, flexible, and ready-to-use market spaces.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('lahan.index') }}" class="bg-primary hover:bg-primary/90 text-slate-900 px-8 py-4 rounded-xl text-lg font-bold shadow-lg transition-transform active:scale-95 text-center">
                            Browse Stalls
                        </a>
                        <button class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-8 py-4 rounded-xl text-lg font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            View Map
                        </button>
                    </div>
                </div>
                <div class="flex-1 w-full relative">
                    <div class="aspect-[4/3] rounded-3xl overflow-hidden shadow-2xl relative z-10">
                        <img class="w-full h-full object-cover" data-alt="Modern indoor market with organized wooden stalls" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCBD-mRgP8BT6LBqFVhYhVqlU4kbrooSw7otxMVS6lUdXJ0kQt3N3qbjF2bWlhNmmvf8p1sxF82qWDU5Njsz4FZgnQvLiZmJ0ceDtCZKYrkKWV8KZbEynB4U-YtfJ--x-btax7JVLliqIzk7MnYQhh247A_PsE1o_3wYQMrWFws-XGCjnH6mN4LZEhJDR8VEKVRO9YyuBOUZaHApbAOqkbXBsZdyct0i8lPts8xD24NjBGeg2N6Q4s0WATNQqfCLxJdq0aqmGrmiIWd" />
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
                        <option value="">All Regions</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region }}">{{ $region }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Size -->
                <div class="flex-1 min-w-[200px]">
                    <select name="size" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 hover:border-primary transition-colors group text-sm font-medium cursor-pointer appearance-none">
                        <option value="">Stall Size</option>
                        <option value="small">&lt; 4 m²</option>
                        <option value="medium">4–9 m²</option>
                        <option value="large">&gt; 9 m²</option>
                    </select>
                </div>
                <!-- Price -->
                <div class="flex-1 min-w-[200px]">
                    <select name="price" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 hover:border-primary transition-colors group text-sm font-medium cursor-pointer appearance-none">
                        <option value="">Price Range</option>
                        <option value="under_1m">&lt; Rp 1 juta/mo</option>
                        <option value="1m_to_2m">Rp 1–2 juta/mo</option>
                        <option value="over_2m">&gt; Rp 2 juta/mo</option>
                    </select>
                </div>
                <button type="submit" class="bg-slate-900 dark:bg-primary dark:text-slate-900 text-white px-8 py-3 rounded-xl font-bold flex items-center gap-2 hover:opacity-90 transition-opacity">
                    <span class="material-symbols-outlined text-xl">tune</span>
                    Apply Filters
                </button>
            </div>
        </form>
    </section>

    <!-- Listings Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-black tracking-tight mb-2">Available Stall Rentals</h2>
                    <p class="text-slate-500">
                        Showing {{ $previewPlots->count() }} high-potential location{{ $previewPlots->count() !== 1 ? 's' : '' }} available for rent
                    </p>
                </div>
                <a class="hidden sm:flex items-center gap-2 text-sm font-bold text-primary group" href="{{ route('lahan.index') }}">
                    See All
                    <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>

            @if ($previewPlots->isEmpty())
                <div class="text-center py-16">
                    <span class="material-symbols-outlined text-6xl text-slate-300 mb-4">storefront</span>
                    <p class="text-slate-500 text-lg">No available stalls at the moment. Check back soon!</p>
                    <a class="mt-4 inline-flex items-center gap-2 text-primary font-bold" href="{{ route('lahan.index') }}">Browse all stalls →</a>
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
                                    <span class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-widest">Available</span>
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
                                        <span class="text-sm font-semibold">Ready</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-slate-500 font-bold uppercase tracking-tight">Price</p>
                                        <p class="text-lg font-black text-slate-900 dark:text-slate-100">
                                            {{ $imgService->formatPrice($plot->base_price_monthly) }}
                                            <span class="text-sm font-normal text-slate-500">/mo</span>
                                        </p>
                                    </div>
                                    <span class="bg-primary hover:bg-primary/90 text-slate-900 px-4 py-2.5 rounded-lg font-bold transition-all shadow-sm text-sm">
                                        Rent Now
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Market Map Teaser -->
    <section class="bg-slate-900 py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-12 relative z-10">
            <div class="flex-1">
                <h2 class="text-3xl lg:text-5xl font-black text-white mb-6">Explore the Market Interactive Map</h2>
                <p class="text-slate-400 text-lg mb-8">Pinpoint the exact location of your future stall. Check foot traffic heatmaps, nearby amenities, and available neighboring vendors.</p>
                <div class="space-y-4 mb-8">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-1">check_circle</span>
                        <div>
                            <h4 class="text-white font-bold">Real-time Availability</h4>
                            <p class="text-slate-500 text-sm">Always see which stalls are open for rent.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary mt-1">check_circle</span>
                        <div>
                            <h4 class="text-white font-bold">Heatmap Analytics</h4>
                            <p class="text-slate-500 text-sm">Understand visitor patterns before you commit.</p>
                        </div>
                    </div>
                </div>
                <button class="bg-primary text-slate-900 px-8 py-4 rounded-xl font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined">map</span>
                    Open Interactive Map
                </button>
            </div>
            <div class="flex-1 w-full">
                <div class="aspect-square bg-slate-800 rounded-3xl overflow-hidden border-4 border-slate-700 shadow-2xl relative">
                    <div class="w-full h-full opacity-60">
                        <img class="w-full h-full object-cover" data-alt="A detailed digital map of a city market area" data-location="Jakarta" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBz7ziiL7OVOi-ahVXkEKF3VkgT7TicxB-h-vJOoFWCJSkmQPxv1xE7anmZj8eHwqfawM6u2Epw1vjH6XGatnPs04XkFFPfexj0NyBXwH1LVTjR5bvtNlOnCKDVD0SV8ubUTnixskL2W-iGQz7vRR8O2r2tv5sSy2UCE8Wzn9Hi69ma7qv20hpSkUt9y93_3XKNr6nQhsetDAFX2ih5MwWZoaZuUIfT5z6IJ8psz3xqgIQAh567-JsPYemIa8TQ_2RzHixFmrenjTKQ" />
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="size-16 bg-primary rounded-full flex items-center justify-center shadow-lg shadow-primary/20 animate-pulse">
                            <span class="material-symbols-outlined text-slate-900 text-3xl font-bold">location_on</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute top-0 right-0 size-96 bg-primary/10 blur-[120px] rounded-full"></div>
    </section>
@endsection
