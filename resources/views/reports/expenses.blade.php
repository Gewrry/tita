@extends('layouts.app')
@section('title', 'Expenditure Analysis')
@section('page-title', 'Financial Outflow Analysis')

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
        <button type="submit" class="bg-red-600 text-white py-2.5 px-8 text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-red-900/10 active:scale-95 transition-all">
            Recalculate Outflows
        </button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <div class="bg-red-600 rounded-[2rem] p-8 text-white shadow-xl shadow-red-900/20 relative overflow-hidden group">
        <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all duration-500"></div>
        <p class="text-[10px] font-black text-red-100 uppercase tracking-[0.2em] mb-3 relative z-10">Total Expenditure</p>
        <p class="text-3xl font-black tracking-tighter relative z-10">₱{{ number_format($totalExpenses, 2) }}</p>
    </div>
    <div class="lg:col-span-2 bg-white border border-beige-200/60 rounded-[2rem] p-8 shadow-sm">
        <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-6">Allocation by Category</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($byCategory as $category => $amount)
            <div class="text-center p-4 rounded-2xl bg-beige-50/50 border border-beige-100 hover:bg-beige-50 transition-colors">
                <p class="text-[10px] font-black text-beige-400 uppercase tracking-[0.1em] mb-1 truncate">{{ $category }}</p>
                <p class="text-sm font-black text-mint-950">₱{{ number_format($amount, 2) }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="bg-white border border-beige-200/60 rounded-3xl overflow-hidden shadow-sm">
    <div class="px-8 py-6 border-b border-beige-100 bg-beige-50/30">
        <h3 class="text-[10px] font-black text-mint-900 uppercase tracking-widest">Expense Ledger Details</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-white border-b border-beige-100 text-left">
                    <th class="px-8 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest">Disbursement Date</th>
                    <th class="px-8 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest">Entry Description</th>
                    <th class="px-8 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest text-center">Category Tag</th>
                    <th class="px-8 py-5 text-[10px] font-black text-beige-500 uppercase tracking-widest text-right">Debit Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-beige-100">
                @forelse($expenses as $expense)
                <tr class="hover:bg-beige-50/50 transition-colors">
                    <td class="px-8 py-5 font-bold text-beige-400">{{ $expense->expense_date->format('M d, Y') }}</td>
                    <td class="px-8 py-5 font-black text-mint-950">{{ $expense->description }}</td>
                    <td class="px-8 py-5 text-center">
                        <span class="inline-flex px-3 py-1 text-[10px] font-black uppercase rounded-xl tracking-widest bg-beige-100 text-mint-700 border border-beige-200/50">
                            {{ $expense->category }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right font-black text-red-600 tracking-tight">₱{{ number_format($expense->amount, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-8 py-16 text-center text-xs font-bold text-beige-400 uppercase tracking-widest">No expenditure records retrieved for this temporal window</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


