<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TITA') }} - @yield('title', 'Finance')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800,900" rel="stylesheet" />

        <!-- TomSelect (Searchable Dropdowns) -->
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')

        <style>
            /* Premium header enhancements */
            .header-search-input::placeholder { color: #AA8D63; opacity: 0.7; }
            .header-search-input:focus { outline: none; }

            /* Animated counter */
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(6px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            .fade-in-up {
                animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            /* Premium TomSelect Overrides */
            .ts-control {
                background-color: transparent !important;
                border: none !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            .ts-dropdown {
                border-radius: 16px !important;
                border: 1px solid rgba(210,194,168,0.5) !important;
                box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
                overflow: hidden;
            }
            .ts-dropdown .option {
                padding: 10px 16px !important;
                font-family: 'Outfit', sans-serif !important;
                font-size: 14px;
                color: #155541;
            }
            .ts-dropdown .option.active {
                background-color: #DDF6ED !important;
                color: #1CA074 !important;
                font-weight: 700;
            }
            .ts-wrapper.single .ts-control {
                display: flex;
                align-items: center;
                height: 100%;
            }

            /* Notification pulse dot */
            @keyframes ping-soft {
                0%, 100% { opacity: 1; transform: scale(1); }
                50%       { opacity: 0.6; transform: scale(1.5); }
            }
            .notif-pulse { animation: ping-soft 2s ease-in-out infinite; }
        </style>
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
                <!-- Premium Top Header -->
                <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-2xl border-b border-beige-200/60 shadow-sm">
                    <div class="flex items-center justify-between h-[68px] px-4 sm:px-6 gap-3">

                        <!-- Left: Hamburger + Page Title -->
                        <div class="flex items-center gap-3 min-w-0">
                            <button @click="sidebarOpen = true"
                                    class="lg:hidden flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-xl text-beige-500 hover:text-mint-700 hover:bg-beige-100 transition-all duration-200"
                                    id="mobile-sidebar-toggle"
                                    aria-label="Open navigation">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            <div class="min-w-0">
                                <h1 class="text-lg sm:text-xl font-extrabold text-mint-900 leading-tight tracking-tight truncate">@yield('page-title', 'Dashboard')</h1>
                                <p class="text-[10px] font-semibold text-beige-400 uppercase tracking-widest hidden sm:block">
                                    {{ now()->format('l, F j Y') }}
                                </p>
                            </div>
                        </div>

                        <!-- Center: Search Bar (hidden on small screens) -->
                        <div class="hidden md:flex flex-1 max-w-xs xl:max-w-sm mx-4">
                            <div class="relative w-full" x-data="{ searchFocused: false }">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none transition-colors duration-200"
                                     :class="searchFocused ? 'text-mint-500' : 'text-beige-400'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="search"
                                       id="global-search"
                                       placeholder="Search invoices, customers…"
                                       class="header-search-input w-full pl-9 pr-4 py-2 bg-beige-50/80 border border-beige-200/70 rounded-xl text-sm text-mint-900 font-medium placeholder-beige-400 focus:bg-white focus:border-mint-400 focus:ring-2 focus:ring-mint-500/15 transition-all duration-200"
                                       @focus="searchFocused = true"
                                       @blur="searchFocused = false">
                            </div>
                        </div>

                        <!-- Right: Actions + User -->
                        <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">

                            <!-- Notification Bell -->
                            <div class="relative" x-data="{ notifOpen: false }">
                                <button @click="notifOpen = !notifOpen"
                                        id="notification-button"
                                        class="relative w-9 h-9 flex items-center justify-center rounded-xl text-beige-500 hover:text-mint-700 hover:bg-beige-100 transition-all duration-200"
                                        aria-label="Notifications">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    @if($overdueCount ?? 0 > 0)
                                    <span class="absolute top-1.5 right-1.5 flex">
                                        <span class="notif-pulse absolute inline-flex h-2 w-2 rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                    </span>
                                    @endif
                                </button>

                                <!-- Notification Dropdown -->
                                <div x-show="notifOpen"
                                     @click.away="notifOpen = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                     class="absolute right-0 mt-2 w-72 bg-white border border-beige-200/70 rounded-2xl shadow-xl shadow-mint-900/8 py-2 z-50 overflow-hidden"
                                     style="display: none;">
                                    <div class="px-4 py-2 border-b border-beige-100">
                                        <p class="text-xs font-bold text-mint-900 uppercase tracking-wider">Alerts</p>
                                    </div>
                                    @if(($overdueCount ?? 0) > 0)
                                    <a href="{{ route('invoices.index') }}" class="flex items-start gap-3 px-4 py-3 hover:bg-beige-50 transition-colors">
                                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-mint-900">{{ $overdueCount ?? 0 }} Overdue Invoice{{ ($overdueCount ?? 0) !== 1 ? 's' : '' }}</p>
                                            <p class="text-xs text-beige-500 mt-0.5">Requires immediate attention</p>
                                        </div>
                                    </a>
                                    @else
                                    <div class="px-4 py-6 text-center">
                                        <div class="w-10 h-10 rounded-xl bg-mint-50 flex items-center justify-center mx-auto mb-2">
                                            <svg class="w-5 h-5 text-mint-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <p class="text-sm font-bold text-mint-900">All Clear!</p>
                                        <p class="text-xs text-beige-500 mt-0.5">No pending alerts</p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="w-px h-5 bg-beige-200 hidden sm:block"></div>

                            <!-- User Profile Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                        id="user-profile-button"
                                        class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-xl hover:bg-beige-100 transition-all duration-200 group"
                                        aria-haspopup="true"
                                        :aria-expanded="open">
                                    <!-- Avatar -->
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-mint-400 to-mint-600 flex items-center justify-center text-sm font-bold text-white shadow-sm ring-2 ring-white group-hover:ring-mint-200 transition-all duration-200">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <div class="hidden sm:block text-left">
                                        <p class="text-xs font-bold text-mint-900 leading-tight max-w-[100px] truncate">{{ Auth::user()->name }}</p>
                                        <p class="text-[10px] text-beige-400 font-semibold leading-tight">{{ business()?->type ?? 'Business' }}</p>
                                    </div>
                                    <svg class="w-3.5 h-3.5 text-beige-400 hidden sm:block transition-transform duration-200"
                                         :class="open ? 'rotate-180' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <!-- User Dropdown Panel -->
                                <div x-show="open"
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                     class="absolute right-0 mt-2 w-56 bg-white border border-beige-200/70 rounded-2xl shadow-xl shadow-mint-900/8 py-2 z-50 overflow-hidden"
                                     style="display: none;">
                                    <!-- User Info Header -->
                                    <div class="px-4 pb-2 pt-1 border-b border-beige-100">
                                        <p class="text-sm font-bold text-mint-900 truncate">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-beige-400 truncate">{{ Auth::user()->email }}</p>
                                    </div>
                                    <div class="py-1">
                                        <a href="{{ route('profile.edit') }}"
                                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold text-beige-700 hover:bg-beige-50 hover:text-mint-700 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            Profile Settings
                                        </a>
                                        <a href="{{ route('settings.index') }}"
                                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold text-beige-700 hover:bg-beige-50 hover:text-mint-700 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Settings
                                        </a>
                                    </div>
                                    <div class="pt-1 border-t border-beige-100">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                    class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm font-semibold text-red-500 hover:bg-red-50 hover:text-red-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                                </svg>
                                                Sign Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>



                <!-- Page Content -->
                <main class="flex-1 p-4 sm:p-6 w-full max-w-full overflow-x-hidden min-h-screen">
                    {{ $slot ?? '' }}
                    @yield('content')
                </main>
            </div>
        </div>

        <!-- Global Toast Container -->
        <div x-data="{ toasts: [] }" 
             @notify.window="toasts.push({ id: Date.now(), msg: $event.detail.msg, type: $event.detail.type || 'success' }); setTimeout(() => toasts.shift(), 4000)"
             x-init="$nextTick(() => {
                @if(session('success'))
                    window.dispatchEvent(new CustomEvent('notify', { detail: { msg: '{{ addslashes(session('success')) }}', type: 'success' } }));
                @endif
                @if(session('error'))
                    window.dispatchEvent(new CustomEvent('notify', { detail: { msg: '{{ addslashes(session('error')) }}', type: 'error' } }));
                @endif
                @if($errors->any())
                    window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'Please check the form for errors.', type: 'error' } }));
                @endif
             })"
             class="fixed top-20 right-6 z-[9999] flex flex-col gap-3 pointer-events-none">
            <template x-for="toast in toasts" :key="toast.id">
                <div class="fade-in-up flex items-center gap-3 px-5 py-4 rounded-2xl shadow-2xl border pointer-events-auto transition-all"
                     :class="toast.type === 'error' ? 'bg-red-50 border-red-200 text-red-800 shadow-red-900/10' : 'bg-mint-50 border-mint-200 text-mint-900 shadow-mint-900/10'"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2">
                    
                    <svg x-show="toast.type !== 'error'" class="w-5 h-5 flex-shrink-0 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <svg x-show="toast.type === 'error'" class="w-5 h-5 flex-shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    
                    <span class="text-sm font-bold tracking-wide" x-text="toast.msg"></span>
                    
                    <button @click="toasts = toasts.filter(t => t.id !== toast.id)" class="ml-4 opacity-50 hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>

        <!-- Global Confirm Modal -->
        <div x-data="{ 
                show: false, 
                title: '', 
                message: '', 
                confirmText: 'Confirm', 
                cancelText: 'Cancel',
                confirmType: 'danger',
                onConfirm: null 
             }" 
             @confirm.window="
                show = true;
                title = $event.detail.title;
                message = $event.detail.message;
                confirmText = $event.detail.confirmText || 'Confirm';
                cancelText = $event.detail.cancelText || 'Cancel';
                confirmType = $event.detail.confirmType || 'danger';
                onConfirm = $event.detail.onConfirm;
             "
             x-show="show" 
             style="display: none;"
             class="fixed inset-0 z-[10000] overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-mint-900/60 backdrop-blur-sm" @click="show = false"></div>
                
                <div class="inline-block w-full max-w-md p-6 my-4 md:p-8 md:my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-[2rem] md:rounded-[3rem] fade-in-up">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                             :class="confirmType === 'danger' ? 'bg-red-100 text-red-600' : (confirmType === 'warning' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600')">
                            <svg x-show="confirmType === 'danger'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <svg x-show="confirmType === 'warning'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <svg x-show="confirmType === 'info'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-mint-950 uppercase tracking-widest" x-text="title"></h3>
                        </div>
                    </div>
                    
                    <p class="text-sm font-medium text-beige-500 mb-8" x-text="message"></p>
                    
                    <div class="flex flex-col-reverse sm:flex-row gap-4">
                        <button type="button" @click="show = false" 
                                class="w-full sm:flex-1 px-6 py-4 bg-beige-100 text-beige-600 text-[10px] font-black uppercase tracking-widest rounded-3xl hover:bg-beige-200 transition-all"
                                x-text="cancelText"></button>
                        <button type="button" @click="if(onConfirm) onConfirm(); show = false;" 
                                class="w-full sm:flex-[2] px-6 py-4 text-white text-[10px] font-black uppercase tracking-widest rounded-3xl transition-all shadow-lg"
                                :class="confirmType === 'danger' ? 'bg-red-500 hover:bg-red-600 shadow-red-500/20' : (confirmType === 'warning' ? 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/20' : 'bg-mint-600 hover:bg-mint-700 shadow-mint-500/20')"
                                x-text="confirmText"></button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <script>
            // Automatically initialize TomSelect on any element with .searchable-select
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.searchable-select').forEach(el => {
                    new TomSelect(el, {
                        create: false,
                        sortField: { field: "text", direction: "asc" }
                    });
                });
            });
        </script>
        @stack('scripts')
    </body>
</html>
