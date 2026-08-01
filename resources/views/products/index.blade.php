@extends('layouts.app')
@section('title', 'Products')
@section('page-title', 'Products')

@push('styles')
<style>
/* =========================================================
   PRODUCTS — Premium Inventory Management Interface
   8px grid · Outfit · warm green-beige palette
   ========================================================= */

/* ── Grid Layout ── */
.product-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(2, 1fr);
}
@media (min-width: 640px)  { .product-grid { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 1024px) { .product-grid { grid-template-columns: repeat(4, 1fr); } }
@media (min-width: 1280px) { .product-grid { grid-template-columns: repeat(5, 1fr); } }
@media (min-width: 1536px) { .product-grid { grid-template-columns: repeat(6, 1fr); } }

.product-grid.compact {
    gap: 0.625rem;
}
.product-grid.compact .prod-card .prod-image-wrap { height: 96px !important; }
.product-grid.compact .prod-card .prod-body { padding: 0.625rem; }

/* ── Product Card ── */
.prod-card {
    background: #fff;
    border: 1px solid rgba(210, 194, 168, 0.5);
    border-radius: 18px;
    overflow: hidden;
    transition: transform 0.22s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.22s ease, border-color 0.18s ease;
    will-change: transform;
    display: flex;
    flex-direction: column;
    position: relative;
}
.prod-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 40px -8px rgba(21,85,65,0.14), 0 4px 12px -4px rgba(21,85,65,0.06);
    border-color: rgba(16, 185, 129, 0.3);
}
.prod-card:hover .prod-actions { opacity: 1; pointer-events: auto; transform: translateY(0); }
.prod-card:hover .prod-image-wrap img { transform: scale(1.06); }

/* ── Image wrap ── */
.prod-image-wrap {
    height: 140px;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #F7F2E8 0%, #EFE6D8 100%);
    flex-shrink: 0;
}
.prod-image-wrap img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform 0.4s ease;
}
.prod-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
}

