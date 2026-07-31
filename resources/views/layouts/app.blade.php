<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TITA') }} - @yield('title', 'Finance')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-beige-50 text-beige-900" x-data="{ sidebarOpen: false }">
        <div class="flex min-h-screen">
            <!-- Sidebar Desktop -->
            <div class="hidden lg:block fixed inset-y-0 left-0 z-40 w-64">
                @include('layouts.sidebar')
            </div>

            <!-- Mobile Sidebar -->
            <div x-show="sidebarOpen" 
                 class="fixed inset-0 z-50 lg:hidden" 
                 role="dialog" aria-modal="true">
                <!-- Backdrop -->
                <div x-show="sidebarOpen" 
                     x-transition:enter="transition-opacity ease-linear duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="transition-opacity ease-linear duration-300" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-mint-900/60 backdrop-blur-sm"></div>

                <!-- Sidebar Content -->
                <div x-show="sidebarOpen" 
                     x-transition:enter="transition ease-in-out duration-300 transform" 
                     x-transition:enter-start="-translate-x-full" 
                     x-transition:enter-end="translate-x-0" 
                     x-transition:leave="transition ease-in-out duration-300 transform" 
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="-translate-x-full" 
                     class="relative flex w-full max-w-xs flex-1 flex-col h-full focus:outline-none"
                     @click.away="sidebarOpen = false">
                    <div class="absolute right-0 top-0 -mr-12 pt-2">
                        <button type="button" @click="sidebarOpen = false" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @include('layouts.sidebar')
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-w-0 lg:ml-64">
                <!-- Top Bar -->
                <header class="sticky top-0 z-30 bg-white/70 backdrop-blur-xl border-b border-beige-200/50">
                    <div class="flex items-center justify-between h-16 px-6">
                        <div class="flex items-center gap-3">
                            <button @click="sidebarOpen = true" class="lg:hidden text-beige-600 hover:text-mint-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                            <h1 class="text-xl font-bold text-mint-900">@yield('page-title', 'Dashboard')</h1>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium text-beige-500">{{ now()->format('M d, Y') }}</span>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-beige-100 transition-colors">
                                    <div class="w-9 h-9 rounded-xl bg-mint-500 flex items-center justify-center text-sm font-bold text-white shadow-sm">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-semibold text-mint-900 hidden sm:block">{{ Auth::user()->name }}</span>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                     class="absolute right-0 mt-2 w-48 bg-white border border-beige-200 rounded-xl shadow-lg py-1 z-50">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-beige-700 hover:bg-beige-50 hover:text-mint-700 transition-colors">Profile</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-beige-700 hover:bg-beige-50 hover:text-mint-700 transition-colors">Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Flash Messages -->
                @if(session('success'))
                <div class="mx-6 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
                    <div class="flex items-center gap-3 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm">{{ session('success') }}</span>
                    </div>
                </div>
                @endif

                @if($errors->any())
                <div class="mx-6 mt-4">
                    <div class="px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400">
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Page Content -->
                <main class="flex-1 p-4 sm:p-6 w-full max-w-full overflow-x-hidden min-h-screen">
                    {{ $slot ?? '' }}
                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>

