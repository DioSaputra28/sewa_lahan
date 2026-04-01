<x-filament-panels::page>
    @php
        $plot = $this->plot;
        $imgService = app(\App\Services\PublicPlotListingQuery::class);
        $hasMonthlyPrice = $plot->base_price_monthly !== null;
        $hasYearlyPrice = $plot->base_price_yearly !== null;
        $monthlyPrice = $hasMonthlyPrice ? $imgService->formatPriceFull((int) $plot->base_price_monthly) : null;
        $yearlyPrice = $hasYearlyPrice ? $imgService->formatPriceFull((int) $plot->base_price_yearly) : null;
        $displayPrice = $monthlyPrice ?? $yearlyPrice ?? '—';
        $displayPeriod = $hasMonthlyPrice
            ? __('web.single.label_per_month')
            : ($hasYearlyPrice ? __('web.single.label_per_year') : '');
        $displayPriceSub = $hasMonthlyPrice && $yearlyPrice
            ? $yearlyPrice.' '.__('web.single.label_per_year')
            : ($hasYearlyPrice ? __('web.single.price_billed_annually') : '—');
        $dimension = $plot->length.' × '.$plot->width.' m';
        $areaSize = rtrim(rtrim(number_format((float) $plot->area_square_meters, 2, '.', ''), '0'), '.').' m²';
        $floorLabel = $plot->floor_level ?? '—';
        $typeLabel = $plot->type ?? '—';
        $marketName = $plot->market->name ?? '—';
        $marketCity = $plot->market->city ?? '—';
        $marketAddress = $plot->market->address ?? '—';
        $areaName = $plot->area?->name ?? '—';
        $mapsUrl = $plot->market->maps_url ?? '#';
        $amenities = [
            ['icon' => 'heroicon-o-bolt', 'key' => 'facility_electricity'],
            ['icon' => 'heroicon-o-wifi', 'key' => 'facility_wifi'],
            ['icon' => 'heroicon-o-truck', 'key' => 'facility_parking'],
            ['icon' => 'heroicon-o-archive-box-arrow-down', 'key' => 'facility_loading'],
            ['icon' => 'heroicon-o-shield-check', 'key' => 'facility_security'],
            ['icon' => 'heroicon-o-sparkles', 'key' => 'facility_cleaning'],
            ['icon' => 'heroicon-o-beaker', 'key' => 'facility_water'],
            ['icon' => 'heroicon-o-credit-card', 'key' => 'facility_payment'],
        ];
    @endphp

    <div class="space-y-8 scheme-light dark:scheme-dark">
        <section class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900 md:p-7">
            <div class="mb-5 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-gray-400">
                <span class="text-primary">{{ __('web.single.breadcrumb_lahan') }}</span>
                <span>/</span>
                <span>{{ $plot->name }}</span>
            </div>

            <div class="flex flex-col gap-8 xl:flex-row">
                <div class="flex-1">
                    <div class="relative mb-4 overflow-hidden rounded-3xl bg-slate-100 dark:bg-gray-950">
                        <img src="{{ $this->primaryImage }}" alt="{{ $plot->name }}" class="h-[24rem] w-full object-cover lg:h-[28rem]" />
                        <div class="absolute left-4 top-4 flex gap-2">
                            <span class="rounded-full bg-white/90 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.22em] text-slate-700">
                                {{ __('web.single.badge_available_now') }}
                            </span>
                            <span class="rounded-full border border-primary/30 bg-primary/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.22em] text-primary">
                                {{ __('web.single.badge_premium') }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-3">
                        @foreach ($this->allImages as $imageUrl)
                            <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white dark:border-white/10 dark:bg-gray-900">
                                <img class="aspect-square w-full object-cover" src="{{ $imageUrl }}" alt="{{ $plot->name }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                <aside class="w-full xl:w-[24rem]">
                    <div class="space-y-5">
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">{{ __('web.single.label_premium_stall') }}</p>
                            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white">{{ $plot->name }}</h1>
                            <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">{{ $marketName }} — {{ $areaName }}</p>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:bg-white/5 dark:text-gray-300">
                                    <x-filament::icon icon="heroicon-o-square-3-stack-3d" class="h-4 w-4" />{{ $areaSize }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:bg-white/5 dark:text-gray-300">
                                    <x-filament::icon icon="heroicon-o-building-office-2" class="h-4 w-4" />Floor {{ $floorLabel }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:bg-white/5 dark:text-gray-300">
                                    <x-filament::icon icon="heroicon-o-tag" class="h-4 w-4" />{{ $typeLabel }}
                                </span>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                            <div class="mb-1">
                                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $displayPrice }}</span>
                                <span class="text-sm text-slate-400">{{ $displayPeriod }}</span>
                            </div>
                            <p class="mb-4 text-xs text-slate-400">{{ $displayPriceSub }}</p>

                            <a
                                href="{{ \App\Filament\User\Resources\Bookings\BookingResource::getUrl('create', ['plot' => $plot->id]) }}"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-4 py-3 text-sm font-bold text-slate-900 transition hover:bg-primary/90"
                            >
                                {{ __('web.single.btn_rent_now') }}
                            </a>
                            <p class="mt-3 text-center text-xs text-slate-400">{{ __('web.single.disclaimer_no_fees') }}</p>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                            <h3 class="mb-4 text-sm font-bold uppercase tracking-[0.2em] text-slate-400">{{ __('web.single.location_title') }}</h3>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <p class="text-xs text-slate-400">{{ __('web.single.label_market') }}</p>
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $marketName }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">{{ __('web.single.label_area_zone') }}</p>
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $areaName }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">{{ __('web.single.label_city') }}</p>
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $marketCity }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">{{ __('web.single.label_address') }}</p>
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $marketAddress }}</p>
                                </div>
                                @if ($mapsUrl && $mapsUrl !== '#')
                                    <div>
                                        <p class="text-xs text-slate-400">{{ __('web.single.label_maps') }}</p>
                                        <a class="font-semibold text-primary hover:underline" href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer">
                                            {{ __('web.single.link_google_maps') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <h2 class="mb-5 text-lg font-black text-slate-900 dark:text-white">{{ __('web.single.specs_title') }}</h2>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs uppercase tracking-wide text-slate-400">{{ __('web.single.label_dimension') }}</p>
                    <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ $dimension }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs uppercase tracking-wide text-slate-400">{{ __('web.single.label_total_area') }}</p>
                    <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ $areaSize }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs uppercase tracking-wide text-slate-400">{{ __('web.single.label_floor') }}</p>
                    <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ $floorLabel }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs uppercase tracking-wide text-slate-400">{{ __('web.single.label_type') }}</p>
                    <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ $typeLabel }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs uppercase tracking-wide text-slate-400">{{ __('web.single.label_status') }}</p>
                    <p class="mt-1 text-sm font-bold text-primary">{{ __('web.single.status_available') }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-center dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs uppercase tracking-wide text-slate-400">{{ __('web.single.label_position') }}</p>
                    <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ $plot->location_note ?? $plot->name }}</p>
                </div>
            </div>
        </section>

        <section class="grid gap-8 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <h2 class="mb-4 text-2xl font-black text-slate-900 dark:text-white">{{ __('web.single.about_title') }}</h2>
                <p class="text-sm leading-7 text-slate-600 dark:text-gray-300">
                    {{ $plot->description ?: __('web.single.about_empty') }}
                </p>
            </div>
            <div>
                <h2 class="mb-4 text-lg font-black text-slate-900 dark:text-white">{{ __('web.single.facilities_title') }}</h2>
                <ul class="space-y-3">
                    @foreach ($amenities as $amenity)
                        <li class="flex items-center gap-3 text-sm">
                            <span class="rounded-lg bg-primary/10 p-1.5 text-primary">
                                <x-filament::icon :icon="$amenity['icon']" class="h-4 w-4" />
                            </span>
                            <span class="font-medium text-slate-700 dark:text-gray-200">{{ __('web.single.'.$amenity['key']) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white">{{ __('web.single.related_title') }}</h2>
                    <p class="text-sm text-slate-500 dark:text-gray-400">{{ __('web.single.related_subtitle', ['market' => $marketName]) }}</p>
                </div>
                <a class="text-sm font-bold text-primary hover:underline" href="{{ \App\Filament\User\Resources\Bookings\BookingResource::getUrl('browse') }}">
                    {{ __('web.single.related_view_all') }}
                </a>
            </div>

            @if ($this->relatedPlots->isEmpty())
                <p class="py-6 text-center text-sm text-slate-500 dark:text-gray-400">{{ __('web.single.related_empty') }}</p>
            @else
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($this->relatedPlots as $relatedPlot)
                        @php
                            $relatedImage = $imgService->primaryImageUrl($relatedPlot);
                            $relatedRawPrice = $relatedPlot->base_price_monthly ?? $relatedPlot->base_price_yearly;
                            $relatedPrice = $relatedRawPrice !== null ? $imgService->formatPrice((int) $relatedRawPrice) : '—';
                            $relatedPricePeriod = $relatedPlot->base_price_monthly !== null
                                ? __('web.single.label_per_month')
                                : ($relatedPlot->base_price_yearly !== null ? __('web.single.label_per_year') : '—');
                            $relatedSize = $relatedPlot->length.' × '.$relatedPlot->width.' m';
                        @endphp
                        <a
                            href="{{ \App\Filament\User\Resources\Bookings\BookingResource::getUrl('plot', ['plot' => $relatedPlot]) }}"
                            class="group block overflow-hidden rounded-2xl border border-slate-200 bg-white transition hover:-translate-y-1 hover:shadow-lg dark:border-white/10 dark:bg-gray-900"
                        >
                            <img class="h-40 w-full object-cover" src="{{ $relatedImage }}" alt="{{ $relatedPlot->name }}">
                            <div class="p-4">
                                <h3 class="text-sm font-bold text-slate-900 transition group-hover:text-primary dark:text-white">{{ $relatedPlot->name }}</h3>
                                <p class="mb-3 mt-1 text-xs text-slate-400">{{ $relatedPlot->market->city ?? '' }}</p>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[11px] text-slate-400">{{ __('web.single.label_size') }}</p>
                                        <p class="text-sm font-bold text-slate-700 dark:text-gray-200">{{ $relatedSize }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-black text-primary">{{ $relatedPrice }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $relatedPricePeriod }}</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-filament-panels::page>
