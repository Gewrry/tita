@extends('layouts.app')
@section('title', 'Edit: ' . $product->name)
@section('page-title', 'Edit Product')

@push('styles')
<style>
/* Reuse same design system as create */
.form-card { background:#fff; border:1px solid rgba(210,194,168,.45); border-radius:20px; overflow:hidden; }
.section-header { padding:20px 24px 16px; border-bottom:1px solid rgba(210,194,168,.3); background:#FAFAF9; }
.input-group { position:relative; }
.input-group .input-icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#AA8D63; pointer-events:none; width:16px; height:16px; transition:color .15s; }
.input-group:focus-within .input-icon { color:#10B981; }
.input-with-icon { padding-left:40px !important; }
.form-input { width:100%; padding:11px 14px; background:#F7F2E8; border:1.5px solid rgba(210,194,168,.6); border-radius:12px; font-size:14px; font-weight:500; color:#155541; font-family:'Outfit',sans-serif; transition:border-color .15s,background .15s,box-shadow .15s; min-height:44px; }
.form-input:focus { outline:none; border-color:#10B981; background:#fff; box-shadow:0 0 0 3px rgba(16,185,129,.12); }
.form-input.error { border-color:#ef4444; background:#fff5f5; box-shadow:0 0 0 3px rgba(239,68,68,.1); }
.form-input::placeholder { color:#BFA885; }
.price-wrap { position:relative; }
.price-prefix { position:absolute; left:12px; top:50%; transform:translateY(-50%); font-weight:800; font-size:14px; color:#AA8D63; pointer-events:none; font-family:'Outfit',sans-serif; }
.price-wrap:focus-within .price-prefix { color:#10B981; }
.form-input.price-input { padding-left:28px !important; }
.field-label { display:block; font-size:11px; font-weight:700; color:#8F7149; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px; }
.field-label .req { color:#10B981; margin-left:2px; }
.field-hint { font-size:11px; font-weight:500; color:#AA8D63; margin-top:4px; line-height:1.4; }
.toggle-switch { position:relative; display:inline-flex; align-items:center; gap:10px; cursor:pointer; }
.toggle-switch input { opacity:0; width:0; height:0; position:absolute; }
.toggle-track { width:44px; height:24px; border-radius:99px; background:rgba(210,194,168,.5); border:1.5px solid rgba(210,194,168,.7); transition:all .25s; position:relative; flex-shrink:0; }
.toggle-thumb { position:absolute; top:2px; left:2px; width:18px; height:18px; border-radius:99px; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.2); transition:transform .25s; }
.toggle-switch input:checked ~ .toggle-track { background:#10B981; border-color:#10B981; }
.toggle-switch input:checked ~ .toggle-track .toggle-thumb { transform:translateX(20px); }
.drop-zone { border:2px dashed rgba(210,194,168,.7); border-radius:16px; background:#F7F2E8; transition:all .2s; cursor:pointer; }
.drop-zone.dragging { border-color:#10B981; background:rgba(16,185,129,.04); box-shadow:0 0 0 4px rgba(16,185,129,.1); }
.drop-zone:hover { border-color:#10B981; background:rgba(16,185,129,.02); }
.profit-badge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:99px; font-size:11px; font-weight:800; transition:all .2s; }
.profit-good { background:#DDF6ED; color:#155541; }
.profit-mid  { background:#FEF3C7; color:#92400E; }
.profit-low  { background:#fee2e2; color:#991b1b; }
.submit-btn { transition:all .2s; position:relative; overflow:hidden; }
.submit-btn:not(:disabled):hover { transform:translateY(-1px); box-shadow:0 12px 28px -6px rgba(16,185,129,.4); }
.submit-btn:disabled { opacity:.7; cursor:not-allowed; transform:none; box-shadow:none; }
@keyframes spin { to { transform:rotate(360deg); } }
.spinner-icon { animation:spin .7s linear infinite; }
.field-error { display:flex; align-items:center; gap:4px; font-size:11px; font-weight:600; color:#dc2626; margin-top:4px; }
.sku-gen-btn { position:absolute; right:8px; top:50%; transform:translateY(-50%); padding:3px 8px; border-radius:7px; background:#DDF6ED; color:#1CA074; font-size:10px; font-weight:800; border:none; cursor:pointer; transition:all .15s; min-height:unset; }
.sku-gen-btn:hover { background:#10B981; color:#fff; }
.margin-meter-track { height:6px; background:rgba(210,194,168,.3); border-radius:99px; overflow:hidden; margin-top:8px; }
.margin-meter-fill { height:100%; border-radius:99px; transition:width .4s cubic-bezier(.4,0,.2,1),background .4s; }

/* Stock adjustment type card */
.adj-type-btn { transition:all .2s; border:1.5px solid rgba(210,194,168,.5); border-radius:12px; padding:10px 12px; cursor:pointer; text-align:left; background:#fff; }
.adj-type-btn.selected { border-color:#10B981; background:rgba(16,185,129,.06); }
.adj-type-btn:hover:not(.selected) { border-color:rgba(16,185,129,.4); background:rgba(16,185,129,.03); }

/* Movement history */
.movement-row { transition:background .15s; }
.movement-row:hover { background:#F7F2E8; }
.movement-type-badge { display:inline-flex; align-items:center; gap:3px; padding:2px 8px; border-radius:99px; font-size:10px; font-weight:800; text-transform:uppercase; }
</style>
@endpush

@section('content')
<div x-data="editProductForm()" x-init="init()" class="max-w-4xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 mb-6 text-sm">
        <a href="{{ route('products.index') }}" class="font-semibold text-beige-400 hover:text-mint-600 transition-colors flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Products
        </a>
        <svg class="w-3 h-3 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-bold text-mint-900 truncate max-w-[200px]">{{ $product->name }}</span>
    </div>

    {{-- Product status bar --}}
    <div class="flex flex-wrap items-center gap-3 mb-6 p-4 bg-white border border-beige-200/60 rounded-2xl">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-mint-50 flex items-center justify-center flex-shrink-0">
                <template x-if="currentImageUrl">
                    <img :src="currentImageUrl" class="w-10 h-10 rounded-xl object-cover">
                </template>
                <template x-if="!currentImageUrl">
                    <svg class="w-5 h-5 text-mint-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </template>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-extrabold text-mint-900 truncate">{{ $product->name }}</p>
                <p class="text-xs text-beige-400 font-medium">ID #{{ $product->id }} {{ $product->sku ? '· SKU: ' . $product->sku : '' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            @if($product->track_stock)
                @if($product->isOutOfStock())
                    <span class="px-3 py-1.5 rounded-full text-xs font-black bg-red-100 text-red-700" style="min-height:unset;">Out of Stock</span>
                @elseif($product->isLowStock())
                    <span class="px-3 py-1.5 rounded-full text-xs font-black bg-amber-100 text-amber-700" style="min-height:unset;">Low Stock · {{ $product->stock_quantity }} left</span>
                @else
                    <span class="px-3 py-1.5 rounded-full text-xs font-black bg-mint-100 text-mint-700" style="min-height:unset;">In Stock · {{ $product->stock_quantity }}</span>
                @endif
            @else
                <span class="px-3 py-1.5 rounded-full text-xs font-black bg-blue-100 text-blue-700" style="min-height:unset;">Unlimited Stock</span>
            @endif
            @if(!$product->is_active)
                <span class="px-3 py-1.5 rounded-full text-xs font-black bg-beige-100 text-beige-600" style="min-height:unset;">Inactive</span>
            @endif
            <span class="text-xs text-beige-400 font-medium">Updated {{ $product->updated_at->diffForHumans() }}</span>
        </div>
    </div>

    {{-- Validation errors summary --}}
    <div x-show="Object.keys(errors).length > 0" style="display: none;" class="mb-5 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3">
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

        {{-- ══ Main product form ══ --}}
        <form method="POST"
              action="{{ route('products.update', $product) }}"
              enctype="multipart/form-data"
              id="main-product-form"
              @submit="handleSubmit($event)"
              novalidate>
            @csrf @method('PUT')

            <div class="space-y-5">

            {{-- ══ SECTION 1: Basic Information ══ --}}
            <div class="form-card mb-5">
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

                    {{-- Name --}}
                    <div class="md:col-span-2">
                        <label class="field-label" for="name">Product Name <span class="req">*</span></label>
                        <div class="input-group">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <input type="text" id="name" name="name"
                                   value="{{ old('name', $product->name) }}"
                                   @input="productName = $event.target.value; markDirty(); delete errors.name"
                                   x-model="productName"
                                   required
                                   placeholder="Product name"
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
                                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" @click="showCategoryModal = true"
                                    class="w-11 h-11 flex items-center justify-center rounded-xl bg-mint-500 text-white hover:bg-mint-600 transition-colors shadow-sm flex-shrink-0 text-xl font-bold" style="min-height:44px; min-width:44px;">+</button>
                            <button type="button" @click="showManageModal = true"
                                    class="w-11 h-11 flex items-center justify-center rounded-xl border border-beige-200 bg-white text-beige-400 hover:text-mint-600 hover:border-mint-200 transition-all flex-shrink-0" style="min-height:44px; min-width:44px;">
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
                                    <option value="{{ $val }}" {{ old('unit', $product->unit) === $val ? 'selected' : '' }}>{{ $label }}</option>
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
                                   value="{{ old('sku', $product->sku) }}"
                                   @input="markDirty()" placeholder="e.g. PROD-001"
                                   class="form-input input-with-icon" style="padding-right:72px;">
                            <button type="button" @click="generateSku()" class="sku-gen-btn">Regen</button>
                        </div>
                    </div>

                    {{-- Barcode --}}
                    <div>
                        <label class="field-label" for="barcode">Barcode / EAN</label>
                        <div class="input-group" style="position:relative;">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="2" height="16" rx="0.5"/><rect x="7" y="4" width="1" height="16"/><rect x="10" y="4" width="2" height="16" rx="0.5"/><rect x="14" y="4" width="1" height="16"/><rect x="17" y="4" width="2" height="16" rx="0.5"/></svg>
                            <input type="text" id="barcode" name="barcode"
                                   value="{{ old('barcode', $product->barcode) }}"
                                   @input="markDirty()" placeholder="Scan or enter barcode"
                                   class="form-input input-with-icon" style="padding-right: 72px;">
                            <button type="button" onclick="openBarcodeScanner(document.getElementById('barcode'))" class="sku-gen-btn" style="background:#E0F2FE; color:#0284C7; display:flex; align-items:center; gap:3px;">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Scan
                            </button>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label class="field-label" for="description">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  @input="markDirty()" placeholder="Optional notes or description…"
                                  class="form-input" style="resize:vertical; min-height:80px;">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ══ SECTION 2: Pricing ══ --}}
            <div class="form-card mb-5">
                <div class="section-header">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-mint-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-mint-900">Pricing</h2>
                            <p class="text-[11px] text-beige-400 font-medium">Cost, selling price, and real-time profit analysis</p>
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
                                       @input="markDirty(); delete errors.cost_price"
                                       value="{{ old('cost_price', $product->cost_price) }}"
                                       step="0.01" min="0" required placeholder="0.00"
                                       :class="errors.cost_price ? 'error' : ''"
                                       class="form-input price-input">
                            </div>
                            <p x-show="errors.cost_price" x-text="errors.cost_price?.[0]" class="field-error" style="display:none;"></p>
                        </div>
                        {{-- Selling Price --}}
                        <div>
                            <label class="field-label" for="selling_price">Selling Price <span class="req">*</span></label>
                            <div class="price-wrap">
                                <span class="price-prefix">₱</span>
                                <input type="number" id="selling_price" name="selling_price"
                                       x-model="sellingPrice"
                                       @input="markDirty(); delete errors.selling_price"
                                       value="{{ old('selling_price', $product->selling_price) }}"
                                       step="0.01" min="0" required placeholder="0.00"
                                       :class="errors.selling_price ? 'error' : ''"
                                       class="form-input price-input">
                            </div>
                            <p x-show="errors.selling_price" x-text="errors.selling_price?.[0]" class="field-error" style="display:none;"></p>
                        </div>
                    </div>

                    {{-- Live profit meter --}}
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
                                  :class="{ 'profit-good': margin >= 30, 'profit-mid': margin >= 10 && margin < 30, 'profit-low': margin < 10 }"
                                  x-text="margin >= 30 ? '✓ Great margin' : (margin >= 10 ? '⚠ Fair margin' : '✗ Low margin')">
                            </span>
                        </div>
                        <div class="margin-meter-track mt-3">
                            <div class="margin-meter-fill"
                                 :style="`width:${Math.min(100,Math.max(0,margin))}%;background:${margin>=30?'#10B981':(margin>=10?'#f59e0b':'#ef4444')};`"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ SECTION 3: Inventory Settings ══ --}}
            <div class="form-card mb-5">
                <div class="section-header">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-mint-900">Inventory Settings</h2>
                            <p class="text-[11px] text-beige-400 font-medium">Reorder level, active status, and stock tracking</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Current stock display --}}
                    <div>
                        <label class="field-label">Current Stock Level</label>
                        <div class="flex items-center justify-between px-4 py-3 bg-mint-50 border border-mint-100 rounded-xl">
                            <div>
                                <span class="text-2xl font-black text-mint-700">{{ $product->stock_quantity }}</span>
                                <span class="text-sm font-bold text-mint-500 ml-1">{{ $product->unit }}s</span>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-mint-500 text-white text-[9px] font-black uppercase tracking-widest" style="min-height:unset;">Live</span>
                        </div>
                        <p class="field-hint">Use the Stock Adjustment section below to change stock</p>
                    </div>

                    {{-- Reorder Level --}}
                    <div>
                        <label class="field-label" for="reorder_level">Reorder Level</label>
                        <div class="input-group">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            <input type="number" id="reorder_level" name="reorder_level"
                                   value="{{ old('reorder_level', $product->reorder_level) }}"
                                   @input="markDirty()" min="0" placeholder="5"
                                   class="form-input input-with-icon">
                        </div>
                        <p class="field-hint">Low-stock alert triggers below this quantity</p>
                    </div>

                    {{-- Toggles --}}
                    <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-4 rounded-2xl border border-beige-100 bg-beige-50">
                            <div>
                                <p class="text-sm font-bold text-mint-900">Active Product</p>
                                <p class="text-[11px] text-beige-400 font-medium mt-0.5">Visible and available at POS</p>
                            </div>
                            <label class="toggle-switch" style="min-height:unset;">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                       @change="markDirty()">
                                <div class="toggle-track"><div class="toggle-thumb"></div></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-2xl border border-beige-100 bg-beige-50">
                            <div>
                                <p class="text-sm font-bold text-mint-900">Track Stock</p>
                                <p class="text-[11px] text-beige-400 font-medium mt-0.5">Enable inventory level tracking</p>
                            </div>
                            <label class="toggle-switch" style="min-height:unset;">
                                <input type="hidden" name="track_stock" value="0">
                                <input type="checkbox" name="track_stock" value="1"
                                       {{ old('track_stock', $product->track_stock) ? 'checked' : '' }}
                                       @change="markDirty()">
                                <div class="toggle-track"><div class="toggle-thumb"></div></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ SECTION 4: Product Image ══ --}}
            <div class="form-card mb-5">
                <div class="section-header">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-mint-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-mint-900">Product Image</h2>
                            <p class="text-[11px] text-beige-400 font-medium">Upload a new image or keep the existing one</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Current image --}}
                    <template x-if="currentImageUrl">
                        <div>
                            <label class="field-label">Current Image</label>
                            <div class="relative inline-block w-full">
                                <img :src="currentImageUrl"
                                     class="h-36 w-full object-cover rounded-xl border border-beige-200 shadow-sm">
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-mint-500 text-white text-[9px] font-black" style="min-height:unset;">Current</span>
                            </div>
                        </div>
                    </template>

                    {{-- Upload input --}}
                    <div :class="currentImageUrl ? 'sm:col-span-1' : 'sm:col-span-2'">
                        <label class="field-label" x-text="currentImageUrl ? 'Replace Image' : 'Upload Image'"></label>
                        <div class="drop-zone p-6 text-center"
                             @dragover.prevent="isDragging = true"
                             @dragleave="isDragging = false"
                             @drop.prevent="handleImageDrop($event)"
                             :class="{ 'dragging': isDragging }"
                             @click="$refs.imageInput.click()">
                            <template x-if="imagePreview">
                                <div class="relative inline-block">
                                    <img :src="imagePreview" class="h-32 w-auto max-w-full rounded-xl object-contain mx-auto shadow-md">
                                    <button type="button" @click.stop="clearImage()"
                                            class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center shadow-lg" style="min-height:unset; min-width:unset;">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                            <template x-if="!imagePreview">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-8 h-8 text-beige-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-xs font-bold text-beige-500">Drop or <span class="text-mint-500">browse</span></p>
                                    <p class="text-[10px] text-beige-400">JPG, PNG, WebP · Max 5MB</p>
                                </div>
                            </template>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
                                   x-ref="imageInput" class="hidden"
                                   @change="handleImageSelect($event)">
                        </div>
                        <p x-show="imageError" x-text="imageError" class="field-error mt-2"></p>
                    </div>
                </div>
            </div>

            {{-- ══ Save / Back bar ══ --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-2 mb-5">
                <div class="flex items-center gap-6">
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center gap-2 text-sm font-semibold text-beige-400 hover:text-mint-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back to Products
                    </a>
                    <button type="button" @click="
                        $dispatch('confirm', {
                            title: 'Delete Product?',
                            message: 'Are you sure you want to delete {{ addslashes($product->name) }}? This cannot be undone.',
                            confirmText: 'Yes, Delete',
                            confirmType: 'danger',
                            onConfirm: () => $refs.deleteForm.submit()
                        })
                    " class="text-sm font-bold text-red-400 hover:text-red-600 transition-colors">
                        Delete Product
                    </button>
                </div>
                <button type="submit"
                        :disabled="submitting || !canSubmit"
                        :title="!canSubmit ? 'Please fill in all required fields first' : ''"
                        :class="!canSubmit ? 'opacity-50 cursor-not-allowed' : 'hover:-translate-y-0.5'"
                        class="submit-btn w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-mint-500 text-white font-extrabold text-sm shadow-lg shadow-mint-500/30 transition-all">
                    <template x-if="!submitting">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Update Product
                        </span>
                    </template>
                    <template x-if="submitting">
                        <span class="flex items-center gap-2">
                            <svg class="spinner-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Updating Product…
                        </span>
                    </template>
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('products.destroy', $product) }}" x-ref="deleteForm" style="display:none;">
            @csrf @method('DELETE')
        </form>

        {{-- ══ SECTION 5: Quick Stock Adjustment ══ --}}
        <div class="form-card">
            <div class="section-header">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-mint-900">Quick Stock Adjustment</h2>
                        <p class="text-[11px] text-beige-400 font-medium">Record stock movements — current level: <strong class="text-mint-700">{{ $product->stock_quantity }} {{ $product->unit }}s</strong></p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('products.adjust-stock', $product) }}" @submit="handleAdjustSubmit($event)" x-ref="adjustForm">
                    @csrf

                    {{-- Adjustment type cards --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-5">
                        @foreach([
                            ['value'=>'stock_in',   'label'=>'Stock In',    'hint'=>'Receive new stock',      'color'=>'text-mint-600',  'bg'=>'bg-mint-100'],
                            ['value'=>'adjustment', 'label'=>'Adjustment',  'hint'=>'Manual correction',      'color'=>'text-blue-600',  'bg'=>'bg-blue-100'],
                            ['value'=>'spoilage',   'label'=>'Spoilage',    'hint'=>'Damaged / expired',      'color'=>'text-red-600',   'bg'=>'bg-red-100'],
                            ['value'=>'return',     'label'=>'Return',      'hint'=>'Customer return',        'color'=>'text-amber-600', 'bg'=>'bg-amber-100'],
                        ] as $type)
                        <label class="adj-type-btn cursor-pointer" :class="adjustType === '{{ $type['value'] }}' ? 'selected' : ''"
                               @click="adjustType = '{{ $type['value'] }}'">
                            <input type="radio" name="type" value="{{ $type['value'] }}"
                                   :checked="adjustType === '{{ $type['value'] }}'"
                                   class="hidden" style="display:none;">
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-6 h-6 rounded-lg {{ $type['bg'] }} flex items-center justify-center flex-shrink-0">
                                    <div class="w-2 h-2 rounded-full {{ str_replace('text-', 'bg-', $type['color']) }}"></div>
                                </div>
                                <span class="text-xs font-bold text-mint-900">{{ $type['label'] }}</span>
                            </div>
                            <p class="text-[10px] text-beige-400 font-medium">{{ $type['hint'] }}</p>
                        </label>
                        @endforeach
                    </div>

                    <div class="flex flex-col md:flex-row items-end gap-4">
                        <div class="w-full md:w-28">
                            <label class="field-label" for="quantity">Quantity</label>
                            <div class="input-group">
                                <input type="number" id="quantity" name="quantity"
                                       value="1" required min="1"
                                       class="form-input text-center font-black text-lg">
                            </div>
                        </div>
                        <div class="flex-1 w-full">
                            <label class="field-label" for="notes">Notes / Reason</label>
                            <div class="input-group">
                                <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <input type="text" id="notes" name="notes"
                                       placeholder="e.g. Received from supplier, spoilage on 08/01…"
                                       class="form-input input-with-icon">
                            </div>
                        </div>
                        <button type="submit"
                                :disabled="adjustSubmitting"
                                class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-amber-500 text-white font-bold text-sm hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/25 flex-shrink-0 disabled:opacity-60">
                            <template x-if="!adjustSubmitting">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Adjust Stock
                                </span>
                            </template>
                            <template x-if="adjustSubmitting">
                                <span class="flex items-center gap-2">
                                    <svg class="spinner-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Adjusting…
                                </span>
                            </template>
                        </button>
                    </div>
                    <p class="text-[11px] text-beige-400 mt-3 font-medium">
                        ⚡ Stock changes apply immediately. Spoilage automatically deducts; all others add to current stock.
                    </p>
                </form>
            </div>
        </div>

        {{-- ══ SECTION 6: Stock Movement History ══ --}}
        @if($product->stockMovements->count() > 0)
        <div class="form-card">
            <div class="section-header">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-beige-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-beige-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-mint-900">Stock History</h2>
                            <p class="text-[11px] text-beige-400 font-medium">Last {{ $product->stockMovements->count() }} movements</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-beige-100">
                            <th class="text-left px-6 py-3 text-[10px] font-bold text-beige-400 uppercase tracking-wider">Type</th>
                            <th class="text-right px-4 py-3 text-[10px] font-bold text-beige-400 uppercase tracking-wider">Qty</th>
                            <th class="text-right px-4 py-3 text-[10px] font-bold text-beige-400 uppercase tracking-wider">Before</th>
                            <th class="text-right px-4 py-3 text-[10px] font-bold text-beige-400 uppercase tracking-wider">After</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-beige-400 uppercase tracking-wider">Notes</th>
                            <th class="text-right px-6 py-3 text-[10px] font-bold text-beige-400 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-beige-50" id="stock-movements-tbody">
                        @foreach($product->stockMovements->sortByDesc('created_at')->take(15) as $mov)
                        <tr class="movement-row">
                            <td class="px-6 py-3">
                                @php
                                    $typeClasses = [
                                        'stock_in'   => 'bg-mint-100 text-mint-700',
                                        'sale'       => 'bg-beige-100 text-beige-700',
                                        'adjustment' => 'bg-blue-100 text-blue-700',
                                        'spoilage'   => 'bg-red-100 text-red-700',
                                        'return'     => 'bg-amber-100 text-amber-700',
                                    ];
                                    $cls = $typeClasses[$mov->type] ?? 'bg-beige-100 text-beige-600';
                                @endphp
                                <span class="movement-type-badge {{ $cls }}" style="min-height:unset;">
                                    {{ ucfirst(str_replace('_',' ',$mov->type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-black {{ $mov->quantity >= 0 ? 'text-mint-600' : 'text-red-600' }}">
                                {{ $mov->quantity >= 0 ? '+' : '' }}{{ $mov->quantity }}
                            </td>
                            <td class="px-4 py-3 text-right text-beige-500 font-semibold">{{ $mov->stock_before }}</td>
                            <td class="px-4 py-3 text-right font-bold text-mint-900">{{ $mov->stock_after }}</td>
                            <td class="px-4 py-3 text-beige-500 text-xs max-w-[160px] truncate">{{ $mov->notes ?: '—' }}</td>
                            <td class="px-6 py-3 text-right text-xs text-beige-400 font-medium whitespace-nowrap">{{ $mov->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @include('categories._modal')
        @include('products._barcode_scanner')
    </div>
</div>
@endsection

@push('scripts')
<script>
function editProductForm() {
    return {
        productName:    '{{ old('name', addslashes($product->name)) }}',
        costPrice:      {{ old('cost_price', (float)$product->cost_price) }},
        sellingPrice:   {{ old('selling_price', (float)$product->selling_price) }},
        submitting:     false,
        adjustSubmitting: false,
        errors:         {},
        isDirty:        false,
        isDragging:     false,
        currentImageUrl:'{{ $product->image_url }}',
        imagePreview:   null,
        imageError:     '',
        adjustType:     'stock_in',

        get canSubmit() {
            return this.productName.trim() !== '' &&
                   parseFloat(this.costPrice) > 0 &&
                   parseFloat(this.sellingPrice) > 0;
        },

        get profit()  { return parseFloat(this.sellingPrice||0) - parseFloat(this.costPrice||0); },
        get margin()  {
            const sell = parseFloat(this.sellingPrice||0);
            return sell === 0 ? 0 : (this.profit/sell)*100;
        },
        get markup()  {
            const cost = parseFloat(this.costPrice||0);
            return cost === 0 ? 0 : (this.profit/cost)*100;
        },

        init() {
            // Unsaved changes alert removed as requested
        },

        markDirty() { this.isDirty = true; },

        generateSku() {
            const name = document.getElementById('name').value.trim();
            if (!name) return;
            const words = name.split(' ').slice(0,3).map(w => w.substring(0,3).toUpperCase());
            const rand  = Math.floor(Math.random()*9000+1000);
            document.getElementById('sku').value = words.join('-') + '-' + rand;
            this.markDirty();
        },

        handleImageSelect(e) { this.processImage(e.target.files[0]); },

        handleImageDrop(e) {
            this.isDragging = false;
            const file = e.dataTransfer.files[0];
            if (file) {
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
            if (!file.type.startsWith('image/')) { this.imageError = 'Please select a valid image file.'; return; }
            if (file.size > 5*1024*1024)         { this.imageError = 'Image must be smaller than 5MB.'; return; }
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

        async handleSubmit(e) {
            if (this.submitting) { e.preventDefault(); return; }
            e.preventDefault();

            // Client-side required field check
            this.errors = {};
            const name = document.getElementById('name').value.trim();
            const costPrice = document.getElementById('cost_price').value.trim();
            const sellingPrice = document.getElementById('selling_price').value.trim();
            if (!name)         this.errors.name          = ['Product name is required.'];
            if (!costPrice)    this.errors.cost_price    = ['Cost price is required.'];
            if (!sellingPrice) this.errors.selling_price = ['Selling price is required.'];
            if (Object.keys(this.errors).length > 0) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'Please fill in all required fields.', type: 'error' } }));
                return;
            }

            this.submitting = true;
            this.errors = {};

            try {
                const formData = new FormData(document.getElementById('main-product-form'));
                const response = await fetch(document.getElementById('main-product-form').action, {
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
                    if (data && data.product && data.product.image_path) {
                        this.currentImageUrl = data.product.image_path.startsWith('http') ? data.product.image_path : '/storage/' + data.product.image_path;
                    }
                    this.clearImage(); // Clear preview since it's now the current image
                    
                    this.isDirty = false;
                    this.submitting = false;
                    window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'Product updated successfully!', type: 'success' } }));
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'Network error occurred while saving the product.', type: 'error' } }));
                this.submitting = false;
            }
        },

        async handleAdjustSubmit(e) {
            if (this.adjustSubmitting) { e.preventDefault(); return; }
            e.preventDefault();
            this.adjustSubmitting = true;

            try {
                const form = this.$refs.adjustForm;
                const formData = new FormData(form);
                const response = await fetch(form.action, {
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
                    this.adjustSubmitting = false;
                    return;
                }

                if (!response.ok) {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'Error: ' + (data.message || response.statusText), type: 'error' } }));
                } else {
                    // Success! 
                    window.dispatchEvent(new CustomEvent('notify', { detail: { msg: data.message || 'Stock adjusted successfully!', type: 'success' } }));
                    this.$refs.adjustForm.reset();
                    
                    // Update current stock in DOM
                    const stockEls = document.querySelectorAll('.stock-count-display, .text-mint-700.font-black, strong.text-mint-700');
                    stockEls.forEach(el => el.textContent = data.new_stock + (el.textContent.includes(' ') ? ' ' + el.textContent.split(' ')[1] : ''));

                    // Inject new row
                    if (data.movement) {
                        this.injectMovementRow(data.movement);
                    }
                }
            } catch (err) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'Network error occurred while adjusting stock.', type: 'error' } }));
            } finally {
                this.adjustSubmitting = false;
            }
        },

        injectMovementRow(mov) {
            const typeClasses = {
                'stock_in': 'bg-mint-100 text-mint-700',
                'sale': 'bg-beige-100 text-beige-700',
                'adjustment': 'bg-blue-100 text-blue-700',
                'spoilage': 'bg-red-100 text-red-700',
                'return': 'bg-amber-100 text-amber-700',
            };
            const cls = typeClasses[mov.type] || 'bg-beige-100 text-beige-600';
            const formattedType = mov.type.charAt(0).toUpperCase() + mov.type.slice(1).replace('_', ' ');
            const sign = mov.quantity >= 0 ? '+' : '';
            const qtyCls = mov.quantity >= 0 ? 'text-mint-600' : 'text-red-600';

            const html = `
                <tr class="movement-row" style="background-color: #f0fdf4;">
                    <td class="px-6 py-3">
                        <span class="movement-type-badge ${cls}" style="min-height:unset;">
                            ${formattedType}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-black ${qtyCls}">
                        ${sign}${mov.quantity}
                    </td>
                    <td class="px-4 py-3 text-right text-beige-500 font-semibold">${mov.stock_before}</td>
                    <td class="px-4 py-3 text-right font-bold text-mint-900">${mov.stock_after}</td>
                    <td class="px-4 py-3 text-beige-500 text-xs max-w-[160px] truncate">${mov.notes || '—'}</td>
                    <td class="px-6 py-3 text-right text-xs text-beige-400 font-medium whitespace-nowrap">${mov.date}</td>
                </tr>
            `;
            const tbody = document.getElementById('stock-movements-tbody');
            if (tbody) tbody.insertAdjacentHTML('afterbegin', html);
        }
    };
}
</script>
@endpush
