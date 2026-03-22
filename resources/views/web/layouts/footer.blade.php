<footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            <div>
                <div class="flex items-center gap-2 mb-6">
                    <div class="flex items-center justify-center size-8 bg-primary rounded-lg text-slate-900">
                        <span class="material-symbols-outlined font-bold">storefront</span>
                    </div>
                    <h2 class="text-xl font-extrabold tracking-tight">PasarSpace</h2>
                </div>
                <p class="text-slate-500 text-sm mb-6">The leading platform for market stall management and rentals across Southeast Asia. Bridging traditional commerce and digital ease.</p>
                <div class="flex gap-4">
                    <a class="size-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center hover:bg-primary transition-colors group" href="#">
                        <span class="material-symbols-outlined text-slate-600 dark:text-slate-400 group-hover:text-slate-900">public</span>
                    </a>
                    <a class="size-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center hover:bg-primary transition-colors group" href="#">
                        <span class="material-symbols-outlined text-slate-600 dark:text-slate-400 group-hover:text-slate-900">alternate_email</span>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="font-bold mb-6">Market Locations</h4>
                <ul class="space-y-4 text-sm text-slate-500">
                    <li><a class="hover:text-primary transition-colors" href="#">Jakarta Raya</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#">Bandung City</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#">Surabaya East</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#">Medan Central</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-6">Resources</h4>
                <ul class="space-y-4 text-sm text-slate-500">
                    <li><a class="hover:text-primary transition-colors" href="#">Vendor Handbook</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#">Pricing Plans</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#">Safety Guidelines</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-6">Stay Updated</h4>
                <p class="text-sm text-slate-500 mb-4">Get the latest stall openings in your inbox.</p>
                <form class="flex gap-2">
                    <input class="flex-1 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary" placeholder="Email address" type="email"/>
                    <button class="bg-primary text-slate-900 p-2 rounded-lg">
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </form>
            </div>
        </div>
        <div class="border-t border-slate-100 dark:border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-slate-400">© {{ date('Y') }} PasarSpace Rentals. All rights reserved.</p>
            <div class="flex gap-6 text-sm text-slate-400">
                <a class="hover:text-primary transition-colors" href="#">Privacy Policy</a>
                <a class="hover:text-primary transition-colors" href="#">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
