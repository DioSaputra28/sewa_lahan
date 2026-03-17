<x-filament.user.auth-shell
    heading="Welcome back"
    subheading="Please enter your details to access your dashboard."
    footer-text="Don't have an account?"
    footer-link-label="Sign up for free"
    :footer-link-url="filament()->getRegistrationUrl()"
    :show-social="true"
>
    <form wire:submit="authenticate" class="space-y-6">
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="email">Email Address</label>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-filament::icon icon="heroicon-o-envelope" class="h-5 w-5" />
                </div>
                <input id="email" wire:model.defer="data.email" type="email" placeholder="name@company.com" class="w-full rounded-lg border border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-slate-900 outline-hidden transition-all focus:border-transparent focus:ring-2 focus:ring-[#47eb7e] dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-100">
            </div>
            @error('data.email') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="password">Password</label>
                <span class="text-sm font-medium text-primary">Forgot password?</span>
            </div>
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-filament::icon icon="heroicon-o-lock-closed" class="h-5 w-5" />
                </div>
                <input id="password" wire:model.defer="data.password" type="password" placeholder="••••••••" class="w-full rounded-lg border border-slate-200 bg-slate-50 py-3 pl-10 pr-12 text-slate-900 outline-hidden transition-all focus:border-transparent focus:ring-2 focus:ring-[#47eb7e] dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-100">
                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                    <x-filament::icon icon="heroicon-o-eye" class="h-5 w-5" />
                </button>
            </div>
            @error('data.password') <p class="text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                <input wire:model.defer="data.remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-[#47eb7e] focus:ring-[#47eb7e] dark:border-slate-600 dark:bg-slate-800">
                Remember for 30 days
            </label>

        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#47eb7e] px-4 py-3 font-bold text-[#112116] transition hover:brightness-95">
            <span>Login to Dashboard</span>
            <x-filament::icon icon="heroicon-m-arrow-right" class="h-4 w-4" />
        </button>
    </form>
</x-filament.user.auth-shell>
