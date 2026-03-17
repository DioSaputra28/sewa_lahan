<x-filament.user.auth-shell
    heading="Verifikasi OTP"
    subheading="Masukkan 6 digit kode OTP yang sudah dikirim ke emailmu untuk mengaktifkan akun."
    footer-text="Email yang kamu masukkan salah?"
    footer-link-label="Kembali ke register"
    :footer-link-url="filament()->getRegistrationUrl()"
>
    <div class="mb-6 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-gray-300">
        OTP dikirim ke <span class="font-semibold text-slate-900 dark:text-white">{{ $this->getPendingUser()->email }}</span>
    </div>

    <form wire:submit="verify" class="space-y-6">
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="otp_code">Kode OTP</label>
            <input id="otp_code" wire:model.defer="otp_code" type="text" inputmode="numeric" maxlength="6" placeholder="123456" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-center text-2xl font-bold tracking-[0.35em] text-slate-900 outline-hidden transition-all focus:border-transparent focus:ring-2 focus:ring-[#47eb7e] dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-100">
            @error('otp_code') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#47eb7e] px-4 py-3 font-bold text-[#112116] transition hover:brightness-95">
                <span>Verifikasi</span>
                <x-filament::icon icon="heroicon-m-check" class="h-4 w-4" />
            </button>

            <button type="button" wire:click="resend" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-3 font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-white/5 dark:text-slate-200 dark:hover:bg-white/10">
                <span>Kirim ulang OTP</span>
                <x-filament::icon icon="heroicon-m-arrow-path" class="h-4 w-4" />
            </button>
        </div>
    </form>
</x-filament.user.auth-shell>
