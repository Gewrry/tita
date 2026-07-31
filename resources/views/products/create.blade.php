@extends('layouts.app')
@section('title', 'Add Product')
@section('page-title', 'Add Product')

@section('content')
<div class="max-w-3xl" x-data="categoryModalHandler()">
    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="bg-white border border-beige-200/60 rounded-3xl p-8 mb-6">
            <h3 class="text-sm font-bold text-mint-900 mb-6">Product Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Category</label>
                    <div class="flex items-center gap-2">
                        <select name="category_id" id="category_select" class="flex-1 px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500">
                            <option value="">No Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                            <option value="{{ $val }}" {{ old('unit', 'piece') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Description</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white border border-beige-200/60 rounded-3xl p-8 mb-6">
            <h3 class="text-sm font-bold text-mint-900 mb-6">Pricing & Stock</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Cost Price (₱) *</label>
                    <input type="number" name="cost_price" value="{{ old('cost_price', '0') }}" step="0.01" min="0" required class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Selling Price (₱) *</label>
                    <input type="number" name="selling_price" value="{{ old('selling_price', '0') }}" step="0.01" min="0" required class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Initial Stock *</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', '0') }}" min="0" required class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Reorder Level</label>
                    <input type="number" name="reorder_level" value="{{ old('reorder_level', '5') }}" min="0" class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-beige-300 text-mint-500 focus:ring-mint-500">
                        <span class="text-sm font-medium text-mint-900">Active</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="track_stock" value="0">
                        <input type="checkbox" name="track_stock" value="1" {{ old('track_stock', true) ? 'checked' : '' }} class="rounded border-beige-300 text-mint-500 focus:ring-mint-500">
                        <span class="text-sm font-medium text-mint-900">Track Stock</span>
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 bg-beige-50 border border-beige-200 rounded-xl text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-mint-500 file:text-white file:text-xs file:font-bold">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-beige-500 hover:text-mint-600">← Back to Products</a>
            <button type="submit" class="px-8 py-3 bg-mint-500 text-white font-bold text-sm rounded-xl hover:bg-mint-600 transition-all shadow-lg shadow-mint-500/30">Save Product</button>
        </div>
    </form>

    @include('categories._modal')
</div>
@endsection
