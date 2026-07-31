@extends('layouts.app')
@section('title', $invoice->invoice_number)
@section('page-title', 'Invoice Overview')

@section('content')
<div class="max-w-4xl">
    <!-- Header Controls -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 mb-8">
        <div class="flex items-center gap-4">
            <span class="inline-flex px-4 py-1.5 text-[10px] font-black uppercase rounded-xl tracking-widest
                @if($invoice->status === 'paid') bg-mint-100 text-mint-700 border border-mint-200/50
                @elseif($invoice->status === 'partial') bg-amber-100 text-amber-700 border border-amber-200/50
                @elseif($invoice->status === 'overdue') bg-red-100 text-red-700 border border-red-200/50
                @else bg-beige-100 text-beige-600 border border-beige-200/50 @endif">
                {{ $invoice->status }}
            </span>
            <h2 class="text-2xl font-black text-mint-950 tracking-tight">{{ $invoice->invoice_number }}</h2>
        </div>
        <div class="flex items-center gap-3">
            @if($invoice->status !== 'paid')
            <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="btn-mint py-2.5 px-5 shadow-lg shadow-mint-900/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                Post Payment
            </a>
            @endif
            <a href="{{ route('invoices.pdf', $invoice) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-beige-200 text-mint-800 text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-beige-50 transition-all shadow-sm">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                PDF
            </a>
            <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-beige-200 text-mint-800 text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-beige-50 transition-all shadow-sm">Edit</a>
            <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" onsubmit="return confirm('Permanently delete this invoice?')">
                @csrf @method('DELETE')
                <button class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-red-100 transition-all border border-red-100">Delete</button>
            </form>
        </div>
    </div>

    <!-- Quick Metrics -->
    <div class="bg-white border border-beige-200/60 rounded-3xl p-8 mb-6 shadow-sm">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            <div>
                <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2">Customer Profile</p>
                <a href="{{ route('customers.show', $invoice->customer) }}" class="text-sm font-black text-mint-600 hover:text-mint-700 underline underline-offset-4">{{ $invoice->customer->name }}</a>
            </div>
            <div>
                <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2">Issuance</p>
                <p class="text-sm font-bold text-mint-900">{{ $invoice->issue_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2">Maturity</p>
                <p class="text-sm font-bold {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-red-500' : 'text-mint-900' }}">{{ $invoice->due_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2">Remaining</p>
                <p class="text-sm font-black {{ $invoice->balance > 0 ? 'text-amber-600' : 'text-mint-600' }}">₱{{ number_format($invoice->balance, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Ledger Details -->
    <div class="bg-white border border-beige-200/60 rounded-3xl overflow-hidden mb-6 shadow-sm">
        <div class="px-8 py-6 border-b border-beige-100 bg-beige-50/30">
            <h3 class="text-xs font-black text-mint-900 uppercase tracking-widest">Itemized Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-white border-b border-beige-100 text-left">
                        <th class="px-8 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Description</th>
                        <th class="px-4 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest text-center">Unit/Qty</th>
                        <th class="px-4 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest text-right">Standard Price</th>
                        <th class="px-8 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest text-right">Line Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-beige-100 text-mint-900 font-semibold">
                    @foreach($invoice->items as $item)
                    <tr class="hover:bg-beige-50/50 transition-colors">
                        <td class="px-8 py-4">{{ $item->description }}</td>
                        <td class="px-4 py-4 text-center font-black">{{ $item->quantity }}</td>
                        <td class="px-4 py-4 text-right text-beige-400">₱{{ number_format($item->price, 2) }}</td>
                        <td class="px-8 py-4 text-right font-black">₱{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-beige-50/30 font-black">
                        <td colspan="3" class="px-8 py-5 text-right text-[11px] text-beige-500 uppercase tracking-widest">Invoice Subtotal</td>
                        <td class="px-8 py-5 text-right text-xl text-mint-950 tracking-tighter">₱{{ number_format($invoice->total_amount, 2) }}</td>
                    </tr>
                    @if($invoice->penalty_amount > 0)
                    <tr class="bg-red-50/30 text-red-600 font-bold">
                        <td colspan="3" class="px-8 py-3 text-right text-[11px] uppercase tracking-widest">Computed Penalty</td>
                        <td class="px-8 py-3 text-right font-black">₱{{ number_format($invoice->penalty_amount, 2) }}</td>
                    </tr>
                    <tr class="bg-mint-900 text-white font-black">
                        <td colspan="3" class="px-8 py-5 text-right text-[11px] uppercase tracking-widest text-white/50">Total Owed Amount</td>
                        <td class="px-8 py-5 text-right text-2xl tracking-tighter">₱{{ number_format($invoice->total_amount + $invoice->penalty_amount, 2) }}</td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Collection Records -->
    <div class="bg-white border border-beige-200/60 rounded-3xl overflow-hidden shadow-sm">
        <div class="px-8 py-6 border-b border-beige-100 bg-beige-50/30">
            <h3 class="text-xs font-black text-mint-900 uppercase tracking-widest">Collection History</h3>
        </div>
        @if($invoice->payments->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-white border-b border-beige-100 text-left">
                        <th class="px-8 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Collection Date</th>
                        <th class="px-4 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Payment Channel</th>
                        <th class="px-4 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Audit Ref</th>
                        <th class="px-8 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest text-right">Received</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-beige-100 font-bold text-mint-900">
                    @foreach($invoice->payments as $payment)
                    <tr class="hover:bg-beige-50/50 transition-colors">
                        <td class="px-8 py-4">{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex px-3 py-1 text-[10px] font-black uppercase rounded-xl tracking-widest
                                @if($payment->payment_method === 'cash') bg-mint-100 text-mint-700
                                @elseif($payment->payment_method === 'gcash') bg-blue-50 text-blue-600
                                @else bg-purple-50 text-purple-600 @endif">
                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-beige-400">{{ $payment->reference_number ?? '—' }}</td>
                        <td class="px-8 py-4 text-right font-black text-mint-600">₱{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-beige-50/30 font-black">
                        <td colspan="3" class="px-8 py-4 text-right text-[11px] text-beige-500 uppercase tracking-widest">Total Collected</td>
                        <td class="px-8 py-4 text-right text-lg text-mint-600 tracking-tight">₱{{ number_format($invoice->total_paid, 2) }}</td>
                    </tr>
                    <tr class="border-t border-beige-100 ring-1 ring-inset ring-beige-100">
                        <td colspan="3" class="px-8 py-3 text-right text-[11px] font-black text-beige-400 uppercase tracking-widest">Net Arrears</td>
                        <td class="px-8 py-3 text-right font-black {{ $invoice->balance > 0 ? 'text-amber-600' : 'text-mint-600' }}">₱{{ number_format($invoice->balance, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="px-8 py-12 text-center text-xs font-bold text-beige-400 uppercase tracking-widest">No payment records found for this invoice</div>
        @endif
    </div>
</div>
@endsection


