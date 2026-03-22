@extends('web.layouts.main')

@push('head')
    <style>
        .filter-btn-active {
            background-color: rgb(var(--color-primary) / 0.1) !important;
            color: var(--color-primary) !important;
        }
        .listing-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .listing-card-img:hover {
            transform: scale(1.1);
        }
    </style>
@endpush

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumbs & Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-black tracking-tight mb-2">Find Your Trading Space</h1>
        <p class="text-slate-600 dark:text-slate-400">Discover premium stall locations across major markets.</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filter -->
        <aside class="w-full lg:w-72 flex-shrink-0">
            <form action="{{ route('lahan.index') }}" method="GET" id="filter-form">
                <!-- Persist sort in filters -->
                <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'newest' }}" />

                <div class="sticky top-24 space-y-6">
                    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">tune</span>
                            Filters
                        </h2>

                        <!-- Search (by market name) -->
                        {{--
                        <div class="mb-6">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Search</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
                                <input class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary" placeholder="Market name..." type="text" name="q" value="{{ $filters['q'] ?? '' }}" />
                            </div>
                        </div>
                        --}}

                        <!-- Region -->
                        <div class="mb-6">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Region</label>
                            <div class="space-y-2">
                                <button type="submit" name="region" value="" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ empty($filters['region']) ? 'bg-primary/10 text-primary' : 'hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                    <span class="material-symbols-outlined {{ empty($filters['region']) ? 'text-primary' : '' }}">location_on</span>
                                    All Regions
                                </button>
                                @foreach ($regions as $region)
                                    <button type="submit" name="region" value="{{ $region }}"
                                            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filters['region'] === $region ? 'bg-primary/10 text-primary' : 'hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                        <span class="material-symbols-outlined {{ $filters['region'] === $region ? 'text-primary' : '' }}">location_on</span>
                                        {{ $region }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Size -->
                        <div class="mb-6">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Stall Size</label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($sizeBuckets as $key => $bucket)
                                    <button type="submit" name="size" value="{{ $key }}"
                                            class="px-3 py-2 rounded-lg text-xs font-medium transition-colors border {{ $filters['size'] === $key ? 'border-primary bg-primary/10 text-primary' : 'border-slate-200 dark:border-slate-700 hover:border-primary' }}">
                                        {{ $bucket['label'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="mb-6">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Price / Month</label>
                            <div class="space-y-2">
                                @foreach ($priceBuckets as $key => $bucket)
                                    <button type="submit" name="price" value="{{ $key }}"
                                            class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ $filters['price'] === $key ? 'bg-primary/10 text-primary' : 'hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                        <span class="material-symbols-outlined text-sm">payments</span>
                                        {{ $bucket['label'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Clear Filters -->
                        @if ($filters['region'] || $filters['size'] || $filters['price'])
                            <a href="{{ route('lahan.index', ['sort' => $filters['sort'] ?? 'newest']) }}"
                               class="w-full py-2.5 border border-red-200 dark:border-red-800 text-red-500 font-bold rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex items-center justify-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-sm">clear</span>
                                Clear Filters
                            </a>
                        @endif
                    </div>

                    <!-- Promotions / Help -->
                    <div class="bg-slate-900 rounded-xl p-6 text-white overflow-hidden relative">
                        <div class="relative z-10">
                            <h3 class="font-bold mb-2">Need Help?</h3>
                            <p class="text-sm text-slate-400 mb-4">Our support team is available 24/7 for consultation.</p>
                            <a class="inline-flex items-center text-primary text-sm font-bold hover:underline" href="{{ route('contact') }}">
                                Contact Support
                                <span class="material-symbols-outlined text-sm ml-1">arrow_forward</span>
                            </a>
                        </div>
                        <div class="absolute -right-4 -bottom-4 opacity-20">
                            <span class="material-symbols-outlined text-8xl">support_agent</span>
                        </div>
                    </div>
                </div>
            </form>
        </aside>

        <!-- Main Content Grid -->
        <div class="flex-1">
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-slate-500">
                    Showing <span class="text-slate-900 dark:text-slate-100 font-bold">{{ $plots->count() }}</span>
                    of <span class="font-bold">{{ $plots->total() }}</span> available stalls
                </p>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-500">Sort by:</span>
                    <form id="sort-form" method="GET" action="{{ route('lahan.index') }}" class="relative">
                        @foreach (array_filter($filters) as $key => $val)
                            @if ($key !== 'sort')
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}" />
                            @endif
                        @endforeach
                        <select name="sort" onchange="document.getElementById('sort-form').submit()"
                                class="text-sm border-none bg-transparent focus:ring-0 font-medium cursor-pointer appearance-none pr-6">
                            <option value="newest" {{ ($filters['sort'] ?? 'newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="price_asc" {{ ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="size_desc" {{ ($filters['sort'] ?? '') === 'size_desc' ? 'selected' : '' }}>Size: Largest</option>
                        </select>
                    </form>
                </div>
            </div>

            @if ($plots->isEmpty())
                <div class="text-center py-16">
                    <span class="material-symbols-outlined text-6xl text-slate-300 mb-4">storefront</span>
                    <p class="text-slate-500 text-lg mb-4">No stalls match your filters.</p>
                    <a href="{{ route('lahan.index') }}" class="text-primary font-bold hover:underline">Clear filters</a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($plots as $plot)
                        @php
                            $imgService = app(\App\Services\PublicPlotListingQuery::class);
                            $cardImg = $imgService->primaryImageUrl($plot);
                            $formattedPrice = $imgService->formatPrice($plot->base_price_monthly);
                            $formattedSize = $plot->length . ' × ' . $plot->width . ' m²';
                        @endphp
                        <a href="{{ route('lahan.show', $plot) }}"
                           class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden group hover:shadow-lg transition-shadow block">
                            <div class="h-48 relative overflow-hidden">
                                <img class="listing-card-img" src="{{ $cardImg }}" alt="{{ $plot->name }}" />
                                <div class="absolute top-3 left-3 bg-white/90 dark:bg-slate-900/90 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest">
                                    {{ ucfirst($plot->status) }}
                                </div>
                            </div>
                            <div class="p-5">
                                <h3 class="font-bold text-lg mb-1 group-hover:text-primary transition-colors">{{ $plot->name }}</h3>
                                <p class="text-slate-500 text-sm flex items-center gap-1 mb-4">
                                    <span class="material-symbols-outlined text-sm text-primary">location_on</span>
                                    {{ $plot->market->city ?? 'Unknown' }}
                                </p>
                                <div class="flex items-center justify-between py-3 border-y border-slate-100 dark:border-slate-800 mb-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Size</span>
                                        <span class="text-sm font-semibold">{{ $formattedSize }}</span>
                                    </div>
                                    <div class="flex flex-col text-right">
                                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Type</span>
                                        <span class="text-sm font-semibold">{{ $plot->type ?? '—' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xl font-black text-primary">{{ $formattedPrice }}</span>
                                        <span class="text-xs text-slate-400">/mo</span>
                                    </div>
                                    <span class="px-4 py-2 bg-primary/20 text-slate-900 font-bold text-xs rounded-lg hover:bg-primary transition-colors">Details</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($plots->hasPages())
                    <div class="mt-12 flex items-center justify-center gap-2">
                        @if ($plots->onFirstPage())
                            <button class="size-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-800 text-slate-400 cursor-not-allowed" disabled>
                                <span class="material-symbols-outlined">chevron_left</span>
                            </button>
                        @else
                            <a href="{{ $plots->previousPageUrl() }}" class="size-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-800 hover:border-primary transition-colors">
                                <span class="material-symbols-outlined">chevron_left</span>
                            </a>
                        @endif

                        @foreach ($plots->getUrlRange(1, $plots->lastPage()) as $page => $url)
                            @if ($page == $plots->currentPage())
                                <button class="size-10 flex items-center justify-center rounded-lg bg-primary text-slate-900 font-bold">{{ $page }}</button>
                            @elseif ($page >= $plots->currentPage() - 1 && $page <= $plots->currentPage() + 1)
                                <a href="{{ $url }}" class="size-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-800 hover:border-primary transition-colors font-bold">{{ $page }}</a>
                            @elseif ($page == 1 || $page == $plots->lastPage())
                                <a href="{{ $url }}" class="size-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-800 hover:border-primary transition-colors font-bold">{{ $page }}</a>
                            @elseif ($page == $plots->currentPage() - 2 || $page == $plots->currentPage() + 2)
                                <span class="px-2">...</span>
                            @endif
                        @endforeach

                        @if ($plots->hasMorePages())
                            <a href="{{ $plots->nextPageUrl() }}" class="size-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-800 hover:border-primary transition-colors">
                                <span class="material-symbols-outlined">chevron_right</span>
                            </a>
                        @else
                            <button class="size-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-800 text-slate-400 cursor-not-allowed" disabled>
                                <span class="material-symbols-outlined">chevron_right</span>
                            </button>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>
</main>
@endsection
