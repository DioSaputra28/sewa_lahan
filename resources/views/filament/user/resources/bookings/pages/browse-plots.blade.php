<x-filament-panels::page>
    <div class="space-y-8 scheme-light dark:scheme-dark">
        <section class="overflow-hidden rounded-[2rem] border border-white/70 bg-linear-to-br from-amber-100 via-white to-sky-100 shadow-sm ring-1 ring-slate-200/70 dark:border-white/10 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 dark:ring-white/10">
            <div class="grid gap-8 px-6 py-8 lg:grid-cols-[1.3fr_0.7fr] lg:px-8">
                <div class="space-y-4">
                    <span class="inline-flex items-center rounded-full bg-white/80 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-amber-700 ring-1 ring-amber-200 dark:bg-white/5 dark:text-amber-300 dark:ring-amber-400/20">
                        Booking Lahan
                    </span>
                    <div class="space-y-3">
                        <h2 class="max-w-3xl text-3xl font-semibold tracking-tight text-slate-950 md:text-4xl dark:text-white">
                            Pilih lahan yang paling pas untuk kebutuhan usahamu.
                        </h2>
                        <p class="max-w-2xl text-sm leading-7 text-slate-600 md:text-base dark:text-gray-400">
                            Jelajahi lahan yang masih tersedia, bandingkan ukuran dan harga, lalu buka detail untuk mengajukan booking dengan lebih yakin.
                        </p>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                    <div class="rounded-2xl border border-white/80 bg-white/80 p-4 shadow-sm backdrop-blur dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-gray-500">Lahan tersedia</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-950 dark:text-white">{{ $this->getPlots()->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/80 bg-white/80 p-4 shadow-sm backdrop-blur dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-gray-500">Status</p>
                        <p class="mt-3 text-sm font-medium text-slate-700 dark:text-gray-300">Semua unit di halaman ini siap diajukan booking.</p>
                    </div>
                    <div class="rounded-2xl border border-white/80 bg-white/80 p-4 shadow-sm backdrop-blur dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-gray-500">Aksi cepat</p>
                        <p class="mt-3 text-sm font-medium text-slate-700 dark:text-gray-300">Klik kartu lahan untuk membuka detail dan lanjut ke form booking.</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-3">
        @foreach ($this->getPlots() as $plot)
            @php
                $primaryImage = $plot->images->firstWhere('is_primary', true) ?? $plot->images->first();
                $imageUrl = $primaryImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($primaryImage->image_path)
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($primaryImage->image_path)
                    : null;
                $priceParts = [];

                if ($plot->base_price_monthly) {
                    $priceParts[] = 'Bulanan: Rp ' . number_format($plot->base_price_monthly, 0, ',', '.');
                }

                if ($plot->base_price_yearly) {
                    $priceParts[] = 'Tahunan: Rp ' . number_format($plot->base_price_yearly, 0, ',', '.');
                }
            @endphp

            <a
                href="{{ \App\Filament\User\Resources\Bookings\BookingResource::getUrl('plot', ['plot' => $plot]) }}"
                class="group flex h-full flex-col overflow-hidden rounded-[1.65rem] border border-slate-200/80 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-amber-300 hover:shadow-xl hover:shadow-amber-100/40 dark:border-white/10 dark:bg-gray-900 dark:shadow-none dark:hover:border-amber-400/50 dark:hover:shadow-none"
            >
                <div class="relative aspect-[16/10] overflow-hidden bg-slate-100 dark:bg-gray-950 sm:aspect-[4/3]">
                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $plot->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
                    @else
                        <div class="absolute inset-0 bg-radial from-amber-100 via-white to-slate-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"></div>
                        <div class="relative flex h-full flex-col items-center justify-center gap-2 px-5 text-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-amber-600 shadow-sm ring-1 ring-slate-200 dark:bg-white/5 dark:text-amber-300 dark:ring-white/10">
                                <x-filament::icon icon="heroicon-o-photo" class="h-6 w-6" />
                            </div>
                            <div class="space-y-1">
                                <p class="text-base font-semibold text-slate-800 dark:text-white">Preview belum tersedia</p>
                                <p class="mx-auto max-w-xs text-xs leading-5 text-slate-500 dark:text-gray-400">Detail lahan tetap bisa dibuka untuk melihat informasi dan mengajukan booking.</p>
                            </div>
                        </div>
                    @endif

                    <div class="absolute inset-x-0 top-0 flex items-start justify-between gap-3 p-3.5">
                        <span class="inline-flex items-center rounded-full bg-white/90 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-700 ring-1 ring-slate-200/80 backdrop-blur dark:bg-gray-950/80 dark:text-gray-300 dark:ring-white/10">
                            {{ $plot->market?->name }}
                        </span>
                        <span class="inline-flex shrink-0 items-center rounded-full bg-emerald-500 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.24em] text-white shadow-sm">
                            Tersedia
                        </span>
                    </div>
                </div>

                <div class="flex flex-1 flex-col gap-4 p-4 sm:p-5">
                    <div class="space-y-2.5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-[1.7rem] font-semibold tracking-tight text-slate-950 dark:text-white sm:text-[1.55rem]">{{ $plot->name }}</h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">{{ $plot->area?->name ?? 'Tanpa area' }} • {{ ucfirst($plot->type) }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-100 px-3 py-2 text-right dark:bg-white/5">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400 dark:text-gray-500">Level</p>
                                <p class="mt-1 text-base font-semibold text-slate-700 dark:text-gray-200">{{ $plot->floor_level ?: '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5 rounded-2xl bg-slate-50 p-2.5 text-sm text-slate-600 dark:bg-gray-950">
                        <div class="rounded-xl bg-white px-3 py-3 ring-1 ring-slate-200/70 dark:bg-white/5 dark:ring-white/10">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 dark:text-gray-500">Ukuran</p>
                            <p class="mt-1.5 text-[1.06rem] font-medium leading-6 text-slate-800 dark:text-gray-100">{{ number_format((float) $plot->length, 2, ',', '.') }} x {{ number_format((float) $plot->width, 2, ',', '.') }} m</p>
                        </div>
                        <div class="rounded-xl bg-white px-3 py-3 ring-1 ring-slate-200/70 dark:bg-white/5 dark:ring-white/10">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 dark:text-gray-500">Luas</p>
                            <p class="mt-1.5 text-[1.06rem] font-medium leading-6 text-slate-800 dark:text-gray-100">{{ number_format((float) $plot->area_square_meters, 2, ',', '.') }} m2</p>
                        </div>
                    </div>

                    <p class="line-clamp-2 text-sm leading-6 text-slate-600 dark:text-gray-400">{{ $plot->description ?: 'Belum ada deskripsi lahan.' }}</p>

                    <div class="rounded-2xl border border-amber-100 bg-amber-50 px-4 py-3.5 text-sm text-amber-950 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-100">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-amber-700 dark:text-amber-300">Harga tersedia</p>
                        <p class="mt-2 text-[15px] font-medium leading-7">{{ implode(' | ', $priceParts) ?: 'Harga belum tersedia' }}</p>
                    </div>

                    <div class="mt-auto flex flex-col items-start gap-3 border-t border-slate-100 pt-3 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                        <p class="max-w-[14rem] text-sm leading-6 font-medium text-slate-500 dark:text-gray-400">Buka detail untuk melihat informasi lengkap.</p>
                        <span class="inline-flex shrink-0 items-center gap-2 rounded-2xl bg-slate-950 px-3.5 py-2.5 text-sm font-semibold text-white transition group-hover:bg-amber-500 dark:bg-white dark:text-gray-950 dark:group-hover:bg-amber-400">
                            Lihat detail
                            <x-filament::icon icon="heroicon-m-arrow-right" class="h-3.5 w-3.5" />
                        </span>
                    </div>
                </div>
            </a>
        @endforeach
        </div>
    </div>
</x-filament-panels::page>
