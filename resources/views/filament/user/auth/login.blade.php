<x-filament.user.auth-shell
    heading="Masuk ke akunmu"
    subheading="Gunakan email dan password untuk mengakses panel customer."
    footer-text="Belum punya akun?"
    footer-link-label="Daftar sekarang"
    :footer-link-url="filament()->getRegistrationUrl()"
    :show-social="false"
>
    <form wire:submit="authenticate" class="space-y-6">
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="email">Alamat email</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-filament::icon icon="heroicon-o-envelope" class="h-5 w-5" />
                </div>
                <input id="email" wire:model.defer="data.email" type="email" placeholder="nama@email.com" class="w-full rounded-lg border border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-slate-900 outline-hidden transition-all focus:border-transparent focus:ring-2 focus:ring-[#47eb7e] dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-100">
            </div>
            @error('data.email') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="password">Password</label>
                <span class="text-sm font-medium text-primary">Lupa password?</span>
            </div>
            <div class="relative" x-data="{ showPassword: false }">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-filament::icon icon="heroicon-o-lock-closed" class="h-5 w-5" />
                </div>
                <input id="password" wire:model.defer="data.password" :type="showPassword ? 'text' : 'password'" placeholder="••••••••" class="w-full rounded-lg border border-slate-200 bg-slate-50 py-3 pl-10 pr-12 text-slate-900 outline-hidden transition-all focus:border-transparent focus:ring-2 focus:ring-[#47eb7e] dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-100">
                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" @click="showPassword = !showPassword">
                    <x-filament::icon x-show="!showPassword" icon="heroicon-o-eye" class="h-5 w-5" />
                    <x-filament::icon x-show="showPassword" icon="heroicon-o-eye-slash" class="h-5 w-5" />
                </button>
            </div>
            @error('data.password') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                <input wire:model.defer="data.remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-[#47eb7e] focus:ring-[#47eb7e] dark:border-slate-600 dark:bg-slate-800">
                Ingat saya selama 30 hari
            </label>

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="authenticate"
            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#47eb7e] px-4 py-3 font-bold text-[#112116] transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-70"
        >
            <span wire:loading.remove wire:target="authenticate">Masuk</span>
            <span wire:loading wire:target="authenticate">Sedang masuk...</span>
            <x-filament::icon wire:loading.remove wire:target="authenticate" icon="heroicon-m-arrow-right" class="h-4 w-4" />
            <svg wire:loading wire:target="authenticate" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
            </svg>
        </button>
    </form>
</x-filament.user.auth-shell>
