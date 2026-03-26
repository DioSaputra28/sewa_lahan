@extends('web.layouts.main')

@section('content')
@php
    $officeEmail = get_office_email();
    $officeWhatsapp = get_office_whatsapp();
    $officeWhatsappLink = get_office_whatsapp_link();
    $officePhone = get_office_phone();
    $officeLocation = get_office_location();
    $officeMapEmbedSrc = get_office_map_embed_src();
    $hasOfficeContactInfo = has_contact_info();
    $socialLinks = collect([
        ['url' => get_youtube_url(), 'label' => 'YouTube', 'icon' => 'https://cdn.simpleicons.org/youtube'],
        ['url' => get_instagram_url(), 'label' => 'Instagram', 'icon' => 'https://cdn.simpleicons.org/instagram'],
        ['url' => get_tiktok_url(), 'label' => 'TikTok', 'icon' => 'https://cdn.simpleicons.org/tiktok'],
        ['url' => get_facebook_url(), 'label' => 'Facebook', 'icon' => 'https://cdn.simpleicons.org/facebook'],
        ['url' => get_twitter_x_url(), 'label' => 'X', 'icon' => 'https://cdn.simpleicons.org/x'],
        ['url' => get_threads_url(), 'label' => 'Threads', 'icon' => 'https://cdn.simpleicons.org/threads'],
    ])->filter(fn (array $socialLink): bool => filled($socialLink['url']))->values();
@endphp

