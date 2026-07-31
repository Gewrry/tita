@extends('layouts.app')
@section('title', 'Expense Tracking')
@section('page-title', 'Expense Ledger')

@section('content')
<div x-data="{ createModalOpen: false }">
    <!-- Summary Stats -->
    <div class="bg-white border border-beige-200/60 rounded-[2.5rem] p-8 mb-8 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
        <div>
            <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2">Monthly Expenditure</p>
            <p class="text-4xl font-black text-red-600 tracking-tighter">₱{{ number_format($totalThisMonth, 2) }}</p>
            <p class="text-[10px] font-bold text-beige-300 mt-2 italic">*Reflects all approved cash outflows</p>
        </div>
        <div class="w-16 h-16 rounded-3xl bg-red-50 flex items-center justify-center ring-8 ring-red-50/50">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
        </div>
    </div>

    <div class="flex flex-col xl:flex-row items-start xl:items-center justify-between gap-6 mb-8">
        <form method="GET" class="flex flex-col sm:flex-row flex-wrap items-center gap-4 w-full xl:w-auto">
            <div class="relative group w-full sm:w-auto">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-beige-400 group-focus-within:text-mint-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search records..."
                       class="w-full sm:w-64 pl-11 pr-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
            </div>
            
            <select name="category" onchange="this.form.submit()" class="w-full sm:w-auto px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-mint-800 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm outline-none">
                <option value="">All Categories</option>
                @foreach($categories as $key => $label)
                <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-2 bg-white border border-beige-200 rounded-2xl px-4 py-2 shadow-sm w-full sm:w-auto">
                <input type="date" name="from" value="{{ request('from') }}" class="bg-transparent border-none text-[10px] font-black text-mint-800 focus:ring-0 p-0 uppercase w-full">
                <span class="text-beige-300 font-black">→</span>
                <input type="date" name="to" value="{{ request('to') }}" class="bg-transparent border-none text-[10px] font-black text-mint-800 focus:ring-0 p-0 uppercase w-full">
            </div>
            
            <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-beige-100 text-mint-800 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-beige-200 transition-all border border-beige-200 active:scale-95">Filter Results</button>
        </form>

        <button @click="createModalOpen = true" class="btn-mint shadow-xl shadow-mint-900/10 whitespace-nowrap active:scale-95 w-full xl:w-auto justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Log New Expense
        </button>
    </div>

    <div class="bg-white border border-beige-200/60 rounded-[2.5rem] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[800px]">
                <thead>
                    <tr class="bg-beige-50/30 border-b border-beige-100">
                        <th class="text-left px-8 py-6 text-[10px] font-black text-beige-500 uppercase tracking-widest">Entry Date</th>
                        <th class="text-left px-8 py-6 text-[10px] font-black text-beige-500 uppercase tracking-widest">Transaction Description</th>
                        <th class="text-left px-8 py-6 text-[10px] font-black text-beige-500 uppercase tracking-widest">Category</th>
                        <th class="text-right px-8 py-6 text-[10px] font-black text-beige-500 uppercase tracking-widest">Amount</th>
                        <th class="text-right px-8 py-6 text-[10px] font-black text-beige-500 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-beige-100">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-beige-50/50 transition-colors group" x-data="{ editModalOpen: false, deleteModalOpen: false }">
                        <td class="px-8 py-5 font-bold text-beige-400 capitalize">{{ $expense->expense_date->format('M d, Y') }}</td>
                        <td class="px-8 py-5 font-black text-mint-950">{{ $expense->description }}</td>
                        <td class="px-8 py-5">
                            <span class="inline-flex px-3 py-1 text-[10px] font-black uppercase rounded-xl tracking-widest bg-beige-100 text-mint-700 border border-beige-200/50">
                                {{ $expense->category }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right font-black text-red-600 tracking-tight">₱{{ number_format($expense->amount, 2) }}</td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="editModalOpen = true" class="p-2.5 rounded-xl text-beige-300 hover:text-mint-600 hover:bg-white hover:border-beige-100 border border-transparent transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="deleteModalOpen = true" class="p-2.5 rounded-xl text-beige-300 hover:text-red-600 hover:bg-white hover:border-beige-100 border border-transparent transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>

                            @include('expenses.partials.row-modals')
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="w-20 h-20 bg-beige-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-beige-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <p class="text-sm font-black text-mint-950 uppercase tracking-widest">No Outflows Logged</p>
                            <p class="text-xs font-bold text-beige-400 mt-2">All financial expenditures will appear in this ledger</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
        <div class="px-8 py-6 border-t border-beige-100 bg-beige-50/10">{{ $expenses->links() }}</div>
        @endif
    </div>

    @include('expenses.partials.create-modal')
</div>
@endsection



