@extends('web.layouts.main')

@section('content')
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
            <!-- Contact Info Card -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-200 dark:border-slate-700">
                <h3 class="text-xl font-black mb-6 uppercase tracking-tight">{{ __('web.contact.hq_title') }}</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                            <span class="material-symbols-outlined" data-icon="location_on">location_on</span>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ __('web.contact.office_label') }}</p>
                            <p class="text-slate-500 dark:text-slate-400 leading-relaxed">{!! __('web.contact.info_description') !!}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                            <span class="material-symbols-outlined" data-icon="call">call</span>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ __('web.contact.phone_label') }}</p>
                            <p class="text-slate-500 dark:text-slate-400">+62 21 5550 1234</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                            <span class="material-symbols-outlined" data-icon="mail">mail</span>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ __('web.contact.email_label') }}</p>
                            <p class="text-slate-500 dark:text-slate-400">hello@pasarspace.com</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Map Section -->
            <div class="relative h-[300px] rounded-3xl overflow-hidden shadow-xl group border-4 border-white">
                <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700" data-alt="Map view showing PasarSpace office location in Jakarta" data-location="Jakarta" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAxhpPNH9JkRIpPEGkyM670nKGTsQ_FZh_GfugzSE22pK3y2AeIYlQvp2YX7QFp6AW9zjXg8budDQqVIT_LirqV6FChQu_o8wK865xO8VKkggQeZBuaC9o-vXctc1SsEhC1_eYbHrTrXyafvVi91aVLueipiGKcs6j2NsGegiriP0T78hScUSrg9vgGbjGL8ywUpD4CRA_ZbpETquv7p7FD2bMMf8EyiX9yBHXnZB6GSraM1QSlkE6gRlGea9oYVXIgzabljsb1UW8J" />
                <div class="absolute inset-0 bg-primary/10 pointer-events-none"></div>
                <div class="absolute bottom-6 left-6 right-6 bg-white/90 backdrop-blur-md p-4 rounded-2xl shadow-lg border border-white/50">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-primary animate-pulse"></div>
                        <span class="text-sm font-bold text-slate-900">{{ __('web.contact.map_label') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection