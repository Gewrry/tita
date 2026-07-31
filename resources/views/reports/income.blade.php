@extends('layouts.app')
@section('title', 'Income Analysis')
@section('page-title', 'Revenue Stream Analysis')

@section('content')
<!-- Filter Control -->
<div class="bg-white border border-beige-200/60 rounded-3xl p-8 mb-8 shadow-sm">
    <form method="GET" class="flex items-end gap-6 flex-wrap">
        <div class="space-y-2">
            <label class="block text-[10px] font-black text-mint-600 uppercase tracking-widest">Period Start</label>
            <input type="date" name="from" value="{{ $from }}" class="px-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
        </div>
        <div class="space-y-2">
            <label class="block text-[10px] font-black text-mint-600 uppercase tracking-widest">Period End</label>
            <input type="date" name="to" value="{{ $to }}" class="px-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
        </div>
        <button type="submit" class="btn-mint py-2.5 px-8 shadow-lg shadow-mint-900/10 active:scale-95 transition-all">
            Refresh Analytics
        </button>
    </form>
</div>

<!-- Aggregated Metrics -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-mint-900 rounded-[2rem] p-8 text-white shadow-xl shadow-mint-900/20 relative overflow-hidden group">
        <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all duration-500"></div>
        <p class="text-[10px] font-black text-mint-300 uppercase tracking-[0.2em] mb-3 relative z-10">Total Gross Income</p>
        <p class="text-3xl font-black tracking-tighter relative z-10">₱{{ number_format($totalIncome, 2) }}</p>
    </div>
    @foreach($byMethod as $method => $amount)
    <div class="bg-white border border-beige-200/60 rounded-[2rem] p-8 shadow-sm hover:shadow-lg transition-all duration-500">
        <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-3">{{ ucfirst(str_replace('_', ' ', $method)) }}</p>
        <p class="text-2xl font-black text-mint-950 tracking-tight">₱{{ number_format($amount, 2) }}</p>
    </div>
    @endforeach
</div>

<!-- Temporal Breakdown -->
@if($dailyIncome->count())
<div class="bg-white border border-beige-200/60 rounded-3xl p-8 mb-8 shadow-sm">
    <h3 class="text-xs font-black text-mint-900 uppercase tracking-widest mb-6">Daily Revenue Progression</h3>
    <div class="grid gap-3">
        @foreach($dailyIncome as $date => $amount)
        <div class="flex items-center justify-between p-4 bg-beige-50/50 rounded-2xl hover:bg-beige-50 transition-colors">
            <span class="text-xs font-bold text-mint-800">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }} <span class="text-beige-400 ml-2">({{ \Carbon\Carbon::parse($date)->format('l') }})</span></span>
            <span class="text-sm font-black text-mint-600">₱{{ number_format($amount, 2) }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Detailed Transaction Ledger -->
<div class="bg-white border border-beige-200/60 rounded-3xl overflow-hidden shadow-sm">
    <div class="px-8 py-6 border-b border-beige-100 bg-beige-50/30">
        <h3 class="text-[10px] font-black text-mint-900 uppercase tracking-widest">Transaction Level Audit</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-white border-b border-beige-100 text-left">
                    <th class="px-8 py-4 text-[10px] font-black text-beige-400 uppercase tracking-widest">Clearing Date</th>
                    <th class="px-4 py-4 text-[10px] font-black text-beige-400 uppercase tracking-widest">Client Name</th>
                    <th class="px-4 py-4 text-[10px] font-black text-beige-400 uppercase tracking-widest">Invoice Ref</th>
                    <th class="px-4 py-4 text-[10px] font-black text-beige-400 uppercase tracking-widest">Method</th>
                    <th class="px-8 py-4 text-[10px] font-black text-beige-400 uppercase tracking-widest text-right">Received</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-beige-100">
                @forelse($payments as $payment)
                <tr class="hover:bg-beige-50/50 transition-colors">
                    <td class="px-8 py-5 font-bold text-beige-400">{{ $payment->payment_date->format('M d, Y') }}</td>
                    <td class="px-4 py-5 font-black text-mint-950">{{ $payment->customer->name }}</td>
                    <td class="px-4 py-5 font-bold text-mint-500 hover:text-mint-600 underline underline-offset-4 decoration-mint-500/30">
                        <a href="{{ route('invoices.show', $payment->invoice) }}">{{ $payment->invoice->invoice_number }}</a>
                    </td>
                    <td class="px-4 py-5 font-bold">
                        <span class="inline-flex px-3 py-1 text-[9px] font-black uppercase rounded-xl tracking-widest
                            @if($payment->payment_method === 'cash') bg-mint-100 text-mint-700
                            @elseif($payment->payment_method === 'gcash') bg-blue-100 text-blue-700
                            @else bg-purple-100 text-purple-700 @endif">
                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right font-black text-mint-600 tracking-tight">₱{{ number_format($payment->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-16 text-center text-xs font-bold text-beige-400 uppercase tracking-widest">No financial data retrieved for this temporal window</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


