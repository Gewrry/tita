@extends('layouts.app')
@section('title', 'Invoices')
@section('page-title', 'Invoice Management')

@section('content')
<div x-data="{ createModalOpen: false }">
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
    <form method="GET" class="flex items-center gap-3 flex-wrap">
        <div class="relative group w-full sm:w-auto">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-beige-400 group-focus-within:text-mint-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search invoices..."
                   class="w-full sm:w-64 pl-11 pr-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
        </div>
        <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-mint-800 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
            <option value="">All Status</option>
            <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
            <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
        </select>
    </form>
    <button type="button" @click="createModalOpen = true" class="btn-mint shadow-lg shadow-mint-900/10 whitespace-nowrap w-full sm:w-auto justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        New Invoice
    </button>
</div>

<div class="bg-white border border-beige-200/60 rounded-3xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[800px]">
            <thead>
                <tr class="bg-beige-50/50 border-b border-beige-100">
                    <th class="text-left px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Invoice Ref</th>
                    <th class="text-left px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Customer</th>
                    <th class="text-left px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Issued Date</th>
                    <th class="text-left px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Due Date</th>
                    <th class="text-right px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Total Amount</th>
                    <th class="text-center px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Status</th>
                    <th class="text-right px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Manage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-beige-100">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-beige-50/50 transition-colors group" x-data="{ viewModalOpen: false, paymentModalOpen: false }">
                    <td class="px-6 py-5">
                        <button @click="viewModalOpen = true" class="font-black text-mint-600 hover:text-mint-700 transition-colors tracking-tight">{{ $invoice->invoice_number }}</button>
                    </td>
                    <td class="px-6 py-5 font-bold text-mint-900">{{ $invoice->customer->name ?? 'N/A' }}</td>
                    <td class="px-6 py-5 font-bold text-beige-400">{{ $invoice->issue_date->format('M d, Y') }}</td>
                    <td class="px-6 py-5">
                        <span class="font-bold {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-red-500' : 'text-beige-400' }}">
                            {{ $invoice->due_date->format('M d, Y') }}
                        </span>
                    </td>
                    <td class="px-6 py-5 text-right font-black text-mint-900 tracking-tight">₱{{ number_format($invoice->total_amount, 2) }}</td>
                    <td class="px-6 py-5 text-center">
                        <span class="inline-flex px-3 py-1 text-[10px] font-black uppercase rounded-xl tracking-widest
                            @if($invoice->status === 'paid') bg-mint-100 text-mint-700
                            @elseif($invoice->status === 'partial') bg-amber-100 text-amber-700
                            @elseif($invoice->status === 'overdue') bg-red-100 text-red-700
                            @else bg-beige-100 text-beige-600 @endif">
                            {{ $invoice->status }}
                        </span>
                    </td>
                    <td class="px-6 py-5 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <button type="button" @click.prevent="viewModalOpen = true" class="p-2.5 rounded-xl text-beige-400 hover:text-mint-600 hover:bg-white hover:border-beige-200 border border-transparent transition-all shadow-sm" title="View Details">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            @if($invoice->status !== 'paid')
                            <button type="button" @click.prevent="paymentModalOpen = true" class="p-2.5 rounded-xl text-beige-400 hover:text-cyan-600 hover:bg-white hover:border-beige-200 border border-transparent transition-all shadow-sm" title="Record Payment">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                            @endif
                        </div>
                        
                        @include('invoices.partials.row-modals')
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-20 text-center">
                        <div class="w-20 h-20 bg-beige-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-beige-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-sm font-black text-mint-900 uppercase tracking-widest">No invoices found</p>
                        <p class="text-xs font-bold text-beige-400 mt-1">Ready to bill your first client?</p>
                        <button type="button" @click="createModalOpen = true" class="text-mint-600 hover:text-mint-700 font-black text-xs mt-4 inline-block uppercase tracking-widest border-b-2 border-mint-500/30">Create Invoice →</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
    <div class="px-6 py-5 border-t border-beige-100 bg-beige-50/30">{{ $invoices->links() }}</div>
    @endif
    
    @include('invoices.partials.create-modal')
</div>
@endsection

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

