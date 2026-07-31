@extends('layouts.app')
@section('title', 'Table Management')
@section('page-title', 'Tables')

@section('content')
<div x-data="tableManager()">
    <!-- Add Table -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-400"></span><span class="text-xs font-bold text-beige-600">Available</span>
                <span class="w-3 h-3 rounded-full bg-red-400 ml-2"></span><span class="text-xs font-bold text-beige-600">Occupied</span>
                <span class="w-3 h-3 rounded-full bg-amber-400 ml-2"></span><span class="text-xs font-bold text-beige-600">Reserved</span>
                <span class="w-3 h-3 rounded-full bg-gray-400 ml-2"></span><span class="text-xs font-bold text-beige-600">Dirty</span>
            </div>
        </div>
        <button @click="showAddTable = true" class="px-6 py-2.5 bg-mint-500 text-white font-bold text-sm rounded-xl hover:bg-mint-600 transition-all shadow-lg shadow-mint-500/30">+ Add Table</button>
    </div>

    <!-- Table Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach($tables as $table)
        <div class="relative group">
            <a href="{{ route('pos.index') }}?table_id={{ $table->id }}"
               class="block bg-white border-2 rounded-2xl p-6 text-center transition-all hover:shadow-xl
                      {{ $table->status === 'available' ? 'border-emerald-300 hover:border-emerald-500' : '' }}
                      {{ $table->status === 'occupied' ? 'border-red-300 bg-red-50 hover:border-red-500' : '' }}
                      {{ $table->status === 'reserved' ? 'border-amber-300 bg-amber-50 hover:border-amber-500' : '' }}
                      {{ $table->status === 'dirty' ? 'border-gray-300 bg-gray-50 hover:border-gray-500' : '' }}">
                <div class="text-3xl mb-2">
                    {{ $table->status === 'available' ? '🟢' : ($table->status === 'occupied' ? '🔴' : ($table->status === 'reserved' ? '🟡' : '⚪')) }}
                </div>
                <h4 class="text-sm font-bold text-mint-900">{{ $table->display_name }}</h4>
                <p class="text-[10px] font-bold text-beige-500 uppercase mt-1">{{ $table->capacity }} seats · {{ ucfirst($table->status) }}</p>
                @if($table->activeOrder)
                    <p class="text-sm font-black text-red-600 mt-2">₱{{ number_format($table->activeOrder->total_amount, 2) }}</p>
                @endif
            </a>
            <!-- Quick Status -->
            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <div x-data="{ open: false }" class="relative">
                    <button @click.prevent="open = !open" class="w-6 h-6 rounded-lg bg-white/80 border border-beige-200 flex items-center justify-center text-beige-400 hover:text-mint-600">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="5" r="2"/><circle cx="10" cy="10" r="2"/><circle cx="10" cy="15" r="2"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-1 w-36 bg-white border border-beige-200 rounded-xl shadow-lg py-1 z-20">
                        <button @click="updateTableStatus({{ $table->id }}, 'available'); open = false" class="block w-full text-left px-3 py-1.5 text-xs font-medium hover:bg-beige-50">🟢 Available</button>
                        <button @click="updateTableStatus({{ $table->id }}, 'reserved'); open = false" class="block w-full text-left px-3 py-1.5 text-xs font-medium hover:bg-beige-50">🟡 Reserved</button>
                        <button @click="updateTableStatus({{ $table->id }}, 'dirty'); open = false" class="block w-full text-left px-3 py-1.5 text-xs font-medium hover:bg-beige-50">⚪ Dirty</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Add Table Modal -->
    <div x-show="showAddTable" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 m-4">
            <h3 class="text-lg font-bold text-mint-900 mb-4">Add New Table</h3>
            <form method="POST" action="{{ route('tables.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-beige-600 uppercase mb-2">Table Number</label>
                        <input type="number" name="table_number" min="1" required class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-beige-600 uppercase mb-2">Name (Optional)</label>
                        <input type="text" name="name" placeholder="e.g., Window Seat" class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-beige-600 uppercase mb-2">Capacity</label>
                        <input type="number" name="capacity" value="4" min="1" required class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" @click="showAddTable = false" class="flex-1 py-3 border border-beige-200 text-beige-600 font-bold text-sm rounded-xl">Cancel</button>
                    <button type="submit" class="flex-1 py-3 bg-mint-500 text-white font-bold text-sm rounded-xl">Add Table</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function tableManager() {
    return {
        showAddTable: false,
        async updateTableStatus(tableId, status) {
            await fetch(`/tables/${tableId}/status`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ status })
            });
            location.reload();
        }
    }
}
</script>
@endpush
