<x-filament-panels::page>
    @php
        $plot = $this->plot;
        $priceParts = [];

        if ($plot->base_price_monthly) {
            $priceParts[] = 'Bulanan: Rp ' . number_format($plot->base_price_monthly, 0, ',', '.');
        }

        if ($plot->base_price_yearly) {
            $priceParts[] = 'Tahunan: Rp ' . number_format($plot->base_price_yearly, 0, ',', '.');
        }
    @endphp

    <div class="space-y-8 scheme-light dark:scheme-dark">
        <div class="grid gap-8 xl:grid-cols-[1.25fr_0.95fr]">
            <div class="space-y-4">
                @php
                    $primaryImage = $plot->images->firstWhere('is_primary', true) ?? $plot->images->first();
                    $primaryImageUrl = $primaryImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($primaryImage->image_path)
                        ? \Illuminate\Support\Facades\Storage::disk('public')->url($primaryImage->image_path)
                        : null;
                @endphp

                <div class="overflow-hidden rounded-[1.9rem] border border-slate-200/80 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900 dark:shadow-none">
                    @if ($primaryImageUrl)
                        <img src="{{ $primaryImageUrl }}" alt="{{ $plot->name }}" class="h-[22rem] w-full object-cover lg:h-[26rem]">
                    @else
                        <div class="relative flex h-[22rem] items-center justify-center overflow-hidden bg-radial from-amber-100 via-white to-slate-100 lg:h-[26rem] dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
                            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(251,191,36,0.18),_transparent_38%),radial-gradient(circle_at_bottom_right,_rgba(56,189,248,0.12),_transparent_40%)] dark:bg-[radial-gradient(circle_at_top_left,_rgba(251,191,36,0.12),_transparent_38%),radial-gradient(circle_at_bottom_right,_rgba(148,163,184,0.08),_transparent_40%)]"></div>
                            <div class="relative flex max-w-sm flex-col items-center gap-3 px-6 text-center">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white text-amber-600 shadow-sm ring-1 ring-slate-200 dark:bg-white/5 dark:text-amber-300 dark:ring-white/10">
                                    <x-filament::icon icon="heroicon-o-photo" class="h-7 w-7" />
                                </div>
                                <div class="space-y-1">
                                    <p class="text-base font-semibold text-slate-800 dark:text-white">Preview utama belum tersedia</p>
                                    <p class="text-sm leading-6 text-slate-500 dark:text-gray-400">Informasi lahan tetap lengkap dan bisa langsung dipakai untuk pengajuan booking.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                @if ($plot->images->count() > 1)
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                        @foreach ($plot->images as $image)
                            @php
                                $thumbnailUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists($image->image_path)
                                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($image->image_path)
                                    : null;
                            @endphp

                            <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900 dark:shadow-none">
                                @if ($thumbnailUrl)
                                    <img src="{{ $thumbnailUrl }}" alt="{{ $plot->name }}" class="aspect-[4/3] w-full object-cover">
                                @else
                                    <div class="flex aspect-[4/3] items-center justify-center bg-slate-100 text-slate-400 dark:bg-gray-950 dark:text-gray-600">
                                        <x-filament::icon icon="heroicon-o-photo" class="h-6 w-6" />
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="space-y-6 rounded-[1.9rem] border border-slate-200/80 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900 dark:shadow-none">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.26em] text-amber-600 dark:text-amber-300">{{ $plot->market?->name }}</p>
                    <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ $plot->name }}</h1>
                    <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">{{ $plot->area?->name ?? 'Tanpa area' }} • {{ ucfirst($plot->type) }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3 rounded-2xl bg-slate-50 p-3 text-sm dark:bg-gray-950">
                    <div class="rounded-xl bg-white px-4 py-3 ring-1 ring-slate-200/70 dark:bg-white/5 dark:ring-white/10">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 dark:text-gray-500">Ukuran</p>
                        <p class="mt-1.5 text-base font-medium text-slate-900 dark:text-gray-100">{{ number_format((float) $plot->length, 2, ',', '.') }} x {{ number_format((float) $plot->width, 2, ',', '.') }} m</p>
                    </div>
                    <div class="rounded-xl bg-white px-4 py-3 ring-1 ring-slate-200/70 dark:bg-white/5 dark:ring-white/10">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 dark:text-gray-500">Luas</p>
                        <p class="mt-1.5 text-base font-medium text-slate-900 dark:text-gray-100">{{ number_format((float) $plot->area_square_meters, 2, ',', '.') }} m2</p>
                    </div>
                    <div class="rounded-xl bg-white px-4 py-3 ring-1 ring-slate-200/70 dark:bg-white/5 dark:ring-white/10">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 dark:text-gray-500">Level</p>
                        <p class="mt-1.5 text-base font-medium text-slate-900 dark:text-gray-100">{{ $plot->floor_level ?: '-' }}</p>
                    </div>
                    <div class="rounded-xl bg-white px-4 py-3 ring-1 ring-slate-200/70 dark:bg-white/5 dark:ring-white/10">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 dark:text-gray-500">Catatan lokasi</p>
                        <p class="mt-1.5 text-base font-medium text-slate-900 dark:text-gray-100">{{ $plot->location_note ?: '-' }}</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-100">
                    <p class="text-[11px] uppercase tracking-[0.2em] text-amber-600 dark:text-amber-300">Harga tersedia</p>
                    <p class="mt-2 text-base font-medium leading-7">{{ implode(' | ', $priceParts) ?: 'Harga belum tersedia' }}</p>
                </div>

                <div>
                    <p class="text-sm leading-7 text-slate-600 dark:text-gray-400">{{ $plot->description ?: 'Belum ada deskripsi tambahan untuk lahan ini.' }}</p>
                </div>

                <a
                    href="{{ \App\Filament\User\Resources\Bookings\BookingResource::getUrl('create', ['plot' => $plot->id]) }}"
                    class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-500 dark:bg-white dark:text-gray-950 dark:hover:bg-amber-400"
                >
                    Ajukan Booking
                </a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
