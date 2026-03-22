@extends('web.layouts.main')

@section('content')
{{-- ============================================================
     STATIC PLOT DETAIL PAGE — "Unit A-12, Pasar Senen"
     Data: Market=Pasar Senen (Jakarta Pusat), Area=Blok A Lt.1,
           Plot=Unit A-12, 3x3m=9m², Dry Goods, Floor 1
     ============================================================ --}}

{{-- Breadcrumb --}}
<nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6" aria-label="Breadcrumb">
    <ol class="flex items-center gap-2 text-sm text-slate-500">
        <li><a class="hover:text-slate-900 transition-colors" href="/">Home</a></li>
        <li><span class="material-symbols-outlined text-base">chevron_right</span></li>
        <li><a class="hover:text-slate-900 transition-colors" href="/lahan">Lahan</a></li>
        <li><span class="material-symbols-outlined text-base">chevron_right</span></li>
        <li class="text-slate-900 font-medium truncate max-w-[200px]">Unit A-12, Pasar Senen</li>
    </ol>
</nav>

{{-- Hero: Image Gallery + Booking Sidebar --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- Left: Image Gallery --}}
        <div class="flex-1">
            {{-- Main Image --}}
            <div class="relative rounded-3xl overflow-hidden bg-slate-100 mb-4 group">
                <img
                    class="w-full aspect-[16/10] object-cover transition-transform duration-700 group-hover:scale-105"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCBD-mRgP8BT6LBqFVhYhVqlU4kbrooSw7otxMVS6lUdXJ0kQt3N3qbjF2bWlhNmmvf8p1sxF82qWDU5Njsz4FZgnQvLiZmJ0ceDtCZKYrkKWV8KZbEynB4U-YtfJ--x-btax7JVLliqIzk7MnYQhh247A_PsE1o_3wYQMrWFws-XGCjnH6mN4LZEhJDR8VEKVRO9YyuBOUZaHApbAOqkbXBsZdyct0i8lPts8xD24NjBGeg2N6Q4s0WATNQqfCLxJdq0aqmGrmiIWd"
                    alt="Interior view of Unit A-12, Pasar Senen stall space"
                />
                {{-- Floating badges --}}
                <div class="absolute top-4 left-4 flex gap-2">
                    <span class="bg-white/90 backdrop-blur-sm text-slate-900 text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full">
                        Available Now
                    </span>
                    <span class="bg-primary/10 text-primary text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full border border-primary/30">
                        Premium Spot
                    </span>
                </div>
                {{-- Favorite button --}}
                <button class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm p-2.5 rounded-full hover:bg-white transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-slate-400 hover:text-red-500 transition-colors">favorite</span>
                </button>
            </div>

            {{-- Thumbnail Strip --}}
            <div class="grid grid-cols-4 gap-3">
                <button class="rounded-xl overflow-hidden ring-2 ring-slate-900 ring-offset-2">
                    <img class="w-full aspect-square object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCBD-mRgP8BT6LBqFVhYhVqlU4kbrooSw7otxMVS6lUdXJ0kQt3N3qbjF2bWlhNmmvf8p1sxF82qWDU5Njsz4FZgnQvLiZmJ0ceDtCZKYrkKWV8KZbEynB4U-YtfJ--x-btax7JVLliqIzk7MnYQhh247A_PsE1o_3wYQMrWFws-XGCjnH6mN4LZEhJDR8VEKVRO9YyuBOUZaHApbAOqkbXBsZdyct0i8lPts8xD24NjBGeg2N6Q4s0WATNQqfCLxJdq0aqmGrmiIWd" alt="Stall interior view" />
                </button>
                <button class="rounded-xl overflow-hidden opacity-60 hover:opacity-100 transition-opacity">
                    <img class="w-full aspect-square object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAnSXIKlwTxTjvzKrLx4qMrk6vvAtI_9upGVO5QcYLq-WWzEkKSAr_RkX5VbaAlXY0TehcfjZh5YSG2QvsA08_hMIh8EY_1pDycxVL-sQoElAutPTsxpm-SRn1QZRGt4AIgUp4cTOxz6TILYB1E-HPhDRjyVRytsQnco23xlYJtVfW_QcCbn2aU-ZPsGYfeOko-aCTmsmby3gkJYHmultp6b1l99N248ucSa-_VZMvpO6WGsoJCRIw5iHe-wRm_BB8HA07vz4qOIKV4" alt="Market corridor view" />
                </button>
                <button class="rounded-xl overflow-hidden opacity-60 hover:opacity-100 transition-opacity">
                    <img class="w-full aspect-square object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDVDZ8VQbb5N-cVX8sKFVqxWB0teVl891gLGNxWyZbznUxsdlsc21os9QwpNSsU5jQbFCbf3becwrzY9TNG6QtZGVvfP3_s3wGK0-MRqVQnwBofMC9YODzVH68U78bb4aR1EC1D93GJ3R5nJLIxDr81yXv9jpUL5nfyK9YZIN-trcdl3zagW_OcSmrWguERvv8-rHI5uA_twhlb96ecX66faoVby_NohAeXJz52arCiHgl-G9JeGzKoyM6FiX2Mb-xIP0Luwg3ZksGT" alt="Nearby vendor stalls" />
                </button>
                <button class="rounded-xl overflow-hidden opacity-60 hover:opacity-100 transition-opacity">
                    <img class="w-full aspect-square object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCUTQdpjfLjzq2AXfHKRQ0YjYfAV9p4etHOxRffrieIYvvUCmuCSGwxftDBwZs9-WbUTAKYdLLVEZQIyI-6wQ9mtc6udqLDE016ONYU9t_MwaZ0mj1Tt01Ya0IZ4QlV8Cs8vRigbAfUHTfyUBwWg2eOwmWvjIGD1kHRKBQc-9KCgx9YB433reUcKjQb3kfW2oqxl5q_jU1L0rW54hToKjVguZLqv7iGcnK38vPMhE8yg5GXBcB8bDgaq2HvxsHzN1TF_JF6je8Y5J0V" alt="Market exterior" />
                </button>
            </div>
        </div>

        {{-- Right: Booking Sidebar (sticky on desktop) --}}
        <aside class="w-full lg:w-96 flex-shrink-0">
            <div class="lg:sticky lg:top-28 space-y-5">

                {{-- Plot Title Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Premium Stall</p>
                            <h1 class="text-2xl lg:text-3xl font-black text-slate-900 leading-tight">Unit A-12</h1>
                            <p class="text-slate-500 flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined text-sm text-slate-400">location_on</span>
                                Pasar Senen — Blok A, Lt. 1
                            </p>
                        </div>
                    </div>

                    {{-- Quick Specs Row --}}
                    <div class="flex flex-wrap gap-2 mt-4">
                        <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                            <span class="material-symbols-outlined text-sm">square_foot</span> 9 m²
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                            <span class="material-symbols-outlined text-sm">layers</span> Floor 1
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                            <span class="material-symbols-outlined text-sm">category</span> Dry Goods
                        </span>
                    </div>
                </div>

                {{-- Pricing Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                    {{-- Billing Toggle --}}
                    <div class="flex bg-slate-100 rounded-xl p-1 mb-5">
                        <button id="toggle-monthly" class="flex-1 py-2 text-sm font-bold rounded-lg bg-white text-slate-900 shadow-sm transition-all">
                            Monthly
                        </button>
                        <button id="toggle-yearly" class="flex-1 py-2 text-sm font-bold rounded-lg text-slate-500 hover:text-slate-700 transition-all">
                            Yearly <span class="text-xs font-normal text-slate-400">-5%</span>
                        </button>
                    </div>

                    {{-- Price Display --}}
                    <div class="mb-1">
                        <span id="price-display" class="text-4xl font-black text-slate-900">Rp 1.500.000</span>
                        <span class="text-slate-400 text-sm">/<span id="price-period">month</span></span>
                    </div>
                    <p id="price-sub" class="text-xs text-slate-400 mb-6">Rp 17.100.000/year if paid annually</p>

                    <button class="w-full py-4 bg-primary hover:bg-primary/90 text-slate-900 font-bold text-base rounded-xl shadow-md transition-all active:scale-[0.98]">
                        Rent Now
                    </button>
                    <p class="text-center text-xs text-slate-400 mt-3">No hidden fees. Cancel anytime.</p>
                </div>

                {{-- Location Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-slate-400 mb-4">Location Details</h3>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 mt-0.5">store</span>
                            <div>
                                <p class="text-xs text-slate-400">Market</p>
                                <p class="text-sm font-semibold text-slate-900">Pasar Senen</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 mt-0.5">grid_view</span>
                            <div>
                                <p class="text-xs text-slate-400">Area / Zone</p>
                                <p class="text-sm font-semibold text-slate-900">Blok A — Lantai 1</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 mt-0.5">place</span>
                            <div>
                                <p class="text-xs text-slate-400">City</p>
                                <p class="text-sm font-semibold text-slate-900">Jakarta Pusat, DKI Jakarta</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-slate-400 mt-0.5">map</span>
                            <div>
                                <p class="text-xs text-slate-400">Maps</p>
                                <a class="text-sm font-semibold text-primary hover:underline" href="#">View on Google Maps →</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </aside>
    </div>
</section>

{{-- Plot Specifications --}}
<section class="bg-white border-y border-slate-200 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-lg font-black text-slate-900 mb-6">Detail Lahan</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            {{-- Dimension --}}
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">straighten</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Dimension</p>
                <p class="text-base font-black text-slate-900">3 × 3 m</p>
            </div>
            {{-- Total Area --}}
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">square_foot</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Total Area</p>
                <p class="text-base font-black text-slate-900">9 m²</p>
            </div>
            {{-- Floor Level --}}
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">layers</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Floor</p>
                <p class="text-base font-black text-slate-900">Level 1</p>
            </div>
            {{-- Type --}}
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">category</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Type</p>
                <p class="text-base font-black text-slate-900">Dry Goods</p>
            </div>
            {{-- Status --}}
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">bolt</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Status</p>
                <p class="text-base font-black text-primary">Available</p>
            </div>
            {{-- Location Note --}}
            <div class="bg-slate-50 rounded-xl p-4 text-center border border-slate-100">
                <span class="material-symbols-outlined text-primary text-2xl mb-2">near_me</span>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Position</p>
                <p class="text-base font-black text-slate-900">A-12</p>
            </div>
        </div>
    </div>
</section>

{{-- Description + Features --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid lg:grid-cols-3 gap-12">
        {{-- Description --}}
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-black text-slate-900 mb-5">About This Stall</h2>
            <div class="prose prose-slate max-w-none space-y-4 text-slate-600 leading-relaxed">
                <p>
                    Unit A-12 is a premium dry goods stall strategically positioned at the main corridor of Pasar Senen's newly renovated Blok A. With generous ceiling height and a wide storefront opening, this unit offers excellent visibility and natural ventilation — ideal for textile vendors, craft sellers, or specialty food packagers.
                </p>
                <p>
                    The stall comes with built-in modular shelving infrastructure and a secured storage cabinet. Pasar Senen sees an average of 8,000 daily visitors, with peak foot traffic between 07:00–10:00 and 16:00–19:00 — giving vendors maximum exposure throughout the trading day.
                </p>
            </div>
        </div>

        {{-- Amenities --}}
        <div>
            <h2 class="text-lg font-black text-slate-900 mb-5">Fasilitas</h2>
            <ul class="space-y-3">
                @php
                    $amenities = [
                        ['icon' => 'electrical_services', 'label' => '230V Electrical Outlet'],
                        ['icon' => 'wifi', 'label' => 'Public WiFi Zone'],
                        ['icon' => 'local_parking', 'label' => 'Motorcycle Parking'],
                        ['icon' => 'local_gas_station', 'label' => 'Dedicated Loading Area'],
                        ['icon' => 'security', 'label' => '24/7 Security Guard'],
                        ['icon' => 'cleaning_services', 'label' => 'Daily Cleaning Service'],
                        ['icon' => 'water_drop', 'label' => 'Clean Water Supply'],
                        ['icon' => 'cash', 'label' => 'ATM &amp; Digital Payment'],
                    ];
                @endphp
                @foreach ($amenities as $a)
                <li class="flex items-center gap-3">
                    <span class="bg-primary/10 text-primary p-1.5 rounded-lg">
                        <span class="material-symbols-outlined text-base">{{ $a['icon'] }}</span>
                    </span>
                    <span class="text-sm font-medium text-slate-700">{{ $a['label'] }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- Related Listings --}}
<section class="bg-slate-50 border-t border-slate-200 py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-black text-slate-900 mb-1">Similar Stalls Nearby</h2>
                <p class="text-slate-500 text-sm">Other available units in Pasar Senen</p>
            </div>
            <a class="text-sm font-bold text-primary hover:underline flex items-center gap-1" href="/lahan">
                View All
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $related = [
                    [
                        'name' => 'Unit B-04, Pasar Senen',
                        'location' => 'Blok B, Lt. 1 — Jakarta Pusat',
                        'size' => '2.5 × 2.5 m',
                        'type' => 'Wet Market',
                        'price' => 'Rp 950k',
                        'img' => 'AB6AXuDEQ1gQwQpFXh4A5db1b9n99Nvw3GEgRiizV03ToxVGGu4o56TFhFRypyWdnFwISt0Wcpar0ejD5GNJbBpUPFaOl9O09i5roVOSOHLyx5qr2fOGHOg76tVhR8ueojPIFGyjSDyD9Pb0PIIZ7H-S7PjFpj87FzNyjcLZBhLsY3Suo5u0FGzplbaZsw88bLyALvCeNnffRMiOdsNw5OtPIG2zLEowKQY2SbWR3q17Y3x4si9Kg16fF7tgteaHdltumLoOacyC6EOTqJGY',
                        'badge' => 'Featured',
                        'badgeClass' => 'bg-primary text-slate-900',
                    ],
                    [
                        'name' => 'Unit A-08, Pasar Senen',
                        'location' => 'Blok A, Lt. 2 — Jakarta Pusat',
                        'size' => '3 × 2 m',
                        'type' => 'Dry Goods',
                        'price' => 'Rp 1.2M',
                        'img' => 'AB6AXuBtgdMnCJK0btvr1gDUctHCcIofvqIi_ZB8edfdylTmErfwoGV5aL9ZYVlISwSErpty67BcL6iOnyv3MsRbmaNY769mM5jb8ZxXESJDC2OAj0x0hQOirhUZTi6acmgXgB-NYTM3VlNkdkdtbo4Rrxm7wGaTzrjozxPFGURJnW1O16QdLUdk7pgyC4ITIR7hfcJl_h49eb0ROi4HU-ZK65vGHMZdLRJyt6ZOLy-FvB1PBvlnYZb3o8YDHL5YRQcGorxtcmFwAiP6yJUk',
                        'badge' => 'Available',
                        'badgeClass' => 'bg-slate-100 text-slate-700',
                    ],
                    [
                        'name' => 'Unit C-11, Blokotosari',
                        'location' => 'Blok C, Lt. 1 — Jakarta Pusat',
                        'size' => '4 × 3 m',
                        'type' => 'Culinary',
                        'price' => 'Rp 2.0M',
                        'img' => 'AB6AXuDO4Bk_cYyZeFRIwmH7EG9igAiFav2VXW317NekzWHGciRE7-t7f3u9LE0umUDh0V4k9GFmmOgz7mq9nQRMNBvw5JpWiZfWK7wIm5mvuChHondN9fo9vPYdDzhsBvqfFI41dH-JDzGzRx7dZ0HkZ4oIbi1jPfR6EQaRujtxfqB2vnbTfFp1pvrTiduP2JmQBtjnw643Xt51ZK2QUJJACbq5hnINPQSL4KrpeXRsqn1f_FpzR3wEmphfkvI90GpUAPHOB9F2I1P7cHJZ',
                        'badge' => 'New',
                        'badgeClass' => 'bg-primary/20 text-primary',
                    ],
                    [
                        'name' => 'Unit D-02, Pasar Senen',
                        'location' => 'Blok D, Lt. 1 — Jakarta Pusat',
                        'size' => '2 × 2 m',
                        'type' => 'Electronics',
                        'price' => 'Rp 800k',
                        'img' => 'AB6AXuBYkQooYMAhcXIOkw5seOZmSl0xXSeSBgEVgeHJrBSWXJ2Wlp7epfqGZCBfwhXyhcMtAwoUumLq0yPBUhG12gkR4GMNy5phXOR8891zYYFqEsux8lF2uHr78GRduw8jX8ddrwdPPy0uf0S_buOfNo-DSA5rpVddVXoVS0BsAbK0SeDqvm18flfxkDWnuV0C3HmFH9qBizeGUlBSj7RweBISMKOFgXNw96aQraOuIOcefb8yIirO_oFFLTrc4a-DYodPMhoA4azcuNqN',
                        'badge' => 'Available',
                        'badgeClass' => 'bg-slate-100 text-slate-700',
                    ],
                ];
            @endphp

            @foreach ($related as $item)
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden group hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                <div class="relative h-40 overflow-hidden">
                    <img
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                        src="https://lh3.googleusercontent.com/aida-public/{{ $item['img'] }}"
                        alt="{{ $item['name'] }}"
                    />
                    <span class="absolute top-3 left-3 text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded-full {{ $item['badgeClass'] }}">
                        {{ $item['badge'] }}
                    </span>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-sm text-slate-900 mb-1 group-hover:text-primary transition-colors leading-tight">{{ $item['name'] }}</h3>
                    <p class="text-xs text-slate-400 flex items-center gap-0.5 mb-3">
                        <span class="material-symbols-outlined text-xs">location_on</span>
                        {{ $item['location'] }}
                    </p>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-400">Size</p>
                            <p class="text-sm font-bold text-slate-700">{{ $item['size'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-primary">{{ $item['price'] }}</p>
                            <p class="text-[10px] text-slate-400">/month</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<script>
    // Simple price toggle
    const btnMonthly = document.getElementById('toggle-monthly');
    const btnYearly = document.getElementById('toggle-yearly');
    const priceDisplay = document.getElementById('price-display');
    const pricePeriod = document.getElementById('price-period');
    const priceSub = document.getElementById('price-sub');

    btnYearly.addEventListener('click', () => {
        btnYearly.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
        btnYearly.classList.remove('text-slate-500');
        btnMonthly.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
        btnMonthly.classList.add('text-slate-500');
        priceDisplay.textContent = 'Rp 17.100.000';
        pricePeriod.textContent = 'year';
        priceSub.textContent = 'Billed annually — save 5%';
    });

    btnMonthly.addEventListener('click', () => {
        btnMonthly.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
        btnMonthly.classList.remove('text-slate-500');
        btnYearly.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
        btnYearly.classList.add('text-slate-500');
        priceDisplay.textContent = 'Rp 1.500.000';
        pricePeriod.textContent = 'month';
        priceSub.textContent = 'Rp 17.100.000/year if paid annually';
    });
</script>
@endsection
