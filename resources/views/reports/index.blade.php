@extends('layouts.app')
@section('title', 'Analytical Reports')
@section('page-title', 'Financial Insights')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
    <!-- Income Report -->
    <a href="{{ route('reports.income') }}" class="group bg-white border border-beige-200/60 rounded-[2.5rem] p-8 hover:shadow-2xl hover:shadow-mint-900/10 transition-all duration-500 hover:-translate-y-1">
        <div class="w-16 h-16 rounded-3xl bg-mint-50 flex items-center justify-center mb-6 border border-mint-100 group-hover:bg-mint-900 transition-all duration-500">
            <svg class="w-8 h-8 text-mint-600 group-hover:text-beige-50 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <h3 class="text-xl font-black text-mint-950 mb-2 tracking-tight group-hover:text-mint-700 transition-colors">Revenue Stream</h3>
        <p class="text-xs font-bold text-beige-400 leading-relaxed uppercase tracking-widest">Post-collection analysis of all verified income streams.</p>
    </a>

    <!-- Expense Report -->
    <a href="{{ route('reports.expenses') }}" class="group bg-white border border-beige-200/60 rounded-[2.5rem] p-8 hover:shadow-2xl hover:shadow-red-900/5 transition-all duration-500 hover:-translate-y-1">
        <div class="w-16 h-16 rounded-3xl bg-red-50 flex items-center justify-center mb-6 border border-red-100 group-hover:bg-red-600 transition-all duration-500">
            <svg class="w-8 h-8 text-red-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
        </div>
        <h3 class="text-xl font-black text-mint-950 mb-2 tracking-tight group-hover:text-red-700 transition-colors">Expenditure Log</h3>
        <p class="text-xs font-bold text-beige-400 leading-relaxed uppercase tracking-widest">Categorized tracking of institutional outflows over time.</p>
    </a>

    <!-- Profit Report -->
    <a href="{{ route('reports.profit') }}" class="group bg-white border border-beige-200/60 rounded-[2.5rem] p-8 hover:shadow-2xl hover:shadow-mint-900/10 transition-all duration-500 hover:-translate-y-1">
        <div class="w-16 h-16 rounded-3xl bg-amber-50 flex items-center justify-center mb-6 border border-amber-100 group-hover:bg-amber-500 transition-all duration-500">
            <svg class="w-8 h-8 text-amber-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <h3 class="text-xl font-black text-mint-950 mb-2 tracking-tight group-hover:text-amber-700 transition-colors">Net Profitability</h3>
        <p class="text-xs font-bold text-beige-400 leading-relaxed uppercase tracking-widest">Bottom-line calculation of net operational efficiency.</p>
    </a>

    <!-- Outstanding Balances -->
    <a href="{{ route('reports.outstanding') }}" class="group bg-white border border-beige-200/60 rounded-[2.5rem] p-8 hover:shadow-2xl hover:shadow-mint-900/10 transition-all duration-500 hover:-translate-y-1">
        <div class="w-16 h-16 rounded-3xl bg-beige-100 flex items-center justify-center mb-6 border border-beige-200 group-hover:bg-mint-900 transition-all duration-500">
            <svg class="w-8 h-8 text-mint-800 group-hover:text-beige-50 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-xl font-black text-mint-950 mb-2 tracking-tight group-hover:text-mint-700 transition-colors">Accrued Arrears</h3>
        <p class="text-xs font-bold text-beige-400 leading-relaxed uppercase tracking-widest">Real-time mapping of uncollected receivables and overdue accounts.</p>
    </a>

    <!-- Customer Marketability -->
    <a href="{{ route('reports.customers') }}" class="group bg-white border border-beige-200/60 rounded-[2.5rem] p-8 hover:shadow-2xl hover:shadow-mint-900/10 transition-all duration-500 hover:-translate-y-1">
        <div class="w-16 h-16 rounded-3xl bg-mint-50 flex items-center justify-center mb-6 border border-mint-100 group-hover:bg-mint-700 transition-all duration-500">
            <svg class="w-8 h-8 text-mint-700 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <h3 class="text-xl font-black text-mint-950 mb-2 tracking-tight group-hover:text-mint-700 transition-colors">Customer Marketability</h3>
        <p class="text-xs font-bold text-beige-400 leading-relaxed uppercase tracking-widest">Customer counts by weekday, month, and year for marketing timing.</p>
    </a>

    <!-- Audit Trail -->
    <a href="{{ route('reports.audit') }}" class="group bg-white border border-beige-200/60 rounded-[2.5rem] p-8 hover:shadow-2xl hover:shadow-mint-900/10 transition-all duration-500 hover:-translate-y-1">
        <div class="w-16 h-16 rounded-3xl bg-blue-50 flex items-center justify-center mb-6 border border-blue-100 group-hover:bg-blue-600 transition-all duration-500">
            <svg class="w-8 h-8 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <h3 class="text-xl font-black text-mint-950 mb-2 tracking-tight group-hover:text-blue-700 transition-colors">Operations Audit</h3>
        <p class="text-xs font-bold text-beige-400 leading-relaxed uppercase tracking-widest">Systemic record of all modifications to critical financial entities.</p>
    </a>
</div>
@endsection


