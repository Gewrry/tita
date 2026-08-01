@extends('layouts.app')
@section('title', 'Savings Management')
@section('page-title', 'Business Savings & Goals')

@section('content')
<div x-data="savingsDashboard()" x-init="init()" class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════
         TOAST NOTIFICATION SYSTEM
    ══════════════════════════════════════════════════════════════ --}}
    <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none" style="max-width:380px">
        <template x-for="t in toasts" :key="t.id">
            <div class="pointer-events-auto flex items-start gap-3 rounded-2xl px-4 py-3.5 shadow-2xl border text-sm font-semibold backdrop-blur-xl"
                 x-show="t.visible"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-12"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-12"
                 :class="{
                    'bg-white border-emerald-200 text-emerald-800': t.type==='success',
                    'bg-white border-red-200 text-red-700':         t.type==='error',
                    'bg-white border-amber-200 text-amber-700':     t.type==='warning',
                    'bg-white border-blue-200 text-blue-700':       t.type==='info'
                 }">
                <span class="text-lg leading-none mt-0.5"
                      x-text="t.type==='success'?'✅':t.type==='error'?'❌':t.type==='warning'?'⚠️':'ℹ️'"></span>
                <span class="flex-1" x-text="t.message"></span>
                <button @click="dismissToast(t.id)" class="text-current opacity-50 hover:opacity-100 transition-opacity ml-1 mt-0.5">✕</button>
            </div>
        </template>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         KPI ANALYTICS CARDS
    ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Balance --}}
        <div class="bg-gradient-to-br from-mint-500 to-mint-600 rounded-2xl p-5 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-black text-white/60 uppercase tracking-widest">Available</span>
            </div>
            <div class="text-2xl font-black text-white leading-none" x-text="'₱' + fmt(analytics.balance ?? 0)"></div>
            <div class="text-xs font-bold text-white/60 mt-1.5 uppercase tracking-widest">Total Savings Balance</div>
        </div>

        {{-- Month Savings --}}
        <div class="bg-white border border-beige-200/70 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <span class="text-[10px] font-black text-beige-400 uppercase tracking-widest">Month</span>
            </div>
            <div class="text-2xl font-black leading-none" :class="(analytics.month_savings ?? 0) >= 0 ? 'text-mint-600' : 'text-red-500'" x-text="'₱' + fmt(analytics.month_savings ?? 0)"></div>
            <div class="text-xs font-bold text-beige-400 mt-1.5 uppercase tracking-widest">Net Savings This Month</div>
        </div>

        {{-- Suggested Savings --}}
        <div class="bg-white border border-beige-200/70 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <span class="text-[10px] font-black text-beige-400 uppercase tracking-widest">Tip (10%)</span>
            </div>
            <div class="text-xl font-black text-amber-600 leading-none truncate" x-text="'₱' + fmt(analytics.suggested_savings ?? 0)"></div>
            <div class="text-xs font-bold text-beige-400 mt-1.5 uppercase tracking-widest">Suggested Today</div>
        </div>

        {{-- Streak --}}
        <div class="bg-white border border-beige-200/70 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center">
                    <span class="text-xl">🔥</span>
                </div>
                <span class="text-[10px] font-black text-beige-400 uppercase tracking-widest">Streak</span>
            </div>
            <div class="text-2xl font-black text-orange-600 leading-none">
                <span x-text="analytics.streak ?? 0"></span> <span class="text-sm">Days</span>
            </div>
            <div class="text-xs font-bold text-beige-400 mt-1.5 uppercase tracking-widest">Savings Streak</div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         SAVINGS GOALS CARDS (NOW WITH INTEGRATED PLANNER)
    ══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-black text-mint-950">Active Goals & Planner</h2>
                <p class="text-[10px] font-bold text-beige-400 uppercase tracking-widest">Track targets and see required savings</p>
            </div>
            <button @click="openGoalModal()" class="text-xs font-black text-mint-600 hover:text-mint-800 transition-colors bg-mint-50 px-3 py-1.5 rounded-lg border border-mint-100">
                + New Goal
            </button>
        </div>

        <div class="flex overflow-x-auto gap-4 pb-4 snap-x">
            <template x-if="loading && goals.length === 0">
                <template x-for="i in 3">
                    <div class="min-w-[320px] w-[320px] bg-white border border-beige-200 rounded-3xl p-5 flex-shrink-0 animate-pulse snap-center">
                        <div class="h-5 w-32 bg-beige-100 rounded mb-4"></div>
                        <div class="h-2 w-full bg-beige-100 rounded-full mb-3"></div>
                        <div class="flex justify-between">
                            <div class="h-3 w-16 bg-beige-100 rounded"></div>
                            <div class="h-3 w-16 bg-beige-100 rounded"></div>
                        </div>
                    </div>
                </template>
            </template>
            
            <template x-if="!loading && goals.length === 0">
                <div class="w-full bg-white border border-beige-200 border-dashed rounded-3xl p-8 text-center text-beige-400 text-sm font-bold flex items-center justify-center">
                    No active savings goals. Create one to start tracking your progress and view your savings plan!
                </div>
            </template>

            <template x-for="g in goals" :key="g.id">
                <div class="min-w-[320px] w-[320px] bg-white border border-beige-200 rounded-3xl p-6 flex-shrink-0 snap-center shadow-sm relative group flex flex-col justify-between">
                    <div>
                        <div class="absolute top-4 right-4 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="openGoalModal(g)" class="p-1.5 bg-mint-50 text-mint-600 rounded-lg hover:bg-mint-100"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                            <button @click="confirmDeleteGoal(g)" class="p-1.5 bg-red-50 text-red-500 rounded-lg hover:bg-red-100"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </div>
                        
                        <h3 class="font-black text-mint-950 mb-1.5 pr-14 truncate text-lg" x-text="g.name"></h3>
                        <div class="text-[10px] font-bold text-beige-500 uppercase tracking-widest mb-4 flex justify-between">
                            <span x-text="'₱' + fmt(g.balance) + ' saved'"></span>
                            <span class="text-mint-600" x-text="g.days_left + ' days left'"></span>
                        </div>

                        {{-- Integrated Planner --}}
                        <div class="bg-beige-50/50 border border-beige-100 rounded-2xl p-4 mb-5 space-y-2.5">
                            <div class="text-[9px] font-black text-beige-400 uppercase tracking-widest mb-1">Savings Plan based on Target Date</div>
                            <template x-if="goalPlan(g)">
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-mint-900 uppercase tracking-widest">Daily</span>
                                        <span class="font-black text-mint-700 text-xs" x-text="'₱' + fmt(goalPlan(g).daily)"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-mint-900 uppercase tracking-widest">Weekly</span>
                                        <span class="font-black text-mint-700 text-xs" x-text="'₱' + fmt(goalPlan(g).weekly)"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-mint-900 uppercase tracking-widest">Monthly</span>
                                        <span class="font-black text-mint-700 text-xs" x-text="'₱' + fmt(goalPlan(g).monthly)"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-mint-900 uppercase tracking-widest">Yearly</span>
                                        <span class="font-black text-mint-700 text-xs" x-text="'₱' + fmt(goalPlan(g).yearly)"></span>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!goalPlan(g)">
                                <div class="text-xs font-bold text-beige-400">Set a Target Date to view your plan.</div>
                            </template>
                        </div>
                    </div>
                    
                    <div>
                        <div class="w-full bg-beige-100 rounded-full h-2.5 mb-2 overflow-hidden">
                            <div class="h-2.5 rounded-full transition-all duration-1000 ease-out" 
                                 :style="`width: ${g.progress}%; background-color: ${g.color_code || '#10b981'}`"></div>
                        </div>
                        <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                            <span :class="g.progress >= 100 ? 'text-mint-600' : 'text-beige-400'" x-text="g.progress.toFixed(0) + '%'"></span>
                            <span class="text-beige-400" x-text="'Target: ₱' + fmt(g.target_amount)"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>




    {{-- ══════════════════════════════════════════════════════════════
         CONTROLS: Filters + Record Button
    ══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col gap-3 mt-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 flex-wrap">
            {{-- Search --}}
            <div class="relative flex-1 min-w-0 w-full sm:w-auto">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-beige-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="filters.search" @input.debounce.300ms="loadData(1)"
                       placeholder="Search transactions…"
                       class="w-full pl-11 pr-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm outline-none">
            </div>

            {{-- Type Filter --}}
            <select x-model="filters.type" @change="loadData(1)"
                    class="w-full sm:w-auto px-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-mint-800 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm outline-none">
                <option value="">All Transactions</option>
                <option value="deposit">Deposits Only</option>
                <option value="withdrawal">Withdrawals Only</option>
            </select>

            {{-- Date Range --}}
            <div class="flex items-center gap-2 bg-white border border-beige-200 rounded-2xl px-3 py-1.5 shadow-sm w-full sm:w-auto">
                <input type="date" x-model="filters.from" @change="loadData(1)"
                       class="bg-transparent border-none text-xs font-bold text-mint-800 focus:ring-0 p-1 outline-none">
                <span class="text-beige-300 font-bold text-sm">→</span>
                <input type="date" x-model="filters.to" @change="loadData(1)"
                       class="bg-transparent border-none text-xs font-bold text-mint-800 focus:ring-0 p-1 outline-none">
            </div>

            <template x-if="filters.search || filters.type || filters.from || filters.to">
                <button @click="clearFilters()" class="px-4 py-2.5 text-xs font-black text-beige-500 bg-beige-50 border border-beige-200 rounded-2xl hover:bg-beige-100 transition-all uppercase tracking-widest whitespace-nowrap">
                    Clear
                </button>
            </template>
        </div>

        <div class="flex items-center justify-end gap-3 mt-2">
            <button @click="openTxModal('deposit')"
                    class="btn-mint shadow-lg shadow-mint-900/10 whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Record Deposit
            </button>
            <button @click="openTxModal('withdrawal')"
                    class="px-5 py-2.5 bg-white border-2 border-red-100 text-red-500 hover:bg-red-50 hover:border-red-200 text-xs font-black uppercase tracking-widest rounded-2xl transition-all shadow-sm flex items-center gap-2">
                Withdraw
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         DATA GRID — Transactions
    ══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-beige-200/60 rounded-3xl shadow-sm overflow-hidden">

        {{-- Desktop table --}}
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-sm min-w-[800px]">
                <thead>
                    <tr class="bg-beige-50/70 border-b border-beige-100">
                        <th @click="sortBy('transaction_date')" class="text-left pl-6 px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest cursor-pointer select-none hover:text-mint-600 transition-colors">
                            <span class="flex items-center gap-1">Date <span x-text="sortIcon('transaction_date')"></span></span>
                        </th>
                        <th @click="sortBy('type')" class="text-left px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest cursor-pointer select-none hover:text-mint-600 transition-colors">
                            <span class="flex items-center gap-1">Type <span x-text="sortIcon('type')"></span></span>
                        </th>
                        <th class="text-left px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest">Details</th>
                        <th @click="sortBy('amount')" class="text-right px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest cursor-pointer select-none hover:text-mint-600 transition-colors">
                            <span class="flex items-center justify-end gap-1">Amount <span x-text="sortIcon('amount')"></span></span>
                        </th>
                        <th class="text-right pr-6 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-beige-100">

                    {{-- Loading skeletons --}}
                    <template x-if="loading">
                        <template x-for="i in 5" :key="i">
                            <tr class="animate-pulse">
                                <td class="pl-6 px-4 py-5"><div class="h-4 w-24 bg-beige-100 rounded"></div></td>
                                <td class="px-4 py-5"><div class="h-6 w-20 bg-beige-100 rounded-lg"></div></td>
                                <td class="px-4 py-5">
                                    <div class="h-4 w-40 bg-beige-100 rounded mb-1.5"></div>
                                    <div class="h-3 w-24 bg-beige-100 rounded"></div>
                                </td>
                                <td class="px-4 py-5 text-right"><div class="h-5 w-20 bg-beige-100 rounded ml-auto"></div></td>
                                <td class="pr-6 py-5 text-right"><div class="h-8 w-8 bg-beige-100 rounded-xl ml-auto"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Empty state --}}
                    <template x-if="!loading && transactions.length === 0">
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="w-20 h-20 bg-beige-50 rounded-full flex items-center justify-center mx-auto mb-5">
                                    <svg class="w-10 h-10 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-base font-black text-mint-900">No transactions found</p>
                                <button @click="openTxModal('deposit')" class="btn-mint text-sm px-5 py-2.5 mt-4">Record Deposit</button>
                            </td>
                        </tr>
                    </template>

                    {{-- Data rows --}}
                    <template x-if="!loading">
                        <template x-for="t in transactions" :key="t.id">
                            <tr class="hover:bg-beige-50/60 transition-colors group">
                                <td class="pl-6 px-4 py-4">
                                    <div class="text-sm font-bold text-mint-900" x-text="fmtDate(t.transaction_date)"></div>
                                    <div class="text-xs text-beige-400 font-medium" x-text="relDate(t.transaction_date)"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wide"
                                          :class="t.type === 'deposit' ? 'bg-mint-100 text-mint-700' : 'bg-red-100 text-red-700'">
                                        <span x-text="t.type === 'deposit' ? '📥' : '📤'"></span>
                                        <span x-text="t.type"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-mint-900 text-sm truncate max-w-[250px]" x-text="t.purpose || (t.type === 'deposit' ? 'Savings Deposit' : 'Withdrawal')"></div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <template x-if="t.goal">
                                            <span class="text-[10px] font-black uppercase tracking-widest text-mint-600 bg-mint-50 px-1.5 py-0.5 rounded border border-mint-100" x-text="'🎯 ' + t.goal.name"></span>
                                        </template>
                                        <template x-if="t.notes">
                                            <span class="text-xs text-beige-400 font-medium truncate max-w-[150px]" x-text="t.notes"></span>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="font-black text-sm" :class="t.type === 'deposit' ? 'text-mint-600' : 'text-red-500'" x-text="(t.type === 'deposit' ? '+₱' : '-₱') + fmt(t.amount)"></span>
                                </td>
                                <td class="pr-6 py-4 text-right">
                                    <button @click="confirmDeleteTx(t)"
                                            class="p-2 rounded-xl bg-white border border-beige-200 text-red-400 hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-all shadow-sm" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- ── Mobile View ─────────────────────────────────────── --}}
        <div class="md:hidden divide-y divide-beige-100">
            <template x-if="!loading">
                <template x-for="t in transactions" :key="t.id + '-m'">
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="font-black text-mint-900 text-sm truncate" x-text="t.purpose || (t.type === 'deposit' ? 'Savings Deposit' : 'Withdrawal')"></div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-black uppercase"
                                          :class="t.type === 'deposit' ? 'bg-mint-100 text-mint-700' : 'bg-red-100 text-red-700'">
                                        <span x-text="t.type === 'deposit' ? '📥' : '📤'"></span>
                                        <span x-text="t.type"></span>
                                    </span>
                                    <span class="text-xs text-beige-400 font-medium" x-text="fmtDate(t.transaction_date)"></span>
                                </div>
                                <template x-if="t.goal">
                                    <div class="mt-1">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-mint-600 bg-mint-50 px-1.5 py-0.5 rounded border border-mint-100" x-text="'🎯 ' + t.goal.name"></span>
                                    </div>
                                </template>
                            </div>
                            <div class="text-right">
                                <div class="font-black text-sm" :class="t.type === 'deposit' ? 'text-mint-600' : 'text-red-500'" x-text="(t.type === 'deposit' ? '+₱' : '-₱') + fmt(t.amount)"></div>
                                <button @click="confirmDeleteTx(t)" class="mt-2 p-1.5 rounded-lg text-red-400 hover:bg-red-50 inline-block">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </template>
        </div>

        {{-- Pagination --}}
        <template x-if="!loading && pagination.last_page > 1">
            <div class="px-5 py-4 border-t border-beige-100 bg-beige-50/30 flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs font-bold text-beige-400">
                    Showing <span class="text-mint-700" x-text="pagination.from"></span>–<span class="text-mint-700" x-text="pagination.to"></span>
                    of <span class="text-mint-700" x-text="pagination.total"></span> records
                </p>
                <div class="flex items-center gap-1.5">
                    <button @click="loadData(pagination.current_page - 1)"
                            :disabled="pagination.current_page === 1"
                            class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black border border-beige-200 bg-white text-mint-700 hover:border-mint-300 disabled:opacity-30 disabled:cursor-not-allowed transition-all">‹</button>
                    <template x-for="pg in pageRange" :key="pg">
                        <button @click="pg !== '…' && loadData(pg)"
                                :class="pg === pagination.current_page
                                    ? 'bg-mint-500 text-white border-mint-500 shadow-md shadow-mint-900/15'
                                    : pg === '…' ? 'cursor-default text-beige-300 border-transparent' : 'bg-white text-mint-800 border-beige-200 hover:border-mint-300'"
                                class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black border transition-all"
                                x-text="pg"></button>
                    </template>
                    <button @click="loadData(pagination.current_page + 1)"
                            :disabled="pagination.current_page === pagination.last_page"
                            class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black border border-beige-200 bg-white text-mint-700 hover:border-mint-300 disabled:opacity-30 disabled:cursor-not-allowed transition-all">›</button>
                </div>
            </div>
        </template>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         MODAL: TRANSACTION (DEPOSIT / WITHDRAWAL)
    ══════════════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="txModal.open" style="display:none" class="fixed inset-0 z-[800] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-mint-950/50 backdrop-blur-sm"
                 x-show="txModal.open"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="txModal.open = false"></div>

            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-beige-200/60 overflow-hidden"
                 x-show="txModal.open"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4">

                <div class="px-7 pt-6 pb-5 border-b border-beige-100 bg-beige-50/60 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-mint-950" x-text="txForm.type === 'deposit' ? 'Record Deposit' : 'Withdraw Savings'"></h3>
                        <p class="text-[10px] font-bold text-beige-400 uppercase tracking-widest mt-0.5" x-text="txForm.type === 'deposit' ? 'Add funds to your savings' : 'Take out funds from savings'"></p>
                    </div>
                    <button @click="txModal.open = false" class="w-9 h-9 rounded-xl hover:bg-red-50 text-beige-400 hover:text-red-500 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-7 space-y-5">
                    {{-- Goal Selection --}}
                    <div>
                        <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">Link to Goal (Optional)</label>
                        <select x-model="txForm.savings_goal_id"
                                :class="txErrors.savings_goal_id ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                                class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 transition-all shadow-sm outline-none">
                            <option value="">— General Savings (No Goal) —</option>
                            <template x-for="g in goals" :key="g.id">
                                <option :value="g.id" x-text="g.name + ' (Target: ₱' + fmt(g.target_amount) + ')'"></option>
                            </template>
                        </select>
                        <p x-show="txErrors.savings_goal_id" x-text="txErrors.savings_goal_id?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-beige-400 text-sm">₱</span>
                            <input type="number" x-model="txForm.amount" step="0.01" min="0.01" placeholder="0.00"
                                   :class="txErrors.amount ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                                   class="w-full pl-8 pr-4 py-3 bg-white border rounded-2xl text-xl font-black text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 transition-all shadow-sm outline-none">
                        </div>
                        <p x-show="txErrors.amount" x-text="txErrors.amount?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>
                        
                        <template x-if="txForm.type === 'withdrawal'">
                            <p class="text-[10px] font-bold text-beige-400 uppercase tracking-widest mt-1">Available: ₱<span x-text="fmt(analytics.balance)"></span></p>
                        </template>
                    </div>

                    {{-- Purpose (only withdrawal) --}}
                    <div x-show="txForm.type === 'withdrawal'">
                        <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">
                            Purpose <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="txForm.purpose" placeholder="Why are you withdrawing?"
                               :class="txErrors.purpose ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                               class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 transition-all shadow-sm outline-none">
                        <p x-show="txErrors.purpose" x-text="txErrors.purpose?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" x-model="txForm.transaction_date"
                                   :class="txErrors.transaction_date ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                                   class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 transition-all shadow-sm outline-none">
                            <p x-show="txErrors.transaction_date" x-text="txErrors.transaction_date?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>
                        </div>
                    </div>
                </div>

                <div class="px-7 py-5 border-t border-beige-100 bg-beige-50/30 flex gap-3">
                    <button @click="submitTxForm()"
                            :disabled="txModal.submitting"
                            class="flex-1 px-6 py-3 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2 rounded-2xl text-sm font-black uppercase tracking-widest text-white shadow-md transition-all"
                            :class="txForm.type === 'deposit' ? 'bg-mint-500 hover:bg-mint-600' : 'bg-red-500 hover:bg-red-600'">
                        <template x-if="txModal.submitting"><svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg></template>
                        <span x-text="txForm.type === 'deposit' ? 'Save Deposit' : 'Confirm Withdrawal'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ══════════════════════════════════════════════════════════════
         MODAL: SAVINGS GOAL (CREATE / EDIT)
    ══════════════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="goalModal.open" style="display:none" class="fixed inset-0 z-[800] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-mint-950/50 backdrop-blur-sm"
                 x-show="goalModal.open"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="goalModal.open = false"></div>

            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-beige-200/60 overflow-hidden"
                 x-show="goalModal.open"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4">

                <div class="px-7 pt-6 pb-5 border-b border-beige-100 bg-beige-50/60 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-mint-950" x-text="goalModal.isEdit ? 'Edit Goal' : 'New Savings Goal'"></h3>
                    </div>
                    <button @click="goalModal.open = false" class="w-9 h-9 rounded-xl hover:bg-red-50 text-beige-400 hover:text-red-500 transition-colors flex items-center justify-center"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>

                <div class="p-7 space-y-4">
                    <div>
                        <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">Goal Name <span class="text-red-500">*</span></label>
                        <input type="text" x-model="goalForm.name" placeholder="e.g. New Equipment, Emergency Fund"
                               :class="goalErrors.name ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                               class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold outline-none focus:ring-4 focus:ring-mint-500/10">
                        <p x-show="goalErrors.name" x-text="goalErrors.name?.[0]" class="text-xs text-red-500 mt-1 font-semibold"></p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">Target Amount <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-beige-400">₱</span>
                            <input type="number" x-model="goalForm.target_amount" step="0.01" placeholder="0.00"
                                   :class="goalErrors.target_amount ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                                   class="w-full pl-8 pr-4 py-3 bg-white border rounded-2xl text-sm font-semibold outline-none focus:ring-4 focus:ring-mint-500/10">
                        </div>
                        <p x-show="goalErrors.target_amount" x-text="goalErrors.target_amount?.[0]" class="text-xs text-red-500 mt-1 font-semibold"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">Start Date <span class="text-red-500">*</span></label>
                            <input type="date" x-model="goalForm.start_date"
                                   :class="goalErrors.start_date ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                                   class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold outline-none focus:ring-4 focus:ring-mint-500/10">
                            <p x-show="goalErrors.start_date" x-text="goalErrors.start_date?.[0]" class="text-xs text-red-500 mt-1 font-semibold"></p>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">Target Date</label>
                            <input type="date" x-model="goalForm.goal_date"
                                   :class="goalErrors.goal_date ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                                   class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold outline-none focus:ring-4 focus:ring-mint-500/10">
                            <p x-show="goalErrors.goal_date" x-text="goalErrors.goal_date?.[0]" class="text-xs text-red-500 mt-1 font-semibold"></p>
                        </div>
                    </div>
                </div>

                <div class="px-7 py-5 border-t border-beige-100 bg-beige-50/30">
                    <button @click="submitGoalForm()"
                            :disabled="goalModal.submitting"
                            class="w-full btn-mint px-6 py-3 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <template x-if="goalModal.submitting"><svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg></template>
                        <span x-text="goalModal.isEdit ? 'Update Goal' : 'Create Goal'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- DELETE CONFIRMATION --}}
    <template x-teleport="body">
        <div x-show="deleteModal.open" style="display:none" class="fixed inset-0 z-[900] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-red-950/40 backdrop-blur-sm" @click="deleteModal.open = false"></div>
            <div class="relative w-full max-w-sm bg-white rounded-3xl p-8 text-center shadow-2xl z-10">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-5 text-red-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-2xl font-black text-red-950 mb-2">Are you sure?</h3>
                <p class="text-sm font-medium text-beige-500 mb-6" x-text="'Delete this ' + deleteModal.type + '?'"></p>
                <div class="flex gap-3">
                    <button @click="deleteModal.open = false" class="flex-1 py-3 rounded-2xl bg-beige-50 font-black text-beige-600 hover:bg-beige-100 uppercase tracking-widest text-xs">Cancel</button>
                    <button @click="executeDelete()" class="flex-1 py-3 rounded-2xl bg-red-500 font-black text-white hover:bg-red-600 uppercase tracking-widest text-xs">Delete</button>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection

