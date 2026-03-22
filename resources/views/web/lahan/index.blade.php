@extends('web.layouts.main')

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
            <div class="sticky top-24 space-y-6">
                <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">tune</span>
                        Filters
                    </h2>
                    <!-- Search -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Search</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
                            <input class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary" placeholder="Market name..." type="text" />
                        </div>
                    </div>
                    <!-- Region -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Region</label>
                        <div class="space-y-2">
                            <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg bg-primary/10 text-slate-900 dark:text-slate-100 text-sm font-medium group">
                                <span class="material-symbols-outlined text-primary group-hover:fill-current">location_on</span>
                                All Regions
                            </button>
                            <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 text-sm font-medium transition-colors">
                                <span class="material-symbols-outlined">location_on</span>
                                Jakarta
                            </button>
                            <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 text-sm font-medium transition-colors">
                                <span class="material-symbols-outlined">location_on</span>
                                Bandung
                            </button>
                            <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 text-sm font-medium transition-colors">
                                <span class="material-symbols-outlined">location_on</span>
                                Surabaya
                            </button>
                        </div>
                    </div>
                    <!-- Size Range -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Stall Size</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button class="px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium hover:border-primary transition-colors">Small (&lt;4m²)</button>
                            <button class="px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium hover:border-primary transition-colors">Medium (4-9m²)</button>
                            <button class="px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium hover:border-primary transition-colors">Large (&gt;9m²)</button>
                            <button class="px-3 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium hover:border-primary transition-colors">Premium</button>
                        </div>
                    </div>
                    <button class="w-full py-3 bg-primary text-slate-900 font-bold rounded-lg hover:opacity-90 transition-opacity flex items-center justify-center gap-2">
                        Apply Filters
                    </button>
                </div>
                <!-- Promotions / Help -->
                <div class="bg-slate-900 rounded-xl p-6 text-white overflow-hidden relative">
                    <div class="relative z-10">
                        <h3 class="font-bold mb-2">Need Help?</h3>
                        <p class="text-sm text-slate-400 mb-4">Our support team is available 24/7 for consultation.</p>
                        <a class="inline-flex items-center text-primary text-sm font-bold hover:underline" href="#">
                            Contact Support
                            <span class="material-symbols-outlined text-sm ml-1">arrow_forward</span>
                        </a>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-20">
                        <span class="material-symbols-outlined text-8xl">support_agent</span>
                    </div>
                </div>
            </div>
        </aside>
        <!-- Main Content Grid -->
        <div class="flex-1">
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-slate-500">Showing <span class="text-slate-900 dark:text-slate-100 font-bold">12</span> available stalls</p>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-500">Sort by:</span>
                    <select class="text-sm border-none bg-transparent focus:ring-0 font-medium cursor-pointer">
                        <option>Newest First</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                        <option>Size: Largest</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <!-- Listing Card 1 -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden group hover:shadow-lg transition-shadow">
                    <div class="h-48 bg-slate-200 relative overflow-hidden">
                        <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" data-alt="Busy traditional market stall area" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCUTQdpjfLjzq2AXfHKRQ0YjYfAV9p4etHOxRffrieIYvvUCmuCSGwxftDBwZs9-WbUTAKYdLLVEZQIyI-6wQ9mtc6udqLDE016ONYU9t_MwaZ0mj1Tt01Ya0IZ4QlV8Cs8vRigbAfUHTfyUBwWg2eOwmWvjIGD1kHRKBQc-9KCgx9YB433reUcKjQb3kfW2oqxl5q_jU1L0rW54hToKjVguZLqv7iGcnK38vPMhE8yg5GXBcB8bDgaq2HvxsHzN1TF_JF6je8Y5J0V" />
                        <div class="absolute top-3 left-3 bg-white/90 dark:bg-slate-900/90 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest">Premium Spot</div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg mb-1 group-hover:text-primary transition-colors">Pasar Baru Block A-12</h3>
                        <p class="text-slate-500 text-sm flex items-center gap-1 mb-4">
                            <span class="material-symbols-outlined text-sm text-primary">location_on</span>
                            Central Jakarta
                        </p>
                        <div class="flex items-center justify-between py-3 border-y border-slate-100 dark:border-slate-800 mb-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Size</span>
                                <span class="text-sm font-semibold">3 x 4 m²</span>
                            </div>
                            <div class="flex flex-col text-right">
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Type</span>
                                <span class="text-sm font-semibold">Dry Goods</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-black text-primary">Rp 1.5M</span>
                                <span class="text-xs text-slate-400">/mo</span>
                            </div>
                            <button class="px-4 py-2 bg-primary/20 text-slate-900 font-bold text-xs rounded-lg hover:bg-primary transition-colors">Details</button>
                        </div>
                    </div>
                </div>
                <!-- Listing Card 2 -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden group hover:shadow-lg transition-shadow">
                    <div class="h-48 bg-slate-200 relative overflow-hidden">
                        <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" data-alt="Traditional market fresh produce area" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDEQ1gQwQpFXh4A5db1b9n99Nvw3GEgRiizV03ToxVGGu4o56TFhFRypyWdnFwISt0Wcpar0ejD5GNJbBpUPFaOl9O09i5roVOSOHLyx5qr2fOGHOg76tVhR8ueojPIFGyjSDyD9Pb0PIIZ7H-S7PjFpj87FzNyjcLZBhLsY3Suo5u0FGzplbaZsw88bLyALvCeNnffRMiOdsNw5OtPIG2zLEowKQY2SbWR3q17Y3x4si9Kg16fF7tgteaHdltumLoOacyC6EOTqJGY" />
                        <div class="absolute top-3 left-3 bg-primary px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest text-slate-900">Featured</div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg mb-1 group-hover:text-primary transition-colors">Kosambi Market #042</h3>
                        <p class="text-slate-500 text-sm flex items-center gap-1 mb-4">
                            <span class="material-symbols-outlined text-sm text-primary">location_on</span>
                            Bandung, West Java
                        </p>
                        <div class="flex items-center justify-between py-3 border-y border-slate-100 dark:border-slate-800 mb-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Size</span>
                                <span class="text-sm font-semibold">2.5 x 2.5 m²</span>
                            </div>
                            <div class="flex flex-col text-right">
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Type</span>
                                <span class="text-sm font-semibold">Wet Market</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-black text-primary">Rp 850k</span>
                                <span class="text-xs text-slate-400">/mo</span>
                            </div>
                            <button class="px-4 py-2 bg-primary/20 text-slate-900 font-bold text-xs rounded-lg hover:bg-primary transition-colors">Details</button>
                        </div>
                    </div>
                </div>
                <!-- Listing Card 3 -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden group hover:shadow-lg transition-shadow">
                    <div class="h-48 bg-slate-200 relative overflow-hidden">
                        <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" data-alt="Vegetable and fruit market stalls" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA7nS5fuu5NukuY1uQOx4TJgOB90B08pm-54rvBMPNuoSrrdLArJ60k987xIySOQwrw32MSosmjclYWieG1rSkmVGoGXyZIZmyq_n0I7vUpuE6ET4vsM133Zs8C_1a_N77BqsxGs6Y1sU8RnLbtSwFpaV2UMCynrQ7WFGotCzlICZym1JhRqw-84ORORww2so1rHLBxtNoO95plJJx7FV1HmEBzCYNfGEpSlez85JUxGiB4S6Kd1DUrThZhhXBsg0j0b6Bq7C4hQfyH" />
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg mb-1 group-hover:text-primary transition-colors">Pasar Turi Indah Lt.2</h3>
                        <p class="text-slate-500 text-sm flex items-center gap-1 mb-4">
                            <span class="material-symbols-outlined text-sm text-primary">location_on</span>
                            Surabaya, East Java
                        </p>
                        <div class="flex items-center justify-between py-3 border-y border-slate-100 dark:border-slate-800 mb-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Size</span>
                                <span class="text-sm font-semibold">4 x 5 m²</span>
                            </div>
                            <div class="flex flex-col text-right">
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Type</span>
                                <span class="text-sm font-semibold">Textile/Apparel</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-black text-primary">Rp 2.1M</span>
                                <span class="text-xs text-slate-400">/mo</span>
                            </div>
                            <button class="px-4 py-2 bg-primary/20 text-slate-900 font-bold text-xs rounded-lg hover:bg-primary transition-colors">Details</button>
                        </div>
                    </div>
                </div>
                <!-- Listing Card 4 -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden group hover:shadow-lg transition-shadow">
                    <div class="h-48 bg-slate-200 relative overflow-hidden">
                        <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" data-alt="Market hallway with empty stalls" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBtgdMnCJK0btvr1gDUctHCcIofvqIi_ZB8edfdylTmErfwoGV5aL9ZYVlISwSErpty67BcL6iOnyv3MsRbmaNY769mM5jb8ZxXESJDC2OAj0x0hQOirhUZTi6acmgXgB-NYTM3VlNkdkdtbo4Rrxm7wGaTzrjozxPFGURJnW1O16QdLUdk7pgyC4ITIR7hfcJl_h49eb0ROi4HU-ZK65vGHMZdLRJyt6ZOLy-FvB1PBvlnYZb3o8YDHL5YRQcGorxtcmFwAiP6yJUk" />
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg mb-1 group-hover:text-primary transition-colors">Mayestik Market C-05</h3>
                        <p class="text-slate-500 text-sm flex items-center gap-1 mb-4">
                            <span class="material-symbols-outlined text-sm text-primary">location_on</span>
                            South Jakarta
                        </p>
                        <div class="flex items-center justify-between py-3 border-y border-slate-100 dark:border-slate-800 mb-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Size</span>
                                <span class="text-sm font-semibold">2 x 2 m²</span>
                            </div>
                            <div class="flex flex-col text-right">
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Type</span>
                                <span class="text-sm font-semibold">Electronics</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-black text-primary">Rp 1.2M</span>
                                <span class="text-xs text-slate-400">/mo</span>
                            </div>
                            <button class="px-4 py-2 bg-primary/20 text-slate-900 font-bold text-xs rounded-lg hover:bg-primary transition-colors">Details</button>
                        </div>
                    </div>
                </div>
                <!-- Listing Card 5 -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden group hover:shadow-lg transition-shadow">
                    <div class="h-48 bg-slate-200 relative overflow-hidden">
                        <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" data-alt="Open air night market stall" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBYkQooYMAhcXIOkw5seOZmSl0xXSeSBgEVgeHJrBSWXJ2Wlp7epfqGZCBfwhXyhcMtAwoUumLq0yPBUhG12gkR4GMNy5phXOR8891zYYFqEsux8lF2uHr78GRduw8jX8ddrwdPPy0uf0S_buOfNo-DSA5rpVddVXoVS0BsAbK0SeDqvm18flfxkDWnuV0C3HmFH9qBizeGUlBSj7RweBISMKOFgXNw96aQraOuIOcefb8yIirO_oFFLTrc4a-DYodPMhoA4azcuNqN" />
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg mb-1 group-hover:text-primary transition-colors">Pasar Gede Stall #12</h3>
                        <p class="text-slate-500 text-sm flex items-center gap-1 mb-4">
                            <span class="material-symbols-outlined text-sm text-primary">location_on</span>
                            Solo, Central Java
                        </p>
                        <div class="flex items-center justify-between py-3 border-y border-slate-100 dark:border-slate-800 mb-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Size</span>
                                <span class="text-sm font-semibold">3 x 3 m²</span>
                            </div>
                            <div class="flex flex-col text-right">
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Type</span>
                                <span class="text-sm font-semibold">Culinary</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-black text-primary">Rp 950k</span>
                                <span class="text-xs text-slate-400">/mo</span>
                            </div>
                            <button class="px-4 py-2 bg-primary/20 text-slate-900 font-bold text-xs rounded-lg hover:bg-primary transition-colors">Details</button>
                        </div>
                    </div>
                </div>
                <!-- Listing Card 6 -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden group hover:shadow-lg transition-shadow">
                    <div class="h-48 bg-slate-200 relative overflow-hidden">
                        <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" data-alt="Traditional indoor market layout" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDO4Bk_cYyZeFRIwmH7EG9igAiFav2VXW317NekzWHGciRE7-t7f3u9LE0umUDh0V4k9GFmmOgz7mq9nQRMNBvw5JpWiZfWK7wIm5mvuChHondN9fo9vPYdDzhsBvqfFI41dH-JDzGzRx7dZ0HkZ4oIbi1jPfR6EQaRujtxfqB2vnbTfFp1pvrTiduP2JmQBtjnw643Xt51ZK2QUJJACbq5hnINPQSL4KrpeXRsqn1f_FpzR3wEmphfkvI90GpUAPHOB9F2I1P7cHJZ" />
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg mb-1 group-hover:text-primary transition-colors">Senen Market Block III</h3>
                        <p class="text-slate-500 text-sm flex items-center gap-1 mb-4">
                            <span class="material-symbols-outlined text-sm text-primary">location_on</span>
                            Central Jakarta
                        </p>
                        <div class="flex items-center justify-between py-3 border-y border-slate-100 dark:border-slate-800 mb-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Size</span>
                                <span class="text-sm font-semibold">2 x 3 m²</span>
                            </div>
                            <div class="flex flex-col text-right">
                                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Type</span>
                                <span class="text-sm font-semibold">Fashion</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xl font-black text-primary">Rp 1.1M</span>
                                <span class="text-xs text-slate-400">/mo</span>
                            </div>
                            <button class="px-4 py-2 bg-primary/20 text-slate-900 font-bold text-xs rounded-lg hover:bg-primary transition-colors">Details</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pagination -->
            <div class="mt-12 flex items-center justify-center gap-2">
                <button class="size-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-800 hover:border-primary transition-colors">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                <button class="size-10 flex items-center justify-center rounded-lg bg-primary text-slate-900 font-bold">1</button>
                <button class="size-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-800 hover:border-primary transition-colors font-bold">2</button>
                <button class="size-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-800 hover:border-primary transition-colors font-bold">3</button>
                <span class="px-2">...</span>
                <button class="size-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-800 hover:border-primary transition-colors font-bold">8</button>
                <button class="size-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-800 hover:border-primary transition-colors">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
            </div>
        </div>
    </div>
</main>
@endsection