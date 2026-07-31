@php
    $currentRoute = Route::currentRouteName();
    $biz = business();
    $isRestaurant = $biz && $biz->isRestaurant();
    $isSariSari = !$isRestaurant;

    $navItems = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>', 'match' => 'dashboard', 'modes' => ['sari_sari']],
        ['route' => 'pos.index', 'label' => 'Point of Sale', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>', 'match' => 'pos.*', 'modes' => ['sari_sari']],
        ['route' => 'tables.index', 'label' => 'Tables', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>', 'match' => 'tables.*', 'modes' => ['hidden']],
        ['route' => 'orders.index', 'label' => 'Orders', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>', 'match' => 'orders.*', 'modes' => ['hidden']],
        ['route' => 'kitchen.index', 'label' => 'Kitchen', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>', 'match' => 'kitchen.*', 'modes' => ['hidden']],
        ['route' => 'profit-dashboard.index', 'label' => 'Smart Dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18M7 7h8a4 4 0 010 8H7"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21l3-3m0 0l-3-3m3 3h-7"/>', 'match' => 'profit-dashboard.*', 'modes' => ['restaurant']],
        ['route' => 'products.index', 'label' => 'Products', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>', 'match' => 'products.*', 'modes' => ['all']],
        ['route' => 'smart-pricing.index', 'label' => 'Smart Pricing', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7l2 2m6 6l2 2m0-10l-2 2M9 15l-2 2"/>', 'match' => 'smart-pricing.*', 'modes' => ['restaurant']],

        ['route' => 'customers.index', 'label' => 'Customers', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>', 'match' => 'customers.*', 'modes' => ['sari_sari']],
        ['route' => 'invoices.index', 'label' => 'Invoices', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>', 'match' => 'invoices.*', 'modes' => ['sari_sari']],
        ['route' => 'payments.index', 'label' => 'Payments', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>', 'match' => 'payments.*', 'modes' => ['sari_sari']],
        ['route' => 'expenses.index', 'label' => 'Expenses', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4M4 12l4-4m-4 4l4 4"/>', 'match' => 'expenses.*', 'modes' => ['sari_sari']],
        ['route' => 'savings.index', 'label' => 'Savings', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12V8a2 2 0 00-2-2h-5.5L10 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-2m-6-2h8m0 0l-3-3m3 3l-3 3"/>', 'match' => 'savings.*', 'modes' => ['sari_sari']],
        ['route' => 'reports.index', 'label' => 'Reports', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>', 'match' => 'reports.*', 'modes' => ['sari_sari']],
        ['route' => 'settings.index', 'label' => 'Settings', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>', 'match' => 'settings.*', 'modes' => ['all']],
    ];
@endphp

<!-- Desktop Sidebar -->
<aside class="flex flex-col h-full bg-mint-900 border-r border-mint-800/50 shadow-2xl shadow-mint-950/20">
    <!-- Logo -->
    <div class="flex items-center gap-3 h-20 px-6 border-b border-mint-800/30">
        <div class="w-10 h-10 rounded-xl bg-mint-500 flex items-center justify-center shadow-lg shadow-mint-950/40">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">TITA</h2>
            <p class="text-[10px] text-mint-400 font-medium uppercase tracking-widest">
                {{ $biz ? ($isRestaurant ? 'Restaurant' : 'Sari-Sari Store') : 'Finance System' }}
            </p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
        @foreach($navItems as $item)
            @php
                $show = in_array('all', $item['modes']) 
                    || ($isRestaurant && in_array('restaurant', $item['modes']))
                    || ($isSariSari && in_array('sari_sari', $item['modes']));
                $isActive = request()->routeIs($item['match']);
            @endphp
            @if($show)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 group
                      {{ $isActive
                          ? 'bg-mint-500 text-white shadow-lg shadow-mint-950/30 ring-1 ring-white/20'
                          : 'text-mint-300 hover:text-white hover:bg-mint-800/40' }}">
                <svg class="w-5 h-5 flex-shrink-0 transition-colors {{ $isActive ? 'text-white' : 'text-mint-600 group-hover:text-mint-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>
                <span class="tracking-wide">{{ $item['label'] }}</span>
                @if($isActive)
                <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white shadow-[0_0_8px_rgba(255,255,255,0.8)]"></div>
                @endif
            </a>
            @endif
        @endforeach
    </nav>

    <!-- Bottom -->
    <div class="p-4 border-t border-mint-800/30 bg-mint-950/20">
        <div class="px-4 py-3 rounded-2xl bg-mint-800/30 border border-white/5 backdrop-blur-sm">
            <p class="text-[10px] text-mint-500 font-bold uppercase tracking-widest mb-1">{{ $biz ? $biz->business_name : 'User Session' }}</p>
            <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
        </div>
    </div>
</aside>
