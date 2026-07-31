@extends('layouts.app')
@section('title', 'Create Invoice')
@section('page-title', 'Create Invoice')

@section('content')
<div class="max-w-4xl" x-init="calcTotal()" x-data="invoiceForm({{ old('use_unknown_customer') ? 'true' : 'false' }})">
    <form method="POST" action="{{ route('invoices.store') }}">
        @csrf
        <div class="bg-white border border-beige-200/60 rounded-3xl p-8 mb-6 shadow-sm">
            <h3 class="text-[11px] font-black text-mint-600 uppercase tracking-widest mb-6">Invoice Details</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Customer <span class="text-red-500">*</span></label>
                    <select name="customer_id" :required="!unknownCustomer" :disabled="unknownCustomer" class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm disabled:bg-beige-50 disabled:text-beige-400">
                        <option value="">Select customer</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id', request('customer_id')) == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    <label class="mt-3 flex items-start gap-2 text-xs font-bold text-beige-500">
                        <input type="checkbox" name="use_unknown_customer" value="1" x-model="unknownCustomer" class="mt-0.5 rounded border-beige-300 text-mint-600 focus:ring-mint-500">
                        <span>Customer name is unknown. Create the next Unknown Customer automatically.</span>
                    </label>
                </div>
                <div>
                    <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Invoice #</label>
                    <input type="text" name="invoice_number" value="{{ old('invoice_number', $invoiceNumber) }}" 
                           class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                </div>
                <div>
                    <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Issue Date <span class="text-red-500">*</span></label>
                    <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required
                           class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                </div>
                <div>
                    <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Due Date <span class="text-red-500">*</span></label>
                    <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required
                           class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                </div>
            </div>
        </div>

        <!-- Line Items -->
        <div class="bg-white border border-beige-200/60 rounded-3xl p-8 mb-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-[11px] font-black text-mint-600 uppercase tracking-widest">Line Items</h3>
                <button type="button" @click="addItem()" class="inline-flex items-center gap-2 px-4 py-2 bg-mint-50 text-mint-600 text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-mint-100 transition-all border border-mint-200/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Add Item
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex items-start gap-4 p-5 bg-beige-50/50 border border-beige-100 rounded-2xl group transition-all hover:bg-white hover:border-beige-200 hover:shadow-sm">
                        <div class="flex-1 min-w-0">
                            <input type="text" :name="'items['+index+'][description]'" x-model="item.description" placeholder="Description of service or product" required
                                   class="w-full px-4 py-2.5 bg-white border border-beige-200 rounded-xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all">
                        </div>
                        <div class="w-24">
                            <input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" min="1" placeholder="Qty" required
                                   @input="calcTotal()"
                                   class="w-full px-4 py-2.5 bg-white border border-beige-200 rounded-xl text-sm font-black text-mint-900 text-center focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all">
                        </div>
                        <div class="w-40">
                            <input type="number" :name="'items['+index+'][price]'" x-model.number="item.price" min="0" step="0.01" placeholder="0.00" required
                                   @input="calcTotal()"
                                   class="w-full px-4 py-2.5 bg-white border border-beige-200 rounded-xl text-sm font-black text-mint-900 text-right focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all">
                        </div>
                        <div class="w-40 flex items-center justify-end px-2">
                            <span class="text-sm font-black text-mint-600 tracking-tight" x-text="'₱' + (item.quantity * item.price).toLocaleString('en-PH', {minimumFractionDigits: 2})"></span>
                        </div>
                        <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="p-2.5 rounded-xl text-beige-300 hover:text-red-500 hover:bg-white hover:border-beige-200 border border-transparent transition-all shadow-sm">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Total Summary -->
            <div class="flex items-center justify-end mt-8 pt-6 border-t border-beige-100">
                <div class="flex items-baseline gap-4">
                    <span class="text-[11px] font-black text-beige-400 uppercase tracking-widest">Total Amount</span>
                    <span class="text-3xl font-black text-mint-900 tracking-tighter" x-text="'₱' + grandTotal.toLocaleString('en-PH', {minimumFractionDigits: 2})"></span>
                </div>
            </div>
        </div>

        <!-- Penalty & Notes -->
        <div class="bg-white border border-beige-200/60 rounded-3xl p-8 mb-8 shadow-sm">
            <h3 class="text-[11px] font-black text-mint-600 uppercase tracking-widest mb-6">Additional Options</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Penalty Type</label>
                    <select name="penalty_type" class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                        <option value="none">None</option>
                        <option value="flat">Flat Fee</option>
                        <option value="percentage">Percentage</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Penalty Value</label>
                    <input type="number" name="penalty_value" value="{{ old('penalty_value', 0) }}" min="0" step="0.01"
                           class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                </div>
                <div>
                    <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Internal Notes</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Optional notes for reference"
                           class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn-mint py-4 px-8 shadow-xl shadow-mint-950/20 active:scale-95 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Authorize & Create Invoice
            </button>
            <a href="{{ route('invoices.index') }}" class="px-6 py-4 text-xs font-black text-mint-800 uppercase tracking-widest hover:bg-beige-100 rounded-2xl transition-all">Cancel</a>
        </div>
    </form>
</div>


@push('scripts')
<script>
function invoiceForm(initialUnknownCustomer = false) {
    return {
        unknownCustomer: initialUnknownCustomer,
        items: [{ description: '', quantity: 1, price: 0 }],
        grandTotal: 0,
        addItem() {
            this.items.push({ description: '', quantity: 1, price: 0 });
        },
        removeItem(index) {
            this.items.splice(index, 1);
            this.calcTotal();
        },
        calcTotal() {
            this.grandTotal = this.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
        }
    }
}
</script>
@endpush
@endsection