<main class="pt-32 pb-20 px-6 max-w-7xl mx-auto">
    <!-- Hero Section -->
    <section class="mb-16">
        <div class="relative overflow-hidden rounded-3xl bg-slate-900 p-12 md:p-20 text-white shadow-2xl">
            <div class="absolute inset-0 opacity-20" data-alt="Abstract green digital pattern background">
                <img alt="Abstract Pattern" class="w-full h-full object-cover" data-alt="Abstract emerald and black geometric flowing shapes" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC9wfWz-zKosKhH5aEj0ZxVrU6wtnd_tR3lEueVyyagflUkI4w6GK1_h0XmaiuMwl5CwqEk90TN6nXChpo5zvNjgWai1n-IAVZOFymOgv8PjdWqud7Wx85-IxmmaMWHzzLWo9tA5TBB9bP9f687yyE6Y6VXTd7lElsMZvXcZwNdfCds8nv0YG6GKFwrRG7U5h5qKAfSAeTD7M8STB1rZt-fo5mos1nGk253wFMXbivJGBy0oFWLZ-36QNiRbBHIfzws7oxmvetEfO6Y" />
            </div>
            <div class="relative z-10 max-w-2xl">
                <span class="text-primary text-xs font-black uppercase tracking-widest mb-4 block">{{ __('web.contact.badge') }}</span>
                <h1 class="text-5xl md:text-7xl font-black tracking-tight mb-6">
                    {{ __('web.contact.hero_title') }} <span class="text-primary">{{ __('web.contact.hero_title_highlight') }}</span> {{ __('web.contact.hero_title_suffix') }}
                </h1>
                <p class="text-lg text-slate-300 font-medium">{{ __('web.contact.hero_desc') }}</p>
            </div>
        </div>
    </section>
        <!-- Bento Contact Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Contact Form Card (Large Bento Piece) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-8 md:p-12 shadow-sm border border-slate-200 dark:border-slate-700">
            <h2 class="text-3xl font-black tracking-tight mb-8">{{ __('web.contact.form_title') }}</h2>
            <form class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('web.contact.label_name') }}</label>
                        <input class="w-full bg-slate-100 dark:bg-slate-800/80 border-none rounded-xl p-4 focus:ring-2 focus:ring-primary text-slate-900 dark:text-slate-100 placeholder:text-slate-400" placeholder="{{ __('web.contact.placeholder_name') }}" type="text" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('web.contact.label_email') }}</label>
                        <input class="w-full bg-slate-100 dark:bg-slate-800/80 border-none rounded-xl p-4 focus:ring-2 focus:ring-primary text-slate-900 dark:text-slate-100 placeholder:text-slate-400" placeholder="{{ __('web.contact.placeholder_email') }}" type="email" />
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('web.contact.label_subject') }}</label>
                    <select class="w-full bg-slate-100 dark:bg-slate-800/80 border-none rounded-xl p-4 focus:ring-2 focus:ring-primary text-slate-900 dark:text-slate-100">
                        <option>{{ __('web.contact.subject_general') }}</option>
                        <option>{{ __('web.contact.subject_partnership') }}</option>
                        <option>{{ __('web.contact.subject_support') }}</option>
                        <option>{{ __('web.contact.subject_sustainability') }}</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('web.contact.label_message') }}</label>
                    <textarea class="w-full bg-slate-100 dark:bg-slate-800/80 border-none rounded-xl p-4 focus:ring-2 focus:ring-primary text-slate-900 dark:text-slate-100 placeholder:text-slate-400" placeholder="{{ __('web.contact.placeholder_message') }}" rows="5"></textarea>
                </div>
                <button class="w-full md:w-auto bg-primary text-slate-900 px-10 py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-primary/20 transition-all active:scale-95" type="submit">
                    {{ __('web.contact.submit') }}
                </button>
            </form>
        </div>
        <!-- Info Sidebar (Bento Column) -->
        <div class="lg:col-span-5 space-y-8">
            @if ($hasOfficeContactInfo)
                <!-- Contact Info Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-200 dark:border-slate-700">
                    <h3 class="text-xl font-black mb-6 uppercase tracking-tight">{{ __('web.contact.hq_title') }}</h3>
                    <div class="space-y-6">
                        @if (filled($officeLocation))
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                                    <span class="material-symbols-outlined" data-icon="location_on">location_on</span>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">{{ __('web.contact.office_label') }}</p>
                                    <p class="text-slate-500 dark:text-slate-400 leading-relaxed">{!! nl2br(e($officeLocation)) !!}</p>
                                </div>
                            </div>
                        @endif

                        @if (filled($officePhone))
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                                    <span class="material-symbols-outlined" data-icon="call">call</span>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">{{ __('web.contact.phone_label') }}</p>
                                    <p class="text-slate-500 dark:text-slate-400">{{ $officePhone }}</p>
                                </div>
                            </div>
                        @endif

                        @if (filled($officeWhatsapp))
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                                    <img
                                        src="https://cdn.simpleicons.org/whatsapp"
                                        alt="WhatsApp"
                                        class="h-6 w-6 object-contain"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">WhatsApp</p>
                                    @if (filled($officeWhatsappLink))
                                        <a
                                            href="{{ $officeWhatsappLink }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            aria-label="WhatsApp"
                                            class="text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                                        >
                                            {{ $officeWhatsapp }}
                                        </a>
                                    @else
                                        <p class="text-slate-500 dark:text-slate-400">{{ $officeWhatsapp }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if (filled($officeEmail))
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                                    <span class="material-symbols-outlined" data-icon="mail">mail</span>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">{{ __('web.contact.email_label') }}</p>
                                    <p class="text-slate-500 dark:text-slate-400">{{ $officeEmail }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if (filled($officeMapEmbedSrc))
                <!-- Map Section -->
                <div class="relative h-[300px] rounded-3xl overflow-hidden shadow-xl group border-4 border-white">
                    <iframe
                        src="{{ $officeMapEmbedSrc }}"
                        class="h-full w-full border-0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen
                        title="Office map"
                    ></iframe>
                    <div class="absolute inset-0 bg-primary/10 pointer-events-none"></div>
                    <div class="absolute bottom-6 left-6 right-6 bg-white/90 backdrop-blur-md p-4 rounded-2xl shadow-lg border border-white/50">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-primary animate-pulse"></div>
                            <span class="text-sm font-bold text-slate-900">{{ __('web.contact.map_label') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($socialLinks->isNotEmpty())
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-200 dark:border-slate-700" data-testid="contact-social-links">
                    <h3 class="text-xl font-black mb-6 uppercase tracking-tight">{{ __('web.contact.social_title') }}</h3>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($socialLinks as $socialLink)
                            <a
                                href="{{ $socialLink['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="{{ $socialLink['label'] }}"
                                class="group inline-flex items-center gap-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 px-4 py-3 hover:border-primary hover:bg-primary/5 transition-colors"
                            >
                                <img
                                    src="{{ $socialLink['icon'] }}"
                                    alt="{{ $socialLink['label'] }}"
                                    class="h-5 w-5 object-contain"
                                    loading="lazy"
                                    decoding="async"
                                >
                                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 group-hover:text-slate-900 dark:group-hover:text-white">
                                    {{ $socialLink['label'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</main>
@endsection
