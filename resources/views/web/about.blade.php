@extends('web.layouts.main')

@section('content')
<!-- Main Content -->
<main class="pt-24 pb-20">
    <!-- Hero Section -->
    <section class="px-8 py-20 max-w-7xl mx-auto">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="min-w-0">
                <span class="text-xs font-black uppercase tracking-widest text-primary bg-primary/10 px-3 py-1 rounded-full inline-block mb-6">{{ __('web.about.badge_mission') }}</span>
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-black tracking-tight leading-[1.02] break-words text-slate-900 dark:text-slate-100 mb-8">{{ __('web.about.hero_title') }}</h1>
                <p class="text-xl text-slate-600 dark:text-slate-400 leading-relaxed max-w-lg">
                    {{ __('web.about.hero_desc') }}
                </p>
            </div>
            <div class="relative group">
                <div class="absolute -inset-4 bg-primary/20 rounded-3xl blur-2xl group-hover:bg-primary/30 transition-all duration-500"></div>
                <img alt="Modern Market Interior" class="relative rounded-3xl shadow-2xl object-cover h-[500px] w-full transform group-hover:scale-[1.02] transition-transform duration-500" data-alt="Modern indoor market with wooden stalls and bright lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA8qFlBXvKqmfV76EvtiiAAweI_2FdkbI15usg7lSEeOGZ_5IpRk6O9SJcbvgzdPRXzM2gUfuOdT7Je9TqBTTP2GGznp8h99vMVChxpeBSlT5s6oR71XA2yFANDldsIKX-2xw-tJSA4isd4IexBkptH757TjC8ckeYTy4-0giDLLtjh7UxYL8CB9yR1pK0IC5HllbZr3BkndZfFjuQYG7zd3JzfYee2L-lRhiIJjwJfegXtFXN49EvPmM3aurrdtzRyXb5olJ6-jHRB" />
            </div>
        </div>
    </section>
    <!-- Our Story: Editorial Layout -->
    <section class="bg-slate-100 dark:bg-slate-900/40 py-24">
        <div class="max-w-7xl mx-auto px-8">
            <div class="flex flex-col md:flex-row gap-16">
                <div class="md:w-1/3">
                    <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-slate-100 sticky top-32">{{ __('web.about.story_title') }}</h2>
                </div>
                <div class="md:w-2/3 space-y-8 text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                    <p>
                        {{ __('web.about.story_p1') }}
                    </p>
                    <p>
                        {{ __('web.about.story_p2') }}
                    </p>
                    <div class="grid grid-cols-2 gap-8 pt-8">
                        <div>
                            <div class="text-4xl font-black text-primary mb-2">500+</div>
                            <div class="text-sm font-bold uppercase tracking-widest opacity-60">{{ __('web.about.stat_stalls') }}</div>
                        </div>
                        <div>
                            <div class="text-4xl font-black text-primary mb-2">12</div>
                            <div class="text-sm font-bold uppercase tracking-widest opacity-60">{{ __('web.about.stat_cities') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Core Values: Bento Grid -->
    <section class="py-24 max-w-7xl mx-auto px-8">
        <h2 class="text-xs font-black uppercase tracking-widest text-primary mb-4 text-center">{{ __('web.about.dna_badge') }}</h2>
        <h3 class="text-4xl font-black text-center mb-16">{{ __('web.about.dna_title') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Transparency -->
            <div class="bg-white dark:bg-slate-800 p-10 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-primary text-3xl">visibility</span>
                </div>
                <h4 class="text-2xl font-bold mb-4">{{ __('web.about.value_transparency') }}</h4>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    {{ __('web.about.value_transparency_desc') }}
                </p>
            </div>
            <!-- Community -->
            <div class="bg-primary text-slate-900 p-10 rounded-3xl shadow-xl shadow-primary/10 hover:scale-[1.02] transition-all duration-300">
                <div class="w-14 h-14 bg-slate-900/10 rounded-2xl flex items-center justify-center mb-8">
                    <span class="material-symbols-outlined text-slate-900 text-3xl" style="font-variation-settings: 'FILL' 1;">groups</span>
                </div>
                <h4 class="text-2xl font-bold mb-4">{{ __('web.about.value_community') }}</h4>
                <p class="opacity-90 leading-relaxed">
                    {{ __('web.about.value_community_desc') }}
                </p>
            </div>
            <!-- Innovation -->
            <div class="bg-white dark:bg-slate-800 p-10 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-primary text-3xl">lightbulb</span>
                </div>
                <h4 class="text-2xl font-bold mb-4">{{ __('web.about.value_innovation') }}</h4>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    {{ __('web.about.value_innovation_desc') }}
                </p>
            </div>
        </div>
    </section>
    <!-- CTA Section -->
    <section class="py-24 px-8 max-w-7xl mx-auto">
        <div class="bg-primary/15 dark:bg-primary/10 rounded-[2rem] p-12 md:p-20 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-64 h-64 bg-primary/20 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
            <div class="relative z-10">
                <h2 class="text-4xl md:text-6xl font-black tracking-tight mb-8">{{ __('web.about.cta_title') }}</h2>
                <div class="flex flex-col md:flex-row justify-center gap-4">
                    <button class="bg-slate-900 text-white dark:bg-white dark:text-slate-900 px-10 py-5 rounded-2xl font-bold text-lg hover:shadow-2xl transition-all active:scale-95">{{ __('web.about.cta_explore') }}</button>
                    <button class="bg-transparent border-2 border-slate-300 text-slate-700 dark:border-slate-600 dark:text-slate-200 px-10 py-5 rounded-2xl font-bold text-lg hover:bg-slate-200/60 dark:hover:bg-slate-700/50 transition-all">{{ __('web.about.cta_partner') }}</button>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
