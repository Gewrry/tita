@extends('layouts.app')
@section('title', $customer->name)
@section('page-title', 'Client Profile')

@section('content')
<!-- Customer Header -->
<div class="bg-white border border-beige-200/60 rounded-3xl p-8 mb-8 shadow-sm">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="w-20 h-20 rounded-3xl bg-mint-50 border border-mint-100 flex items-center justify-center text-2xl font-black text-mint-600 shadow-sm shadow-mint-900/5">
                {{ strtoupper(substr($customer->name, 0, 2)) }}
            </div>
            <div>
                <h2 class="text-2xl font-black text-mint-950 tracking-tight">{{ $customer->name }}</h2>
                <div class="flex items-center gap-4 mt-1.5 text-xs font-bold">
                    @if($customer->email)
                    <span class="flex items-center gap-1.5 text-mint-600 bg-mint-50 px-2 py-1 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $customer->email }}
                    </span>
                    @endif
                    @if($customer->phone)
                    <span class="flex items-center gap-1.5 text-beige-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $customer->phone }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('customers.soa-pdf', $customer) }}" class="inline-flex items-center gap-2 px-5 py-3 bg-white border border-beige-200 text-mint-800 text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-beige-50 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                Statement PDF
            </a>
            <a href="{{ route('customers.edit', $customer) }}" class="btn-mint py-3 px-6 shadow-lg shadow-mint-900/10">Configure Profile</a>
        </div>
    </div>
</div>

<!-- Financial Summary -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white border border-beige-200/60 rounded-3xl p-6 shadow-sm">
        <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2">Cumulative Billings</p>
        <p class="text-2xl font-black text-mint-900 tracking-tight">₱{{ number_format($customer->total_billed, 2) }}</p>
    </div>
    <div class="bg-white border border-beige-200/60 rounded-3xl p-6 shadow-sm">
        <p class="text-[10px] font-black text-mint-600 uppercase tracking-widest mb-2">Total Collections</p>
        <p class="text-2xl font-black text-mint-600 tracking-tight">₱{{ number_format($customer->total_paid, 2) }}</p>
    </div>
    <div class="bg-white border border-beige-200/60 rounded-3xl p-6 shadow-sm ring-2 ring-amber-500/10">
        <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-2">Outstanding Balance</p>
        <p class="text-2xl font-black {{ $customer->balance > 0 ? 'text-amber-600' : 'text-mint-600' }} tracking-tight">₱{{ number_format($customer->balance, 2) }}</p>
    </div>
</div>

<!-- Invoices Ledger -->
<div class="bg-white border border-beige-200/60 rounded-3xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between p-6 border-b border-beige-100 bg-beige-50/30">
        <h3 class="text-xs font-black text-mint-900 uppercase tracking-widest">Invoicing History</h3>
        <a href="{{ route('invoices.create', ['customer_id' => $customer->id]) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-mint-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-mint-600 transition-all shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Add Invoice
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-white border-b border-beige-100">
                    <th class="text-left px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Invoice Ref</th>
                    <th class="text-left px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Issuance Date</th>
                    <th class="text-left px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Maturity Date</th>
                    <th class="text-right px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Amount</th>
                    <th class="text-right px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Collected</th>
                    <th class="text-right px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Arrears</th>
                    <th class="text-center px-6 py-4 text-[11px] font-bold text-beige-500 uppercase tracking-widest">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-beige-100">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-beige-50/50 transition-colors group">
                    <td class="px-6 py-4 font-black text-mint-600"><a href="{{ route('invoices.show', $invoice) }}" class="hover:underline transition-all">{{ $invoice->invoice_number }}</a></td>
                    <td class="px-6 py-4 font-bold text-beige-400">{{ $invoice->issue_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 font-bold text-beige-400">{{ $invoice->due_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-right font-bold text-mint-900 uppercase">₱{{ number_format($invoice->total_amount, 2) }}</td>
                    <td class="px-6 py-4 text-right font-bold text-mint-600">₱{{ number_format($invoice->total_paid, 2) }}</td>
                    <td class="px-6 py-4 text-right font-black {{ $invoice->balance > 0 ? 'text-amber-600' : 'text-mint-600' }}">₱{{ number_format($invoice->balance, 2) }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex px-3 py-1 text-[10px] font-black uppercase rounded-xl tracking-widest
                            @if($invoice->status === 'paid') bg-mint-100 text-mint-700
                            @elseif($invoice->status === 'partial') bg-amber-100 text-amber-700
                            @elseif($invoice->status === 'overdue') bg-red-100 text-red-700
                            @else bg-beige-100 text-beige-600 @endif">
                            {{ $invoice->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-20 text-center text-xs font-bold text-beige-400 uppercase tracking-widest">No transaction history recorded</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
    <div class="px-6 py-5 border-t border-beige-100 bg-beige-50/30">{{ $invoices->links() }}</div>
    @endif
</div>
@endsection

