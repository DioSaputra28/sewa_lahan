<footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 pt-16 pb-8">
    @php
        $siteName = get_site_name();
        $siteLogoUrl = get_site_logo_url();
        $marketLocations = \App\Models\Plot::query()
            ->with('market:id,city,status')
            ->where('status', 'available')
            ->whereHas('market', fn ($query) => $query->where('status', 'active'))
            ->get()
            ->pluck('market.city')
            ->filter()
            ->unique()
            ->sort()
            ->take(4)
            ->values();
        $resourceLinks = [
            ['label' => __('web.footer.resource_home'), 'url' => route('home')],
            ['label' => __('web.footer.resource_about'), 'url' => route('about')],
            ['label' => __('web.footer.resource_lahan'), 'url' => route('lahan.index')],
            ['label' => __('web.footer.resource_contact'), 'url' => route('contact')],
        ];
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            <div>
                <div class="flex items-center gap-2 mb-6">
                    @if (filled($siteLogoUrl))
                        <img src="{{ $siteLogoUrl }}" alt="{{ $siteName }}" class="h-8 w-auto rounded-md object-contain">
                    @else
                        <div class="flex items-center justify-center size-8 bg-primary rounded-lg text-slate-900">
                            <span class="material-symbols-outlined font-bold">storefront</span>
                        </div>
                    @endif
                    <h2 class="text-xl font-extrabold tracking-tight">{{ $siteName }}</h2>
                </div>
                <p class="text-slate-500 text-sm mb-6">{{ __('web.footer.brand_description') }}</p>
            </div>
            <div>
                <h4 class="font-bold mb-6">{{ __('web.footer.market_locations') }}</h4>
                <ul class="space-y-4 text-sm text-slate-500">
                    @forelse ($marketLocations as $marketLocation)
                        <li>
                            <a
                                class="hover:text-primary transition-colors"
                                href="{{ route('lahan.index', ['region' => $marketLocation]) }}"
                            >
                                {{ $marketLocation }}
                            </a>
                        </li>
                    @empty
                        <li><a class="hover:text-primary transition-colors" href="{{ route('lahan.index') }}">{{ __('web.footer.resource_lahan') }}</a></li>
                    @endforelse
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-6">{{ __('web.footer.resources') }}</h4>
                <ul class="space-y-4 text-sm text-slate-500">
                    @foreach ($resourceLinks as $resourceLink)
                        <li>
                            <a class="hover:text-primary transition-colors" href="{{ $resourceLink['url'] }}">
                                {{ $resourceLink['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-6">{{ __('web.footer.stay_updated') }}</h4>
                <p class="text-sm text-slate-500 mb-4">{{ __('web.footer.email_subscribe') }}</p>
                <form class="flex gap-2">
                    <input class="flex-1 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary" placeholder="{{ __('web.footer.email_placeholder') }}" type="email"/>
                    <button class="bg-primary text-slate-900 p-2 rounded-lg" type="submit">
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </form>
            </div>
        </div>
        <div class="border-t border-slate-100 dark:border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex flex-col items-center gap-2 text-center md:items-start md:text-left">
                <p class="text-sm text-slate-400">{{ __('web.footer.copyright', ['year' => now()->year, 'siteName' => $siteName]) }}</p>
                <p class="text-sm text-slate-400">
                    {{ __('web.footer.developed_by') }}
                    <a
                        class="font-medium text-slate-500 underline decoration-slate-300 underline-offset-4 transition-colors hover:text-primary dark:text-slate-300 dark:decoration-slate-600"
                        href="https://www.bibakuteknologi.com/"
                        rel="noopener noreferrer"
                        target="_blank"
                    >
                        Bibaku Teknologi
                    </a>
                </p>
            </div>
            <div class="flex gap-6 text-sm text-slate-400">
                <a class="hover:text-primary transition-colors" href="#">{{ __('web.footer.privacy') }}</a>
                <a class="hover:text-primary transition-colors" href="#">{{ __('web.footer.terms') }}</a>
            </div>
        </div>
    </div>
</footer>
