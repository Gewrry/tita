@extends('layouts.app')
@section('title', 'Inventory Report')
@section('page-title', 'Inventory Report')

@section('content')
<!-- KPI Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-beige-200/60 rounded-2xl p-5">
        <p class="text-[10px] font-bold text-beige-500 uppercase mb-1">Total Products</p>
        <p class="text-2xl font-black text-mint-900">{{ $products->count() }}</p>
    </div>
    <div class="bg-white border border-beige-200/60 rounded-2xl p-5">
        <p class="text-[10px] font-bold text-beige-500 uppercase mb-1">Inventory Value (Cost)</p>
        <p class="text-2xl font-black text-mint-600">₱{{ number_format($totalValue, 2) }}</p>
    </div>
    <div class="bg-white border border-amber-200/60 rounded-2xl p-5">
        <p class="text-[10px] font-bold text-beige-500 uppercase mb-1">Low Stock Items</p>
        <p class="text-2xl font-black text-amber-600">{{ $lowStockCount }}</p>
    </div>
    <div class="bg-white border border-red-200/60 rounded-2xl p-5">
        <p class="text-[10px] font-bold text-beige-500 uppercase mb-1">Out of Stock</p>
        <p class="text-2xl font-black text-red-600">{{ $outOfStockCount }}</p>
    </div>
</div>

<!-- Products Table -->
<div class="bg-white border border-beige-200/60 rounded-3xl overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-beige-100">
                <th class="px-6 py-4 text-left text-[10px] font-bold text-beige-500 uppercase">Product</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-beige-500 uppercase">Category</th>
                <th class="px-6 py-4 text-right text-[10px] font-bold text-beige-500 uppercase">Cost</th>
                <th class="px-6 py-4 text-right text-[10px] font-bold text-beige-500 uppercase">Price</th>
                <th class="px-6 py-4 text-right text-[10px] font-bold text-beige-500 uppercase">Stock</th>
                <th class="px-6 py-4 text-right text-[10px] font-bold text-beige-500 uppercase">Value</th>
                <th class="px-6 py-4 text-center text-[10px] font-bold text-beige-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-beige-100">
            @foreach($products as $product)
            <tr class="hover:bg-beige-50">
                <td class="px-6 py-3 text-sm font-bold text-mint-900">{{ $product->name }}</td>
                <td class="px-6 py-3 text-sm text-beige-600">{{ $product->category?->name ?? '-' }}</td>
                <td class="px-6 py-3 text-sm text-right text-beige-600">₱{{ number_format($product->cost_price, 2) }}</td>
                <td class="px-6 py-3 text-sm text-right font-bold text-mint-900">₱{{ number_format($product->selling_price, 2) }}</td>
                <td class="px-6 py-3 text-sm text-right font-bold text-mint-900">{{ $product->track_stock ? $product->stock_quantity : '∞' }}</td>
                <td class="px-6 py-3 text-sm text-right font-bold text-mint-600">₱{{ number_format($product->stock_quantity * $product->cost_price, 2) }}</td>
                <td class="px-6 py-3 text-center">
                    @if($product->isOutOfStock()) <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[9px] font-black uppercase rounded-lg">OUT</span>
                    @elseif($product->isLowStock()) <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[9px] font-black uppercase rounded-lg">LOW</span>
                    @else <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[9px] font-black uppercase rounded-lg">OK</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
