@extends('layouts.app')
@section('title', 'Point of Sale')
@section('page-title', 'Point of Sale')

@push('styles')
<style>
/* =========================================================
   POS — Premium Enterprise Checkout System
   8px grid · Outfit · warm green-beige palette
   ========================================================= */

/* Full-height layout */
#pos-root {
    height: calc(100vh - 90px);
    min-height: 600px;
}
@media (max-width: 1023px) {
    #pos-root { height: auto; min-height: unset; }
}

/* Scrollbar styling */
.custom-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
.custom-scroll::-webkit-scrollbar-track { background: transparent; }
.custom-scroll::-webkit-scrollbar-thumb { background: rgba(210,194,168,0.5); border-radius: 99px; }

/* Product card */
.product-card {
    transition: transform 0.2s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.2s ease, border-color 0.15s ease;
    cursor: pointer;
    will-change: transform;
}
.product-card:hover:not(.disabled) {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px -8px rgba(16,185,129,0.18);
    border-color: rgba(16,185,129,0.4);
}
.product-card:active:not(.disabled) {
    transform: scale(0.97);
}
.product-card.disabled {
    opacity: 0.45;
    cursor: not-allowed;
    filter: grayscale(0.5);
}

/* Category chip */
.cat-chip {
    transition: all 0.2s ease;
    white-space: nowrap;
    flex-shrink: 0;
}
.cat-chip.active {
    background: #10B981;
    color: #fff;
    box-shadow: 0 4px 12px rgba(16,185,129,0.3);
}
.cat-chip:not(.active):hover {
    border-color: rgba(16,185,129,0.5);
    color: #1CA074;
    background: rgba(16,185,129,0.05);
}

/* Cart item row */
.cart-row {
    transition: background 0.15s ease;
}
.cart-row:hover { background: #F7F2E8; }

/* Quantity button */
.qty-btn {
    min-width: 32px; min-height: 32px;
    width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 10px;
    font-size: 16px; font-weight: 800;
    transition: all 0.15s ease;
    flex-shrink: 0;
}
.qty-btn:not(:disabled):hover { transform: scale(1.1); }
.qty-btn:active:not(:disabled) { transform: scale(0.92); }

/* Payment method button */
.pay-btn {
    transition: all 0.2s cubic-bezier(0.34,1.56,0.64,1);
    min-height: 52px;
    touch-action: manipulation;
    position: relative;
    overflow: hidden;
}
.pay-btn.active {
    transform: scale(1.03);
}
.pay-btn::after {
    content: '';
    position: absolute; inset: 0;
    background: rgba(255,255,255,0.15);
    opacity: 0;
    transition: opacity 0.2s;
}
.pay-btn:active::after { opacity: 1; }

/* Checkout button pulse */
@keyframes completePulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.4); }
    50%       { box-shadow: 0 0 0 10px rgba(16,185,129,0); }
}
.checkout-btn:not(:disabled) { animation: completePulse 2.5s ease infinite; }
.checkout-btn:disabled { animation: none; }

/* Loading spinner */
@keyframes spin { to { transform: rotate(360deg); } }
.spinner { animation: spin 0.7s linear infinite; }

/* Cart badge bounce */
@keyframes cartBounce {
    0%, 100% { transform: scale(1); }
    40%       { transform: scale(1.35); }
    60%       { transform: scale(0.9); }
}
.cart-badge-anim { animation: cartBounce 0.4s ease; }

/* Empty cart illustration */
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-8px); }
}
.empty-cart-icon { animation: float 3s ease-in-out infinite; }

/* Success checkmark */
@keyframes checkDraw {
    from { stroke-dashoffset: 60; }
    to   { stroke-dashoffset: 0; }
}
.check-path {
    stroke-dasharray: 60;
    stroke-dashoffset: 60;
    animation: checkDraw 0.6s ease 0.2s both;
}

/* Bottom sheet (mobile checkout) */
.bottom-sheet {
    transition: transform 0.35s cubic-bezier(0.32, 0.72, 0, 1), opacity 0.25s ease;
}
.bottom-sheet.hidden-sheet {
    transform: translateY(100%);
    opacity: 0;
    pointer-events: none;
}

