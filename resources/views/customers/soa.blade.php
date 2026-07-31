@extends('layouts.app')
@section('title', 'Statement of Account - ' . $customer->name)
@section('page-title', 'Statement of Account')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- SOA Record -->
    <div class="bg-white border border-beige-200/60 rounded-3xl p-10 mb-8 shadow-sm" id="soa-content">
        <div class="flex items-start justify-between mb-10 pb-8 border-b border-beige-100">
            <div>
                <h2 class="text-3xl font-black text-mint-950 tracking-tighter">Statement of <br>Account</h2>
                <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mt-2">Inventory Period: {{ now()->format('F d, Y') }}</p>
            </div>
            <div class="text-right">
                <div class="inline-flex items-center gap-2 mb-1">
                    <span class="text-xl font-black text-mint-900 tracking-tight">TITA</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-mint-500"></span>
                </div>
                <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest leading-tight">Institutional<br>Finance System</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
            <div class="space-y-4">
                <p class="text-[10px] font-black text-mint-600 uppercase tracking-widest opacity-60">Client Information</p>
                <div>
                    <p class="text-xl font-black text-mint-950 tracking-tight">{{ $customer->name }}</p>
                    <div class="mt-2 space-y-1">
                        @if($customer->email)<p class="text-xs font-bold text-mint-600">{{ $customer->email }}</p>@endif
                        @if($customer->phone)<p class="text-xs font-bold text-mint-600">{{ $customer->phone }}</p>@endif
                        @if($customer->address)<p class="text-xs font-medium text-beige-500 mt-2 leading-relaxed max-w-xs">{{ $customer->address }}</p>@endif
                    </div>
                </div>
            </div>
            <div class="bg-beige-50/50 border border-beige-100 rounded-3xl p-6 flex flex-col justify-center items-end">
                <p class="text-[10px] font-black text-mint-600 uppercase tracking-widest mb-2">Total Arrears</p>
                <p class="text-4xl font-black {{ $customer->balance > 0 ? 'text-amber-600' : 'text-mint-600' }} tracking-tighter">₱{{ number_format($customer->balance, 2) }}</p>
                <p class="text-[9px] font-black text-beige-400 uppercase tracking-widest mt-2 italic">*As of current system time</p>
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-3 gap-6 mb-10">
            <div class="p-5 rounded-2xl bg-white border border-beige-100 shadow-sm text-center">
                <p class="text-[9px] font-black text-beige-400 uppercase tracking-widest mb-1.5">Gross Billing</p>
                <p class="text-lg font-black text-mint-900 tracking-tight">₱{{ number_format($customer->total_billed, 2) }}</p>
            </div>
            <div class="p-5 rounded-2xl bg-white border border-beige-100 shadow-sm text-center">
                <p class="text-[9px] font-black text-mint-600 uppercase tracking-widest mb-1.5">Net Collections</p>
                <p class="text-lg font-black text-mint-600 tracking-tight">₱{{ number_format($customer->total_paid, 2) }}</p>
            </div>
            <div class="p-5 rounded-2xl bg-white border border-beige-100 shadow-sm text-center">
                <p class="text-[9px] font-black text-amber-600 uppercase tracking-widest mb-1.5">Current Balance</p>
                <p class="text-lg font-black {{ $customer->balance > 0 ? 'text-amber-600' : 'text-mint-600' }} tracking-tight">₱{{ number_format($customer->balance, 2) }}</p>
            </div>
        </div>

        <!-- Ledger Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-mint-900/5 text-left">
                        <th class="py-4 text-[11px] font-black text-mint-900 uppercase tracking-widest">Reference</th>
                        <th class="py-4 text-[11px] font-black text-mint-900 uppercase tracking-widest">Issuance</th>
                        <th class="py-4 text-[11px] font-black text-mint-900 uppercase tracking-widest">Maturity</th>
                        <th class="py-4 text-right text-[11px] font-black text-mint-900 uppercase tracking-widest">Amount</th>
                        <th class="py-4 text-right text-[11px] font-black text-mint-900 uppercase tracking-widest">Paid</th>
                        <th class="py-4 text-right text-[11px] font-black text-mint-900 uppercase tracking-widest">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-beige-100 font-bold text-mint-900">
                    @foreach($invoices as $invoice)
                    <tr>
                        <td class="py-4 font-black">{{ $invoice->invoice_number }}</td>
                        <td class="py-4 text-beige-400">{{ $invoice->issue_date->format('M d, Y') }}</td>
                        <td class="py-4 text-beige-400">{{ $invoice->due_date->format('M d, Y') }}</td>
                        <td class="py-4 text-right">₱{{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="py-4 text-right text-mint-600">₱{{ number_format($invoice->total_paid, 2) }}</td>
                        <td class="py-4 text-right font-black {{ $invoice->balance > 0 ? 'text-amber-600' : 'text-mint-600' }}">₱{{ number_format($invoice->balance, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-20 pt-10 border-t border-beige-100 flex justify-between items-end">
            <div class="space-y-1">
                <p class="text-[9px] font-black text-beige-400 uppercase tracking-widest">Finance Officer Signature</p>
                <div class="w-48 h-px bg-beige-200 mt-6"></div>
                <p class="text-xs font-bold text-mint-950">{{ Auth::user()->name }}</p>
            </div>
            <p class="text-[9px] font-black text-beige-300 uppercase tracking-widest">© {{ date('Y') }} TITA Finance Operations</p>
        </div>
    </div>

    <div class="flex items-center gap-4 print:hidden">
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-6 py-3.5 bg-white border border-beige-200 text-mint-800 text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-beige-50 transition-all shadow-sm active:scale-95">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Statement
        </button>
        <a href="{{ route('customers.soa-pdf', $customer) }}" class="btn-mint py-3.5 px-8 shadow-xl shadow-mint-900/10 active:scale-95">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
            Export PDF
        </a>
        <a href="{{ route('customers.show', $customer) }}" class="px-6 py-3.5 text-xs font-black text-mint-800 uppercase tracking-widest hover:bg-beige-100 rounded-2xl transition-all">← Back to Client</a>
    </div>
</div>

@push('styles')
<style>
@media print {
    body { background: white !important; }
    body * { visibility: hidden; }
    #soa-content, #soa-content * { visibility: visible; }
    #soa-content { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; padding: 0 !important; }
    .print\:hidden { display: none !important; }
}
</style>
@endpush
@endsection