@push('scripts')
<script>
function savingsDashboard() {
    return {
        // ── State ──────────────────────────────────────────────────────
        transactions: [],
        goals:        [],
        analytics:    {},
        loading:      true,
        pagination:   { current_page: 1, last_page: 1, total: 0, per_page: 15, from: 0, to: 0 },
        toasts:       [],
        _toastId:     0,
        
        filters: { search: '', type: '', from: '', to: '' },
        sort:    { by: 'transaction_date', dir: 'desc' },

        txModal: { open: false, submitting: false },
        txForm: { type: 'deposit', amount: '', transaction_date: '', purpose: '', savings_goal_id: '' },
        txErrors: {},

        goalModal: { open: false, isEdit: false, id: null, submitting: false },
        goalForm: { name: '', target_amount: '', start_date: '', goal_date: '', status: 'active', color_code: '#10b981' },
        goalErrors: {},

        deleteModal: { open: false, type: '', id: null, deleting: false },

        goalPlan(g) {
            if (!g.goal_date || !g.start_date) return null;
            const start = new Date(g.start_date + 'T00:00:00');
            const end = new Date(g.goal_date + 'T00:00:00');
            const totalDays = Math.max(1, (end - start) / (1000 * 60 * 60 * 24));
            const daily = g.target_amount / totalDays;
            return {
                daily: daily,
                weekly: daily * 7,
                monthly: daily * 30,
                yearly: daily * 365
            };
        },

        // ── Init ───────────────────────────────────────────────────────
        init() {
            this.loadData();
            this.loadAnalytics();
        },

        get pageRange() {
            const { current_page: cur, last_page: last } = this.pagination;
            const pages = [];
            if (last <= 7) {
                for (let i = 1; i <= last; i++) pages.push(i);
            } else {
                pages.push(1);
                if (cur > 3) pages.push('…');
                for (let i = Math.max(2, cur - 1); i <= Math.min(last - 1, cur + 1); i++) pages.push(i);
                if (cur < last - 2) pages.push('…');
                pages.push(last);
            }
            return pages;
        },

        // ── Data ───────────────────────────────────────────────────────
        async loadData(page = 1) {
            this.loading = true;
            const params = new URLSearchParams({
                page,
                per_page: this.pagination.per_page,
                sort_by:  this.sort.by,
                sort_dir: this.sort.dir,
                ...Object.fromEntries(Object.entries(this.filters).filter(([, v]) => v)),
            });
            try {
                const res  = await fetch('/savings?' + params, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const json = await res.json();
                this.transactions = json.data;
                this.goals        = json.goals;
                this.pagination   = json.pagination;
            } catch {
                this.toast('Failed to load data.', 'error');
            } finally {
                this.loading = false;
            }
        },

        async loadAnalytics() {
            try {
                const res = await fetch('/savings/analytics', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                this.analytics = await res.json();
            } catch { /* silent */ }
        },

        // ── Actions ────────────────────────────────────────────────────
        sortBy(col) {
            if (this.sort.by === col) this.sort.dir = this.sort.dir === 'asc' ? 'desc' : 'asc';
            else { this.sort.by = col; this.sort.dir = 'desc'; }
            this.loadData(1);
        },
        sortIcon(col) {
            if (this.sort.by !== col) return '↕';
            return this.sort.dir === 'asc' ? '↑' : '↓';
        },
        clearFilters() {
            this.filters = { search: '', type: '', from: '', to: '' };
            this.loadData(1);
        },

        // ── Transaction Modal ──────────────────────────────────────────
        openTxModal(type) {
            this.txErrors = {};
            this.txForm = {
                type,
                amount: '',
                transaction_date: new Date().toISOString().split('T')[0],
                purpose: '',
                savings_goal_id: ''
            };
            this.txModal.open = true;
        },

        async submitTxForm() {
            if (this.txModal.submitting) return;
            this.txModal.submitting = true;
            this.txErrors = {};

            try {
                const res = await fetch('/savings', {
                    method:  'POST',
                    headers: {
                        'Content-Type':     'application/json',
                        'Accept':           'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.txForm),
                });
                const data = await res.json();

                if (!res.ok) {
                    if (res.status === 422) {
                        this.txErrors = data.errors;
                        this.toast('Please check the form for errors.', 'error');
                    } else {
                        this.toast(data.message || 'An error occurred.', 'error');
                    }
                } else {
                    this.txModal.open = false;
                    this.toast(data.message, 'success');
                    this.loadData(1);
                    this.loadAnalytics();
                }
            } catch {
                this.toast('Network error.', 'error');
            } finally {
                this.txModal.submitting = false;
            }
        },

        // ── Goal Modal ─────────────────────────────────────────────────
        openGoalModal(goal = null) {
            this.goalErrors = {};
            this.goalModal.isEdit = !!goal;
            if (goal) {
                this.goalModal.id = goal.id;
                this.goalForm = {
                    name: goal.name, target_amount: goal.target_amount,
                    start_date: goal.start_date, goal_date: goal.goal_date || '',
                    status: 'active', color_code: goal.color_code || '#10b981'
                };
            } else {
                this.goalModal.id = null;
                this.goalForm = {
                    name: '', target_amount: '',
                    start_date: new Date().toISOString().split('T')[0], goal_date: '',
                    status: 'active', color_code: '#10b981'
                };
            }
            this.goalModal.open = true;
        },

        async submitGoalForm() {
            if (this.goalModal.submitting) return;
            this.goalModal.submitting = true;
            this.goalErrors = {};

            const url = this.goalModal.isEdit ? `/savings-goals/${this.goalModal.id}` : '/savings-goals';
            const method = this.goalModal.isEdit ? 'PUT' : 'POST';

            try {
                const res = await fetch(url, {
                    method:  method,
                    headers: {
                        'Content-Type':     'application/json',
                        'Accept':           'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.goalForm),
                });
                const data = await res.json();

                if (!res.ok) {
                    if (res.status === 422) {
                        this.goalErrors = data.errors;
                        this.toast('Please check the form for errors.', 'error');
                    } else {
                        this.toast(data.message || 'Error saving goal.', 'error');
                    }
                } else {
                    this.goalModal.open = false;
                    this.toast(data.message, 'success');
                    this.loadData(this.pagination.current_page);
                }
            } catch {
                this.toast('Network error.', 'error');
            } finally {
                this.goalModal.submitting = false;
            }
        },

        // ── Deletion ───────────────────────────────────────────────────
        confirmDeleteTx(tx) {
            this.deleteModal = { open: true, type: 'transaction', id: tx.id };
        },
        confirmDeleteGoal(goal) {
            this.deleteModal = { open: true, type: 'goal', id: goal.id };
        },
        async executeDelete() {
            if (this.deleteModal.deleting) return;
            this.deleteModal.deleting = true;
            const url = this.deleteModal.type === 'transaction' 
                ? `/savings/${this.deleteModal.id}` 
                : `/savings-goals/${this.deleteModal.id}`;
            try {
                const res = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const data = await res.json();
                if (res.ok) {
                    this.deleteModal.open = false;
                    this.toast(data.message, 'success');
                    this.loadData(this.pagination.current_page);
                    this.loadAnalytics();
                }
            } catch {
                this.toast('Error deleting.', 'error');
            } finally {
                this.deleteModal.deleting = false;
            }
        },

        // ── Utilities ──────────────────────────────────────────────────
        toast(message, type = 'info') {
            const id = ++this._toastId;
            this.toasts.push({ id, message, type, visible: true });
            setTimeout(() => this.dismissToast(id), 5000);
        },
        dismissToast(id) {
            const t = this.toasts.find(t => t.id === id);
            if (t) t.visible = false;
            setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 400);
        },
        fmt(n) { return Number(n ?? 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },
        fmtDate(d) {
            if (!d) return '—';
            return new Date(d + 'T00:00:00').toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
        },
        relDate(d) {
            if (!d) return '';
            const diff = Date.now() - new Date(d + 'T00:00:00').getTime();
            const days = Math.floor(diff / 86400000);
            if (days === 0) return 'Today';
            if (days === 1) return 'Yesterday';
            if (days < 30)  return `${days}d ago`;
            if (days < 365) return `${Math.floor(days / 30)}mo ago`;
            return `${Math.floor(days / 365)}yr ago`;
        },
    };
}
</script>
@endpush