/* Badge */
.badge-low  { background: #FEF3C7; color: #92400E; }
.badge-out  { background: #fee2e2; color: #991b1b; }
.badge-free { background: #DDF6ED; color: #155541; }

/* Search highlight */
.search-bar:focus-within {
    box-shadow: 0 0 0 3px rgba(16,185,129,0.15);
}

/* Smooth fade transition for product grid */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.product-fade { animation: fadeIn 0.25s ease both; }

/* Touch targets */
button, a, input, select {
    min-height: 44px;
}
.qty-btn { min-height: 32px !important; height: 32px !important; }
.cat-chip { min-height: 38px; }
</style>
@endpush

@section('content')
{{-- Remove default padding for full-height POS --}}
<div x-data="posApp()" x-init="init()" id="pos-root" class="flex flex-col lg:flex-row gap-0 -m-4 sm:-m-6">

    {{-- ══════════════════════════════════════
         LEFT PANEL — Product Catalog
    ══════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-beige-50 lg:border-r border-beige-200">

        {{-- ── Search Bar ── --}}
        <div class="p-4 sm:p-5 border-b border-beige-200 bg-white">
            <div class="search-bar flex items-center gap-2 bg-beige-50 border border-beige-200 rounded-2xl px-4 py-1 transition-all duration-200" style="min-height:52px;">
                {{-- Search icon --}}
                <svg class="w-5 h-5 text-beige-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                {{-- Input --}}
                <input id="pos-search"
                       type="text"
                       x-model="search"
                       @input="filterProducts()"
                       @keydown.escape="search=''; filterProducts()"
                       placeholder="Search products or scan barcode…"
                       autocomplete="off"
                       class="flex-1 bg-transparent text-sm font-semibold text-mint-900 placeholder-beige-400 focus:outline-none border-none ring-0 py-2"
                       style="min-height:unset;">
                {{-- Clear button --}}
                <button x-show="search"
                        @click="search=''; filterProducts(); $el.previousElementSibling.previousElementSibling.focus()"
                        class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-beige-200 text-beige-500 hover:bg-beige-300 transition-colors"
                        style="min-height:unset;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                {{-- Barcode icon --}}
                <div class="flex-shrink-0 w-8 h-8 rounded-xl bg-mint-50 border border-mint-100 flex items-center justify-center ml-1" title="Scan barcode — focus search and scan" style="min-height:unset;">
                    <svg class="w-4 h-4 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4h.01M8 4h.01M16 4h.01M4 8v.01M20 8v.01M4 12v.01M20 12v.01M4 16v.01M20 16v.01M8 20h.01M12 20h.01M16 20h.01"/>
                        <rect x="7" y="7" width="10" height="10" rx="1" stroke-width="1.5"/>
                    </svg>
                </div>
            </div>
            {{-- Results count --}}
            <div class="mt-2 flex items-center justify-between px-1">
                <p class="text-[11px] font-semibold text-beige-400">
                    <span x-text="filteredProducts.length"></span> product<span x-show="filteredProducts.length !== 1">s</span>
                    <span x-show="search || selectedCategory"> found</span>
                </p>
                <p class="text-[10px] text-beige-300 font-medium hidden sm:block">Press <kbd class="px-1.5 py-0.5 rounded bg-beige-100 text-beige-500 font-mono text-[9px]">Esc</kbd> to clear</p>
            </div>
        </div>

        {{-- ── Category Chips ── --}}
        <div class="px-4 sm:px-5 py-3 border-b border-beige-200 bg-white overflow-x-auto custom-scroll flex-shrink-0">
            <div class="flex items-center gap-2" style="min-width: max-content;">
                {{-- All --}}
                <button @click="selectedCategory = null; filterProducts()"
                        :class="!selectedCategory ? 'active' : ''"
                        class="cat-chip inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-beige-200 text-xs font-bold text-mint-900 bg-white transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    All
                    <span class="px-1.5 py-0.5 rounded-full bg-white/30 text-[9px] font-black" :class="!selectedCategory ? 'text-white bg-white/30' : 'text-beige-400 bg-beige-100'" x-text="allProducts.length"></span>
                </button>
                {{-- Categories --}}
                @foreach($categories as $cat)
                <button @click="selectedCategory = {{ $cat->id }}; filterProducts()"
                        :class="selectedCategory === {{ $cat->id }} ? 'active' : ''"
                        class="cat-chip inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-beige-200 text-xs font-bold text-mint-900 bg-white transition-all">
                    {{ $cat->name }}
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black" :class="selectedCategory === {{ $cat->id }} ? 'text-white bg-white/30' : 'text-beige-400 bg-beige-100'">{{ $cat->active_products_count }}</span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- ── Product Grid ── --}}
        <div class="flex-1 overflow-y-auto custom-scroll p-4 sm:p-5">
            {{-- Empty state --}}
            <div x-show="filteredProducts.length === 0" class="flex flex-col items-center justify-center py-16 gap-4">
                <div class="empty-cart-icon w-20 h-20 rounded-3xl bg-beige-100 flex items-center justify-center">
                    <svg class="w-10 h-10 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold text-beige-500">No products found</p>
                    <p class="text-xs text-beige-400 mt-1">Try a different search or category</p>
                </div>
                <button @click="search=''; selectedCategory=null; filterProducts()"
                        class="px-4 py-2 rounded-xl bg-mint-500 text-white text-xs font-bold hover:bg-mint-600 transition-colors">
                    Clear filters
                </button>
            </div>

            {{-- Product cards grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-3">
                <template x-for="(product, idx) in filteredProducts" :key="product.id">
                    <button
                        @click="addToCart(product)"
                        :disabled="product.track_stock && product.stock_quantity <= 0"
                        :class="{
                            'product-card': true,
                            'disabled': product.track_stock && product.stock_quantity <= 0,
                            'product-fade': true
                        }"
                        class="bg-white border border-beige-200/70 rounded-2xl p-4 text-left flex flex-col gap-3 shadow-sm hover:shadow-lg transition-all group focus:outline-none focus:ring-2 focus:ring-mint-400 focus:ring-offset-1"
                        :style="`animation-delay: ${Math.min(idx * 30, 200)}ms`">

                        {{-- Product icon / image placeholder --}}
                        <div class="w-full aspect-square rounded-xl flex items-center justify-center text-3xl relative overflow-hidden"
                             :class="{
                                 'bg-mint-50': !(product.track_stock && product.stock_quantity <= 0),
                                 'bg-beige-50': product.track_stock && product.stock_quantity <= 0
                             }">
                            <svg class="w-10 h-10 transition-transform duration-300 group-hover:scale-110"
                                 :class="product.track_stock && product.stock_quantity <= 0 ? 'text-beige-300' : 'text-mint-300'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            {{-- Stock badge --}}
                            <div class="absolute top-2 right-2">
                                <span x-show="product.track_stock && product.stock_quantity <= 0"
                                      class="badge-out text-[9px] font-black uppercase px-2 py-0.5 rounded-full">Out</span>
                                <span x-show="product.track_stock && product.stock_quantity > 0 && product.stock_quantity <= (product.reorder_level || 5)"
                                      class="badge-low text-[9px] font-black uppercase px-2 py-0.5 rounded-full">Low</span>
                            </div>
                            {{-- Add overlay on hover --}}
                            <div class="absolute inset-0 bg-mint-500/8 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center rounded-xl"
                                 x-show="!(product.track_stock && product.stock_quantity <= 0)">
                                <div class="w-8 h-8 rounded-full bg-mint-500 flex items-center justify-center shadow-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Product info --}}
                        <div class="flex-1 min-w-0">
                            {{-- Category --}}
                            <p class="text-[9px] font-bold uppercase tracking-widest text-beige-400 truncate mb-0.5" x-text="product.category?.name || 'Uncategorized'"></p>
                            {{-- Name --}}
                            <p class="text-sm font-extrabold text-mint-900 leading-tight line-clamp-2" x-text="product.name"></p>
                        </div>

                        {{-- Price row --}}
                        <div class="flex items-end justify-between gap-1">
                            <span class="text-base font-black text-mint-600" x-text="'₱' + parseFloat(product.selling_price).toFixed(2)"></span>
                            <span class="text-[10px] font-bold text-beige-400 flex-shrink-0"
                                  x-text="product.track_stock ? (product.stock_quantity + ' left') : '∞ stk'"></span>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         RIGHT PANEL — Checkout (Desktop sticky)
    ══════════════════════════════════════ --}}
    <div class="hidden lg:flex w-96 flex-col bg-white border-l border-beige-200 flex-shrink-0 overflow-hidden" id="checkout-panel">

        {{-- Cart Header --}}
        <div class="px-5 py-4 border-b border-beige-100 bg-mint-900 flex-shrink-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        {{-- Item count badge --}}
                        <span x-show="cart.length > 0"
                              x-text="cart.reduce((s,i) => s+i.qty, 0)"
                              class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] rounded-full bg-amber-400 text-[10px] font-black text-mint-900 flex items-center justify-center px-1 shadow-sm">
                        </span>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-white">Current Sale</h2>
                        <p class="text-[10px] text-mint-300 font-medium" x-text="cart.length === 0 ? 'Empty cart' : cart.length + ' item type' + (cart.length !== 1 ? 's' : '')"></p>
                    </div>
                </div>
                <button @click="clearCart()"
                        x-show="cart.length > 0"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-[10px] font-bold text-mint-200 uppercase tracking-wide transition-all"
                        style="min-height:unset;">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Clear
                </button>
            </div>
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto custom-scroll divide-y divide-beige-100 min-h-0">

            {{-- Empty state --}}
            <template x-if="cart.length === 0">
                <div class="flex flex-col items-center justify-center h-full py-12 gap-4 px-6">
                    <div class="empty-cart-icon">
                        <div class="w-20 h-20 rounded-3xl bg-mint-50 border-2 border-dashed border-mint-200 flex items-center justify-center">
                            <svg class="w-10 h-10 text-mint-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-bold text-mint-900">Cart is empty</p>
                        <p class="text-xs text-beige-400 mt-1.5 leading-relaxed">Click any product card to add it to the cart. Scan a barcode for instant add.</p>
                    </div>
                    <button @click="$refs.searchInput?.focus()"
                            class="px-4 py-2 rounded-xl border border-mint-200 text-xs font-bold text-mint-600 hover:bg-mint-50 transition-colors">
                        Search Products
                    </button>
                </div>
            </template>

            {{-- Cart items --}}
            <template x-for="(item, index) in cart" :key="item.product_id">
                <div class="cart-row px-4 py-3.5 flex items-center gap-3 group">
                    {{-- Product icon --}}
                    <div class="w-10 h-10 rounded-xl bg-mint-50 border border-mint-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-mint-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    {{-- Name + price --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-mint-900 truncate" x-text="item.name"></p>
                        <p class="text-xs text-beige-400 font-semibold" x-text="'₱' + parseFloat(item.price).toFixed(2) + ' each'"></p>
                    </div>
                    {{-- Qty controls --}}
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        {{-- Minus / remove --}}
                        <button @click="updateQty(index, -1)"
                                class="qty-btn bg-beige-100 text-beige-600 hover:bg-red-100 hover:text-red-600 transition-all"
                                :title="item.qty === 1 ? 'Remove item' : 'Decrease quantity'">
                            <template x-if="item.qty === 1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </template>
                            <template x-if="item.qty > 1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                            </template>
                        </button>
                        {{-- Qty display --}}
                        <span class="w-7 text-center text-sm font-black text-mint-900" x-text="item.qty"></span>
                        {{-- Plus --}}
                        <button @click="updateQty(index, 1)"
                                :disabled="item.track_stock && item.qty >= item.stock"
                                :class="item.track_stock && item.qty >= item.stock ? 'opacity-30 cursor-not-allowed' : 'hover:bg-mint-100 hover:text-mint-600'"
                                class="qty-btn bg-beige-100 text-beige-600 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                    {{-- Line total --}}
                    <div class="w-20 text-right flex-shrink-0">
                        <span class="text-sm font-black text-mint-900" x-text="'₱' + (item.price * item.qty).toFixed(2)"></span>
                    </div>
                </div>
            </template>
        </div>

        {{-- ── CHECKOUT FOOTER ── --}}
        <div class="border-t border-beige-200 bg-white flex-shrink-0 overflow-y-auto custom-scroll" style="max-height: 62vh;">
            <div class="p-4 space-y-3">

                {{-- Subtotal row --}}
                <div class="flex justify-between items-center text-sm">
                    <span class="font-semibold text-beige-500">Subtotal</span>
                    <span class="font-bold text-mint-900" x-text="'₱' + subtotal.toFixed(2)"></span>
                </div>

                {{-- Discount --}}
                <div class="flex items-center gap-2">
                    <label class="text-sm font-semibold text-beige-500 whitespace-nowrap">Discount</label>
                    <div class="flex-1 flex items-center gap-1 bg-beige-50 border border-beige-200 rounded-xl px-3" style="min-height:40px;">
                        <span class="text-sm text-beige-400 font-bold">₱</span>
                        <input type="number" x-model.number="discount" min="0" step="0.01"
                               :max="subtotal"
                               class="flex-1 bg-transparent text-sm font-bold text-mint-900 text-right focus:outline-none border-none ring-0 py-2"
                               style="min-height:unset;" placeholder="0.00">
                    </div>
                </div>

                {{-- Total --}}
                <div class="flex justify-between items-center py-3 border-t border-b border-beige-100">
                    <span class="text-base font-extrabold text-mint-900">Total Due</span>
                    <span class="text-2xl font-black text-mint-600" x-text="'₱' + total.toFixed(2)"></span>
                </div>

                {{-- Customer Selector --}}
                <div>
                    <label class="block text-[10px] font-bold text-beige-400 uppercase tracking-widest mb-1.5">Customer</label>
                    <select x-model="customerId"
                            class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm font-semibold text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all"
                            style="min-height:44px;">
                        <option value="">👤 Walk-in Customer</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}">
                                {{ $cust->name }}@if($cust->balance > 0) — Utang: ₱{{ number_format($cust->balance, 2) }}@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Payment Method --}}
                <div>
                    <label class="block text-[10px] font-bold text-beige-400 uppercase tracking-widest mb-2">Payment Method</label>
                    <div class="grid grid-cols-2 gap-2">
                        {{-- Cash --}}
                        <button @click="paymentMethod = 'cash'; isCredit = false"
                                :class="paymentMethod === 'cash' && !isCredit ? 'active bg-mint-500 text-white border-mint-500 shadow-lg shadow-mint-200' : 'bg-white border-beige-200 text-mint-900 hover:border-mint-300'"
                                class="pay-btn flex items-center justify-center gap-2 rounded-xl border text-xs font-bold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Cash
                        </button>
                        {{-- GCash --}}
                        <button @click="paymentMethod = 'gcash'; isCredit = false"
                                :class="paymentMethod === 'gcash' && !isCredit ? 'active bg-blue-500 text-white border-blue-500 shadow-lg shadow-blue-200' : 'bg-white border-beige-200 text-mint-900 hover:border-blue-300'"
                                class="pay-btn flex items-center justify-center gap-2 rounded-xl border text-xs font-bold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            GCash
                        </button>
                        {{-- Bank Transfer --}}
                        <button @click="paymentMethod = 'bank_transfer'; isCredit = false"
                                :class="paymentMethod === 'bank_transfer' && !isCredit ? 'active bg-purple-500 text-white border-purple-500 shadow-lg shadow-purple-200' : 'bg-white border-beige-200 text-mint-900 hover:border-purple-300'"
                                class="pay-btn flex items-center justify-center gap-2 rounded-xl border text-xs font-bold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                            Bank
                        </button>
                        {{-- Store Credit / Utang --}}
                        @if(is_sari_sari())
                        <button @click="isCredit = !isCredit; if(isCredit) paymentMethod = 'cash'"
                                :class="isCredit ? 'active bg-amber-500 text-white border-amber-500 shadow-lg shadow-amber-200' : 'bg-white border-beige-200 text-mint-900 hover:border-amber-300'"
                                class="pay-btn flex items-center justify-center gap-2 rounded-xl border text-xs font-bold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Utang
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Credit badge --}}
                <div x-show="isCredit" x-transition
                     class="flex items-center gap-2 px-3 py-2.5 bg-amber-50 border border-amber-200 rounded-xl">
                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs font-bold text-amber-700">This will be recorded as credit (utang). A 30-day due date will be set.</p>
                </div>

                {{-- Cash tendered (only for cash, non-credit) --}}
                <div x-show="paymentMethod === 'cash' && !isCredit" x-transition>
                    <label class="block text-[10px] font-bold text-beige-400 uppercase tracking-widest mb-1.5">Amount Tendered</label>
                    <div class="flex items-center gap-1 bg-beige-50 border border-beige-200 rounded-xl px-3 focus-within:border-mint-400 focus-within:ring-2 focus-within:ring-mint-500/15 transition-all" style="min-height:48px;">
                        <span class="text-base font-black text-beige-400">₱</span>
                        <input type="number"
                               x-model.number="amountTendered"
                               min="0" step="1"
                               class="flex-1 bg-transparent text-lg font-black text-mint-900 text-right focus:outline-none border-none ring-0 py-2"
                               style="min-height:unset;"
                               placeholder="0">
                    </div>
                    {{-- Quick amount buttons --}}
                    <div class="flex gap-2 mt-2 flex-wrap">
                        <template x-for="quick in quickAmounts" :key="quick">
                            <button @click="amountTendered = quick"
                                    :class="amountTendered === quick ? 'bg-mint-500 text-white border-mint-500' : 'bg-white border-beige-200 text-mint-900 hover:border-mint-300'"
                                    class="flex-1 min-w-[56px] py-2 rounded-xl border text-xs font-bold transition-all"
                                    style="min-height:36px;"
                                    x-text="'₱' + quick.toLocaleString()">
                            </button>
                        </template>
                        <button @click="amountTendered = Math.ceil(total / 100) * 100"
                                class="flex-1 min-w-[56px] py-2 rounded-xl border border-beige-200 bg-white text-mint-900 text-xs font-bold hover:border-mint-300 transition-all"
                                style="min-height:36px;">Exact</button>
                    </div>
                    {{-- Change display --}}
                    <div x-show="amountTendered > 0" x-transition
                         class="mt-2 flex items-center justify-between px-3 py-2 rounded-xl"
                         :class="change >= 0 ? 'bg-mint-50 border border-mint-100' : 'bg-red-50 border border-red-100'">
                        <span class="text-xs font-bold" :class="change >= 0 ? 'text-mint-700' : 'text-red-700'">Change</span>
                        <span class="text-base font-black" :class="change >= 0 ? 'text-mint-600' : 'text-red-600'" x-text="'₱' + Math.abs(change).toFixed(2)"></span>
                    </div>
                </div>

                {{-- Complete Sale Button --}}
                <button @click="processCheckout()"
                        :disabled="cart.length === 0 || processing || (paymentMethod === 'cash' && !isCredit && amountTendered > 0 && change < 0)"
                        class="checkout-btn w-full py-4 rounded-2xl font-extrabold text-sm transition-all shadow-lg disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none relative overflow-hidden"
                        :class="isCredit ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-amber-300' : 'bg-mint-500 hover:bg-mint-600 text-white shadow-mint-300'"
                        id="complete-sale-btn">
                    {{-- Normal state --}}
                    <span x-show="!processing" class="flex items-center justify-center gap-2">
                        <template x-if="isCredit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </template>
                        <template x-if="!isCredit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </template>
                        <span x-text="isCredit ? 'Record Utang' : 'Complete Sale  ·  ₱' + total.toFixed(2)"></span>
                    </span>
                    {{-- Loading state --}}
                    <span x-show="processing" class="flex items-center justify-center gap-2">
                        <svg class="spinner w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Processing…
                    </span>
                </button>

                {{-- Keyboard shortcut hint --}}
                <p class="text-center text-[10px] text-beige-300 font-medium -mt-1">
                    Press <kbd class="px-1.5 py-0.5 rounded bg-beige-100 text-beige-400 font-mono text-[9px]">F9</kbd> to complete sale
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MOBILE — Floating Cart Button
    ══════════════════════════════════════ --}}
    <div class="lg:hidden fixed bottom-6 right-4 z-40" x-show="!mobileCartOpen">
        <button @click="mobileCartOpen = true"
                class="relative flex items-center gap-3 pl-4 pr-5 py-3.5 rounded-2xl bg-mint-900 text-white shadow-2xl shadow-mint-900/40 hover:bg-mint-800 active:scale-95 transition-all"
                style="min-height:56px;">
            <div class="relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span x-show="cart.length > 0"
                      x-text="cart.reduce((s,i) => s+i.qty, 0)"
                      class="absolute -top-2 -right-2 min-w-[18px] h-[18px] rounded-full bg-amber-400 text-[10px] font-black text-mint-900 flex items-center justify-center px-1">
                </span>
            </div>
            <div class="text-left">
                <p class="text-xs font-bold leading-tight" x-text="cart.length === 0 ? 'Cart Empty' : cart.length + ' item' + (cart.length !== 1 ? 's' : '')"></p>
                <p class="text-[10px] text-mint-300 font-semibold leading-tight" x-text="'₱' + total.toFixed(2)"></p>
            </div>
        </button>
    </div>

    {{-- ══════════════════════════════════════
         MOBILE — Bottom Sheet Checkout
    ══════════════════════════════════════ --}}
    <div class="lg:hidden fixed inset-0 z-50" x-show="mobileCartOpen" style="display:none;">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-mint-900/50 backdrop-blur-sm"
             @click="mobileCartOpen = false"
             x-transition:enter="transition-opacity ease duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>
        {{-- Sheet --}}
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl max-h-[90vh] flex flex-col"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-y-full"
             x-transition:enter-end="transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform translate-y-0"
             x-transition:leave-end="transform translate-y-full">

            {{-- Sheet handle --}}
            <div class="flex justify-center pt-3 pb-2">
                <div class="w-10 h-1 rounded-full bg-beige-200"></div>
            </div>

            {{-- Sheet header --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-beige-100">
                <h2 class="text-base font-extrabold text-mint-900">Current Sale</h2>
                <div class="flex items-center gap-3">
                    <button x-show="cart.length > 0" @click="clearCart()"
                            class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors"
                            style="min-height:unset;">Clear</button>
                    <button @click="mobileCartOpen = false"
                            class="w-8 h-8 flex items-center justify-center rounded-xl bg-beige-100 text-beige-500 hover:bg-beige-200 transition-colors"
                            style="min-height:unset; min-width:32px;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
            </div>

            {{-- Sheet content --}}
            <div class="flex-1 overflow-y-auto custom-scroll">
                {{-- Cart items --}}
                <div class="divide-y divide-beige-100">
                    <template x-if="cart.length === 0">
                        <div class="py-12 text-center">
                            <p class="text-sm font-bold text-beige-400">Cart is empty</p>
                            <p class="text-xs text-beige-300 mt-1">Go back and tap a product</p>
                        </div>
                    </template>
                    <template x-for="(item, index) in cart" :key="item.product_id">
                        <div class="cart-row flex items-center gap-3 px-5 py-3.5">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-mint-900 truncate" x-text="item.name"></p>
                                <p class="text-xs text-beige-400 font-semibold" x-text="'₱' + parseFloat(item.price).toFixed(2)"></p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <button @click="updateQty(index, -1)" class="qty-btn bg-beige-100 text-beige-600 hover:bg-red-100 hover:text-red-600 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                </button>
                                <span class="w-7 text-center text-sm font-black text-mint-900" x-text="item.qty"></span>
                                <button @click="updateQty(index, 1)"
                                        :disabled="item.track_stock && item.qty >= item.stock"
                                        class="qty-btn bg-beige-100 text-beige-600 hover:bg-mint-100 hover:text-mint-600 transition-all disabled:opacity-30">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                            <span class="w-16 text-right text-sm font-black text-mint-900 flex-shrink-0" x-text="'₱' + (item.price * item.qty).toFixed(2)"></span>
                        </div>
                    </template>
                </div>

                {{-- Mobile checkout form --}}
                <div class="p-5 space-y-4 border-t border-beige-100">
                    <div class="flex justify-between">
                        <span class="text-sm text-beige-500 font-semibold">Subtotal</span>
                        <span class="text-sm font-bold text-mint-900" x-text="'₱' + subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-xl font-black border-t border-beige-100 pt-3">
                        <span class="text-mint-900">Total</span>
                        <span class="text-mint-600" x-text="'₱' + total.toFixed(2)"></span>
                    </div>

                    {{-- Customer --}}
                    <select x-model="customerId" class="w-full px-3 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm font-semibold text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all">
                        <option value="">👤 Walk-in Customer</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}">{{ $cust->name }}@if($cust->balance > 0) (Utang: ₱{{ number_format($cust->balance, 2) }})@endif</option>
                        @endforeach
                    </select>

                    {{-- Payment Method --}}
                    <div class="grid grid-cols-2 gap-2">
                        <button @click="paymentMethod='cash'; isCredit=false" :class="paymentMethod==='cash'&&!isCredit?'bg-mint-500 text-white border-mint-500':'bg-white border-beige-200 text-mint-900'" class="pay-btn flex items-center justify-center gap-1.5 rounded-xl border text-xs font-bold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Cash
                        </button>
                        <button @click="paymentMethod='gcash'; isCredit=false" :class="paymentMethod==='gcash'&&!isCredit?'bg-blue-500 text-white border-blue-500':'bg-white border-beige-200 text-mint-900'" class="pay-btn flex items-center justify-center gap-1.5 rounded-xl border text-xs font-bold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            GCash
                        </button>
                        <button @click="paymentMethod='bank_transfer'; isCredit=false" :class="paymentMethod==='bank_transfer'&&!isCredit?'bg-purple-500 text-white border-purple-500':'bg-white border-beige-200 text-mint-900'" class="pay-btn flex items-center justify-center gap-1.5 rounded-xl border text-xs font-bold transition-all">
                            Bank
                        </button>
                        @if(is_sari_sari())
                        <button @click="isCredit=!isCredit" :class="isCredit?'bg-amber-500 text-white border-amber-500':'bg-white border-beige-200 text-mint-900'" class="pay-btn flex items-center justify-center gap-1.5 rounded-xl border text-xs font-bold transition-all">
                            Utang
                        </button>
                        @endif
                    </div>

                    {{-- Cash tendered --}}
                    <div x-show="paymentMethod==='cash'&&!isCredit" x-transition>
                        <label class="block text-[10px] font-bold text-beige-400 uppercase tracking-widest mb-1.5">Amount Tendered</label>
                        <input type="number" x-model.number="amountTendered" min="0"
                               class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-lg font-black text-mint-900 text-right focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all"
                               placeholder="0.00">
                        <div x-show="amountTendered > 0" class="mt-2 flex justify-between px-3 py-2 rounded-xl"
                             :class="change >= 0 ? 'bg-mint-50 border border-mint-100' : 'bg-red-50 border border-red-100'">
                            <span class="text-xs font-bold" :class="change >= 0 ? 'text-mint-700' : 'text-red-700'">Change</span>
                            <span class="text-sm font-black" :class="change >= 0 ? 'text-mint-600' : 'text-red-600'" x-text="'₱' + Math.abs(change).toFixed(2)"></span>
                        </div>
                    </div>

                    {{-- Complete button --}}
                    <button @click="processCheckout()"
                            :disabled="cart.length === 0 || processing"
                            class="checkout-btn w-full py-4 rounded-2xl font-extrabold text-sm transition-all shadow-lg disabled:opacity-40 disabled:cursor-not-allowed"
                            :class="isCredit ? 'bg-amber-500 text-white' : 'bg-mint-500 text-white'">
                        <span x-show="!processing" class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span x-text="isCredit ? 'Record Utang' : 'Complete Sale · ₱' + total.toFixed(2)"></span>
                        </span>
                        <span x-show="processing" class="flex items-center justify-center gap-2">
                            <svg class="spinner w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Processing…
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         SUCCESS RECEIPT MODAL
    ══════════════════════════════════════ --}}
    <div x-show="showReceipt"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         style="display:none;">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            {{-- Success header --}}
            <div class="bg-mint-500 p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-9 h-9" viewBox="0 0 36 36" fill="none">
                        <circle cx="18" cy="18" r="17" stroke="white" stroke-width="2"/>
                        <path class="check-path" d="M10 18l5.5 5.5L26 12" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-white">Sale Complete!</h3>
                <p class="text-sm text-mint-100 mt-1" x-text="receiptMessage"></p>
            </div>

            {{-- Receipt body --}}
            <div class="p-5">
                <template x-if="receiptData">
                    <div class="border border-beige-200 rounded-2xl overflow-hidden mb-4">
                        {{-- Invoice number --}}
                        <div class="bg-beige-50 px-4 py-3 border-b border-beige-100 text-center">
                            <p class="text-[10px] font-bold text-beige-400 uppercase tracking-widest">Invoice</p>
                            <p class="text-sm font-black text-mint-900" x-text="receiptData.invoice?.invoice_number"></p>
                        </div>
                        {{-- Items --}}
                        <div class="divide-y divide-beige-100">
                            <template x-for="item in receiptData.invoice?.items" :key="item.id">
                                <div class="flex justify-between px-4 py-2.5 text-sm">
                                    <span class="text-beige-600 truncate pr-2" x-text="item.description + ' ×' + item.quantity"></span>
                                    <span class="font-bold text-mint-900 flex-shrink-0" x-text="'₱' + parseFloat(item.amount).toFixed(2)"></span>
                                </div>
                            </template>
                        </div>
                        {{-- Totals --}}
                        <div class="bg-mint-50 px-4 py-3 border-t border-mint-100">
                            <div class="flex justify-between font-extrabold text-base">
                                <span class="text-mint-900">Total</span>
                                <span class="text-mint-600" x-text="'₱' + parseFloat(receiptData.invoice?.total_amount).toFixed(2)"></span>
                            </div>
                            <div x-show="receiptData.change > 0" class="flex justify-between text-sm font-bold text-mint-700 mt-1">
                                <span>Change</span>
                                <span x-text="'₱' + parseFloat(receiptData.change).toFixed(2)"></span>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Actions --}}
                <div class="grid grid-cols-2 gap-3">
                    <button @click="showReceipt = false; mobileCartOpen = false"
                            class="py-3 rounded-xl border border-beige-200 text-sm font-bold text-beige-600 hover:bg-beige-50 transition-colors">
                        Close
                    </button>
                    <button @click="showReceipt = false; mobileCartOpen = false; $nextTick(() => document.getElementById('pos-search')?.focus())"
                            class="py-3 rounded-xl bg-mint-500 text-white text-sm font-bold hover:bg-mint-600 transition-colors shadow-lg shadow-mint-200">
                        New Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function posApp() {
    return {
        /* ─── State ─── */
        allProducts:      @json($products),
        filteredProducts: @json($products),
        selectedCategory: null,
        search:           '',
        cart:             [],
        customerId:       '',
        paymentMethod:    'cash',
        isCredit:         false,
        discount:         0,
        amountTendered:   0,
        processing:       false,
        showReceipt:      false,
        receiptData:      null,
        receiptMessage:   '',
        mobileCartOpen:   false,
        quickAmounts:     [20, 50, 100, 200, 500, 1000],

        /* ─── Computed ─── */
        get subtotal() { return this.cart.reduce((s, i) => s + i.price * i.qty, 0); },
        get total()    { return Math.max(0, this.subtotal - (this.discount || 0)); },
        get change()   { return this.amountTendered - this.total; },

        /* ─── Init ─── */
        init() {
            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                // F9 → complete sale
                if (e.key === 'F9') {
                    e.preventDefault();
                    this.processCheckout();
                }
                // Escape closes modals
                if (e.key === 'Escape' && this.showReceipt) {
                    this.showReceipt = false;
                    this.mobileCartOpen = false;
                }
            });

            // Focus search on load
            this.$nextTick(() => {
                document.getElementById('pos-search')?.focus();
            });

            // Generate smart quick-amount list based on total
            this.$watch('total', (val) => {
                const base = Math.ceil(val / 50) * 50;
                this.quickAmounts = [...new Set([50, 100, 200, 500, 1000, base])].filter(a => a >= val).sort((a,b)=>a-b).slice(0, 5);
                if (this.quickAmounts.length < 3) this.quickAmounts = [50, 100, 200, 500, 1000];
            });
        },

        /* ─── Filter Products ─── */
        filterProducts() {
            const q = this.search.toLowerCase().trim();
            this.filteredProducts = this.allProducts.filter(p => {
                const matchCat    = !this.selectedCategory || p.category_id === this.selectedCategory;
                const matchSearch = !q
                    || p.name.toLowerCase().includes(q)
                    || (p.barcode && p.barcode.includes(q))
                    || (p.sku    && p.sku.toLowerCase().includes(q));
                return matchCat && matchSearch;
            });

            // Auto-add on exact barcode scan
            if (q && this.filteredProducts.length === 1 && this.filteredProducts[0].barcode === this.search.trim()) {
                this.addToCart(this.filteredProducts[0]);
                this.search = '';
                this.filterProducts();
            }
        },

        /* ─── Add to Cart ─── */
        addToCart(product) {
            if (product.track_stock && product.stock_quantity <= 0) return;

            const existing = this.cart.find(i => i.product_id === product.id);
            if (existing) {
                if (product.track_stock && existing.qty >= product.stock_quantity) return;
                existing.qty++;
            } else {
                this.cart.push({
                    product_id:  product.id,
                    name:        product.name,
                    price:       parseFloat(product.selling_price),
                    qty:         1,
                    stock:       product.stock_quantity,
                    track_stock: product.track_stock,
                });
            }
        },

        /* ─── Update Qty ─── */
        updateQty(index, delta) {
            const item = this.cart[index];
            if (delta > 0 && item.track_stock && item.qty >= item.stock) return;
            item.qty += delta;
            if (item.qty <= 0) this.cart.splice(index, 1);
        },

        /* ─── Clear Cart ─── */
        clearCart() {
            this.cart = [];
            this.discount = 0;
            this.amountTendered = 0;
            this.isCredit = false;
        },

        /* ─── Process Checkout ─── */
        async processCheckout() {
            if (this.cart.length === 0 || this.processing) return;
            this.processing = true;
            try {
                const res = await fetch('{{ route("pos.checkout") }}', {
                    method:  'POST',
                    headers: {
                        'Content-Type':  'application/json',
                        'X-CSRF-TOKEN':  '{{ csrf_token() }}',
                        'Accept':        'application/json',
                    },
                    body: JSON.stringify({
                        items:           this.cart.map(i => ({ product_id: i.product_id, quantity: i.qty, price: i.price })),
                        customer_id:     this.customerId || null,
                        payment_method:  this.paymentMethod,
                        amount_tendered: this.amountTendered,
                        is_credit:       this.isCredit,
                        discount:        this.discount,
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    this.receiptData    = data;
                    this.receiptMessage = data.message;
                    this.showReceipt    = true;
                    this.clearCart();
                    this.customerId = '';
                } else {
                    alert(data.message || 'Checkout failed. Please try again.');
                }
            } catch (err) {
                alert('Connection error: ' + err.message);
            }
            this.processing = false;
        },
    };
}
</script>
@endpush
