@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Overview')

@section('content')
<!-- Filters -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-3">
        <select name="month" onchange="this.form.submit()" class="px-4 py-2 bg-white border border-beige-200 rounded-xl text-sm font-semibold text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all shadow-sm">
            @for ($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                    {{ Carbon\Carbon::create()->month($m)->format('F') }}
                </option>
            @endfor
        </select>
        <select name="year" onchange="this.form.submit()" class="px-4 py-2 bg-white border border-beige-200 rounded-xl text-sm font-semibold text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all shadow-sm">
            @for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>
    <div class="px-4 py-1.5 bg-mint-100/50 rounded-full border border-mint-200/50 text-[11px] font-bold text-mint-700 uppercase tracking-widest">
        {{ Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Today's Income -->
    <div class="group relative bg-white border border-beige-200/60 rounded-3xl p-6 hover:shadow-xl hover:shadow-mint-900/5 transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl bg-mint-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-[10px] font-bold text-mint-500 uppercase tracking-widest">Daily</span>
        </div>
        <p class="text-[11px] font-bold text-beige-500 uppercase tracking-wider mb-1">Today's Income</p>
        <p class="text-2xl font-black text-mint-900">₱{{ number_format($todayIncome, 2) }}</p>
        <div class="mt-4 flex items-center text-[10px] font-bold text-mint-600">
            <span class="px-2 py-0.5 bg-mint-100 rounded-lg mr-2">+ Payments</span>
        </div>
    </div>

    <!-- Month Income -->
    <div class="group relative bg-white border border-beige-200/60 rounded-3xl p-6 hover:shadow-xl hover:shadow-mint-900/5 transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl bg-mint-600/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-mint-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <span class="text-[10px] font-bold text-mint-500 uppercase tracking-widest">Monthly</span>
        </div>
        <p class="text-[11px] font-bold text-beige-500 uppercase tracking-wider mb-1">Total Revenue</p>
        <p class="text-2xl font-black text-mint-900">₱{{ number_format($monthIncome, 2) }}</p>
        <div class="mt-4 flex items-center text-[10px] font-bold text-mint-600">
            <span class="px-2 py-0.5 bg-mint-100 rounded-lg mr-2">{{ Carbon\Carbon::create()->month($month)->format('M') }} {{ $year }}</span>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="group relative bg-white border border-beige-200/60 rounded-3xl p-6 hover:shadow-xl hover:shadow-mint-900/5 transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl bg-beige-500/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-beige-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-[10px] font-bold text-beige-500 uppercase tracking-widest">Receivables</span>
        </div>
        <p class="text-[11px] font-bold text-beige-500 uppercase tracking-wider mb-1">Pending Amount</p>
        <p class="text-2xl font-black text-mint-900">₱{{ number_format($pendingPayments, 2) }}</p>
        <div class="mt-4 flex items-center text-[10px] font-bold text-beige-600">
            <span class="px-2 py-0.5 bg-beige-100 rounded-lg mr-2">Awaiting</span>
        </div>
    </div>

    <!-- Overdue -->
    <div class="group relative bg-white border border-beige-200/60 rounded-3xl p-6 hover:shadow-xl hover:shadow-mint-900/5 transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <span class="text-[10px] font-bold text-red-500 uppercase tracking-widest">Alert</span>
        </div>
        <p class="text-[11px] font-bold text-beige-500 uppercase tracking-wider mb-1">Overdue Invoices</p>
        <p class="text-2xl font-black text-red-600">{{ $overdueCount }} <span class="text-sm font-bold text-beige-400">Cases</span></p>
        <div class="mt-4 flex items-center text-[10px] font-bold text-red-500">
            <span class="px-2 py-0.5 bg-red-50 rounded-lg mr-2">₱{{ number_format($overdueAmount, 2) }}</span>
        </div>
    </div>
</div>

<!-- Profit Summary + Chart -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Profit Card -->
    <div class="bg-white border border-beige-200/60 rounded-3xl p-8 flex flex-col justify-between">
        <div>
            <h3 class="text-[11px] font-bold text-beige-500 uppercase tracking-widest mb-6">Financial Summary</h3>
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-mint-800">Total Income</span>
                    <span class="text-base font-bold text-mint-600">₱{{ number_format($monthIncome, 2) }}</span>
                </div>
                <div class="flex items-center justify-between font-medium">
                    <span class="text-sm text-mint-800">Total Expenses</span>
                    <span class="text-base font-bold text-red-500">-₱{{ number_format($monthExpenses, 2) }}</span>
                </div>
                <div class="pt-6 border-t border-beige-100">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-mint-900">Net Profit</span>
                        <div class="text-right">
                            <span class="text-2xl font-black {{ $monthProfit >= 0 ? 'text-mint-600' : 'text-red-600' }}">
                                ₱{{ number_format(abs($monthProfit), 2) }}
                            </span>
                            @if($monthProfit < 0) <p class="text-[10px] font-bold text-red-500 uppercase">Loss Recorded</p> @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-8 p-4 bg-beige-50 rounded-2xl border border-beige-100 flex items-center justify-between">
            <span class="text-[10px] font-bold text-beige-500 uppercase">Total Clients</span>
            <span class="text-sm font-black text-mint-900">{{ $totalCustomers }}</span>
        </div>
    </div>

    <!-- Chart Area -->
    <div class="lg:col-span-2 bg-white border border-beige-200/60 rounded-3xl p-8 shadow-sm">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-[11px] font-bold text-beige-500 uppercase tracking-widest">Revenue vs Expenses</h3>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-mint-500"></div>
                    <span class="text-[10px] font-bold text-mint-900 uppercase">Income</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-red-400"></div>
                    <span class="text-[10px] font-bold text-mint-900 uppercase">Expenses</span>
                </div>
            </div>
        </div>
        <div class="relative h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Invoices -->
    <div class="bg-white border border-beige-200/60 rounded-3xl shadow-sm overflow-hidden flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-beige-100">
            <h3 class="text-sm font-bold text-mint-900">Recent Invoices</h3>
            <a href="{{ route('invoices.index') }}" class="text-[10px] font-bold text-mint-600 uppercase tracking-widest hover:text-mint-700 transition-colors">Viel All</a>
        </div>
        <div class="flex-1 divide-y divide-beige-100">
            @forelse($recentInvoices as $invoice)
            <div class="flex items-center justify-between px-6 py-4 hover:bg-beige-50 transition-all group">
                <a href="{{ route('invoices.show', $invoice) }}" class="flex items-center gap-4 min-w-0 flex-1">
                    <div class="w-10 h-10 rounded-xl bg-mint-50 flex items-center justify-center text-xs font-black text-mint-600 flex-shrink-0 group-hover:bg-mint-100 transition-colors">
                        {{ substr($invoice->customer->name ?? '?', 0, 2) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-mint-900 truncate">{{ $invoice->invoice_number }}</p>
                        <p class="text-xs font-medium text-beige-500 truncate">{{ $invoice->customer->name ?? 'N/A' }}</p>
                    </div>
                </a>
                <div class="flex items-center gap-4 ml-4">
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-black text-mint-900">₱{{ number_format($invoice->total_amount, 2) }}</p>
                        <span class="inline-flex px-2.5 py-0.5 text-[9px] font-black uppercase rounded-full tracking-wider
                            @if($invoice->status === 'paid') bg-mint-100 text-mint-700
                            @elseif($invoice->status === 'partial') bg-amber-100 text-amber-700
                            @elseif($invoice->status === 'overdue') bg-red-100 text-red-700
                            @else bg-beige-100 text-beige-600 @endif">
                            {{ $invoice->status }}
                        </span>
                    </div>
                    <a href="{{ route('invoices.pdf', $invoice) }}" class="p-2 rounded-xl text-beige-300 hover:text-mint-600 hover:bg-white border border-transparent hover:border-beige-200 transition-all shadow-sm group-hover:opacity-100 md:opacity-0" title="Download PDF">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                    </a>
                </div>
            </div>
            @empty
            <div class="px-5 py-12 text-center text-sm font-bold text-beige-400">No recent activity detected</div>
            @endforelse
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="bg-white border border-beige-200/60 rounded-3xl shadow-sm overflow-hidden flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-beige-100">
            <h3 class="text-sm font-bold text-mint-900">Recent Payments</h3>
            <a href="{{ route('payments.index') }}" class="text-[10px] font-bold text-mint-600 uppercase tracking-widest hover:text-mint-700 transition-colors">View All</a>
        </div>
        <div class="flex-1 divide-y divide-beige-100">
            @forelse($recentPayments as $payment)
            <div class="flex items-center justify-between px-6 py-4 hover:bg-beige-50 transition-all group">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-white border border-beige-100 group-hover:border-mint-200 transition-colors shadow-sm">
                        @if($payment->payment_method === 'cash')
                        <svg class="w-5 h-5 text-mint-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        @elseif($payment->payment_method === 'gcash')
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        @else
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-mint-900 truncate">{{ $payment->customer->name ?? 'N/A' }}</p>
                        <p class="text-[10px] font-bold text-beige-500 uppercase">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }} • {{ $payment->payment_date->format('M d') }}</p>
                    </div>
                </div>
                <p class="text-sm font-black text-mint-600 flex-shrink-0 ml-4 group-hover:scale-105 transition-transform">+₱{{ number_format($payment->amount, 2) }}</p>
            </div>
            @empty
            <div class="px-5 py-12 text-center text-sm font-bold text-beige-400">No payment records found</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Business-Mode Widgets -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
    <!-- Low Stock Alert -->
    @if($lowStockProducts->count() > 0)
    <div class="bg-white border border-amber-200/60 rounded-3xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-amber-100 bg-amber-50/50">
            <h3 class="text-sm font-bold text-amber-800">⚠️ Low Stock Alert</h3>
            <a href="{{ route('products.index', ['stock_status' => 'low']) }}" class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">View All</a>
        </div>
        <div class="divide-y divide-beige-100">
            @foreach($lowStockProducts as $product)
            <div class="flex items-center justify-between px-6 py-3">
                <div>
                    <p class="text-sm font-bold text-mint-900">{{ $product->name }}</p>
                    <p class="text-xs text-beige-500">Reorder at {{ $product->reorder_level }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ $product->stock_quantity <= 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $product->stock_quantity }} left
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Best Sellers -->
    @if($bestSellers->count() > 0)
    <div class="bg-white border border-beige-200/60 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-beige-100">
            <h3 class="text-sm font-bold text-mint-900">🏆 Best Sellers This Month</h3>
        </div>
        <div class="divide-y divide-beige-100">
            @foreach($bestSellers as $i => $seller)
            <div class="flex items-center justify-between px-6 py-3">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-mint-100 flex items-center justify-center text-xs font-black text-mint-600">{{ $i + 1 }}</span>
                    <p class="text-sm font-bold text-mint-900">{{ $seller->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-black text-mint-600">{{ $seller->total_sold }} sold</p>
                    <p class="text-[10px] text-beige-500">₱{{ number_format($seller->total_revenue, 2) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Top Utang (Sari-sari) -->
    @if(is_sari_sari() && $topUtang->count() > 0)
    <div class="bg-white border border-red-200/60 rounded-3xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-red-100 bg-red-50/50">
            <h3 class="text-sm font-bold text-red-800">📝 Top Utang</h3>
            <a href="{{ route('customers.index') }}" class="text-[10px] font-bold text-red-600 uppercase tracking-widest">View All</a>
        </div>
        <div class="divide-y divide-beige-100">
            @foreach($topUtang as $customer)
            <a href="{{ route('customers.show', $customer) }}" class="flex items-center justify-between px-6 py-3 hover:bg-beige-50 transition-colors">
                <p class="text-sm font-bold text-mint-900">{{ $customer->name }}</p>
                <span class="text-sm font-black text-red-600">₱{{ number_format($customer->balance, 2) }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Restaurant: Active Tables & Kitchen Queue -->
    @if(is_restaurant())
    <div class="bg-white border border-beige-200/60 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-beige-100">
            <h3 class="text-sm font-bold text-mint-900">🍽️ Restaurant Overview</h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-sm text-beige-600">Kitchen Queue</span>
                <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm font-black">{{ $kitchenQueue }} orders</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-beige-600">Today's Orders</span>
                <span class="text-sm font-black text-mint-900">{{ $todayOrders }}</span>
            </div>
            @if($activeTables)
            <div class="pt-2 border-t border-beige-100">
                <p class="text-xs font-bold text-beige-500 uppercase mb-2">Table Status</p>
                <div class="flex gap-2 flex-wrap">
                    @foreach($activeTables->take(10) as $table)
                    <span class="px-2 py-1 rounded-lg text-xs font-bold
                        {{ $table->status === 'available' ? 'bg-emerald-100 text-emerald-700' : '' }}
                        {{ $table->status === 'occupied' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $table->status === 'reserved' ? 'bg-amber-100 text-amber-700' : '' }}
                        {{ $table->status === 'dirty' ? 'bg-gray-100 text-gray-600' : '' }}">
                        T{{ $table->table_number }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chartData = @json($monthlyChart);

    const gradientIncome = ctx.createLinearGradient(0, 0, 0, 400);
    gradientIncome.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
    gradientIncome.addColorStop(1, 'rgba(16, 185, 129, 0.05)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(d => d.month),
            datasets: [
                {
                    label: 'Income',
                    data: chartData.map(d => d.income),
                    backgroundColor: gradientIncome,
                    borderColor: '#10B981',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10B981',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Expenses',
                    data: chartData.map(d => d.expenses),
                    borderColor: '#f87171',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4,
                    pointRadius: 0,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                x: {
                    border: { display: false },
                    grid: { color: 'rgba(210, 194, 168, 0.2)', drawTicks: false },
                    ticks: { color: '#AA8D63', font: { size: 10, weight: '600' }, padding: 10 }
                },
                y: {
                    border: { display: false },
                    grid: { color: 'rgba(210, 194, 168, 0.2)', drawTicks: false },
                    ticks: {
                        color: '#AA8D63',
                        font: { size: 10, weight: '600' },
                        padding: 10,
                        callback: v => '₱' + (v >= 1000 ? (v/1000) + 'k' : v)
                    }
                }
            }
        }
    });
});
</script>
@endpush

