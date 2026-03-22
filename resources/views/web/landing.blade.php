@extends('web.layouts.main')

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
                        <button class="bg-primary hover:bg-primary/90 text-slate-900 px-8 py-4 rounded-xl text-lg font-bold shadow-lg transition-transform active:scale-95">
                            Browse Stalls
                        </button>
                        <button class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-8 py-4 rounded-xl text-lg font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            View Map
                        </button>
                    </div>
                </div>
                <div class="flex-1 w-full relative">
                    <div class="aspect-[4/3] rounded-3xl overflow-hidden shadow-2xl relative z-10">
                        <img class="w-full h-full object-cover" data-alt="Modern indoor market with organized wooden stalls" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCBD-mRgP8BT6LBqFVhYhVqlU4kbrooSw7otxMVS6lUdXJ0kQt3N3qbjF2bWlhNmmvf8p1sxF82qWDU5Njsz4FZgnQvLiZmJ0ceDtCZKYrkKWV8KZbEynB4U-YtfJ--x-btax7JVLliqIzk7MnYQhh247A_PsE1o_3wYQMrWFws-XGCjnH6mN4LZEhJDR8VEKVRO9YyuBOUZaHApbAOqkbXBsZdyct0i8lPts8xD24NjBGeg2N6Q4s0WATNQqfCLxJdq0aqmGrmiIWd"/>
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

    <!-- Filter Bar Prominent -->
    <section class="relative z-30 -mt-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[200px]">
                    <button class="w-full flex items-center justify-between gap-2 px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 hover:border-primary transition-colors group">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-400 group-hover:text-primary">location_on</span>
                            <span class="text-sm font-medium">All Regions</span>
                        </div>
                        <span class="material-symbols-outlined text-slate-400">expand_more</span>
                    </button>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <button class="w-full flex items-center justify-between gap-2 px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 hover:border-primary transition-colors group">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-400 group-hover:text-primary">square_foot</span>
                            <span class="text-sm font-medium">Stall Size</span>
                        </div>
                        <span class="material-symbols-outlined text-slate-400">expand_more</span>
                    </button>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <button class="w-full flex items-center justify-between gap-2 px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 hover:border-primary transition-colors group">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-400 group-hover:text-primary">payments</span>
                            <span class="text-sm font-medium">Price Range</span>
                        </div>
                        <span class="material-symbols-outlined text-slate-400">expand_more</span>
                    </button>
                </div>
                <button class="bg-slate-900 dark:bg-primary dark:text-slate-900 text-white px-8 py-3 rounded-xl font-bold flex items-center gap-2 hover:opacity-90 transition-opacity">
                    <span class="material-symbols-outlined text-xl">tune</span>
                    Apply Filters
                </button>
            </div>
        </div>
    </section>

    <!-- Listings Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-black tracking-tight mb-2">Available Stall Rentals</h2>
                    <p class="text-slate-500">Showing 24 high-potential locations available for rent</p>
                </div>
                <button class="hidden sm:flex items-center gap-2 text-sm font-bold text-primary group">
                    See All
                    <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Stall Card 1 -->
                <div class="group bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all">
                    <div class="relative aspect-video">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" data-alt="Interior of a traditional market stall space" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDIkNS3QaQ4m_CvBV-PgDAVhK9iEoB5-3Zs7MyJYjf7d9DxBqjdm_7FHvPYwPr5TbHFTJDkOJXXWgJk3bB27VEaRhLyCWdtzHwbwS3CzZF_LWaDpLcP9wYz1hOKWPu_wjpz2StzE-yytcsO3COV4uCoeDpiedJEjnmtxE9Q2bt3sJ0gCcMdpbhGrijEOH79ItYDnZ-L91Cw4Z4_yuxHM4PoqqA28Nceeq_-VSaYErlZV8Mg595dDpRCYh4Khzl3lbLvVPCG--jQQZy2"/>
                        <div class="absolute top-3 left-3 bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm px-3 py-1 rounded-lg">
                            <span class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-widest">Premium</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold mb-1 group-hover:text-primary transition-colors">Unit A-12, Pasar Senen</h3>
                                <p class="text-slate-500 text-sm flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">location_on</span>
                                    Jakarta Pusat
                                </p>
                            </div>
                            <div class="bg-primary/10 text-primary p-2 rounded-lg">
                                <span class="material-symbols-outlined">favorite</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-slate-400 text-lg">aspect_ratio</span>
                                <span class="text-sm font-semibold">3x3m</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-slate-400 text-lg">bolt</span>
                                <span class="text-sm font-semibold">Ready</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-tight">Price</p>
                                <p class="text-lg font-black text-slate-900 dark:text-slate-100">Rp 1.5M<span class="text-sm font-normal text-slate-500">/mo</span></p>
                            </div>
                            <button class="bg-primary hover:bg-primary/90 text-slate-900 px-4 py-2.5 rounded-lg font-bold transition-all shadow-sm">
                                Rent Now
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Stall Card 2 -->
                <div class="group bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all">
                    <div class="relative aspect-video">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" data-alt="Outdoor covered marketplace area stalls" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAnSXIKlwTxTjvzKrLx4qMrk6vvAtI_9upGVO5QcYLq-WWzEkKSAr_RkX5VbaAlXY0TehcfjZh5YSG2QvsA08_hMIh8EY_1pDycxVL-sQoElAutPTsxpm-SRn1QZRGt4AIgUp4cTOxz6TILYB1E-HPhDRjyVRytsQnco23xlYJtVfW_QcCbn2aU-ZPsGYfeOko-aCTmsmby3gkJYHmultp6b1l99N248ucSa-_VZMvpO6WGsoJCRIw5iHe-wRm_BB8HA07vz4qOIKV4"/>
                        <div class="absolute top-3 left-3 bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm px-3 py-1 rounded-lg">
                            <span class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-widest">Seasonal</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold mb-1 group-hover:text-primary transition-colors">Zone B, Bandung Raya</h3>
                                <p class="text-slate-500 text-sm flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">location_on</span>
                                    Bandung, West Java
                                </p>
                            </div>
                            <div class="bg-slate-100 dark:bg-slate-700 text-slate-400 p-2 rounded-lg">
                                <span class="material-symbols-outlined">favorite</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-slate-400 text-lg">aspect_ratio</span>
                                <span class="text-sm font-semibold">2x2m</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-slate-400 text-lg">bolt</span>
                                <span class="text-sm font-semibold">Limited</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-tight">Price</p>
                                <p class="text-lg font-black text-slate-900 dark:text-slate-100">Rp 850k<span class="text-sm font-normal text-slate-500">/mo</span></p>
                            </div>
                            <button class="bg-primary hover:bg-primary/90 text-slate-900 px-4 py-2.5 rounded-lg font-bold transition-all shadow-sm">
                                Rent Now
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Stall Card 3 -->
                <div class="group bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all">
                    <div class="relative aspect-video">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" data-alt="Close up of modern clean stall space" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDVDZ8VQbb5N-cVX8sKFVqxWB0teVl891gLGNxWyZbznUxsdlsc21os9QwpNSsU5jQbFCbf3becwrzY9TNG6QtZGVvfP3_s3wGK0-MRqVQnwBofMC9YODzVH68U78bb4aR1EC1D93GJ3R5nJLIxDr81yXv9jpUL5nfyK9YZIN-trcdl3zagW_OcSmrWguERvv8-rHI5uA_twhlb96ecX66faoVby_NohAeXJz52arCiHgl-G9JeGzKoyM6FiX2Mb-xIP0Luwg3ZksGT"/>
                        <div class="absolute top-3 left-3 bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm px-3 py-1 rounded-lg">
                            <span class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-widest">New Opening</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold mb-1 group-hover:text-primary transition-colors">West East, Surabaya</h3>
                                <p class="text-slate-500 text-sm flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">location_on</span>
                                    Surabaya East
                                </p>
                            </div>
                            <div class="bg-slate-100 dark:bg-slate-700 text-slate-400 p-2 rounded-lg">
                                <span class="material-symbols-outlined">favorite</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-slate-400 text-lg">aspect_ratio</span>
                                <span class="text-sm font-semibold">4x4m</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-slate-400 text-lg">bolt</span>
                                <span class="text-sm font-semibold">Standard</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-tight">Price</p>
                                <p class="text-lg font-black text-slate-900 dark:text-slate-100">Rp 2.1M<span class="text-sm font-normal text-slate-500">/mo</span></p>
                            </div>
                            <button class="bg-primary hover:bg-primary/90 text-slate-900 px-4 py-2.5 rounded-lg font-bold transition-all shadow-sm">
                                Rent Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
                        <img class="w-full h-full object-cover" data-alt="A detailed digital map of a city market area" data-location="Jakarta" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBz7ziiL7OVOi-ahVXkEKF3VkgT7TicxB-h-vJOoFWCJSkmQPxv1xE7anmZj8eHwqfawM6u2Epw1vjH6XGatnPs04XkFFPfexj0NyBXwH1LVTjR5bvtNlOnCKDVD0SV8ubUTnixskL2W-iGQz7vRR8O2r2tv5sSy2UCE8Wzn9Hi69ma7qv20hpSkUt9y93_3XKNr6nQhsetDAFX2ih5MwWZoaZuUIfT5z6IJ8psz3xqgIQAh567-JsPYemIa8TQ_2RzHixFmrenjTKQ"/>
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
