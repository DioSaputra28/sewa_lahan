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

    <form
        wire:submit="verify"
        class="space-y-6"
        x-data="{
            resendAvailableAt: $wire.entangle('resendAvailableAt').live,
            remainingSeconds: {{ $this->resendCooldownRemainingSeconds() }},
            init() {
                this.syncCooldown();
                window.setInterval(() => this.syncCooldown(), 1000);
                this.$watch('resendAvailableAt', () => this.syncCooldown());
            },
            syncCooldown() {
                this.remainingSeconds = Math.max((this.resendAvailableAt ?? 0) - Math.floor(Date.now() / 1000), 0);
            },
            formattedCooldown() {
                const minutes = String(Math.floor(this.remainingSeconds / 60)).padStart(2, '0');
                const seconds = String(this.remainingSeconds % 60).padStart(2, '0');

                return `${minutes}:${seconds}`;
            },
        }"
    >
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="otp_code">Kode OTP</label>
            <input id="otp_code" wire:model.defer="otp_code" type="text" inputmode="numeric" maxlength="6" placeholder="123456" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-center text-2xl font-bold tracking-[0.35em] text-slate-900 outline-hidden transition-all focus:border-transparent focus:ring-2 focus:ring-[#47eb7e] dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-100">
            @error('otp_code') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="verify"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#47eb7e] px-4 py-3 font-bold text-[#112116] transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-70"
            >
                <span wire:loading.remove wire:target="verify">Verifikasi</span>
                <span wire:loading wire:target="verify">Memverifikasi...</span>
                <x-filament::icon wire:loading.remove wire:target="verify" icon="heroicon-m-check" class="h-4 w-4" />
                <svg wire:loading wire:target="verify" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
                </svg>
            </button>

            <button
                type="button"
                wire:click="resend"
                wire:loading.attr="disabled"
                wire:target="resend"
                @disabled($this->hasResendCooldown())
                x-bind:disabled="remainingSeconds > 0"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-3 font-semibold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-70 dark:border-white/10 dark:bg-white/5 dark:text-slate-200 dark:hover:bg-white/10"
            >
                <span
                    wire:loading.remove
                    wire:target="resend"
                    x-text="remainingSeconds > 0 ? `Kirim ulang dalam ${formattedCooldown()}` : 'Kirim ulang OTP'"
                >
                    {{ $this->hasResendCooldown() ? 'Kirim ulang dalam '.$this->formattedResendCooldown() : 'Kirim ulang OTP' }}
                </span>
                <span wire:loading wire:target="resend">Mengirim ulang...</span>
                <x-filament::icon wire:loading.remove wire:target="resend" icon="heroicon-m-arrow-path" class="h-4 w-4" />
                <svg wire:loading wire:target="resend" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
                </svg>
            </button>
        </div>
    </form>
</x-filament.user.auth-shell>
