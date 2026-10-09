<x-guest-layout>
    @section('seo_title', 'Retunly — AI-Powered Returns Management for E-Commerce')
    @section('seo_description', 'Automate your e-commerce returns with AI photo verification, fraud detection, and smart customer replies. Save hundreds of hours on support. Start free.')
    @section('seo_canonical', 'https://retunly.tech')

    @push('json_ld')
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "Retunly",
        "url": "https://retunly.tech",
        "description": "AI-powered returns management platform for e-commerce businesses. Automate photo verification, fraud detection, and customer replies.",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "offers": [
            {
                "@type": "Offer",
                "name": "Starter",
                "price": "99000",
                "priceCurrency": "IDR",
                "priceValidUntil": "{{ date('Y-12-31') }}",
                "description": "Up to 100 returns/month, self-serve portal, staff dashboard"
            },
            {
                "@type": "Offer",
                "name": "Pro",
                "price": "299000",
                "priceCurrency": "IDR",
                "priceValidUntil": "{{ date('Y-12-31') }}",
                "description": "Up to 1000 returns/month, AI Vision analysis, CS Copilot, fraud scoring"
            }
        ],
        "creator": {
            "@type": "Organization",
            "name": "Retunly",
            "url": "https://retunly.tech"
        },
        "featureList": [
            "AI Vision photo verification",
            "Automated fraud detection",
            "CS Copilot auto-drafting",
            "Self-serve customer portal",
            "Multi-language support (EN/ID)"
        ]
    }
    </script>
    @endpush

    <div class="flex min-h-screen flex-col bg-white text-slate-900 font-sans selection:bg-brand-100 selection:text-brand-900">
        
        {{-- ===== NAVBAR ===== --}}
        <header class="sticky top-0 z-50 w-full border-b border-slate-200 bg-white/90 backdrop-blur-md">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="/" class="flex items-center gap-2">
                    <svg class="h-6 w-6 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span class="font-display text-xl font-bold tracking-tight text-slate-900">Retunly</span>
                </a>
                <nav class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">{{ __('Platform') }}</a>
                    <a href="#how-it-works" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">{{ __('How it works') }}</a>
                    <a href="#pricing" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">{{ __('Pricing') }}</a>
                </nav>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-sm font-medium">
                        <a href="{{ route('lang.switch', 'id') }}" class="transition-colors {{ session('locale', config('app.locale')) === 'id' ? 'text-slate-900 font-bold' : 'text-slate-400 hover:text-slate-600' }}">ID</a>
                        <span class="text-slate-300">|</span>
                        <a href="{{ route('lang.switch', 'en') }}" class="transition-colors {{ session('locale', config('app.locale')) === 'en' ? 'text-slate-900 font-bold' : 'text-slate-400 hover:text-slate-600' }}">EN</a>
                    </div>
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 hidden sm:block transition-colors">{{ __('Log in') }}</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                        {{ __('Get Started') }}
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-1">
            {{-- ===== HERO ===== --}}
            <section class="relative pt-16 pb-20 lg:pt-24 lg:pb-28 overflow-hidden">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-center">
                        <div class="sm:text-center md:mx-auto lg:col-span-6 lg:text-left">
                            <div class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-sm font-medium text-slate-600 mb-6">
                                <span class="flex h-2 w-2 rounded-full bg-brand-500 mr-2"></span>
                                {{ __('Now powered by Next-Gen AI') }}
                            </div>
                            
                            <h1 class="text-4xl font-display font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-6xl leading-tight">
                                {{ __('Automate your') }} <br class="hidden lg:block"/>
                                <span class="text-brand-600">{{ __('returns workflow') }}</span>
                            </h1>
                            
                            <p class="mt-6 text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                                {{ __('Retunly is the enterprise-grade platform that uses computer vision to verify damaged goods, draft empathetic replies, and process refunds automatically. Save hours on customer support.') }}
                            </p>
                            
                            <div class="mt-8 sm:flex sm:justify-center lg:justify-start gap-4">
                                <a href="{{ route('register') }}" class="flex items-center justify-center rounded-lg bg-brand-600 px-8 py-3 text-base font-medium text-white transition-colors hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-600 focus:ring-offset-2">
                                    Start 14-day free trial
                                </a>
                                <a href="{{ route('portal.create') }}" class="mt-3 sm:mt-0 flex items-center justify-center rounded-lg border border-slate-300 bg-white px-8 py-3 text-base font-medium text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-brand-600 focus:ring-offset-2">
                                    View customer portal
                                </a>
                            </div>
                            <p class="mt-4 text-sm text-slate-500">{{ __('No credit card required. Setup in 5 minutes.') }}</p>
                        </div>
                        
                        <div class="mt-16 sm:mt-24 lg:col-span-6 lg:mt-0 relative">
                            <div class="relative mx-auto w-full rounded-lg shadow-lg border border-slate-200 lg:max-w-md bg-white overflow-hidden">
                                <div class="bg-slate-100 border-b border-slate-200 px-4 py-2 flex items-center gap-2">
                                    <div class="h-3 w-3 rounded-full bg-slate-300"></div>
                                    <div class="h-3 w-3 rounded-full bg-slate-300"></div>
                                    <div class="h-3 w-3 rounded-full bg-slate-300"></div>
                                </div>
                                <div class="bg-white flex flex-col h-full">
                                    <div class="border-b border-slate-200 px-4 py-3 flex justify-between items-center bg-slate-50">
                                        <div class="font-semibold text-slate-800 text-sm flex items-center gap-2">
                                            <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            Returns Dashboard
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center rounded-md bg-brand-50 px-2 py-1 text-[10px] font-medium text-brand-700 ring-1 ring-inset ring-brand-600/20">AI Copilot Active</span>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-slate-50/50 flex-1">
                                        <div class="grid grid-cols-3 gap-3 mb-4">
                                            <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm">
                                                <div class="text-[10px] text-slate-500 mb-1 uppercase tracking-wider font-semibold">Pending</div>
                                                <div class="text-xl font-display font-bold text-amber-600">12</div>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm">
                                                <div class="text-[10px] text-slate-500 mb-1 uppercase tracking-wider font-semibold">Auto-Approved</div>
                                                <div class="text-xl font-display font-bold text-green-600">89%</div>
                                            </div>
                                            <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm">
                                                <div class="text-[10px] text-slate-500 mb-1 uppercase tracking-wider font-semibold">Fraud Blocked</div>
                                                <div class="text-xl font-display font-bold text-red-600">14</div>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
                                            <table class="w-full text-left text-xs">
                                                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500">
                                                    <tr>
                                                        <th class="px-3 py-2 font-medium">ID</th>
                                                        <th class="px-3 py-2 font-medium">Item</th>
                                                        <th class="px-3 py-2 font-medium text-right">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                                    <tr class="bg-brand-50/30">
                                                        <td class="px-3 py-2.5 font-mono text-brand-600 font-medium">#RET-090</td>
                                                        <td class="px-3 py-2.5 font-medium text-slate-800">MacBook Pro</td>
                                                        <td class="px-3 py-2.5 text-right"><span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-800">AI Reviewing</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-3 py-2.5 font-mono text-slate-500">#RET-089</td>
                                                        <td class="px-3 py-2.5 font-medium text-slate-800">Nike Air Max</td>
                                                        <td class="px-3 py-2.5 text-right"><span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-semibold text-green-800">Approved</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="px-3 py-2.5 font-mono text-slate-500">#RET-088</td>
                                                        <td class="px-3 py-2.5 font-medium text-slate-800">Basic T-Shirt</td>
                                                        <td class="px-3 py-2.5 text-right"><span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-semibold text-red-800">Rejected</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <div class="mt-4 flex justify-end">
                                             <div class="bg-white border border-brand-200 rounded-lg p-3 text-xs text-slate-600 max-w-[280px] shadow-md relative">
                                                 <div class="absolute -top-2 -left-2 bg-brand-500 text-white rounded-full p-1.5 shadow-sm border-2 border-white">
                                                     <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                 </div>
                                                 <div class="mb-1 text-[10px] uppercase tracking-wider font-bold text-brand-600">Vision Analysis Complete</div>
                                                 <p class="leading-relaxed">Damaged screen verified via customer photo. Fraud score is <span class="font-bold text-green-600">Low (2%)</span>. Auto-refund initiated.</p>
                                             </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>



            {{-- ===== FEATURES ===== --}}
            <section id="features" class="py-20 bg-white">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="max-w-3xl mb-16">
                        <h2 class="text-3xl font-display font-bold text-slate-900 sm:text-4xl">{{ __('Everything you need to handle returns at scale.') }}</h2>
                        <p class="mt-4 text-lg text-slate-600">{{ __('Replace manual workflows with a streamlined, AI-assisted process that your team and customers will love.') }}</p>
                    </div>

                    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                        {{-- Feature 1 --}}
                        <div class="border border-slate-200 rounded-lg p-6 bg-white shadow-sm">
                            <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center mb-4 border border-slate-200">
                                <svg class="w-5 h-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Automated Vision Analysis</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Our AI Vision instantly analyzes customer-uploaded photos. It detects damages, verifies conditions, and provides a fraud probability score before your team even looks at the ticket.</p>
                        </div>

                        {{-- Feature 2 --}}
                        <div class="border border-slate-200 rounded-lg p-6 bg-white shadow-sm">
                            <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center mb-4 border border-slate-200">
                                <svg class="w-5 h-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">CS Copilot</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Stop writing repetitive emails. Our AI reads the complaint history, understands context, and drafts a professional, policy-compliant reply. You just review and send.</p>
                        </div>

                        {{-- Feature 3 --}}
                        <div class="border border-slate-200 rounded-lg p-6 bg-white shadow-sm">
                            <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center mb-4 border border-slate-200">
                                <svg class="w-5 h-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Self-Serve Portal</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Provide customers with a clean, branded portal to submit returns. They don't need to register an account—just an email and order number to get started.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ===== PRICING ===== --}}
            <section id="pricing" class="py-20 bg-slate-50 border-t border-slate-200">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <h2 class="text-3xl font-display font-bold text-slate-900 sm:text-4xl">Simple, predictable pricing</h2>
                        <p class="mt-4 text-lg text-slate-600">Start for free, then choose a plan that scales with your volume.</p>
                    </div>

                    <div class="grid gap-8 md:grid-cols-3 max-w-6xl mx-auto items-stretch">
                        {{-- Starter --}}
                        <div class="bg-white border border-slate-200 rounded-lg p-8 shadow-sm flex flex-col">
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Starter</h3>
                            <p class="text-slate-500 text-sm mb-6 flex-1">For emerging brands.</p>
                            <div class="text-3xl font-display font-bold text-slate-900 mb-8">Rp 99k<span class="text-base font-normal text-slate-500">/bln</span></div>
                            <ul class="space-y-4 mb-8 text-sm text-slate-700">
                                <li class="flex items-start gap-3"><svg class="w-5 h-5 text-brand-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Up to 100 returns/month</li>
                                <li class="flex items-start gap-3"><svg class="w-5 h-5 text-brand-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Self-serve customer portal</li>
                                <li class="flex items-start gap-3"><svg class="w-5 h-5 text-brand-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Staff dashboard</li>
                            </ul>
                            <a href="{{ route('register') }}" class="mt-auto block w-full text-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">{{ __('Start 14-day free trial') }}</a>
                        </div>

                        {{-- Pro --}}
                        <div class="bg-slate-900 border border-slate-800 rounded-lg p-8 shadow-md relative flex flex-col">
                            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 rounded-full bg-brand-500 px-3 py-1 text-xs font-semibold text-white">Most Popular</div>
                            <h3 class="text-xl font-bold text-white mb-2">Pro</h3>
                            <p class="text-slate-400 text-sm mb-6 flex-1">Full automation for growing teams.</p>
                            <div class="text-3xl font-display font-bold text-white mb-8">Rp 299k<span class="text-base font-normal text-slate-400">/bln</span></div>
                            <ul class="space-y-4 mb-8 text-sm text-slate-300">
                                <li class="flex items-start gap-3"><svg class="w-5 h-5 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Up to 1,000 returns/month</li>
                                <li class="flex items-start gap-3"><svg class="w-5 h-5 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> <b>AI Vision photo analysis</b></li>
                                <li class="flex items-start gap-3"><svg class="w-5 h-5 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> <b>CS Copilot auto-drafting</b></li>
                                <li class="flex items-start gap-3"><svg class="w-5 h-5 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Fraud probability scoring</li>
                            </ul>
                            <a href="{{ route('register') }}" class="mt-auto block w-full text-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 transition-colors">{{ __('Start 14-day free trial') }}</a>
                        </div>

                        {{-- Custom --}}
                        <div class="bg-white border border-slate-200 rounded-lg p-8 shadow-sm flex flex-col">
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Custom</h3>
                            <p class="text-slate-500 text-sm mb-6 flex-1">For high-volume enterprise retailers.</p>
                            <div class="text-3xl font-display font-bold text-slate-900 mb-8">Hubungi Kami</div>
                            <ul class="space-y-4 mb-8 text-sm text-slate-700">
                                <li class="flex items-start gap-3"><svg class="w-5 h-5 text-brand-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Unlimited returns</li>
                                <li class="flex items-start gap-3"><svg class="w-5 h-5 text-brand-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Custom ERP integrations</li>
                                <li class="flex items-start gap-3"><svg class="w-5 h-5 text-brand-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Dedicated account manager</li>
                                <li class="flex items-start gap-3"><svg class="w-5 h-5 text-brand-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Custom AI model training</li>
                            </ul>
                            <a href="mailto:support@retunly.tech" class="mt-auto block w-full text-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Kirim Email</a>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ===== CTA ===== --}}
            <section class="py-20 bg-white border-t border-slate-200">
                <div class="mx-auto max-w-4xl px-4 text-center">
                    <h2 class="text-3xl font-display font-bold text-slate-900 sm:text-4xl">Ready to streamline your operations?</h2>
                    <p class="mt-4 text-lg text-slate-600 mb-8">
                        Join modern brands saving hundreds of hours on support every month.
                    </p>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-6 py-3 text-base font-medium text-white transition-colors hover:bg-slate-800">
                        Create your free account
                    </a>
                </div>
            </section>
        </main>

        @include('portal.partials.footer')

    </div>
</x-guest-layout>