<header class="sticky top-0 z-50 w-full border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md">
    @php
        $siteName = get_site_name();
        $siteLogoUrl = get_site_logo_url();
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-8">
                <a class="flex items-center gap-2" href="{{ route('home') }}">
                    @if (filled($siteLogoUrl))
                        <img src="{{ $siteLogoUrl }}" alt="{{ $siteName }}" class="h-8 w-auto rounded-md object-contain">
                    @else
                        <div class="flex items-center justify-center size-8 bg-primary rounded-lg text-slate-900">
                            <span class="material-symbols-outlined font-bold">storefront</span>
                        </div>
                    @endif
                    <h2 class="text-xl font-extrabold tracking-tight">{{ $siteName }}</h2>
                </a>
                <nav class="hidden md:flex items-center gap-6">
                    <a
                        class="text-sm font-semibold transition-colors {{ request()->routeIs('home') ? 'text-primary' : 'hover:text-primary' }}"
                        href="{{ route('home') }}"
                    >
                        {{ __('web.nav.home') }}
                    </a>
                    <a
                        class="text-sm font-semibold transition-colors {{ request()->routeIs('about') ? 'text-primary' : 'hover:text-primary' }}"
                        href="{{ route('about') }}"
                    >
                        {{ __('web.nav.about') }}
                    </a>
                    <a
                        class="text-sm font-semibold transition-colors {{ request()->routeIs('lahan.*') ? 'text-primary' : 'hover:text-primary' }}"
                        href="{{ route('lahan.index') }}"
                    >
                        {{ __('web.nav.lahan') }}
                    </a>
                    <a
                        class="text-sm font-semibold transition-colors {{ request()->routeIs('contact') ? 'text-primary' : 'hover:text-primary' }}"
                        href="{{ route('contact') }}"
                    >
                        {{ __('web.nav.contact') }}
                    </a>
                </nav>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden lg:flex items-center bg-slate-100 dark:bg-slate-800 rounded-lg px-3 py-1.5 border border-transparent focus-within:border-primary">
                    <span class="material-symbols-outlined text-slate-400 text-lg">search</span>
                    <input class="bg-transparent border-none focus:ring-0 text-sm w-40 placeholder:text-slate-500" placeholder="{{ __('web.nav.search_placeholder') }}"/>
                </div>
                <div class="hidden md:flex items-center gap-1">
                    <form action="{{ route('locale.switch', ['locale' => 'id']) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-2 py-1 text-xs font-bold rounded transition-colors {{ app()->getLocale() === 'id' ? 'bg-primary text-slate-900' : 'text-slate-400 hover:text-slate-600' }}">
                            ID
                        </button>
                    </form>
                    <span class="text-slate-300">|</span>
                    <form action="{{ route('locale.switch', ['locale' => 'en']) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-2 py-1 text-xs font-bold rounded transition-colors {{ app()->getLocale() === 'en' ? 'bg-primary text-slate-900' : 'text-slate-400 hover:text-slate-600' }}">
                            EN
                        </button>
                    </form>
                </div>
                <a href="{{ route('filament.user.auth.register') }}" class="hidden md:inline-flex bg-primary hover:bg-primary/90 text-slate-900 px-5 py-2 rounded-lg text-sm font-bold transition-all shadow-sm">
                    {{ __('web.nav.register') }}
                </a>
                <details class="relative md:hidden">
                    <summary class="list-none cursor-pointer inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-700 p-2 text-slate-700 dark:text-slate-100">
                        <span class="material-symbols-outlined">menu</span>
                    </summary>
                    <div class="absolute right-0 mt-3 w-72 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-xl p-4">
                        <nav class="flex flex-col gap-2">
                            <a class="rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('home') ? 'bg-primary/15 text-primary' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}" href="{{ route('home') }}">
                                {{ __('web.nav.home') }}
                            </a>
                            <a class="rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('about') ? 'bg-primary/15 text-primary' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}" href="{{ route('about') }}">
                                {{ __('web.nav.about') }}
                            </a>
                            <a class="rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('lahan.*') ? 'bg-primary/15 text-primary' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}" href="{{ route('lahan.index') }}">
                                {{ __('web.nav.lahan') }}
                            </a>
                            <a class="rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('contact') ? 'bg-primary/15 text-primary' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}" href="{{ route('contact') }}">
                                {{ __('web.nav.contact') }}
                            </a>
                        </nav>
                        <div class="my-3 h-px bg-slate-200 dark:bg-slate-700"></div>
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-1">
                                <form action="{{ route('locale.switch', ['locale' => 'id']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 text-xs font-bold rounded transition-colors {{ app()->getLocale() === 'id' ? 'bg-primary text-slate-900' : 'text-slate-400 hover:text-slate-600' }}">
                                        ID
                                    </button>
                                </form>
                                <span class="text-slate-300">|</span>
                                <form action="{{ route('locale.switch', ['locale' => 'en']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 text-xs font-bold rounded transition-colors {{ app()->getLocale() === 'en' ? 'bg-primary text-slate-900' : 'text-slate-400 hover:text-slate-600' }}">
                                        EN
                                    </button>
                                </form>
                            </div>
                            <a href="{{ route('filament.user.auth.register') }}" class="inline-flex bg-primary hover:bg-primary/90 text-slate-900 px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-sm">
                                {{ __('web.nav.register') }}
                            </a>
                        </div>
                    </div>
                </details>
            </div>
        </div>
    </div>
</header>
