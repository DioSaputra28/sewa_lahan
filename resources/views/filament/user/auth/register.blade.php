<x-filament.user.auth-shell
    heading="Buat akun customer"
    subheading="Daftarkan akunmu untuk mulai melihat lahan, invoice, dan kontrak sewa."
    footer-text="Sudah punya akun?"
    footer-link-label="Masuk di sini"
    :footer-link-url="filament()->getLoginUrl()"
>
    <form wire:submit="register" class="space-y-6">
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="name">Nama lengkap</label>
            <input id="name" wire:model.defer="data.name" type="text" placeholder="Nama lengkap kamu" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-hidden transition-all focus:border-transparent focus:ring-2 focus:ring-[#47eb7e] dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-100">
            @error('data.name') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="space-y-2 md:col-span-2">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="register-email">Alamat email</label>
                <input id="register-email" wire:model.defer="data.email" type="email" placeholder="nama@email.com" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-hidden transition-all focus:border-transparent focus:ring-2 focus:ring-[#47eb7e] dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-100">
                @error('data.email') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="phone">Nomor telepon</label>
                <input id="phone" wire:model.defer="data.phone" type="text" placeholder="08xxxxxxxxxx" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-hidden transition-all focus:border-transparent focus:ring-2 focus:ring-[#47eb7e] dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-100">
                @error('data.phone') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="password">Password</label>
                <input id="password" wire:model.defer="data.password" type="password" placeholder="Buat password yang kuat" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-hidden transition-all focus:border-transparent focus:ring-2 focus:ring-[#47eb7e] dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-100">
                @error('data.password') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="password-confirmation">Konfirmasi password</label>
                <input id="password-confirmation" wire:model.defer="data.passwordConfirmation" type="password" placeholder="Ulangi password kamu" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-hidden transition-all focus:border-transparent focus:ring-2 focus:ring-[#47eb7e] dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-100">
                @error('data.passwordConfirmation') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="register"
            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#47eb7e] px-4 py-3 font-bold text-[#112116] transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-70"
        >
            <span wire:loading.remove wire:target="register">Daftar Akun</span>
            <span wire:loading wire:target="register">Sedang membuat akun...</span>
            <x-filament::icon wire:loading.remove wire:target="register" icon="heroicon-m-arrow-right" class="h-4 w-4" />
            <svg wire:loading wire:target="register" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
            </svg>
        </button>
    </form>
</x-filament.user.auth-shell>
