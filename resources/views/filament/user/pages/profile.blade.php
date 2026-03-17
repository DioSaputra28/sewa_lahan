<x-filament-panels::page>
    @php
        $summary = $this->getAccountSummary();
    @endphp

    <div class="space-y-8 scheme-light dark:scheme-dark">
        <section class="overflow-hidden rounded-[2rem] border border-white/70 bg-linear-to-br from-amber-100 via-white to-sky-100 shadow-sm ring-1 ring-slate-200/70 dark:border-white/10 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 dark:ring-white/10">
            <div class="grid gap-6 px-6 py-8 lg:grid-cols-[1.2fr_0.8fr] lg:px-8">
                <div class="space-y-4">
                    <span class="inline-flex items-center rounded-full bg-white/80 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-amber-700 ring-1 ring-amber-200 dark:bg-white/5 dark:text-amber-300 dark:ring-amber-400/20">
                        Account Settings
                    </span>
                    <div class="space-y-3">
                        <h2 class="max-w-3xl text-3xl font-semibold tracking-tight text-slate-950 md:text-4xl dark:text-white">
                            Kelola informasi akunmu dengan lebih rapi.
                        </h2>
                        <p class="max-w-2xl text-sm leading-7 text-slate-600 md:text-base dark:text-gray-400">
                            Halaman ini dipakai untuk memperbarui identitas dasar akun dan menjaga keamanan password tanpa mengubah email login utama.
                        </p>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                    <div class="rounded-2xl border border-white/80 bg-white/80 p-4 shadow-sm backdrop-blur dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-gray-500">Status akun</p>
                        <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">{{ $summary['status'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/80 bg-white/80 p-4 shadow-sm backdrop-blur dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-gray-500">Member sejak</p>
                        <p class="mt-3 text-sm font-medium text-slate-700 dark:text-gray-300">{{ $summary['member_since'] }}</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <section class="space-y-4 rounded-[1.9rem] border border-slate-200/80 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900 dark:shadow-none">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 text-lg font-semibold text-amber-700 dark:bg-amber-400/10 dark:text-amber-300">
                        {{ auth()->user()?->initials() }}
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ $summary['name'] }}</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">{{ $summary['email'] }}</p>
                    </div>
                </div>

                <div class="grid gap-3 rounded-2xl bg-slate-50 p-3 text-sm dark:bg-gray-950">
                    <div class="rounded-xl bg-white px-4 py-3 ring-1 ring-slate-200/70 dark:bg-white/5 dark:ring-white/10">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 dark:text-gray-500">Nomor telepon</p>
                        <p class="mt-1.5 text-base font-medium text-slate-900 dark:text-gray-100">{{ $summary['phone'] }}</p>
                    </div>
                    <div class="rounded-xl bg-white px-4 py-3 ring-1 ring-slate-200/70 dark:bg-white/5 dark:ring-white/10">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400 dark:text-gray-500">Email terverifikasi</p>
                        <p class="mt-1.5 text-base font-medium text-slate-900 dark:text-gray-100">{{ $summary['email_verified_at'] }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-[1.9rem] border border-slate-200/80 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900 dark:shadow-none">
                <form wire:submit="save" class="space-y-8">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Informasi akun</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">Perbarui nama dan nomor telepon yang dipakai di akunmu.</p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700 dark:text-gray-200">Nama</span>
                                <input wire:model.defer="name" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-hidden transition focus:border-amber-400 dark:border-white/10 dark:bg-gray-950 dark:text-white dark:focus:border-amber-400">
                                @error('name') <span class="text-sm text-rose-500">{{ $message }}</span> @enderror
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700 dark:text-gray-200">Nomor telepon</span>
                                <input wire:model.defer="phone" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-hidden transition focus:border-amber-400 dark:border-white/10 dark:bg-gray-950 dark:text-white dark:focus:border-amber-400">
                                @error('phone') <span class="text-sm text-rose-500">{{ $message }}</span> @enderror
                            </label>
                        </div>

                        <label class="space-y-2 block">
                            <span class="text-sm font-medium text-slate-700 dark:text-gray-200">Email login</span>
                            <input value="{{ $email }}" type="email" disabled class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500 shadow-sm dark:border-white/10 dark:bg-white/5 dark:text-gray-400">
                            <span class="text-sm text-slate-500 dark:text-gray-400">Email tidak dapat diubah dari halaman ini.</span>
                        </label>
                    </div>

                    <div class="space-y-4 border-t border-slate-100 pt-8 dark:border-white/10">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Keamanan akun</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">Isi bagian ini hanya jika kamu ingin mengganti password akun.</p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2 md:col-span-2">
                                <span class="text-sm font-medium text-slate-700 dark:text-gray-200">Password saat ini</span>
                                <input wire:model.defer="current_password" type="password" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-hidden transition focus:border-amber-400 dark:border-white/10 dark:bg-gray-950 dark:text-white dark:focus:border-amber-400">
                                @error('current_password') <span class="text-sm text-rose-500">{{ $message }}</span> @enderror
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700 dark:text-gray-200">Password baru</span>
                                <input wire:model.defer="password" type="password" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-hidden transition focus:border-amber-400 dark:border-white/10 dark:bg-gray-950 dark:text-white dark:focus:border-amber-400">
                                @error('password') <span class="text-sm text-rose-500">{{ $message }}</span> @enderror
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-medium text-slate-700 dark:text-gray-200">Konfirmasi password baru</span>
                                <input wire:model.defer="password_confirmation" type="password" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-hidden transition focus:border-amber-400 dark:border-white/10 dark:bg-gray-950 dark:text-white dark:focus:border-amber-400">
                                @error('password_confirmation') <span class="text-sm text-rose-500">{{ $message }}</span> @enderror
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6 dark:border-white/10">
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-500 dark:bg-white dark:text-gray-950 dark:hover:bg-amber-400">
                            Simpan perubahan
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-filament-panels::page>
