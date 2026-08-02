@extends('layouts.app')
@section('title', 'Add Product')
@section('page-title', 'Add Product')

@push('styles')
<style>
/* ──────────────────────────────────────────────
   PRODUCT FORM — Premium SaaS Design System
   ────────────────────────────────────────────── */

/* Form section card */
.form-card {
    background: #fff;
    border: 1px solid rgba(210,194,168,0.45);
    border-radius: 20px;
    overflow: hidden;
    transition: box-shadow 0.2s ease;
}

/* Section header */
.section-header {
    padding: 20px 24px 16px;
    border-bottom: 1px solid rgba(210,194,168,0.3);
    background: #FAFAF9;
}

/* Input group */
.input-group { position: relative; }
.input-group .input-icon {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
    color: #AA8D63; pointer-events: none; width: 16px; height: 16px;
    transition: color 0.15s ease;
}
.input-group:focus-within .input-icon { color: #10B981; }
.input-with-icon { padding-left: 40px !important; }

/* Premium input */
.form-input {
    width: 100%;
    padding: 11px 14px;
    background: #F7F2E8;
    border: 1.5px solid rgba(210,194,168,0.6);
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    color: #155541;
    font-family: 'Outfit', sans-serif;
    transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
    min-height: 44px;
}
.form-input:focus {
    outline: none;
    border-color: #10B981;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
}
.form-input.error {
    border-color: #ef4444;
    background: #fff5f5;
    box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
}
.form-input::placeholder { color: #BFA885; }

/* Price input prefix */
.price-wrap { position: relative; }
.price-prefix {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    font-weight: 800; font-size: 14px; color: #AA8D63;
    pointer-events: none; transition: color 0.15s;
    font-family: 'Outfit', sans-serif;
}
.price-wrap:focus-within .price-prefix { color: #10B981; }
.form-input.price-input { padding-left: 28px !important; }

/* Field label */
.field-label {
    display: block;
    font-size: 11px; font-weight: 700;
    color: #8F7149;
    text-transform: uppercase; letter-spacing: 0.06em;
    margin-bottom: 6px;
}
.field-label .req { color: #10B981; margin-left: 2px; }

/* Field hint */
.field-hint {
    font-size: 11px; font-weight: 500; color: #AA8D63;
    margin-top: 4px; line-height: 1.4;
}

/* Toggle switch */
.toggle-switch {
    position: relative; display: inline-flex;
    align-items: center; gap: 10px; cursor: pointer;
}
.toggle-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
.toggle-track {
    width: 44px; height: 24px; border-radius: 99px;
    background: rgba(210,194,168,0.5);
    border: 1.5px solid rgba(210,194,168,0.7);
    transition: all 0.25s ease; position: relative; flex-shrink: 0;
}
.toggle-thumb {
    position: absolute; top: 2px; left: 2px;
    width: 18px; height: 18px; border-radius: 99px;
    background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.2);
    transition: transform 0.25s ease;
}
.toggle-switch input:checked ~ .toggle-track { background: #10B981; border-color: #10B981; }
.toggle-switch input:checked ~ .toggle-track .toggle-thumb { transform: translateX(20px); }

/* Image drop zone */
.drop-zone {
    border: 2px dashed rgba(210,194,168,0.7);
    border-radius: 16px;
    background: #F7F2E8;
    transition: all 0.2s ease;
    cursor: pointer; position: relative;
}
.drop-zone.dragging {
    border-color: #10B981;
    background: rgba(16,185,129,0.04);
    box-shadow: 0 0 0 4px rgba(16,185,129,0.1);
}
.drop-zone:hover {
    border-color: #10B981;
    background: rgba(16,185,129,0.02);
}

/* Profit badge */
.profit-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; border-radius: 99px;
    font-size: 11px; font-weight: 800;
    transition: all 0.2s ease;
}
.profit-good   { background: #DDF6ED; color: #155541; }
.profit-mid    { background: #FEF3C7; color: #92400E; }
.profit-low    { background: #fee2e2; color: #991b1b; }

/* Submit button states */
.submit-btn {
    transition: all 0.2s ease;
    position: relative; overflow: hidden;
}
.submit-btn:not(:disabled):hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 28px -6px rgba(16,185,129,0.4);
}
.submit-btn:disabled { opacity: 0.7; cursor: not-allowed; transform: none; box-shadow: none; }

/* Spinner */
@keyframes spin { to { transform: rotate(360deg); } }
.spinner-icon { animation: spin 0.7s linear infinite; }

/* Error message */
.field-error {
    display: flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 600; color: #dc2626; margin-top: 4px;
}

/* SKU auto-generate button */
.sku-gen-btn {
    position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
    padding: 3px 8px; border-radius: 7px;
    background: #DDF6ED; color: #1CA074;
    font-size: 10px; font-weight: 800;
    border: none; cursor: pointer;
    transition: all 0.15s ease;
    min-height: unset;
}
.sku-gen-btn:hover { background: #10B981; color: #fff; }

/* Margin meter */
.margin-meter-track {
    height: 6px; background: rgba(210,194,168,0.3);
    border-radius: 99px; overflow: hidden; margin-top: 8px;
}
.margin-meter-fill {
    height: 100%; border-radius: 99px;
    transition: width 0.4s cubic-bezier(0.4,0,0.2,1), background 0.4s;
}
</style>
@endpush

@section('content')
<div x-data="productForm()" x-init="init()" class="max-w-4xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 mb-6 text-sm">
        <a href="{{ route('products.index') }}" class="font-semibold text-beige-400 hover:text-mint-600 transition-colors flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Products
        </a>
        <svg class="w-3 h-3 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-bold text-mint-900">Add Product</span>
    </div>

    {{-- Validation errors summary (Dynamic) --}}
    <div x-show="Object.keys(errors).length > 0" style="display: none;" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3">
        <div class="w-8 h-8 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-red-800">Please fix the following errors:</p>
            <ul class="mt-1 space-y-0.5">
                <template x-for="(messages, field) in errors" :key="field">
                    <template x-for="msg in messages" :key="msg">
                        <li class="text-xs font-medium text-red-600" x-text="'• ' + msg"></li>
                    </template>
                </template>
            </ul>
        </div>
    </div>

    <div x-data="categoryModalHandler()">
        <form method="POST"
              action="{{ route('products.store') }}"
              enctype="multipart/form-data"
              id="product-form"
              @submit="handleSubmit($event)"
              x-ref="form"
              novalidate>
            @csrf

            <div class="space-y-5">

            {{-- ══ SECTION 1: Basic Information ══ --}}
            <div class="form-card">
                <div class="section-header">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-mint-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-mint-900">Basic Information</h2>
                            <p class="text-[11px] text-beige-400 font-medium">Product name, category, SKU and description</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Product Name --}}
                    <div class="md:col-span-2">
                        <label class="field-label" for="name">Product Name <span class="req">*</span></label>
                        <div class="input-group">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <input type="text" id="name" name="name"
                                   value="{{ old('name') }}"
                                   @input="productName = $event.target.value; markDirty(); delete errors.name"
                                   x-model="productName"
                                   required
                                   placeholder="e.g. Coca-Cola 1.5L"
                                   :class="errors.name ? 'error' : ''"
                                   class="form-input input-with-icon">
                        </div>
                        <p x-show="errors.name" x-text="errors.name?.[0]" class="field-error" style="display:none;"></p>
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="field-label" for="category_id">Category</label>
                        <div class="flex items-center gap-2">
                            <div class="input-group flex-1">
                                <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/></svg>
                                <select name="category_id" id="category_id" @change="markDirty()" class="form-input input-with-icon searchable-select">
                                    <option value="">No Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" @click="showCategoryModal = true"
                                    class="w-11 h-11 flex items-center justify-center rounded-xl bg-mint-500 text-white hover:bg-mint-600 transition-colors shadow-sm flex-shrink-0 text-xl font-bold leading-none"
                                    title="Create New Category" style="min-height:44px; min-width:44px;">+</button>
                            <button type="button" @click="showManageModal = true"
                                    class="w-11 h-11 flex items-center justify-center rounded-xl border border-beige-200 bg-white text-beige-400 hover:text-mint-600 hover:border-mint-200 transition-all flex-shrink-0"
                                    title="Manage Categories" style="min-height:44px; min-width:44px;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Unit --}}
                    <div>
                        <label class="field-label" for="unit">Unit of Measure <span class="req">*</span></label>
                        <div class="input-group">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            <select name="unit" id="unit" @change="markDirty()" class="form-input input-with-icon">
                                @foreach($units as $val => $label)
                                    <option value="{{ $val }}" {{ old('unit', 'piece') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- SKU --}}
                    <div>
                        <label class="field-label" for="sku">SKU</label>
                        <div class="input-group" style="position:relative;">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <input type="text" id="sku" name="sku"
                                   value="{{ old('sku') }}"
                                   @input="markDirty()"
                                   placeholder="e.g. PROD-001"
                                   class="form-input input-with-icon" style="padding-right: 72px;">
                            <button type="button" @click="generateSku()" class="sku-gen-btn">Auto</button>
                        </div>
                        <p class="field-hint">Leave blank to auto-generate on save</p>
                    </div>

                    {{-- Barcode --}}
                    <div>
                        <label class="field-label" for="barcode">Barcode / EAN</label>
                        <div class="input-group" style="position:relative;">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="2" height="16" rx="0.5"/><rect x="7" y="4" width="1" height="16"/><rect x="10" y="4" width="2" height="16" rx="0.5"/><rect x="14" y="4" width="1" height="16"/><rect x="17" y="4" width="2" height="16" rx="0.5"/></svg>
                            <input type="text" id="barcode" name="barcode"
                                   value="{{ old('barcode') }}"
                                   @input="markDirty()"
                                   placeholder="Scan or enter barcode"
                                   class="form-input input-with-icon" style="padding-right: 72px;">
                            <button type="button" onclick="openBarcodeScanner(document.getElementById('barcode'))" class="sku-gen-btn" style="background:#E0F2FE; color:#0284C7; display:flex; align-items:center; gap:3px;">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Scan
                            </button>
                        </div>
                        <p class="field-hint">Used for barcode scanning at the POS</p>
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label class="field-label" for="description">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  @input="markDirty()"
                                  placeholder="Optional product description, notes, or specifications…"
                                  class="form-input" style="resize: vertical; min-height: 88px;">{{ old('description') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- ══ SECTION 2: Pricing ══ --}}
            <div class="form-card">
                <div class="section-header">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-mint-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-mint-900">Pricing</h2>
                            <p class="text-[11px] text-beige-400 font-medium">Cost price, selling price, and profit analysis</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                        {{-- Cost Price --}}
                        <div>
                            <label class="field-label" for="cost_price">Cost Price <span class="req">*</span></label>
                            <div class="price-wrap">
                                <span class="price-prefix">₱</span>
                                <input type="number" id="cost_price" name="cost_price"
                                       x-model="costPrice"
                                       @input="calcProfit(); markDirty(); delete errors.cost_price"
                                       value="{{ old('cost_price', '0.00') }}"
                                       step="0.01" min="0" required
                                       placeholder="0.00"
                                       :class="errors.cost_price ? 'error' : ''"
                                       class="form-input price-input">
                            </div>
                            <p class="field-hint">What you paid to acquire this product</p>
                            <p x-show="errors.cost_price" x-text="errors.cost_price?.[0]" class="field-error" style="display:none;"></p>
                        </div>

                        {{-- Selling Price --}}
                        <div>
                            <label class="field-label" for="selling_price">Selling Price <span class="req">*</span></label>
                            <div class="price-wrap">
                                <span class="price-prefix">₱</span>
                                <input type="number" id="selling_price" name="selling_price"
                                       x-model="sellingPrice"
                                       @input="calcProfit(); markDirty(); delete errors.selling_price"
                                       value="{{ old('selling_price', '0.00') }}"
                                       step="0.01" min="0" required
                                       placeholder="0.00"
                                       :class="errors.selling_price ? 'error' : ''"
                                       class="form-input price-input">
                            </div>
                            <p class="field-hint">Price shown to customers at the POS</p>
                            <p x-show="errors.selling_price" x-text="errors.selling_price?.[0]" class="field-error" style="display:none;"></p>
                        </div>
                    </div>

                    {{-- Live profit calculator --}}
                    <div class="rounded-2xl border border-beige-100 bg-beige-50 p-4">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center gap-4 flex-wrap">
                                <div>
                                    <p class="text-[10px] font-bold text-beige-400 uppercase tracking-wider">Profit / Unit</p>
                                    <p class="text-xl font-black" :class="profit >= 0 ? 'text-mint-600' : 'text-red-600'"
                                       x-text="'₱' + Math.abs(profit).toFixed(2)"></p>
                                </div>
                                <div class="w-px h-8 bg-beige-200"></div>
                                <div>
                                    <p class="text-[10px] font-bold text-beige-400 uppercase tracking-wider">Margin</p>
                                    <p class="text-xl font-black" :class="margin >= 0 ? 'text-mint-600' : 'text-red-600'"
                                       x-text="margin.toFixed(1) + '%'"></p>
                                </div>
                                <div class="w-px h-8 bg-beige-200"></div>
                                <div>
                                    <p class="text-[10px] font-bold text-beige-400 uppercase tracking-wider">Markup</p>
                                    <p class="text-xl font-black text-mint-700" x-text="markup.toFixed(1) + '%'"></p>
                                </div>
                            </div>
                            <span class="profit-badge"
                                  :class="{
                                      'profit-good': margin >= 30,
                                      'profit-mid':  margin >= 10 && margin < 30,
                                      'profit-low':  margin < 10
                                  }"
                                  x-text="margin >= 30 ? '✓ Great margin' : (margin >= 10 ? '⚠ Fair margin' : '✗ Low margin')">
                            </span>
                        </div>
                        <div class="margin-meter-track mt-3">
                            <div class="margin-meter-fill"
                                 :style="`width: ${Math.min(100, Math.max(0, margin))}%; background: ${margin >= 30 ? '#10B981' : (margin >= 10 ? '#f59e0b' : '#ef4444')};`"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ SECTION 3: Inventory ══ --}}
            <div class="form-card">
                <div class="section-header">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-mint-900">Inventory</h2>
                            <p class="text-[11px] text-beige-400 font-medium">Stock levels, reorder thresholds, and tracking</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Initial Stock --}}
                    <div>
                        <label class="field-label" for="stock_quantity">Initial Stock <span class="req">*</span></label>
                        <div class="input-group">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <input type="number" id="stock_quantity" name="stock_quantity"
                                   value="{{ old('stock_quantity', 0) }}"
                                   x-model="stockQty"
                                   @input="markDirty(); delete errors.stock_quantity" min="0" required
                                   placeholder="0"
                                   :class="errors.stock_quantity ? 'error' : ''"
                                   class="form-input input-with-icon">
                        </div>
                        <p class="field-hint">Starting inventory count for this product</p>
                        <p x-show="errors.stock_quantity" x-text="errors.stock_quantity?.[0]" class="field-error" style="display:none;"></p>
                    </div>

                    {{-- Reorder Level --}}
                    <div>
                        <label class="field-label" for="reorder_level">Reorder Level</label>
                        <div class="input-group">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <input type="number" id="reorder_level" name="reorder_level"
                                   value="{{ old('reorder_level', 5) }}"
                                   @input="markDirty()" min="0"
                                   placeholder="5"
                                   class="form-input input-with-icon">
                        </div>
                        <p class="field-hint">Low-stock warning triggers at this quantity</p>
                    </div>

                    {{-- Toggles row --}}
                    <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Active toggle --}}
                        <div class="flex items-center justify-between p-4 rounded-2xl border border-beige-100 bg-beige-50">
                            <div>
                                <p class="text-sm font-bold text-mint-900">Active Product</p>
                                <p class="text-[11px] text-beige-400 font-medium mt-0.5">Visible and purchasable at POS</p>
                            </div>
                            <label class="toggle-switch" style="min-height:unset;">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       @change="markDirty()">
                                <div class="toggle-track"><div class="toggle-thumb"></div></div>
                            </label>
                        </div>

                        {{-- Track stock toggle --}}
                        <div class="flex items-center justify-between p-4 rounded-2xl border border-beige-100 bg-beige-50">
                            <div>
                                <p class="text-sm font-bold text-mint-900">Track Stock</p>
                                <p class="text-[11px] text-beige-400 font-medium mt-0.5">Monitor and alert on stock levels</p>
                            </div>
                            <label class="toggle-switch" style="min-height:unset;">
                                <input type="hidden" name="track_stock" value="0">
                                <input type="checkbox" name="track_stock" value="1"
                                       {{ old('track_stock', true) ? 'checked' : '' }}
                                       @change="markDirty()">
                                <div class="toggle-track"><div class="toggle-thumb"></div></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ SECTION 4: Product Image ══ --}}
            <div class="form-card">
                <div class="section-header">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-mint-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-mint-900">Product Image</h2>
                            <p class="text-[11px] text-beige-400 font-medium">Optional. JPG, PNG, WebP up to 5MB</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="drop-zone rounded-2xl p-8 text-center"
                         @dragover.prevent="isDragging = true"
                         @dragleave="isDragging = false"
                         @drop.prevent="handleImageDrop($event)"
                         :class="{ 'dragging': isDragging }"
                         @click="$refs.imageInput.click()">
                        {{-- Preview --}}
                        <template x-if="imagePreview">
                            <div class="relative inline-block">
                                <img :src="imagePreview" class="h-40 w-auto max-w-full rounded-xl object-contain mx-auto shadow-md">
                                <button type="button" @click.stop="clearImage()"
                                        class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-red-500 text-white flex items-center justify-center shadow-lg hover:bg-red-600 transition-colors"
                                        style="min-height:unset; min-width:unset;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        {{-- Placeholder --}}
                        <template x-if="!imagePreview">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 rounded-2xl bg-white border-2 border-dashed border-beige-300 flex items-center justify-center shadow-sm">
                                    <svg class="w-7 h-7 text-beige-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-mint-900">Drop image here or <span class="text-mint-500 underline">browse</span></p>
                                    <p class="text-xs text-beige-400 font-medium mt-1">JPG, PNG, WebP · Max 5MB · Min 200×200px</p>
                                </div>
                            </div>
                        </template>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
                               x-ref="imageInput" class="hidden"
                               @change="handleImageSelect($event)">
                    </div>
                    <p x-show="imageError" x-text="imageError" class="field-error mt-2"></p>
                </div>
            </div>

            {{-- ══ ACTION BAR ══ --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-2">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-2 text-sm font-semibold text-beige-400 hover:text-mint-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Products
                </a>

                <button type="submit"
                        :disabled="submitting || !canSubmit"
                        :title="!canSubmit ? 'Please fill in all required fields first' : ''"
                        :class="!canSubmit ? 'opacity-50 cursor-not-allowed' : 'hover:-translate-y-0.5'"
                        class="submit-btn w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-mint-500 text-white font-extrabold text-sm shadow-lg shadow-mint-500/30 transition-all">
                    <template x-if="!submitting">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Save Product
                        </span>
                    </template>
                    <template x-if="submitting">
                        <span class="flex items-center gap-2">
                            <svg class="spinner-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Saving Product…
                        </span>
                    </template>
                </button>
            </div>

        </form>

        @include('categories._modal')
        @include('products._barcode_scanner')
    </div>
</div>
@endsection

@push('scripts')
<script>
function productForm() {
    return {
        productName:  '{{ old('name', '') }}',
        costPrice:    {{ old('cost_price', 0) }},
        sellingPrice: {{ old('selling_price', 0) }},
        stockQty:     '{{ old('stock_quantity', '') }}',
        submitting:   false,
        errors:       {},
        isDirty:      false,
        isDragging:   false,
        imagePreview: null,
        imageError:   '',

        get canSubmit() {
            return this.productName.trim() !== '' &&
                   parseFloat(this.costPrice) > 0 &&
                   parseFloat(this.sellingPrice) > 0 &&
                   String(this.stockQty).trim() !== '';
        },
        get profit()  { return parseFloat(this.sellingPrice || 0) - parseFloat(this.costPrice || 0); },
        get margin()  {
            const sell = parseFloat(this.sellingPrice || 0);
            if (sell === 0) return 0;
            return (this.profit / sell) * 100;
        },
        get markup()  {
            const cost = parseFloat(this.costPrice || 0);
            if (cost === 0) return 0;
            return (this.profit / cost) * 100;
        },

        init() {
            // Unsaved changes alert removed as requested
        },

        calcProfit() { /* reactive via getters */ },

        markDirty() { this.isDirty = true; },

        generateSku() {
            const name = document.getElementById('name').value.trim();
            if (!name) return;
            const words = name.split(' ').slice(0,3).map(w => w.substring(0,3).toUpperCase());
            const rand  = Math.floor(Math.random() * 9000 + 1000);
            document.getElementById('sku').value = words.join('-') + '-' + rand;
            this.markDirty();
        },

        handleImageSelect(event) {
            const file = event.target.files[0];
            this.processImage(file);
        },

        handleImageDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            if (file) {
                // Manually assign to file input
                const dt = new DataTransfer();
                dt.items.add(file);
                this.$refs.imageInput.files = dt.files;
                this.processImage(file);
                this.markDirty();
            }
        },

        processImage(file) {
            this.imageError = '';
            if (!file) return;
            if (!file.type.startsWith('image/')) {
                this.imageError = 'Please select a valid image file (JPG, PNG, WebP).';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                this.imageError = 'Image must be smaller than 5MB.';
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => { this.imagePreview = e.target.result; };
            reader.readAsDataURL(file);
            this.markDirty();
        },

        clearImage() {
            this.imagePreview = null;
            this.imageError   = '';
            this.$refs.imageInput.value = '';
            this.markDirty();
        },

        async handleSubmit(event) {
            if (this.submitting) {
                event.preventDefault();
                return;
            }
            event.preventDefault(); // Intercept normal submission

            // Client-side required field check
            this.errors = {};
            const name = document.getElementById('name').value.trim();
            const costPrice = document.getElementById('cost_price').value.trim();
            const sellingPrice = document.getElementById('selling_price').value.trim();
            const stockQty = document.getElementById('stock_quantity').value.trim();
            if (!name)        this.errors.name           = ['Product name is required.'];
            if (!costPrice)   this.errors.cost_price     = ['Cost price is required.'];
            if (!sellingPrice) this.errors.selling_price = ['Selling price is required.'];
            if (!stockQty)    this.errors.stock_quantity = ['Initial stock is required.'];
            if (Object.keys(this.errors).length > 0) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'Please fill in all required fields.', type: 'error' } }));
                return;
            }

            this.submitting = true;
            this.errors = {};

            try {
                const formData = new FormData(this.$refs.form);
                const response = await fetch(document.getElementById('product-form').action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });

                let data;
                const responseClone = response.clone();
                try {
                    data = await response.json();
                } catch (jsonError) {
                    const rawText = await responseClone.text();
                    console.error("JSON Parse Error. Server returned:", rawText);
                    window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'Server returned HTML instead of JSON. Check console for details.', type: 'error' } }));
                    this.submitting = false;
                    return;
                }

                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors;
                        window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'Please fix the errors in the form.', type: 'error' } }));
                    } else {
                        window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'An unexpected error occurred: ' + (data.message || response.statusText), type: 'error' } }));
                    }
                    this.submitting = false;
                } else {
                    // Success without reload
                    this.isDirty = false;
                    this.submitting = false;
                    
                    // Show toast notification
                    window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'Product created successfully! You can now add another.', type: 'success' } }));
                    
                    // Reset form and UI state
                    this.$refs.form.reset();
                    this.costPrice = 0;
                    this.sellingPrice = 0;
                    this.clearImage();
                    this.errors = {};
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'Network error occurred while saving the product.', type: 'error' } }));
                this.submitting = false;
            }
        },
    };
}
</script>
@endpush