/* ── Quick action overlay ── */
.prod-actions {
    opacity: 0;
    pointer-events: none;
    transform: translateY(4px);
    transition: opacity 0.2s ease, transform 0.2s ease;
    position: absolute;
    top: 8px; right: 8px;
    display: flex; flex-direction: column; gap: 4px;
}
.prod-action-btn {
    width: 30px; height: 30px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(210,194,168,0.4);
    color: #57442D;
    transition: all 0.15s ease;
    cursor: pointer;
    text-decoration: none;
}
.prod-action-btn:hover { background: #fff; color: #10B981; border-color: rgba(16,185,129,0.3); transform: scale(1.1); }
.prod-action-btn.danger:hover { color: #dc2626; border-color: rgba(220,38,38,0.3); }

/* ── Progress bar ── */
.stock-bar-track {
    height: 4px;
    background: rgba(210,194,168,0.3);
    border-radius: 99px;
    overflow: hidden;
    margin-top: 4px;
}
.stock-bar-fill {
    height: 100%;
    border-radius: 99px;
    transition: width 0.8s cubic-bezier(0.4,0,0.2,1);
}

/* ── Stock badges ── */
.stock-badge {
    display: inline-flex; align-items: center;
    gap: 3px; padding: 2px 7px;
    border-radius: 99px;
    font-size: 9px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.05em;
    flex-shrink: 0;
}
.badge-in     { background: #DDF6ED; color: #155541; }
.badge-low    { background: #FEF3C7; color: #92400E; }
.badge-out    { background: #fee2e2; color: #991b1b; }
.badge-free   { background: #e0f2fe; color: #075985; }

/* ── Skeleton loader ── */
@keyframes shimmer {
    0%   { background-position: -600px 0; }
    100% { background-position: 600px 0; }
}
.skeleton {
    background: linear-gradient(90deg, #F7F2E8 25%, #EFE6D8 50%, #F7F2E8 75%);
    background-size: 1200px 100%;
    animation: shimmer 1.5s ease infinite;
    border-radius: 8px;
}

/* ── List view ── */
.product-list { display: flex; flex-direction: column; gap: 0.5rem; }
.product-list .prod-card {
    flex-direction: row;
    border-radius: 16px;
    transform: none !important;
}
.product-list .prod-card:hover { transform: translateY(-1px) !important; }
.product-list .prod-image-wrap { width: 80px; height: 80px !important; flex-shrink: 0; border-radius: 0; }
.product-list .prod-actions { flex-direction: row; top: 50%; right: 12px; transform: translateY(-50%) translateX(4px); }
.product-list .prod-card:hover .prod-actions { transform: translateY(-50%) translateX(0); }
.product-list .prod-body { padding: 12px 16px; flex: 1; min-width: 0; display: flex; align-items: center; gap: 16px; }
.product-list .prod-meta-main { flex: 1; min-width: 0; }
.product-list .prod-price-col { flex-shrink: 0; text-align: right; }
.product-list .prod-stock-col { width: 120px; flex-shrink: 0; }
.product-list .stock-bar-track { display: none; }
.product-list .prod-desc { display: none; }

/* ── Search bar focus ring ── */
.search-bar-wrap:focus-within {
    box-shadow: 0 0 0 3px rgba(16,185,129,0.15);
    border-color: #10B981 !important;
}

/* ── Filter chips active state ── */
.sort-btn.active { background: #10B981; color: #fff; border-color: #10B981; }

/* ── Fade in animation ── */
@keyframes fadeInCard {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.prod-card { animation: fadeInCard 0.3s ease both; }

/* ── View toggle button ── */
.view-btn { transition: all 0.15s ease; }
.view-btn.active { background: #10B981; color: #fff; }

/* ── Filter drawer (mobile) ── */
.filter-drawer-backdrop { backdrop-filter: blur(4px); }

/* Touch targets */
.prod-action-btn { min-width: 30px; min-height: 30px; }
button, a, select, input { min-height: 40px; }
.stock-badge { min-height: unset !important; }
</style>
@endpush

@section('content')
<div x-data="productIndex()" x-init="init()">

    {{-- ═══════════════════════════════════════════
         TOOLBAR
    ═══════════════════════════════════════════ --}}
    <div class="mb-6 space-y-3">
        {{-- Row 1: Search + Add button --}}
        <div class="flex items-center gap-3">
            {{-- Search bar --}}
            <form method="GET" action="{{ route('products.index') }}" class="flex-1 min-w-0" id="product-filter-form">
                <div class="search-bar-wrap flex items-center gap-2 bg-white border border-beige-200 rounded-2xl px-4 transition-all duration-200" style="min-height:48px;">
                    <svg class="w-4 h-4 text-beige-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by name, SKU, or barcode…"
                           autocomplete="off"
                           class="flex-1 bg-transparent text-sm font-semibold text-mint-900 placeholder-beige-400 focus:outline-none border-none ring-0 py-3"
                           style="min-height:unset;"
                           x-ref="searchInput">
                    {{-- Preserve other filters --}}
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <input type="hidden" name="stock_status" value="{{ request('stock_status') }}">
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @if(request('search'))
                    <a href="{{ route('products.index', array_merge(request()->except('search'), [])) }}"
                       class="w-5 h-5 flex items-center justify-center rounded-full bg-beige-200 text-beige-500 hover:bg-beige-300 transition-colors flex-shrink-0"
                       style="min-height:unset;">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                    @endif
                    <button type="submit" class="flex-shrink-0 px-4 py-1.5 rounded-xl bg-mint-500 text-white text-xs font-bold hover:bg-mint-600 transition-colors shadow-sm" style="min-height:unset;">
                        Search
                    </button>
                </div>
            </form>

            {{-- Add Product --}}
            <a href="{{ route('products.create') }}"
               class="flex-shrink-0 inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-mint-500 text-white text-sm font-extrabold hover:bg-mint-600 transition-all shadow-lg shadow-mint-500/25 hover:shadow-mint-500/35 hover:-translate-y-0.5 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">Add Product</span>
                <span class="sm:hidden">Add</span>
            </a>
        </div>

        {{-- Row 2: Filters + View Options --}}
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Category filter --}}
            <form method="GET" action="{{ route('products.index') }}" id="filter-form" class="contents">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="sort" value="{{ request('sort') }}">

                <select name="category" onchange="this.form.submit()"
                        class="px-3 py-2 bg-white border border-beige-200 rounded-xl text-xs font-bold text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all"
                        style="min-height:38px;">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                {{-- Stock status --}}
                <select name="stock_status" onchange="this.form.submit()"
                        class="px-3 py-2 bg-white border border-beige-200 rounded-xl text-xs font-bold text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all"
                        style="min-height:38px;">
                    <option value="">All Stock</option>
                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>⚠ Low Stock</option>
                    <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>🚫 Out of Stock</option>
                </select>

                {{-- Sort --}}
                <select name="sort" onchange="this.form.submit()"
                        class="px-3 py-2 bg-white border border-beige-200 rounded-xl text-xs font-bold text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all"
                        style="min-height:38px;">
                    <option value="" {{ !request('sort') ? 'selected' : '' }}>Newest First</option>
                    <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name A→Z</option>
                    <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name Z→A</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price ↑</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price ↓</option>
                    <option value="stock_asc" {{ request('sort') === 'stock_asc' ? 'selected' : '' }}>Stock Low→High</option>
                    <option value="stock_desc" {{ request('sort') === 'stock_desc' ? 'selected' : '' }}>Stock High→Low</option>
                </select>
            </form>

            {{-- Active filter chips --}}
            @if(request('search') || request('category') || request('stock_status'))
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-red-50 border border-red-100 text-xs font-bold text-red-500 hover:bg-red-100 transition-colors"
               style="min-height:38px;">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                Clear Filters
            </a>
            @endif

            {{-- Spacer --}}
            <div class="flex-1"></div>

            {{-- Category management --}}
            <div x-data="categoryModalHandler()" class="flex items-center gap-2">
                <button type="button" @click="showCategoryModal = true"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-mint-50 text-mint-600 rounded-xl hover:bg-mint-100 transition-colors border border-mint-200 text-xs font-bold"
                        style="min-height:38px;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Category
                </button>
                <button type="button" @click="showManageModal = true"
                        class="inline-flex items-center justify-center w-9 h-9 bg-white border border-beige-200 rounded-xl text-beige-500 hover:text-mint-600 hover:border-mint-200 hover:bg-mint-50 transition-all"
                        title="Manage Categories" style="min-height:38px; min-width:38px;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                @include('categories._modal')
            </div>

            {{-- View toggle --}}
            <div class="flex items-center gap-1 p-1 bg-beige-100 rounded-xl">
                <button @click="view = 'grid'"
                        :class="view === 'grid' ? 'active bg-white text-mint-700 shadow-sm' : 'text-beige-500 hover:text-mint-700'"
                        class="view-btn w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                        style="min-height:unset; min-width:unset;" title="Grid View">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </button>
                <button @click="view = 'compact'"
                        :class="view === 'compact' ? 'active bg-white text-mint-700 shadow-sm' : 'text-beige-500 hover:text-mint-700'"
                        class="view-btn w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                        style="min-height:unset; min-width:unset;" title="Compact Grid">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h8M3 10h8M3 15h8M3 20h8M14 5h7M14 10h7M14 15h7M14 20h7"/></svg>
                </button>
                <button @click="view = 'list'"
                        :class="view === 'list' ? 'active bg-white text-mint-700 shadow-sm' : 'text-beige-500 hover:text-mint-700'"
                        class="view-btn w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                        style="min-height:unset; min-width:unset;" title="List View">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </button>
            </div>

            {{-- Total count pill --}}
            <div class="px-3 py-1.5 rounded-xl bg-white border border-beige-200 text-xs font-bold text-beige-500 whitespace-nowrap" style="min-height:38px; display:flex; align-items:center;">
                {{ $products->total() }} products
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         PRODUCT GRID / LIST
    ═══════════════════════════════════════════ --}}

    {{-- Grid view --}}
    <div :class="{
            'product-grid': view === 'grid' || view === 'compact',
            'compact': view === 'compact',
            'product-list': view === 'list'
         }">

        @forelse($products as $i => $product)
        @php
            $isOut  = $product->track_stock && $product->stock_quantity <= 0;
            $isLow  = $product->track_stock && !$isOut && $product->stock_quantity <= ($product->reorder_level ?? 5);
            $isFree = !$product->track_stock;
            $stockPct = 0;
            if ($product->track_stock && $product->reorder_level > 0) {
                $max = $product->reorder_level * 3;
                $stockPct = min(100, round(($product->stock_quantity / $max) * 100));
            } elseif ($product->track_stock && $product->stock_quantity > 0) {
                $stockPct = min(100, ($product->stock_quantity / 50) * 100);
            }
            $barColor = $isOut ? '#ef4444' : ($isLow ? '#f59e0b' : '#10B981');
            $profit = (float)$product->selling_price - (float)$product->cost_price;
            $margin = (float)$product->selling_price > 0 ? round(($profit / (float)$product->selling_price) * 100) : 0;
        @endphp
        <div class="prod-card" style="animation-delay: {{ min($i * 40, 400) }}ms">

            {{-- ── Image area ── --}}
            <div class="prod-image-wrap">
                @if($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" loading="lazy">
                @else
                    <div class="prod-placeholder">
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-10 h-10 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                @endif

                {{-- Category badge --}}
                @if($product->category)
                <span class="absolute bottom-2 left-2 px-2 py-0.5 text-[9px] font-extrabold uppercase rounded-full text-white shadow-sm"
                      style="background-color: {{ $product->category->color ?? '#10B981' }}; min-height: unset;">
                    {{ $product->category->name }}
                </span>
                @endif

                {{-- Status badge top-right --}}
                <div class="absolute top-2 right-2">
                    @if($isOut)
                        <span class="stock-badge badge-out">
                            <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                            Out
                        </span>
                    @elseif($isLow)
                        <span class="stock-badge badge-low">
                            <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                            Low
                        </span>
                    @elseif($isFree)
                        <span class="stock-badge badge-free">∞</span>
                    @else
                        <span class="stock-badge badge-in">
                            <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                            In Stock
                        </span>
                    @endif
                </div>

                {{-- Quick actions overlay --}}
                <div class="prod-actions">
                    <a href="{{ route('products.edit', $product) }}"
                       class="prod-action-btn" title="Edit product">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    @if(business()?->isRestaurant())
                    <a href="{{ route('smart-pricing.index', ['product' => $product->id]) }}"
                       class="prod-action-btn" title="Smart Pricing">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </a>
                    @endif
                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete {{ addslashes($product->name) }}? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="prod-action-btn danger" title="Delete product" style="min-height:unset;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── Card body ── --}}
            <div class="prod-body p-4 flex flex-col gap-2.5 flex-1">
                {{-- Name + SKU --}}
                <div class="prod-meta-main">
                    <h3 class="text-sm font-extrabold text-mint-900 leading-tight line-clamp-2 group" title="{{ $product->name }}">
                        {{ $product->name }}
                    </h3>
                    @if($product->sku)
                    <p class="text-[10px] font-semibold text-beige-400 mt-0.5 truncate">SKU: {{ $product->sku }}</p>
                    @endif
                </div>

                {{-- Price row --}}
                <div class="prod-price-col flex items-end justify-between gap-2">
                    <div>
                        <p class="text-[9px] font-bold text-beige-400 uppercase tracking-wider">Sell Price</p>
                        <p class="text-xl font-black text-mint-600 leading-tight">₱{{ number_format($product->selling_price, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-bold text-beige-400 uppercase tracking-wider">Cost</p>
                        <p class="text-sm font-bold text-beige-500">₱{{ number_format($product->cost_price, 2) }}</p>
                    </div>
                </div>

                {{-- Margin pill --}}
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold {{ $margin >= 30 ? 'bg-mint-50 text-mint-700' : ($margin >= 10 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600') }}" style="min-height:unset;">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        {{ $margin }}% margin
                    </span>
                    <span class="text-[10px] font-bold text-beige-400 uppercase">{{ $product->unit }}</span>
                </div>

                {{-- Stock section --}}
                <div class="prod-stock-col">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-beige-400 uppercase tracking-wide">Stock</span>
                        <span class="text-xs font-black {{ $isOut ? 'text-red-600' : ($isLow ? 'text-amber-600' : 'text-mint-700') }}">
                            {{ $product->track_stock ? $product->stock_quantity . ' ' . $product->unit : '∞' }}
                        </span>
                    </div>
                    @if($product->track_stock)
                    <div class="stock-bar-track">
                        <div class="stock-bar-fill" style="width: {{ $stockPct }}%; background: {{ $barColor }};"></div>
                    </div>
                    @if($product->reorder_level)
                    <p class="text-[9px] text-beige-300 mt-1 font-medium">Reorder at {{ $product->reorder_level }}</p>
                    @endif
                    @endif
                </div>

                {{-- Barcode indicator + edit link footer --}}
                <div class="pt-2 border-t border-beige-100 flex items-center justify-between -mb-1">
                    <div class="flex items-center gap-2">
                        @if($product->barcode)
                        <span class="inline-flex items-center gap-1 text-[9px] font-bold text-beige-400" title="Barcode: {{ $product->barcode }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="2" height="16" rx="0.5"/><rect x="7" y="4" width="1" height="16"/><rect x="10" y="4" width="2" height="16" rx="0.5"/><rect x="14" y="4" width="1" height="16"/><rect x="17" y="4" width="2" height="16" rx="0.5"/><rect x="21" y="4" width="2" height="16" rx="0.5"/></svg>
                            {{ substr($product->barcode, 0, 8) }}…
                        </span>
                        @endif
                        @if(!$product->is_active)
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-beige-100 text-beige-500 uppercase" style="min-height:unset;">Inactive</span>
                        @endif
                    </div>
                    <a href="{{ route('products.edit', $product) }}"
                       class="inline-flex items-center gap-1 text-[10px] font-bold text-mint-500 hover:text-mint-700 transition-colors"
                       style="min-height:unset;">
                        Edit
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
        @empty
        {{-- ── Empty state ── --}}
        <div class="col-span-full flex flex-col items-center justify-center py-20 gap-6">
            <div class="relative">
                <div class="w-24 h-24 rounded-3xl bg-mint-50 border-2 border-dashed border-mint-200 flex items-center justify-center">
                    <svg class="w-12 h-12 text-mint-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="absolute -bottom-2 -right-2 w-8 h-8 rounded-xl bg-mint-500 flex items-center justify-center shadow-lg shadow-mint-300">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-lg font-extrabold text-mint-900 mb-2">
                    @if(request('search') || request('category') || request('stock_status'))
                        No products match your filters
                    @else
                        No products yet
                    @endif
                </h3>
                <p class="text-sm text-beige-400 font-medium max-w-xs">
                    @if(request('search') || request('category') || request('stock_status'))
                        Try adjusting your search terms or filters.
                    @else
                        Add your first product to start tracking inventory and making sales.
                    @endif
                </p>
            </div>
            @if(request('search') || request('category') || request('stock_status'))
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl border border-beige-200 text-sm font-bold text-beige-600 hover:bg-beige-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                Clear Filters
            </a>
            @else
            <a href="{{ route('products.create') }}"
               class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-mint-500 text-white text-sm font-extrabold hover:bg-mint-600 transition-all shadow-lg shadow-mint-500/25">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Add Your First Product
            </a>
            @endif
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="mt-8 flex items-center justify-between gap-4 flex-wrap">
        <p class="text-xs font-semibold text-beige-400">
            Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }} products
        </p>
        <div class="pagination-premium">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
    @endif

</div>
@endsection

@push('styles')
<style>
/* Pagination styling */
.pagination-premium nav { display: flex; align-items: center; gap: 4px; }
.pagination-premium span[aria-current="page"] > span,
.pagination-premium a {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 36px; padding: 0 12px;
    border-radius: 12px;
    font-size: 13px; font-weight: 700;
    border: 1px solid rgba(210,194,168,0.5);
    color: #57442D;
    background: #fff;
    transition: all 0.15s ease;
    text-decoration: none;
}
.pagination-premium a:hover { background: #F7F2E8; color: #10B981; border-color: rgba(16,185,129,0.3); }
.pagination-premium span[aria-current="page"] > span {
    background: #10B981; color: #fff; border-color: #10B981;
    box-shadow: 0 4px 12px rgba(16,185,129,0.3);
}
.pagination-premium span[aria-disabled="true"] > span {
    opacity: 0.35; cursor: not-allowed;
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 36px; padding: 0 12px;
    border-radius: 12px; font-size: 13px; font-weight: 700;
    border: 1px solid rgba(210,194,168,0.3); color: #AA8D63;
}
</style>
@endpush

@push('scripts')
<script>
function productIndex() {
    return {
        view: localStorage.getItem('products_view') || 'grid',

        init() {
            this.$watch('view', v => {
                localStorage.setItem('products_view', v);
            });
        },
    };
}
</script>
@endpush
