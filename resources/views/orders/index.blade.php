@extends('layouts.app')
@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
<div class="flex items-center justify-between mb-6">
    <form method="GET" class="flex items-center gap-3">
        <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 bg-white border border-beige-200 rounded-xl text-sm font-semibold">
            <option value="">Active Orders</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Preparing</option>
            <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready</option>
            <option value="served" {{ request('status') === 'served' ? 'selected' : '' }}>Served</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </form>
</div>

<div class="bg-white border border-beige-200/60 rounded-3xl overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-beige-100">
                <th class="px-6 py-4 text-left text-[10px] font-bold text-beige-500 uppercase tracking-widest">Order</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-beige-500 uppercase tracking-widest">Type</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-beige-500 uppercase tracking-widest">Table</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-beige-500 uppercase tracking-widest">Items</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-beige-500 uppercase tracking-widest">Total</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-beige-500 uppercase tracking-widest">Status</th>
                <th class="px-6 py-4 text-left text-[10px] font-bold text-beige-500 uppercase tracking-widest">Time</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-beige-100">
            @forelse($orders as $order)
            <tr class="hover:bg-beige-50 transition-colors">
                <td class="px-6 py-4 text-sm font-bold text-mint-900">{{ $order->order_number }}</td>
                <td class="px-6 py-4 text-sm text-beige-600">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</td>
                <td class="px-6 py-4 text-sm text-beige-600">{{ $order->table ? $order->table->display_name : '-' }}</td>
                <td class="px-6 py-4 text-sm text-beige-600">{{ $order->items->count() }} items</td>
                <td class="px-6 py-4 text-sm font-bold text-mint-900">₱{{ number_format($order->total_amount, 2) }}</td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-0.5 text-[9px] font-black uppercase rounded-full
                        {{ $order->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                        {{ $order->status === 'preparing' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $order->status === 'ready' ? 'bg-emerald-100 text-emerald-700' : '' }}
                        {{ $order->status === 'served' ? 'bg-mint-100 text-mint-700' : '' }}
                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-xs text-beige-500">{{ $order->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-6 py-12 text-center text-sm text-beige-400">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $orders->links() }}</div>
@endsection
