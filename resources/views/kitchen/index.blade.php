@extends('layouts.app')
@section('title', 'Kitchen Display')
@section('page-title', 'Kitchen Display')

@section('content')
<div x-data="kitchenApp()" x-init="startPolling()">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <span class="text-sm font-bold text-mint-900">Active Orders:</span>
            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm font-black">{{ $orders->where('status', 'pending')->count() }} Pending</span>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-black">{{ $orders->where('status', 'preparing')->count() }} Preparing</span>
            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-black">{{ $orders->where('status', 'ready')->count() }} Ready</span>
        </div>
        <div class="flex items-center gap-2 text-xs text-beige-400">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Auto-refreshing
        </div>
    </div>

    <!-- Orders Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($orders as $order)
        <div class="bg-white border-2 rounded-2xl overflow-hidden transition-all
                    {{ $order->status === 'pending' ? 'border-amber-300' : '' }}
                    {{ $order->status === 'preparing' ? 'border-blue-300' : '' }}
                    {{ $order->status === 'ready' ? 'border-emerald-300 animate-pulse' : '' }}">
            <!-- Order Header -->
            <div class="p-4 flex items-center justify-between
                        {{ $order->status === 'pending' ? 'bg-amber-50' : '' }}
                        {{ $order->status === 'preparing' ? 'bg-blue-50' : '' }}
                        {{ $order->status === 'ready' ? 'bg-emerald-50' : '' }}">
                <div>
                    <h4 class="text-sm font-black text-mint-900">{{ $order->order_number }}</h4>
                    <p class="text-[10px] font-bold text-beige-500 uppercase">
                        {{ $order->table ? $order->table->display_name : ucfirst(str_replace('_', ' ', $order->order_type)) }}
                        · {{ $order->created_at->diffForHumans() }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase
                                {{ $order->status === 'pending' ? 'bg-amber-200 text-amber-800' : '' }}
                                {{ $order->status === 'preparing' ? 'bg-blue-200 text-blue-800' : '' }}
                                {{ $order->status === 'ready' ? 'bg-emerald-200 text-emerald-800' : '' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            <!-- Order Items -->
            <div class="divide-y divide-beige-100">
                @foreach($order->items as $item)
                <div class="px-4 py-3 flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-mint-900">{{ $item->quantity }}×</span>
                            <span class="text-sm font-medium text-mint-900">{{ $item->product->name }}</span>
                        </div>
                        @if($item->special_instructions)
                            <p class="text-xs text-amber-600 mt-0.5 italic">⚠️ {{ $item->special_instructions }}</p>
                        @endif
                    </div>
                    <button @click="updateItemStatus({{ $item->id }}, '{{ $item->status === 'pending' ? 'preparing' : ($item->status === 'preparing' ? 'ready' : 'served') }}')"
                            class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all
                                   {{ $item->status === 'pending' ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : '' }}
                                   {{ $item->status === 'preparing' ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' : '' }}
                                   {{ $item->status === 'ready' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : '' }}
                                   {{ $item->status === 'served' ? 'bg-gray-100 text-gray-500' : '' }}">
                        {{ $item->status === 'pending' ? '▶ Start' : ($item->status === 'preparing' ? '✓ Ready' : ($item->status === 'ready' ? '🍽 Serve' : '✅ Done')) }}
                    </button>
                </div>
                @endforeach
            </div>

            <!-- Order Actions -->
            <div class="p-3 bg-beige-50 border-t border-beige-100 flex gap-2">
                @if($order->status !== 'ready')
                <button @click="markOrderReady({{ $order->id }})" class="flex-1 py-2 bg-emerald-500 text-white font-bold text-xs rounded-xl hover:bg-emerald-600 transition-all">✅ All Ready</button>
                @endif
                @if($order->notes)
                <div class="flex-1 py-2 text-center text-xs text-beige-500 italic">{{ $order->notes }}</div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white border border-beige-200/60 rounded-3xl p-16 text-center">
            <div class="text-5xl mb-4">👨‍🍳</div>
            <p class="text-sm font-bold text-beige-400">No active orders. Kitchen is clear!</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
function kitchenApp() {
    return {
        startPolling() { setInterval(() => location.reload(), 15000); },

        async updateItemStatus(itemId, status) {
            await fetch(`/kitchen/item/${itemId}/status`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ status })
            });
            location.reload();
        },

        async markOrderReady(orderId) {
            await fetch(`/kitchen/order/${orderId}/ready`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            });
            location.reload();
        }
    }
}
</script>
@endpush
