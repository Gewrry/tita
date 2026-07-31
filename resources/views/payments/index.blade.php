@extends('layouts.app')
@section('title', 'Payments')
@section('page-title', 'Payment History')

@section('content')
<div x-data="{ createModalOpen: false }">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <form method="GET" class="flex items-center gap-3 flex-wrap flex-1 w-full sm:w-auto">
            <div class="relative group w-full sm:w-auto">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-beige-400 group-focus-within:text-mint-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search payments..."
                       class="w-full sm:w-56 pl-11 pr-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm outline-none">
            </div>
            <select name="method" onchange="this.form.submit()" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-mint-800 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm outline-none">
                <option value="">All Methods</option>
                <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="bank_transfer" {{ request('method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="gcash" {{ request('method') === 'gcash' ? 'selected' : '' }}>GCash</option>
            </select>
            <div class="flex items-center gap-2 bg-white border border-beige-200 rounded-2xl px-3 py-1 shadow-sm w-full sm:w-auto">
                <input type="date" name="from" value="{{ request('from') }}" class="bg-transparent border-none text-[10px] font-black text-mint-800 focus:ring-0 p-1.5 uppercase">
                <span class="text-beige-300 font-bold">→</span>
                <input type="date" name="to" value="{{ request('to') }}" class="bg-transparent border-none text-[10px] font-black text-mint-800 focus:ring-0 p-1.5 uppercase">
            </div>
            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-beige-100 text-mint-800 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-beige-200 transition-all shadow-sm border border-beige-200 active:scale-95">Filter</button>
        </form>
        <button @click="createModalOpen = true" class="btn-mint shadow-lg shadow-mint-900/10 whitespace-nowrap w-full sm:w-auto justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Record Payment
        </button>
    </div>

    <div class="bg-white border border-beige-200/60 rounded-[2.5rem] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[800px]">
                <thead>
                    <tr class="bg-beige-50/50 border-b border-beige-100">
                        <th class="text-left px-8 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest">Payment Date</th>
                        <th class="text-left px-8 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest">Customer</th>
                        <th class="text-left px-8 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest">Invoice Ref</th>
                        <th class="text-left px-8 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest">Method</th>
                        <th class="text-left px-8 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest">Reference #</th>
                        <th class="text-right px-8 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest">Amount Paid</th>
                        <th class="text-right px-8 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-beige-100">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-beige-50/50 transition-colors group" x-data="{ viewModalOpen: false }">
                        <td class="px-8 py-5 font-bold text-beige-400 capitalize">{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td class="px-8 py-5 font-black text-mint-900">
                            <a href="{{ route('customers.show', $payment->customer) }}" class="hover:text-mint-600 transition-colors">{{ $payment->customer->name }}</a>
                        </td>
                        <td class="px-8 py-5">
                            <span class="font-bold text-mint-500 bg-mint-50 px-2 py-1 rounded-lg border border-mint-100">
                                {{ $payment->invoice->invoice_number }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="inline-flex px-3 py-1 text-[10px] font-black uppercase rounded-xl tracking-widest
                                @if($payment->payment_method === 'cash') bg-mint-100 text-mint-700
                                @elseif($payment->payment_method === 'gcash') bg-blue-100 text-blue-700
                                @else bg-purple-100 text-purple-700 @endif">
                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            </span>
                        </td>
                        <td class="px-8 py-5 font-bold text-beige-300 font-mono text-xs">{{ $payment->reference_number ?? '—' }}</td>
                        <td class="px-8 py-5 text-right font-black text-mint-600 tracking-tight">₱{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-8 py-5 text-right">
                            <button @click="viewModalOpen = true" class="p-2.5 rounded-xl text-beige-300 hover:text-mint-600 hover:bg-white hover:border-beige-100 border border-transparent transition-all shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            
                            @include('payments.partials.row-modals')
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-20 text-center">
                            <div class="w-20 h-20 bg-beige-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-beige-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <p class="text-sm font-black text-mint-900 uppercase tracking-widest">No payment records</p>
                            <p class="text-xs font-bold text-beige-400 mt-1">Payments recorded in the system will appear here</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-8 py-6 border-t border-beige-100 bg-beige-50/30">{{ $payments->links() }}</div>
        @endif
    </div>

    @include('payments.partials.create-modal')
</div>
@endsection

@push('scripts')
<script>
function paymentForm() {
    return {
        selectedInvoice: '',
        balance: 0,
        onInvoiceChange() {
            const select = document.querySelector('select[name="invoice_id"]');
            const option = select.options[select.selectedIndex];
            this.balance = parseFloat(option.dataset.balance || 0);
        }
    }
}
</script>
@endpush


