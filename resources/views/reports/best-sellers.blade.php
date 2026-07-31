@extends('layouts.app')
@section('title', 'Best Sellers')
@section('page-title', 'Best Sellers Report')

@section('content')
<form method="GET" class="flex items-center gap-3 mb-6">
    <input type="date" name="from" value="{{ $from }}" class="px-4 py-2.5 bg-white border border-beige-200 rounded-xl text-sm">
    <span class="text-sm text-beige-400">to</span>
    <input type="date" name="to" value="{{ $to }}" class="px-4 py-2.5 bg-white border border-beige-200 rounded-xl text-sm">
    <button type="submit" class="px-6 py-2.5 bg-mint-500 text-white font-bold text-sm rounded-xl">Filter</button>
</form>

<div class="bg-white border border-beige-200/60 rounded-3xl overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-beige-100">
                <th class="px-6 py-4 text-left text-[10px] font-bold text-beige-500 uppercase">#</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-beige-500 uppercase">Product</th>
                <th class="px-6 py-4 text-right text-[10px] font-bold text-beige-500 uppercase">Units Sold</th>
                <th class="px-6 py-4 text-right text-[10px] font-bold text-beige-500 uppercase">Revenue</th>
                <th class="px-6 py-4 text-right text-[10px] font-bold text-beige-500 uppercase">Est. Profit</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-beige-100">
            @forelse($sellers as $i => $seller)
            <tr class="hover:bg-beige-50">
                <td class="px-6 py-3"><span class="w-7 h-7 rounded-lg bg-mint-100 flex items-center justify-center text-xs font-black text-mint-600">{{ $i + 1 }}</span></td>
                <td class="px-6 py-3 text-sm font-bold text-mint-900">{{ $seller->name }}</td>
                <td class="px-6 py-3 text-sm text-right font-black text-mint-900">{{ number_format($seller->total_sold) }}</td>
                <td class="px-6 py-3 text-sm text-right font-bold text-mint-600">₱{{ number_format($seller->total_revenue, 2) }}</td>
                <td class="px-6 py-3 text-sm text-right font-bold text-emerald-600">₱{{ number_format(($seller->selling_price - $seller->cost_price) * $seller->total_sold, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-beige-400">No sales data for this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
