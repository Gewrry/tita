@extends('layouts.app')
@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="max-w-3xl" x-data="categoryModalHandler()">
    <!-- Main Product Update Form -->
    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" id="main-product-form">
        @csrf @method('PUT')
        
        <div class="bg-white border border-beige-200/60 rounded-3xl p-8 mb-6">
            <h3 class="text-sm font-bold text-mint-900 mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-mint-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Product Details
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Category</label>
                    <div class="flex items-center gap-2">
                        <select name="category_id" id="category_select" class="flex-1 w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                            <option value="">No Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" @click="showCategoryModal = true" class="px-4 py-3 bg-mint-500 text-white rounded-xl hover:bg-mint-600 transition-colors shadow-sm font-bold text-lg leading-none" title="Create New Category">+</button>
                        <button type="button" @click="showManageModal = true" class="px-4 py-3 bg-white text-beige-500 rounded-xl hover:bg-beige-50 transition-colors border border-beige-200" title="Manage Categories">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Unit</label>
                    <select name="unit" class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                        @foreach($units as $val => $label)
                            <option value="{{ $val }}" {{ old('unit', $product->unit) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Description</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white border border-beige-200/60 rounded-3xl p-8 mb-6">
            <h3 class="text-sm font-bold text-mint-900 mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-mint-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Pricing & Stock Configuration
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Cost Price (₱)</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" min="0" required class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Selling Price (₱)</label>
                    <input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" step="0.01" min="0" required class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Current Stock</label>
                    <div class="px-4 py-3 bg-beige-100 border border-beige-200 rounded-xl text-sm font-bold text-mint-900 flex justify-between items-center">
                        <span>{{ $product->stock_quantity }} {{ $product->unit }}s</span>
                        <span class="text-[10px] bg-mint-500 text-white px-2 py-0.5 rounded-full uppercase tracking-widest">Live</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Reorder Level</label>
                    <input type="number" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}" min="0" class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <div class="flex items-center gap-6 py-2">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded border-beige-300 text-mint-500 focus:ring-mint-500 transition-all">
                        <span class="text-sm font-bold text-mint-900 group-hover:text-mint-600">Active</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="hidden" name="track_stock" value="0">
                        <input type="checkbox" name="track_stock" value="1" {{ old('track_stock', $product->track_stock) ? 'checked' : '' }} class="w-5 h-5 rounded border-beige-300 text-mint-500 focus:ring-mint-500 transition-all">
                        <span class="text-sm font-bold text-mint-900 group-hover:text-mint-600">Track Stock</span>
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Product Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 bg-beige-50 border border-beige-200 rounded-xl text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-mint-500 file:text-white file:text-xs file:font-bold transition-all">
                </div>
            </div>
        </div>
    </form>

    <!-- Quick Stock Adjustment Form (Separate) -->
    <div class="bg-white border border-beige-200/60 rounded-3xl p-8 mb-6">
        <h3 class="text-sm font-bold text-mint-900 mb-6 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
            Quick Stock Adjustment
        </h3>
        <form method="POST" action="{{ route('products.adjust-stock', $product) }}">
            @csrf
            <div class="flex flex-col md:flex-row items-end gap-4">
                <div class="flex-1 w-full">
                    <label class="block text-[10px] font-bold text-beige-500 uppercase mb-1">Adjustment Type</label>
                    <select name="type" class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                        <option value="stock_in">Stock In (Add)</option>
                        <option value="adjustment">General Adjustment (Add/Subtract)</option>
                        <option value="spoilage">Spoilage (Remove)</option>
                        <option value="return">Return (Add)</option>
                    </select>
                </div>
                <div class="w-full md:w-32">
                    <label class="block text-[10px] font-bold text-beige-500 uppercase mb-1">Qty</label>
                    <input type="number" name="quantity" min="-1000" value="1" required class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-[10px] font-bold text-beige-500 uppercase mb-1">Reason / Notes</label>
                    <input type="text" name="notes" placeholder="Optional" class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-amber-500 text-white font-bold text-sm rounded-xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/30">
                    Adjust
                </button>
            </div>
            <p class="text-[10px] text-beige-400 mt-4 italic">* This instantly updates your stock levels. Use positive for additions, negative for deductions.</p>
        </form>
    </div>

    <!-- Actions Bottom Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-4">
        <div class="flex items-center gap-6">
            <a href="{{ route('products.index') }}" class="text-sm font-bold text-beige-400 hover:text-mint-600 flex items-center gap-1 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Inventory
            </a>
            <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('⚠️ Are you sure? This will permanently delete the product.')">
                @csrf @method('DELETE')
                <button type="submit" class="text-sm font-bold text-red-300 hover:text-red-500 transition-all">Delete Product</button>
            </form>
        </div>
        <button type="submit" form="main-product-form" class="w-full sm:w-auto px-10 py-3.5 bg-mint-500 text-white font-black text-sm rounded-xl hover:bg-mint-600 transition-all shadow-xl shadow-mint-500/40">
            Update Product Information
        </button>
    </div>

    @include('categories._modal')
</div>
@endsection
