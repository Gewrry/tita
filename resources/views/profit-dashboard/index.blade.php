@extends('layouts.app')
@section('title', 'Smart Dashboard')
@section('page-title', 'Smart Dashboard')

@php
    $currency = fn ($value) => '&#8369;'.number_format((float) $value, 2);
    $maxTrend = max(1, (float) $dailyTrend->max('revenue'));
    $productOptions = $products->map(fn ($product) => [
        'id' => $product->id,
        'name' => $product->name,
        'price' => (float) $product->selling_price,
        'cost' => (float) ($product->pricingProfile?->complete_cost_per_serving ?? $product->cost_price),
    ])->values();
@endphp

@section('content')
<div class="space-y-6" x-data="profitDashboard({{ Js::from($productOptions) }})">
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-black text-beige-500 uppercase tracking-widest">Food Profit Intelligence</p>
            <h2 class="text-2xl font-black text-mint-900 mt-1">Sales, margins, and price risks in one place</h2>
        </div>
        <form method="GET" action="{{ route('profit-dashboard.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div>
                <label class="block text-[10px] font-black text-beige-500 uppercase mb-1" for="start_date">From</label>
                <input id="start_date" name="start_date" type="date" value="{{ $startDate->format('Y-m-d') }}" class="w-full min-h-[44px] px-3 py-2 bg-white border border-beige-200 rounded-xl text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-black text-beige-500 uppercase mb-1" for="end_date">To</label>
                <input id="end_date" name="end_date" type="date" value="{{ $endDate->format('Y-m-d') }}" class="w-full min-h-[44px] px-3 py-2 bg-white border border-beige-200 rounded-xl text-sm">
            </div>
            <button type="submit" class="self-end min-h-[44px] px-5 py-2 bg-mint-500 text-white rounded-xl text-sm font-black hover:bg-mint-600 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M5 8h14M7 12h10M9 16h6"/></svg>
                Filter
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
        <div class="bg-white border border-beige-200/60 rounded-2xl p-5">
            <p class="text-[10px] font-black text-beige-500 uppercase tracking-widest">Revenue</p>
            <p class="text-2xl font-black text-mint-900 mt-2">{!! $currency($revenue) !!}</p>
            <p class="text-xs font-bold text-beige-500 mt-2">{{ $startDate->format('M d') }} - {{ $endDate->format('M d') }}</p>
        </div>
        <div class="bg-white border border-beige-200/60 rounded-2xl p-5">
            <p class="text-[10px] font-black text-beige-500 uppercase tracking-widest">Total Cost</p>
            <p class="text-2xl font-black text-beige-800 mt-2">{!! $currency($totalCost) !!}</p>
            <p class="text-xs font-bold text-beige-500 mt-2">Cost snapshots from sales</p>
        </div>
        <div class="bg-white border border-beige-200/60 rounded-2xl p-5">
            <p class="text-[10px] font-black text-beige-500 uppercase tracking-widest">Gross Profit</p>
            <p class="text-2xl font-black {{ $grossProfit >= 0 ? 'text-mint-700' : 'text-red-600' }} mt-2">{!! $currency($grossProfit) !!}</p>
            <p class="text-xs font-bold text-beige-500 mt-2">Before full accounting costs</p>
        </div>
        <div class="bg-white border border-beige-200/60 rounded-2xl p-5">
            <p class="text-[10px] font-black text-beige-500 uppercase tracking-widest">Profit Margin</p>
            <p class="text-2xl font-black {{ $profitMargin >= 30 ? 'text-mint-700' : 'text-amber-600' }} mt-2">{{ number_format($profitMargin, 2) }}%</p>
            <p class="text-xs font-bold text-beige-500 mt-2">Gross profit / revenue</p>
        </div>
        <div class="bg-white border border-beige-200/60 rounded-2xl p-5">
            <p class="text-[10px] font-black text-beige-500 uppercase tracking-widest">Units Sold</p>
            <p class="text-2xl font-black text-mint-900 mt-2">{{ number_format($unitsSold, 2) }}</p>
            <p class="text-xs font-bold text-beige-500 mt-2">Avg profit {!! $currency($averageProfitPerSale) !!}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 2xl:grid-cols-[380px_minmax(0,1fr)] gap-6">
        <form method="POST" action="{{ route('profit-dashboard.sales.store') }}" class="bg-white border border-beige-200/60 rounded-2xl p-5 space-y-4">
            @csrf
            <div>
                <h3 class="text-sm font-black text-mint-900">Record Simple Sale</h3>
                <p class="text-xs text-beige-500 mt-1">No POS needed. Enter quantity sold and the dashboard updates.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="product_id">Product</label>
                <select id="product_id" name="product_id" x-model="selectedProductId" @change="syncProductPrice" required class="w-full min-h-[44px] px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                    <option value="">Select product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} - {!! $currency($product->selling_price) !!}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="sale_date">Date</label>
                    <input id="sale_date" name="sale_date" type="date" value="{{ today()->format('Y-m-d') }}" required class="w-full min-h-[44px] px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="quantity">Qty Sold</label>
                    <input id="quantity" name="quantity" type="number" min="0.01" step="0.01" x-model.number="quantity" required class="w-full min-h-[44px] px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="selling_price">Sale Price</label>
                    <input id="selling_price" name="selling_price" type="number" min="0" step="0.01" x-model.number="sellingPrice" class="w-full min-h-[44px] px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="channel">Channel</label>
                    <select id="channel" name="channel" class="w-full min-h-[44px] px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                        <option value="manual">Manual</option>
                        <option value="walk_in">Walk-in</option>
                        <option value="delivery">Delivery</option>
                        <option value="pickup">Pickup</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="notes">Notes</label>
                <input id="notes" name="notes" type="text" maxlength="255" class="w-full min-h-[44px] px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm" placeholder="Optional">
            </div>

            <div class="grid grid-cols-3 gap-2 pt-2">
                <div class="px-3 py-2 bg-mint-50 border border-mint-100 rounded-xl">
                    <p class="text-[10px] font-black text-mint-600 uppercase">Revenue</p>
                    <p class="text-sm font-black text-mint-900 tabular-nums" x-text="currency(projectedRevenue())"></p>
                </div>
                <div class="px-3 py-2 bg-beige-50 border border-beige-100 rounded-xl">
                    <p class="text-[10px] font-black text-beige-500 uppercase">Cost</p>
                    <p class="text-sm font-black text-mint-900 tabular-nums" x-text="currency(projectedCost())"></p>
                </div>
                <div class="px-3 py-2 bg-white border border-beige-200 rounded-xl">
                    <p class="text-[10px] font-black text-beige-500 uppercase">Profit</p>
                    <p class="text-sm font-black text-mint-900 tabular-nums" x-text="currency(projectedProfit())"></p>
                </div>
            </div>

            <button type="submit" class="w-full min-h-[48px] px-5 py-3 bg-mint-500 text-white font-black text-sm rounded-xl hover:bg-mint-600 transition-all shadow-lg shadow-mint-500/25 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Save Sale
            </button>
        </form>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <section class="bg-white border border-beige-200/60 rounded-2xl p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <h3 class="text-sm font-black text-mint-900">Profit Trend</h3>
                    <span class="text-[10px] font-black text-beige-500 uppercase">Revenue and profit</span>
                </div>
                <div class="space-y-3">
                    @forelse($dailyTrend as $day)
                        @php
                            $revenueWidth = min(100, max(4, ((float) $day['revenue'] / $maxTrend) * 100));
                            $profitWidth = min(100, max($day['profit'] > 0 ? 4 : 0, ((float) $day['profit'] / $maxTrend) * 100));
                        @endphp
                        <div class="grid grid-cols-[56px_minmax(0,1fr)_86px] gap-3 items-center">
                            <span class="text-xs font-bold text-beige-500">{{ $day['label'] }}</span>
                            <div class="space-y-1.5">
                                <div class="h-2 rounded-full bg-beige-100 overflow-hidden">
                                    <div class="h-full bg-mint-500 rounded-full" style="width: {{ $revenueWidth }}%"></div>
                                </div>
                                <div class="h-1.5 rounded-full bg-beige-100 overflow-hidden">
                                    <div class="h-full bg-beige-500 rounded-full" style="width: {{ $profitWidth }}%"></div>
                                </div>
                            </div>
                            <span class="text-xs font-black text-mint-900 text-right tabular-nums">{!! $currency($day['revenue']) !!}</span>
                        </div>
                    @empty
                        <p class="text-sm font-bold text-beige-500 text-center py-10">No sales in this period.</p>
                    @endforelse
                </div>
            </section>

            <section class="bg-white border border-beige-200/60 rounded-2xl p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <h3 class="text-sm font-black text-mint-900">Smart Alerts</h3>
                    <span class="text-[10px] font-black text-beige-500 uppercase">{{ $marginAlerts->count() + $pendingPriceUpdates->count() }} alerts</span>
                </div>
                <div class="space-y-3">
                    @forelse($marginAlerts as $alert)
                        <a href="{{ route('smart-pricing.index', ['product' => $alert['product']->id]) }}" class="block px-4 py-3 bg-red-50 border border-red-100 rounded-xl hover:bg-red-100/60 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-black text-red-800">{{ $alert['product']->name }}</p>
                                    <p class="text-xs text-red-700 mt-1">{{ $alert['warning'] }}. Margin {{ number_format($alert['margin'], 2) }}%, minimum {{ number_format($alert['minimum_margin'], 2) }}%.</p>
                                </div>
                                <svg class="w-4 h-4 text-red-500 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-3 bg-mint-50 border border-mint-100 rounded-xl">
                            <p class="text-sm font-black text-mint-800">All active prices clear the configured cost and margin rules.</p>
                        </div>
                    @endforelse

                    @foreach($pendingPriceUpdates as $update)
                        <a href="{{ route('smart-pricing.index', ['product' => $update['product']->id]) }}" class="block px-4 py-3 bg-amber-50 border border-amber-100 rounded-xl hover:bg-amber-100/60 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-black text-amber-800">{{ $update['product']->name }}</p>
                                    <p class="text-xs text-amber-700 mt-1">Recommendation changed from {!! $currency($update['current_price']) !!} to {!! $currency($update['recommended_price']) !!}.</p>
                                </div>
                                <svg class="w-4 h-4 text-amber-500 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    </div>

    <div class="grid grid-cols-1 2xl:grid-cols-[minmax(0,1fr)_420px] gap-6">
        <section class="bg-white border border-beige-200/60 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-beige-100">
                <h3 class="text-sm font-black text-mint-900">Menu Engineering</h3>
                <p class="text-xs text-beige-500 mt-1">Classification uses sold quantity share and contribution margin during the selected period.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-beige-50 text-[11px] uppercase tracking-wider text-beige-500">
                        <tr>
                            <th class="text-left font-black px-4 py-3">Product</th>
                            <th class="text-left font-black px-4 py-3">Class</th>
                            <th class="text-left font-black px-4 py-3">Qty</th>
                            <th class="text-left font-black px-4 py-3">Revenue</th>
                            <th class="text-left font-black px-4 py-3">Gross Profit</th>
                            <th class="text-left font-black px-4 py-3">Profit/Unit</th>
                            <th class="text-left font-black px-4 py-3">Recommendation</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-beige-100">
                        @forelse($menuInsights as $item)
                            @php
                                $classMap = [
                                    'Star' => 'bg-mint-100 text-mint-800',
                                    'Price Risk' => 'bg-red-100 text-red-800',
                                    'Promote' => 'bg-amber-100 text-amber-800',
                                    'Review' => 'bg-beige-100 text-beige-700',
                                ];
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-black text-mint-900">{{ $item['product']?->name ?? 'Deleted product' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-[11px] font-black uppercase {{ $classMap[$item['classification']] ?? 'bg-beige-100 text-beige-700' }}">{{ $item['classification'] }}</span>
                                </td>
                                <td class="px-4 py-3 tabular-nums">{{ number_format($item['quantity'], 2) }}</td>
                                <td class="px-4 py-3 tabular-nums">{!! $currency($item['revenue']) !!}</td>
                                <td class="px-4 py-3 tabular-nums">{!! $currency($item['gross_profit']) !!}</td>
                                <td class="px-4 py-3 tabular-nums">{!! $currency($item['contribution_margin']) !!}</td>
                                <td class="px-4 py-3 text-beige-700">{{ $item['recommendation'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-sm font-bold text-beige-500">Record sales to generate menu intelligence.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-white border border-beige-200/60 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-beige-100">
                <h3 class="text-sm font-black text-mint-900">Recent Sales</h3>
            </div>
            <div class="divide-y divide-beige-100">
                @forelse($recentSales as $sale)
                    <div class="px-5 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-black text-mint-900 truncate">{{ $sale->product?->name ?? 'Deleted product' }}</p>
                                <p class="text-xs text-beige-500 mt-1">{{ $sale->sale_date->format('M d, Y') }} &middot; {{ number_format($sale->quantity, 2) }} sold</p>
                            </div>
                            <form method="POST" action="{{ route('profit-dashboard.sales.destroy', $sale) }}" onsubmit="return confirm('Remove this sale entry?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="min-w-[44px] min-h-[44px] inline-flex items-center justify-center rounded-xl text-beige-400 hover:text-red-500 hover:bg-red-50 transition-colors" aria-label="Remove sale">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                            </form>
                        </div>
                        <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
                            <div>
                                <p class="font-black text-beige-500 uppercase">Revenue</p>
                                <p class="font-black text-mint-900 tabular-nums">{!! $currency($sale->total_revenue) !!}</p>
                            </div>
                            <div>
                                <p class="font-black text-beige-500 uppercase">Cost</p>
                                <p class="font-black text-mint-900 tabular-nums">{!! $currency($sale->total_cost) !!}</p>
                            </div>
                            <div>
                                <p class="font-black text-beige-500 uppercase">Profit</p>
                                <p class="font-black text-mint-900 tabular-nums">{!! $currency($sale->gross_profit) !!}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm font-bold text-beige-500">No sales recorded yet.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
function profitDashboard(products) {
    return {
        products,
        selectedProductId: '',
        quantity: 1,
        sellingPrice: 0,
        unitCost: 0,
        syncProductPrice() {
            const product = this.products.find((item) => String(item.id) === String(this.selectedProductId));
            this.sellingPrice = product ? Number(product.price || 0) : 0;
            this.unitCost = product ? Number(product.cost || 0) : 0;
        },
        projectedRevenue() {
            return Number(this.quantity || 0) * Number(this.sellingPrice || 0);
        },
        projectedCost() {
            return Number(this.quantity || 0) * Number(this.unitCost || 0);
        },
        projectedProfit() {
            return this.projectedRevenue() - this.projectedCost();
        },
        currency(value) {
            return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(Number(value || 0));
        },
    };
}
</script>
@endpush
