<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TITA') }} — Institutional Finance</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-outfit text-mint-900 antialiased bg-beige-50 selection:bg-mint-500/10 selection:text-mint-600">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-mint-500/5 rounded-full blur-[120px]"></div>
                <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-beige-200/20 rounded-full blur-[120px]"></div>
            </div>

            <div class="relative z-10 w-full flex flex-col items-center px-4">
                <a href="/" class="flex flex-col items-center gap-4 mb-10 group">
                    <div class="w-16 h-16 rounded-[2rem] bg-mint-900 flex items-center justify-center shadow-2xl shadow-mint-950/20 group-hover:scale-105 transition-all duration-500 ring-4 ring-white">
                        <svg class="w-9 h-9 text-beige-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="text-center">
                        <h1 class="text-3xl font-black text-mint-950 tracking-tighter">TITA<span class="text-mint-500">.</span></h1>
                        <p class="text-[9px] font-black text-beige-400 uppercase tracking-widest mt-1">Transaction, Inventory, Tracking & Analytics</p>
                    </div>
                </a>

                <div class="w-full sm:max-w-md px-10 py-12 bg-white/80 backdrop-blur-xl border border-beige-200/50 shadow-[0_32px_64px_-16px_rgba(16,185,129,0.1)] rounded-[3rem]">
                    {{ $slot }}
                </div>
                
                <div class="mt-12 flex items-center gap-6 text-[10px] font-black text-beige-400 uppercase tracking-widest">
                    <span>&copy; {{ date('Y') }} TITA</span>
                    <span class="w-1 h-1 rounded-full bg-beige-200"></span>
                    <span>Institutional Grade</span>
                </div>
            </div>
        </div>
    </body>
</html>

