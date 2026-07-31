<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>TITA Finance | Clarity for Small Business</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-outfit antialiased bg-slate-950 text-slate-200 selection:bg-emerald-500/30 selection:text-emerald-400">
        <div class="relative min-h-screen flex flex-col overflow-hidden">
            
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-emerald-500/10 rounded-full blur-[120px]"></div>
                <div class="absolute top-[20%] -right-[10%] w-[40%] h-[40%] bg-cyan-500/10 rounded-full blur-[120px]"></div>
                <div class="absolute -bottom-[10%] left-[20%] w-[40%] h-[40%] bg-indigo-500/10 rounded-full blur-[120px]"></div>
            </div>

            <!-- Navigation -->
            <nav class="relative z-20 px-6 py-8">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center gap-2 group cursor-default">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-cyan-500 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                            <svg class="w-6 h-6 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400 tracking-tight">TITA Finance</span>
                    </div>

                    <div class="flex items-center gap-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-full bg-slate-900 border border-slate-800 text-sm font-medium hover:border-emerald-500/50 transition-all duration-300">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-medium hover:text-white transition-colors">Log In</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full bg-emerald-500 text-slate-950 text-sm font-bold shadow-lg shadow-emerald-500/20 hover:bg-emerald-400 hover:scale-105 active:scale-95 transition-all duration-300">Get Started</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <main class="relative z-10 flex-grow flex flex-col items-center justify-center px-6 text-center">
                <div class="max-w-4xl mx-auto">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold uppercase tracking-widest mb-8 animate-pulse">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Trusted by Smart Businesses
                    </div>
                    
                    <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 tracking-tight leading-tight">
                        Financial Clarity for <br>
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 via-cyan-400 to-indigo-400">Small Business Owners.</span>
                    </h1>
                    
                    <p class="text-xl text-slate-400 mb-12 max-w-2xl mx-auto leading-relaxed">
                        Say goodbye to complex spreadsheets. Track invoices, manage payments, and monitor your profit with the most simplified finance system ever built.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-20">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-emerald-500 text-slate-950 font-bold text-lg shadow-xl shadow-emerald-500/20 hover:bg-emerald-400 hover:-translate-y-1 transition-all duration-300">
                            Create Your Account
                        </a>
                        <a href="#features" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-slate-900 border border-slate-800 font-semibold text-lg hover:bg-slate-800 transition-all duration-300">
                            Explore Features
                        </a>
                    </div>

                    <!-- App Preview / Features -->
                    <div id="features" class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                        <div class="p-8 rounded-3xl bg-slate-900/50 border border-slate-800/50 hover:border-emerald-500/30 transition-all">
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 mb-6">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Smart Invoicing</h3>
                            <p class="text-slate-500 text-sm">Generate professional PDF invoices and Statement of Accounts in one click.</p>
                        </div>

                        <div class="p-8 rounded-3xl bg-slate-900/50 border border-slate-800/50 hover:border-cyan-500/30 transition-all">
                            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 flex items-center justify-center text-cyan-400 mb-6">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Payment Mastery</h3>
                            <p class="text-slate-500 text-sm">Track Cash, GCash, and Bank transfers. Support for installments and partials.</p>
                        </div>

                        <div class="p-8 rounded-3xl bg-slate-900/50 border border-slate-800/50 hover:border-indigo-500/30 transition-all">
                            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 mb-6">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Auto Control</h3>
                            <p class="text-slate-500 text-sm">Automated overdue detection, penalties, and email reminders to customers.</p>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="relative z-10 py-12 border-t border-slate-900">
                <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="text-slate-500 text-sm">
                        &copy; {{ date('Y') }} TITA Finance System. Designed for growth.
                    </div>
                    <div class="flex items-center gap-6 text-sm font-medium text-slate-400">
                        <a href="#" class="hover:text-emerald-400 transition-colors">Privacy</a>
                        <a href="#" class="hover:text-emerald-400 transition-colors">Terms</a>
                        <a href="#" class="hover:text-emerald-400 transition-colors">Support</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>

