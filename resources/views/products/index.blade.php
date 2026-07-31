@extends('layouts.app')
@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
<div x-data="categoryModalHandler()">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex items-center gap-3 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="px-4 py-2.5 bg-white border border-beige-200 rounded-xl text-sm w-64">
        <div class="flex items-center gap-2">
            <select name="category" id="category_select" onchange="this.form.submit()" class="px-4 py-2.5 bg-white border border-beige-200 rounded-xl text-sm">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="button" @click="showCategoryModal = true" class="px-3 py-2.5 bg-mint-50 text-mint-600 rounded-xl hover:bg-mint-100 transition-colors border border-mint-200 font-bold text-sm" title="Create New Category">
                + New
            </button>
            <button type="button" @click="showManageModal = true" class="px-3 py-2.5 bg-white text-beige-500 rounded-xl hover:bg-beige-50 transition-colors border border-beige-200" title="Manage Categories">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <select name="stock_status" onchange="this.form.submit()" class="px-4 py-2.5 bg-white border border-beige-200 rounded-xl text-sm">
            <option value="">All Stock</option>
            <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>⚠️ Low Stock</option>
            <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>🚫 Out of Stock</option>
        </select>
    </form>
    <a href="{{ route('products.create') }}" class="px-6 py-2.5 bg-mint-500 text-white font-bold text-sm rounded-xl hover:bg-mint-600 transition-all shadow-lg shadow-mint-500/30">+ Add Product</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($products as $product)
    <div class="bg-white border border-beige-200/60 rounded-2xl overflow-hidden group hover:shadow-xl transition-all">
        <div class="h-28 bg-gradient-to-br from-beige-50 to-beige-100 flex items-center justify-center relative">
            @if($product->image_path)
                <img src="{{ asset('storage/' . $product->image_path) }}" class="w-full h-full object-cover">
            @else
                <span class="text-4xl">📦</span>
            @endif
            @if($product->track_stock && $product->isOutOfStock())
                <span class="absolute top-2 right-2 px-2 py-0.5 bg-red-500 text-white text-[9px] font-black uppercase rounded-lg">OUT</span>
            @elseif($product->track_stock && $product->isLowStock())
                <span class="absolute top-2 right-2 px-2 py-0.5 bg-amber-500 text-white text-[9px] font-black uppercase rounded-lg">LOW</span>
            @endif
            @if($product->category)
                <span class="absolute top-2 left-2 px-2 py-0.5 text-[9px] font-bold uppercase rounded-lg text-white" style="background-color: {{ $product->category->color }}">{{ $product->category->name }}</span>
            @endif
        </div>
        <div class="p-4">
            <h4 class="text-sm font-bold text-mint-900 truncate mb-1">{{ $product->name }}</h4>
            <div class="flex items-center justify-between mb-3">
                <span class="text-lg font-black text-mint-600">₱{{ number_format($product->selling_price, 2) }}</span>
                <span class="text-[10px] font-bold text-beige-500 uppercase">{{ $product->unit }}</span>
            </div>
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs text-beige-500">Stock: <strong class="text-mint-900">{{ $product->track_stock ? $product->stock_quantity : '∞' }}</strong></span>
                <div class="flex items-center gap-1">
                    @if(business()?->isRestaurant())
                    <a href="{{ route('smart-pricing.index', ['product' => $product->id]) }}" class="p-1.5 text-beige-400 hover:text-mint-600 rounded-lg transition-colors" title="Smart Pricing" aria-label="Smart Pricing for {{ $product->name }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </a>
                    @endif
                    <a href="{{ route('products.edit', $product) }}" class="p-1.5 text-beige-400 hover:text-mint-600 rounded-lg transition-colors" title="Edit Product" aria-label="Edit {{ $product->name }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white border border-beige-200/60 rounded-3xl p-12 text-center">
        <p class="text-4xl mb-4">📦</p>
        <p class="text-sm font-bold text-beige-400 mb-4">No products yet.</p>
        <a href="{{ route('products.create') }}" class="px-6 py-2.5 bg-mint-500 text-white font-bold text-sm rounded-xl">+ Add Product</a>
    </div>
    @endforelse
</div>
<div class="mt-6">{{ $products->links() }}</div>

    @include('categories._modal')
</div>
@endsection
