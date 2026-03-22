@extends('web.layouts.main')

@section('content')
<!-- Main Content -->
<main class="pt-24 pb-20">
    <!-- Hero Section -->
    <section class="px-8 py-20 max-w-7xl mx-auto">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-xs font-black uppercase tracking-widest text-primary bg-primary/10 px-3 py-1 rounded-full inline-block mb-6">Our Mission</span>
                <h1 class="text-6xl md:text-8xl font-black tracking-tight text-slate-900 dark:text-slate-100 mb-8">Empowering The Local <span class="text-primary italic">Artisan.</span></h1>
                <p class="text-xl text-slate-600 dark:text-slate-400 leading-relaxed max-w-lg">
                    We are bridging the gap between traditional market heritage and the digital future, providing high-end physical infrastructure for modern commerce.
                </p>
            </div>
            <div class="relative group">
                <div class="absolute -inset-4 bg-primary/20 rounded-3xl blur-2xl group-hover:bg-primary/30 transition-all duration-500"></div>
                <img alt="Modern Market Interior" class="relative rounded-3xl shadow-2xl object-cover h-[500px] w-full transform group-hover:scale-[1.02] transition-transform duration-500" data-alt="Modern indoor market with wooden stalls and bright lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA8qFlBXvKqmfV76EvtiiAAweI_2FdkbI15usg7lSEeOGZ_5IpRk6O9SJcbvgzdPRXzM2gUfuOdT7Je9TqBTTP2GGznp8h99vMVChxpeBSlT5s6oR71XA2yFANDldsIKX-2xw-tJSA4isd4IexBkptH757TjC8ckeYTy4-0giDLLtjh7UxYL8CB9yR1pK0IC5HllbZr3BkndZfFjuQYG7zd3JzfYee2L-lRhiIJjwJfegXtFXN49EvPmM3aurrdtzRyXb5olJ6-jHRB" />
            </div>
        </div>
    </section>
    <!-- Our Story: Editorial Layout -->
    <section class="bg-slate-100 dark:bg-slate-900/40 py-24">
        <div class="max-w-7xl mx-auto px-8">
            <div class="flex flex-col md:flex-row gap-16">
                <div class="md:w-1/3">
                    <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-slate-100 sticky top-32">The Genesis of the Modern Agora.</h2>
                </div>
                <div class="md:w-2/3 space-y-8 text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                    <p>
                        PasarSpace began in 2024 with a simple observation: while the world was moving online, the soul of our communities—the local market—was being left behind. Traditional vendors possessed incredible craft but lacked the modern spaces to compete in a high-expectation retail environment.
                    </p>
                    <p>
                        We set out to redesign the "stall" as a high-performance modular asset. By integrating architectural excellence with logistical smarts, we've created a platform where any vendor can step into a premium retail experience without the massive overhead of a traditional storefront.
                    </p>
                    <div class="grid grid-cols-2 gap-8 pt-8">
                        <div>
                            <div class="text-4xl font-black text-primary mb-2">500+</div>
                            <div class="text-sm font-bold uppercase tracking-widest opacity-60">Active Stalls</div>
                        </div>
                        <div>
                            <div class="text-4xl font-black text-primary mb-2">12</div>
                            <div class="text-sm font-bold uppercase tracking-widest opacity-60">Cities Reclaimed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Core Values: Bento Grid -->
    <section class="py-24 max-w-7xl mx-auto px-8">
        <h2 class="text-xs font-black uppercase tracking-widest text-primary mb-4 text-center">Our DNA</h2>
        <h3 class="text-4xl font-black text-center mb-16">Built on Principles.</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Transparency -->
            <div class="bg-white dark:bg-slate-800 p-10 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-primary text-3xl">visibility</span>
                </div>
                <h4 class="text-2xl font-bold mb-4">Transparency</h4>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    No hidden fees. No complicated leases. We believe the relationship between landlord and vendor should be a clear, open partnership.
                </p>
            </div>
            <!-- Community -->
            <div class="bg-primary text-slate-900 p-10 rounded-3xl shadow-xl shadow-primary/10 hover:scale-[1.02] transition-all duration-300">
                <div class="w-14 h-14 bg-slate-900/10 rounded-2xl flex items-center justify-center mb-8">
                    <span class="material-symbols-outlined text-slate-900 text-3xl" style="font-variation-settings: 'FILL' 1;">groups</span>
                </div>
                <h4 class="text-2xl font-bold mb-4">Community</h4>
                <p class="opacity-90 leading-relaxed">
                    A market is only as strong as its neighbors. We curate vendor ecosystems that complement rather than compete with each other.
                </p>
            </div>
            <!-- Innovation -->
            <div class="bg-white dark:bg-slate-800 p-10 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all duration-300 group">
                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-primary text-3xl">lightbulb</span>
                </div>
                <h4 class="text-2xl font-bold mb-4">Innovation</h4>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    From modular stall components to smart traffic analytics, we use technology to protect and enhance traditional commerce.
                </p>
            </div>
        </div>
    </section>
    <!-- Team Section -->
    <section class="py-24 bg-slate-900 text-white overflow-hidden relative">
        <div class="absolute top-0 right-0 w-1/2 h-full opacity-10 pointer-events-none">
            <div class="absolute inset-0 bg-gradient-to-l from-primary/20 to-transparent"></div>
        </div>
        <div class="max-w-7xl mx-auto px-8 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8">
                <div>
                    <h2 class="text-xs font-black uppercase tracking-widest text-primary mb-4">The Architects</h2>
                    <h3 class="text-5xl font-black">Meet the Visionaries.</h3>
                </div>
                <p class="text-slate-400 max-w-sm mb-2">A collective of urban planners, designers, and market veterans working to reclaim the streetscape.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Team Member 1 -->
                <div class="group">
                    <div class="relative overflow-hidden rounded-2xl mb-6">
                        <img alt="Marcus Chen" class="w-full aspect-[3/4] object-cover group-hover:scale-110 transition-transform duration-700" data-alt="Portrait of a male founder in a modern office" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDiN-sBQodYz7-bCXhI_7nNKE-xkvpOZIgnHwxQcYC1ujAP2wKnEfjWlKO4gGBEg4lt8wcYf9i_n9wegC5QJXGBerSItMY-4tNhNQzQkKKzxVx3FJ9j9tUsLKKLC4rXY6HbBOP-HM1QP_dtSC_17ek4n43XXVPt-2IT6v8SjqZVAwndelApac_oR_nSBkZCXJroUZcDD9YlYBvTzxsAdbZdQbMunR9PQFskX_dR9eS0OiK_Cp5Av4Fu6qDcx0B4-iXXvXcqI2Vghv9K" />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent opacity-60"></div>
                    </div>
                    <h5 class="text-xl font-bold">Marcus Chen</h5>
                    <p class="text-primary text-sm font-black tracking-widest uppercase">Founder &amp; CEO</p>
                </div>
                <!-- Team Member 2 -->
                <div class="group">
                    <div class="relative overflow-hidden rounded-2xl mb-6">
                        <img alt="Sarah Jenkins" class="w-full aspect-[3/4] object-cover group-hover:scale-110 transition-transform duration-700" data-alt="Portrait of a female architect" src="https://lh3.googleusercontent.com/aida-public/AB6AXuARZMZq7lAp--2xSE198ihGRODyuIwZJrjebpMZcHPSR8LvLsaj1QLt_osOFL7TGewpHvRF_tCenXKn3VCG9HmOBe-MeckOHpk8RpOSm5fle-AXwpOzxE8m89Mk45zHdPZn3Zzmy9wJEA7FZcPV4ABiZmghGeBwmjtbyibzdZgVICUSXJEtn16kOX7H01OMadaBikNEguKfeYa-tK8SNDcb5BPeVGOxur3tEWACkQTG1V7RaA4lr2j2Hzh2uNrdxD9Yu77FXuCXMXfw" />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent opacity-60"></div>
                    </div>
                    <h5 class="text-xl font-bold">Sarah Jenkins</h5>
                    <p class="text-primary text-sm font-black tracking-widest uppercase">Head of Design</p>
                </div>
                <!-- Team Member 3 -->
                <div class="group">
                    <div class="relative overflow-hidden rounded-2xl mb-6">
                        <img alt="David Okoro" class="w-full aspect-[3/4] object-cover group-hover:scale-110 transition-transform duration-700" data-alt="Portrait of a male operations leader" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCgMbhiuMzkaZ3aUQXXzZVTkAH84biQWfJadfbM0pa38MA7H70IYV1zTxJMUfGyIs4gQ6WxFaMgeLaVSfirLAFEKppS4Mk5FRMmtcK_o2NL7X9FPzWfHH-Uo3ro1Farj3VjrYVnysqGD5mvPJsE2k9xHiJEgOKXNSafrYMvcV9rrAagBcgFRGZtN05epSTqAoLzyQdRpp0SW1NuiEAzYmSPDeLeTgXnupdGur7Tg6jFwe8FbtlJOF0T4WKP9BG8_2u2ZW7NKHDIeMMn" />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent opacity-60"></div>
                    </div>
                    <h5 class="text-xl font-bold">David Okoro</h5>
                    <p class="text-primary text-sm font-black tracking-widest uppercase">Operations</p>
                </div>
                <!-- Team Member 4 -->
                <div class="group">
                    <div class="relative overflow-hidden rounded-2xl mb-6">
                        <img alt="Elena Rodriguez" class="w-full aspect-[3/4] object-cover group-hover:scale-110 transition-transform duration-700" data-alt="Portrait of a female community manager" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA3smjRMR_KyZLDqmL9WBjZ_PI4uOT4DQWRGBOVEXXP7IFlpoJcO5XHZTiU6Wg8BUHOGAlV_2FhxEpoQBQShSFhLM0Iv2zgL82p8cDrKGnvFCPnFBEWsSGdXxYpDUfUP2evG1flfQ4wGCi7-0JGgwn8oz9z0m1VaJKOU1CKMuujv7JAnqyAyXdOsfEOle8w4WsUXion6T1S88W1MZ5pQE7pYOp9E1O4sxarOUVjKaBpSUrtxLv_HG0J_tnMlG8GImMHYNeRH_5DbXAT" />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent opacity-60"></div>
                    </div>
                    <h5 class="text-xl font-bold">Elena Rodriguez</h5>
                    <p class="text-primary text-sm font-black tracking-widest uppercase">Community Liaison</p>
                </div>
            </div>
        </div>
    </section>
    <!-- CTA Section -->
    <section class="py-24 px-8 max-w-7xl mx-auto">
        <div class="bg-primary/15 dark:bg-primary/10 rounded-[2rem] p-12 md:p-20 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-64 h-64 bg-primary/20 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
            <div class="relative z-10">
                <h2 class="text-4xl md:text-6xl font-black tracking-tight mb-8">Ready to secure your spot in the future of retail?</h2>
                <div class="flex flex-col md:flex-row justify-center gap-4">
                    <button class="bg-slate-900 text-white dark:bg-white dark:text-slate-900 px-10 py-5 rounded-2xl font-bold text-lg hover:shadow-2xl transition-all active:scale-95">Explore Available Stalls</button>
                    <button class="bg-transparent border-2 border-slate-300 text-slate-700 dark:border-slate-600 dark:text-slate-200 px-10 py-5 rounded-2xl font-bold text-lg hover:bg-slate-200/60 dark:hover:bg-slate-700/50 transition-all">Partner With Us</button>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection