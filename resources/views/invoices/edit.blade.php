@extends('layouts.app')
@section('title', 'Edit Invoice')
@section('page-title', 'Edit Invoice')

@section('content')
<div class="max-w-4xl" x-data="invoiceForm()">
    <form method="POST" action="{{ route('invoices.update', $invoice) }}">
        @csrf @method('PUT')
        <div class="bg-slate-900 border border-slate-800/50 rounded-2xl p-6 mb-5">
            <h3 class="text-sm font-medium text-slate-400 uppercase tracking-wider mb-4">Invoice Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Customer <span class="text-red-400">*</span></label>
                    <select name="customer_id" required class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-emerald-500/50 transition-all">
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $invoice->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Invoice #</label>
                    <input type="text" name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}" 
                           class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Issue Date <span class="text-red-400">*</span></label>
                    <input type="date" name="issue_date" value="{{ $invoice->issue_date->format('Y-m-d') }}" required class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Due Date <span class="text-red-400">*</span></label>
                    <input type="date" name="due_date" value="{{ $invoice->due_date->format('Y-m-d') }}" required class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800/50 rounded-2xl p-6 mb-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-slate-400 uppercase tracking-wider">Line Items</h3>
                <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 text-emerald-400 text-xs font-medium rounded-lg hover:bg-emerald-500/20 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Item
                </button>
            </div>
            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex items-start gap-3 p-3 bg-slate-800/30 rounded-xl">
                        <div class="flex-1"><input type="text" :name="'items['+index+'][description]'" x-model="item.description" placeholder="Description" required class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-emerald-500/50 transition-all"></div>
                        <div class="w-24"><input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" min="1" required @input="calcTotal()" class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-sm text-slate-200 text-center focus:outline-none focus:border-emerald-500/50 transition-all"></div>
                        <div class="w-36"><input type="number" :name="'items['+index+'][price]'" x-model.number="item.price" min="0" step="0.01" required @input="calcTotal()" class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-sm text-slate-200 text-right focus:outline-none focus:border-emerald-500/50 transition-all"></div>
                        <div class="w-36 flex items-center justify-end"><span class="text-sm font-semibold text-white" x-text="'₱' + (item.quantity * item.price).toLocaleString('en-PH', {minimumFractionDigits: 2})"></span></div>
                        <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="p-2 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-500/10 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>
            <div class="flex items-center justify-end mt-4 pt-4 border-t border-slate-800/50">
                <span class="text-sm text-slate-400">Total: </span>
                <span class="text-xl font-bold text-white ml-2" x-text="'₱' + grandTotal.toLocaleString('en-PH', {minimumFractionDigits: 2})"></span>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800/50 rounded-2xl p-6 mb-5">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Penalty Type</label>
                    <select name="penalty_type" class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-emerald-500/50 transition-all">
                        <option value="none" {{ $invoice->penalty_type === 'none' ? 'selected' : '' }}>None</option>
                        <option value="flat" {{ $invoice->penalty_type === 'flat' ? 'selected' : '' }}>Flat Fee</option>
                        <option value="percentage" {{ $invoice->penalty_type === 'percentage' ? 'selected' : '' }}>Percentage</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Penalty Value</label>
                    <input type="number" name="penalty_value" value="{{ $invoice->penalty_value }}" min="0" step="0.01" class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Notes</label>
                    <input type="text" name="notes" value="{{ $invoice->notes }}" class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-cyan-500 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-emerald-500/25 transition-all duration-300">Update Invoice</button>
            <a href="{{ route('invoices.show', $invoice) }}" class="px-5 py-2.5 text-sm text-slate-400 hover:text-white transition-colors">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function invoiceForm() {
    return {
        items: @json($invoice->items->map(fn($i) => ['description' => $i->description, 'quantity' => $i->quantity, 'price' => (float)$i->price])),
        grandTotal: {{ $invoice->total_amount }},
        addItem() { this.items.push({ description: '', quantity: 1, price: 0 }); },
        removeItem(i) { this.items.splice(i, 1); this.calcTotal(); },
        calcTotal() { this.grandTotal = this.items.reduce((s, i) => s + (i.quantity * i.price), 0); }
    }
}
</script>
@endpush
@endsection

