@extends('layouts.app')
@section('title', 'Payment History')
@section('page-title', 'Payment History')

@section('content')
<div x-data="paymentDashboard()" x-init="init()" class="space-y-6">

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

        {{-- Total Collected --}}
        <div class="bg-gradient-to-br from-mint-500 to-mint-600 rounded-2xl p-5 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-[10px] font-black text-white/60 uppercase tracking-widest">All Time</span>
            </div>
            <div class="text-2xl font-black text-white leading-none" x-text="'₱' + fmt(analytics.total_collected ?? 0)"></div>
            <div class="text-xs font-bold text-white/60 mt-1.5 uppercase tracking-widest">Total Collected</div>
        </div>

        {{-- This Month --}}
        <div class="bg-white border border-beige-200/70 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-[10px] font-black text-beige-400 uppercase tracking-widest">Month</span>
            </div>
            <div class="text-2xl font-black text-mint-900 leading-none" x-text="'₱' + fmt(analytics.total_this_month ?? 0)"></div>
            <div class="text-xs font-bold text-beige-400 mt-1.5 uppercase tracking-widest">Collected This Month</div>
        </div>

        {{-- Total Transactions --}}
        <div class="bg-white border border-beige-200/70 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-[10px] font-black text-beige-400 uppercase tracking-widest">Count</span>
            </div>
            <div class="text-2xl font-black text-mint-900 leading-none" x-text="analytics.total_payments ?? '—'"></div>
            <div class="text-xs font-bold text-beige-400 mt-1.5 uppercase tracking-widest">Transactions</div>
        </div>

        {{-- Outstanding --}}
        <div class="bg-white border border-beige-200/70 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-black text-beige-400 uppercase tracking-widest">Pending</span>
            </div>
            <div class="text-2xl font-black text-red-500 leading-none" x-text="'₱' + fmt(analytics.total_outstanding ?? 0)"></div>
            <div class="text-xs font-bold text-beige-400 mt-1.5 uppercase tracking-widest">Outstanding Balance</div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         CONTROLS: Filters + Bulk Actions + Record Button
    ══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col gap-3">
        {{-- Row 1: Search + Method + Date Range --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 flex-wrap">
            {{-- Search --}}
            <div class="relative flex-1 min-w-0 w-full sm:w-auto">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-beige-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="filters.search" @input.debounce.300ms="loadData(1)"
                       placeholder="Search customer, invoice, ref #…"
                       class="w-full pl-11 pr-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm outline-none">
            </div>

            {{-- Method Filter --}}
            <select x-model="filters.method" @change="loadData(1)"
                    class="w-full sm:w-auto px-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-mint-800 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm outline-none">
                <option value="">All Methods</option>
                <option value="cash">💵 Cash</option>
                <option value="gcash">📱 GCash</option>
                <option value="bank_transfer">🏦 Bank Transfer</option>
                <option value="credit_card">💳 Credit Card</option>
                <option value="check">📄 Check</option>
                <option value="store_credit">🏪 Store Credit</option>
            </select>

            {{-- Date Range --}}
            <div class="flex items-center gap-2 bg-white border border-beige-200 rounded-2xl px-3 py-1.5 shadow-sm w-full sm:w-auto">
                <input type="date" x-model="filters.from" @change="loadData(1)"
                       class="bg-transparent border-none text-xs font-bold text-mint-800 focus:ring-0 p-1 outline-none">
                <span class="text-beige-300 font-bold text-sm">→</span>
                <input type="date" x-model="filters.to" @change="loadData(1)"
                       class="bg-transparent border-none text-xs font-bold text-mint-800 focus:ring-0 p-1 outline-none">
            </div>

            {{-- Clear Filters --}}
            <template x-if="filters.search || filters.method || filters.from || filters.to">
                <button @click="clearFilters()" class="px-4 py-2.5 text-xs font-black text-beige-500 bg-beige-50 border border-beige-200 rounded-2xl hover:bg-beige-100 transition-all uppercase tracking-widest whitespace-nowrap">
                    Clear Filters
                </button>
            </template>
        </div>

        {{-- Row 2: Bulk Actions + Record Button --}}
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <template x-if="selectedIds.length > 0">
                    <div class="flex items-center gap-2 animate-fade-in">
                        <span class="text-xs font-black text-mint-700 bg-mint-50 border border-mint-200 rounded-xl px-3 py-2"
                              x-text="selectedIds.length + ' selected'"></span>
                        <button @click="bulkDelete()"
                                class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs font-black uppercase tracking-widest hover:bg-red-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete Selected
                        </button>
                    </div>
                </template>
            </div>

            <button @click="openCreate()"
                    class="btn-mint shadow-lg shadow-mint-900/10 whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Record Payment
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         DATA GRID — Desktop
    ══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-beige-200/60 rounded-3xl shadow-sm overflow-hidden">

        {{-- Desktop table --}}
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-sm min-w-[900px]">
                <thead>
                    <tr class="bg-beige-50/70 border-b border-beige-100">
                        <th class="w-10 pl-5 py-4">
                            <input type="checkbox" @change="toggleSelectAll($event)"
                                   :checked="payments.length > 0 && selectedIds.length === payments.length"
                                   class="w-4 h-4 rounded accent-mint-500">
                        </th>
                        <th @click="sortBy('payment_date')" class="text-left px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest cursor-pointer select-none hover:text-mint-600 transition-colors">
                            <span class="flex items-center gap-1">Date <span x-text="sortIcon('payment_date')"></span></span>
                        </th>
                        <th class="text-left px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest">Customer</th>
                        <th class="text-left px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest">Invoice</th>
                        <th @click="sortBy('payment_method')" class="text-left px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest cursor-pointer select-none hover:text-mint-600 transition-colors">
                            <span class="flex items-center gap-1">Method <span x-text="sortIcon('payment_method')"></span></span>
                        </th>
                        <th class="text-left px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest">Reference #</th>
                        <th @click="sortBy('amount')" class="text-right px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest cursor-pointer select-none hover:text-mint-600 transition-colors">
                            <span class="flex items-center justify-end gap-1">Amount <span x-text="sortIcon('amount')"></span></span>
                        </th>
                        <th class="text-right pr-5 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-beige-100">

                    {{-- Loading skeletons --}}
                    <template x-if="loading">
                        <template x-for="i in 7" :key="i">
                            <tr class="animate-pulse">
                                <td class="pl-5 py-5"><div class="w-4 h-4 bg-beige-100 rounded"></div></td>
                                <td class="px-4 py-5"><div class="h-3.5 w-24 bg-beige-100 rounded"></div></td>
                                <td class="px-4 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-beige-100 flex-shrink-0"></div>
                                        <div class="h-3.5 w-28 bg-beige-100 rounded"></div>
                                    </div>
                                </td>
                                <td class="px-4 py-5"><div class="h-6 w-24 bg-beige-100 rounded-lg"></div></td>
                                <td class="px-4 py-5"><div class="h-6 w-20 bg-beige-100 rounded-lg"></div></td>
                                <td class="px-4 py-5"><div class="h-3.5 w-28 bg-beige-100 rounded font-mono"></div></td>
                                <td class="px-4 py-5 text-right"><div class="h-4 w-20 bg-beige-100 rounded ml-auto"></div></td>
                                <td class="pr-5 py-5 text-right"><div class="h-8 w-20 bg-beige-100 rounded-xl ml-auto"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Empty state --}}
                    <template x-if="!loading && payments.length === 0">
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center">
                                <div class="w-20 h-20 bg-beige-50 rounded-full flex items-center justify-center mx-auto mb-5">
                                    <svg class="w-10 h-10 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <p class="text-base font-black text-mint-900">No payment records found</p>
                                <p class="text-sm font-medium text-beige-400 mt-1 mb-5">Payments recorded in the system will appear here</p>
                                <button @click="openCreate()" class="btn-mint text-sm px-5 py-2.5">Record First Payment</button>
                            </td>
                        </tr>
                    </template>

                    {{-- Data rows --}}
                    <template x-if="!loading">
                        <template x-for="p in payments" :key="p.id">
                            <tr class="hover:bg-beige-50/60 transition-colors group">
                                <td class="pl-5 py-4 w-10">
                                    <input type="checkbox" :value="p.id"
                                           :checked="selectedIds.includes(p.id)"
                                           @change="toggleSelect(p.id)"
                                           class="w-4 h-4 rounded accent-mint-500">
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-bold text-mint-900" x-text="fmtDate(p.payment_date)"></div>
                                    <div class="text-xs text-beige-400 font-medium" x-text="relDate(p.payment_date)"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black text-white flex-shrink-0 transition-transform group-hover:scale-105"
                                             :style="`background: ${avatarColor(p.customer?.name)}`"
                                             x-text="initials(p.customer?.name)"></div>
                                        <a :href="p.customer ? `/customers/${p.customer.id}` : '#'"
                                           class="font-bold text-mint-900 hover:text-mint-600 transition-colors text-sm truncate max-w-[150px]"
                                           x-text="p.customer?.name ?? 'Unknown'"></a>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-mint-50 border border-mint-100 text-mint-600 text-xs font-black"
                                          x-text="p.invoice?.invoice_number ?? '—'"></span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-black uppercase tracking-wide"
                                          :class="methodStyle(p.payment_method).badge">
                                        <span x-text="methodStyle(p.payment_method).icon"></span>
                                        <span x-text="methodStyle(p.payment_method).label"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-mono text-xs text-beige-400 font-bold"
                                          x-text="p.reference_number || '—'"></span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="font-black text-mint-600 text-sm" x-text="'₱' + fmt(p.amount)"></span>
                                </td>
                                <td class="pr-5 py-4">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button @click="openDetail(p)"
                                                class="p-2 rounded-xl bg-white border border-beige-200 text-mint-600 hover:bg-mint-50 hover:border-mint-200 transition-all shadow-sm" title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <button @click="confirmDelete(p)"
                                                class="p-2 rounded-xl bg-white border border-beige-200 text-red-400 hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-all shadow-sm" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>

                </tbody>
            </table>
        </div>

        {{-- ── Mobile Card View ─────────────────────────────────────── --}}
        <div class="md:hidden divide-y divide-beige-100">
            <template x-if="loading">
                <template x-for="i in 4" :key="i">
                    <div class="p-4 animate-pulse space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-beige-100 flex-shrink-0"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-4 bg-beige-100 rounded w-2/3"></div>
                                <div class="h-3 bg-beige-100 rounded w-1/3"></div>
                            </div>
                            <div class="h-5 w-20 bg-beige-100 rounded"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="h-8 bg-beige-100 rounded-xl"></div>
                            <div class="h-8 bg-beige-100 rounded-xl"></div>
                            <div class="h-8 bg-beige-100 rounded-xl"></div>
                        </div>
                    </div>
                </template>
            </template>

            <template x-if="!loading && payments.length === 0">
                <div class="px-6 py-16 text-center">
                    <div class="w-16 h-16 bg-beige-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <p class="font-black text-mint-900">No payments found</p>
                    <button @click="openCreate()" class="btn-mint text-sm mt-4 px-5 py-2.5">Record Payment</button>
                </div>
            </template>

            <template x-if="!loading">
                <template x-for="p in payments" :key="p.id + '-m'">
                    <div class="p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-sm font-black text-white flex-shrink-0"
                                 :style="`background: ${avatarColor(p.customer?.name)}`"
                                 x-text="initials(p.customer?.name)"></div>
                            <div class="flex-1 min-w-0">
                                <div class="font-black text-mint-900 text-sm truncate" x-text="p.customer?.name ?? 'Unknown'"></div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs font-bold text-mint-500 bg-mint-50 px-1.5 py-0.5 rounded border border-mint-100"
                                          x-text="p.invoice?.invoice_number ?? '—'"></span>
                                    <span class="text-xs text-beige-400 font-medium" x-text="fmtDate(p.payment_date)"></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-black text-mint-600 text-sm" x-text="'₱' + fmt(p.amount)"></div>
                                <div class="mt-0.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-black"
                                          :class="methodStyle(p.payment_method).badge">
                                        <span x-text="methodStyle(p.payment_method).icon"></span>
                                        <span x-text="methodStyle(p.payment_method).label"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-beige-100">
                            <button @click="openDetail(p)" class="flex-1 py-2.5 rounded-xl bg-mint-50 text-mint-700 text-xs font-black hover:bg-mint-100 transition-colors">View</button>
                            <button @click="confirmDelete(p)" class="py-2.5 px-3 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
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
                    of <span class="text-mint-700" x-text="pagination.total"></span> payments
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
         RECORD PAYMENT MODAL
    ══════════════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="modal.open" style="display:none" class="fixed inset-0 z-[800] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-mint-950/50 backdrop-blur-sm"
                 x-show="modal.open"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="modal.open = false"></div>

            <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-beige-200/60 overflow-hidden max-h-[90vh] flex flex-col"
                 x-show="modal.open"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4">

                {{-- Modal Header --}}
                <div class="px-7 pt-6 pb-5 border-b border-beige-100 bg-beige-50/60 flex items-center justify-between flex-shrink-0">
                    <div>
                        <h3 class="text-xl font-black text-mint-950">Record Payment</h3>
                        <p class="text-[10px] font-bold text-beige-400 uppercase tracking-widest mt-0.5">Apply a payment to an outstanding invoice</p>
                    </div>
                    <button @click="modal.open = false" class="w-9 h-9 rounded-xl hover:bg-red-50 text-beige-400 hover:text-red-500 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="overflow-y-auto flex-1 p-7">
                    <div class="space-y-5">

                        {{-- Invoice Selection --}}
                        <div>
                            <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">
                                Select Invoice <span class="text-red-500">*</span>
                            </label>
                            <select x-model="form.invoice_id" @change="onInvoiceChange()"
                                    :class="formErrors.invoice_id ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                                    class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 transition-all shadow-sm outline-none">
                                <option value="">— Choose an outstanding invoice —</option>
                                <template x-for="inv in invoices" :key="inv.id">
                                    <option :value="inv.id"
                                            x-text="inv.invoice_number + ' — ' + inv.customer_name + ' (₱' + fmt(inv.balance) + ' due)'">
                                    </option>
                                </template>
                            </select>
                            <p x-show="formErrors.invoice_id" x-text="formErrors.invoice_id?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>

                            {{-- Invoice Summary Card --}}
                            <template x-if="selectedInvoice">
                                <div class="mt-3 bg-mint-50 border border-mint-100 rounded-2xl p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-black text-mint-700 uppercase tracking-widest" x-text="selectedInvoice.invoice_number"></span>
                                        <span class="text-xs font-bold text-mint-600 bg-white border border-mint-200 rounded-lg px-2 py-0.5" x-text="selectedInvoice.customer_name"></span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <span class="font-bold text-beige-400 uppercase tracking-widest block text-[10px]">Total Amount</span>
                                            <span class="font-black text-mint-900" x-text="'₱' + fmt(selectedInvoice.total_amount)"></span>
                                        </div>
                                        <div>
                                            <span class="font-bold text-beige-400 uppercase tracking-widest block text-[10px]">Outstanding</span>
                                            <span class="font-black text-red-500" x-text="'₱' + fmt(selectedInvoice.balance)"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Amount --}}
                        <div>
                            <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">
                                Payment Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-beige-400 text-sm">₱</span>
                                <input type="number" x-model="form.amount" step="0.01" min="0.01"
                                       :max="selectedInvoice ? selectedInvoice.balance : undefined"
                                       placeholder="0.00"
                                       @input="calcChange()"
                                       :class="formErrors.amount ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                                       class="w-full pl-8 pr-4 py-3 bg-white border rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 transition-all shadow-sm outline-none">
                            </div>
                            <p x-show="formErrors.amount" x-text="formErrors.amount?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>

                            {{-- Quick fill buttons --}}
                            <template x-if="selectedInvoice">
                                <div class="flex gap-2 mt-2">
                                    <button @click="form.amount = selectedInvoice.balance; calcChange()"
                                            type="button" class="flex-1 py-1.5 rounded-xl bg-mint-50 border border-mint-100 text-mint-700 text-xs font-black hover:bg-mint-100 transition-colors">
                                        Pay Full (₱<span x-text="fmt(selectedInvoice.balance)"></span>)
                                    </button>
                                    <button @click="form.amount = (selectedInvoice.balance / 2).toFixed(2); calcChange()"
                                            type="button" class="flex-1 py-1.5 rounded-xl bg-beige-50 border border-beige-200 text-beige-600 text-xs font-black hover:bg-beige-100 transition-colors">
                                        Pay Half
                                    </button>
                                </div>
                            </template>

                            {{-- Cash change calculator --}}
                            <template x-if="form.payment_method === 'cash' && form.amount && selectedInvoice">
                                <div class="mt-2 bg-amber-50 border border-amber-100 rounded-xl p-3">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-amber-700">Cash Tendered: ₱<span x-text="fmt(form.amount)"></span></span>
                                        <span class="font-black" :class="cashChange >= 0 ? 'text-mint-700' : 'text-red-600'">
                                            Change: ₱<span x-text="fmt(Math.abs(cashChange))"></span>
                                            <template x-if="cashChange < 0"><span class="text-red-500"> (short)</span></template>
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Payment Method --}}
                        <div>
                            <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                <template x-for="m in paymentMethods" :key="m.value">
                                    <button type="button" @click="form.payment_method = m.value; calcChange()"
                                            :class="form.payment_method === m.value
                                                ? 'bg-mint-500 border-mint-500 text-white shadow-md'
                                                : 'bg-white border-beige-200 text-beige-600 hover:border-mint-300 hover:text-mint-700'"
                                            class="flex flex-col items-center gap-1 py-2.5 rounded-2xl border text-xs font-black transition-all">
                                        <span class="text-base" x-text="m.icon"></span>
                                        <span x-text="m.label"></span>
                                    </button>
                                </template>
                            </div>
                            <p x-show="formErrors.payment_method" x-text="formErrors.payment_method?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>
                        </div>

                        {{-- Reference & Date --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">Reference Number</label>
                                <input type="text" x-model="form.reference_number" placeholder="e.g. GCash ref, check #"
                                       class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">
                                    Payment Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" x-model="form.payment_date"
                                       :class="formErrors.payment_date ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                                       class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 transition-all shadow-sm outline-none">
                                <p x-show="formErrors.payment_date" x-text="formErrors.payment_date?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">Notes</label>
                            <textarea x-model="form.notes" rows="2" placeholder="Any additional notes about this payment…"
                                      class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm outline-none resize-none"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-7 py-5 border-t border-beige-100 bg-beige-50/30 flex flex-col sm:flex-row items-center gap-3 flex-shrink-0">
                    <button @click="submitForm()"
                            :disabled="modal.submitting"
                            class="btn-mint shadow-md w-full sm:w-auto px-6 py-3 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <template x-if="modal.submitting">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        </template>
                        <span x-text="modal.submitting ? 'Recording Payment…' : 'Record Payment'"></span>
                    </button>
                    <button @click="modal.open = false" class="px-6 py-3 text-xs font-black text-mint-800 uppercase tracking-widest hover:bg-beige-100 rounded-2xl transition-all w-full sm:w-auto">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ══════════════════════════════════════════════════════════════
         PAYMENT DETAIL DRAWER
    ══════════════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="detail.open" style="display:none" class="fixed inset-0 z-[800] flex justify-end">
            <div class="absolute inset-0 bg-mint-950/30 backdrop-blur-sm"
                 x-show="detail.open"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="detail.open = false"></div>

            <div class="relative w-full max-w-sm bg-white shadow-2xl overflow-y-auto flex flex-col h-full"
                 x-show="detail.open"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full">

                <template x-if="detail.payment">
                    <div>
                        {{-- Drawer header --}}
                        <div class="sticky top-0 bg-white border-b border-beige-100 px-6 py-5 flex items-center justify-between z-10">
                            <div>
                                <h3 class="font-black text-mint-950">Payment Details</h3>
                                <p class="text-xs text-beige-400 font-bold mt-0.5" x-text="'#' + detail.payment.id"></p>
                            </div>
                            <button @click="detail.open = false" class="w-9 h-9 rounded-xl hover:bg-beige-100 text-beige-400 transition-colors flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Amount hero --}}
                        <div class="bg-gradient-to-br from-mint-500 to-mint-600 px-6 py-8 text-center">
                            <p class="text-white/70 text-xs font-black uppercase tracking-widest mb-1">Amount Paid</p>
                            <p class="text-4xl font-black text-white" x-text="'₱' + fmt(detail.payment.amount)"></p>
                            <div class="mt-3 flex items-center justify-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/20 text-white text-xs font-black"
                                      x-text="methodStyle(detail.payment.payment_method).icon + ' ' + methodStyle(detail.payment.payment_method).label"></span>
                            </div>
                        </div>

                        {{-- Details body --}}
                        <div class="p-6 space-y-5">
                            {{-- Customer --}}
                            <div>
                                <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2">Customer</p>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-black text-white"
                                         :style="`background: ${avatarColor(detail.payment.customer?.name)}`"
                                         x-text="initials(detail.payment.customer?.name)"></div>
                                    <div>
                                        <p class="font-black text-mint-900 text-sm" x-text="detail.payment.customer?.name ?? 'Unknown'"></p>
                                        <p class="text-xs text-beige-400" x-text="detail.payment.customer?.email ?? ''"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Invoice --}}
                            <div class="bg-beige-50 rounded-2xl p-4">
                                <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2">Applied to Invoice</p>
                                <p class="font-black text-mint-600" x-text="detail.payment.invoice?.invoice_number ?? '—'"></p>
                                <div class="grid grid-cols-2 gap-3 mt-2 text-xs">
                                    <div>
                                        <span class="text-[10px] font-bold text-beige-400 uppercase tracking-widest block">Total</span>
                                        <span class="font-black text-mint-900" x-text="'₱' + fmt(detail.payment.invoice?.total_amount)"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold text-beige-400 uppercase tracking-widest block">Remaining</span>
                                        <span class="font-black" :class="(detail.payment.invoice?.balance ?? 0) > 0 ? 'text-red-500' : 'text-mint-600'"
                                              x-text="'₱' + fmt(detail.payment.invoice?.balance ?? 0)"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Meta info --}}
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-beige-100">
                                    <span class="text-xs font-bold text-beige-400 uppercase tracking-widest">Date</span>
                                    <span class="text-sm font-bold text-mint-900" x-text="fmtDate(detail.payment.payment_date)"></span>
                                </div>
                                <template x-if="detail.payment.reference_number">
                                    <div class="flex justify-between items-center py-2 border-b border-beige-100">
                                        <span class="text-xs font-bold text-beige-400 uppercase tracking-widest">Reference #</span>
                                        <span class="text-sm font-mono font-bold text-mint-900" x-text="detail.payment.reference_number"></span>
                                    </div>
                                </template>
                                <template x-if="detail.payment.notes">
                                    <div class="py-2">
                                        <span class="text-xs font-bold text-beige-400 uppercase tracking-widest block mb-1">Notes</span>
                                        <p class="text-sm text-mint-800 font-medium" x-text="detail.payment.notes"></p>
                                    </div>
                                </template>
                            </div>

                            {{-- Actions --}}
                            <button @click="detail.open = false; confirmDelete(detail.payment)"
                                    class="w-full flex items-center justify-center gap-2 py-3 rounded-2xl bg-red-50 border border-red-200 text-red-600 text-sm font-black hover:bg-red-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Delete Payment
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- ══════════════════════════════════════════════════════════════
         DELETE CONFIRMATION MODAL
    ══════════════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="deleteModal.open" style="display:none" class="fixed inset-0 z-[900] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-red-950/40 backdrop-blur-sm"
                 x-show="deleteModal.open"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="deleteModal.open = false"></div>

            <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl border border-red-200/50 overflow-hidden"
                 x-show="deleteModal.open"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
                <div class="p-8 text-center">
                    <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-5">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-2xl font-black text-red-950 mb-2">Delete Payment?</h3>
                    <p class="text-sm font-medium text-beige-500 mb-2">
                        This will permanently remove this payment of
                        <strong class="text-red-700" x-text="'₱' + fmt(deleteModal.amount)"></strong>
                        and revert the invoice balance. This cannot be undone.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 mt-6">
                        <button @click="deleteModal.open = false"
                                class="flex-1 px-6 py-3.5 rounded-2xl text-sm font-black text-beige-600 bg-beige-50 hover:bg-beige-100 transition-colors uppercase tracking-widest">
                            Cancel
                        </button>
                        <button @click="executeDelete()"
                                :disabled="deleteModal.deleting"
                                class="flex-1 px-6 py-3.5 rounded-2xl text-sm font-black text-white bg-red-500 hover:bg-red-600 shadow-lg shadow-red-500/20 transition-all disabled:opacity-70 uppercase tracking-widest flex items-center justify-center gap-2">
                            <template x-if="deleteModal.deleting">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            </template>
                            <span x-text="deleteModal.deleting ? 'Deleting…' : 'Delete Payment'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection

@push('scripts')
<script>
function paymentDashboard() {
    return {
        // ── State ──────────────────────────────────────────────────────
        payments:    [],
        invoices:    [],
        analytics:   {},
        loading:     true,
        selectedIds: [],
        pagination:  { current_page: 1, last_page: 1, total: 0, per_page: 15, from: 0, to: 0 },
        toasts:      [],
        _toastId:    0,

        filters: { search: '', method: '', from: '', to: '' },
        sort:    { by: 'payment_date', dir: 'desc' },

        paymentMethods: [
            { value: 'cash',         label: 'Cash',         icon: '💵' },
            { value: 'gcash',        label: 'GCash',        icon: '📱' },
            { value: 'bank_transfer',label: 'Bank',         icon: '🏦' },
            { value: 'credit_card',  label: 'Card',         icon: '💳' },
            { value: 'check',        label: 'Check',        icon: '📄' },
            { value: 'store_credit', label: 'Store Credit', icon: '🏪' },
        ],

        modal: { open: false, submitting: false },
        form: {
            invoice_id: '', amount: '', payment_method: 'cash',
            reference_number: '', payment_date: new Date().toISOString().split('T')[0], notes: '',
        },
        formErrors:      {},
        selectedInvoice: null,
        cashChange:      0,

        detail: { open: false, payment: null },

        deleteModal: { open: false, paymentId: null, amount: 0, deleting: false },

        // ── Init ───────────────────────────────────────────────────────
        init() {
            this.loadData();
            this.loadAnalytics();
        },

        // ── Computed: Page Range ───────────────────────────────────────
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

        // ── Data Loading ───────────────────────────────────────────────
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
                const res  = await fetch('/payments?' + params, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const json = await res.json();
                this.payments   = json.data;
                this.invoices   = json.invoices ?? this.invoices;
                this.pagination = json.pagination;
                this.selectedIds = [];
            } catch {
                this.toast('Failed to load payments.', 'error');
            } finally {
                this.loading = false;
            }
        },

        async loadAnalytics() {
            try {
                const res = await fetch('/payments/analytics', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                this.analytics = await res.json();
            } catch { /* silent */ }
        },

        // ── Sort ───────────────────────────────────────────────────────
        sortBy(col) {
            if (this.sort.by === col) this.sort.dir = this.sort.dir === 'asc' ? 'desc' : 'asc';
            else { this.sort.by = col; this.sort.dir = 'desc'; }
            this.loadData(1);
        },
        sortIcon(col) {
            if (this.sort.by !== col) return '↕';
            return this.sort.dir === 'asc' ? '↑' : '↓';
        },

        // ── Filters ────────────────────────────────────────────────────
        clearFilters() {
            this.filters = { search: '', method: '', from: '', to: '' };
            this.loadData(1);
        },

        // ── Selection ──────────────────────────────────────────────────
        toggleSelect(id) {
            const idx = this.selectedIds.indexOf(id);
            if (idx === -1) this.selectedIds.push(id);
            else            this.selectedIds.splice(idx, 1);
        },
        toggleSelectAll(e) {
            this.selectedIds = e.target.checked ? this.payments.map(p => p.id) : [];
        },

        // ── Modal: Record Payment ──────────────────────────────────────
        openCreate() {
            this.formErrors      = {};
            this.selectedInvoice = null;
            this.cashChange      = 0;
            this.form = {
                invoice_id: '', amount: '', payment_method: 'cash',
                reference_number: '', payment_date: new Date().toISOString().split('T')[0], notes: '',
            };
            this.modal.open = true;
        },

        onInvoiceChange() {
            const inv = this.invoices.find(i => String(i.id) === String(this.form.invoice_id));
            this.selectedInvoice = inv || null;
            if (inv) this.form.amount = inv.balance.toFixed(2);
            this.calcChange();
        },

        calcChange() {
            if (!this.selectedInvoice) { this.cashChange = 0; return; }
            this.cashChange = parseFloat(this.form.amount || 0) - this.selectedInvoice.balance;
        },

        async submitForm() {
            if (this.modal.submitting) return;
            this.modal.submitting = true;
            this.formErrors = {};

            try {
                const res = await fetch('/payments', {
                    method:  'POST',
                    headers: {
                        'Content-Type':     'application/json',
                        'Accept':           'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.form),
                });
                const data = await res.json();

                if (!res.ok) {
                    if (res.status === 422) {
                        this.formErrors = data.errors;
                        this.toast('Please fix the errors highlighted below.', 'error');
                    } else {
                        this.toast(data.message || 'An error occurred.', 'error');
                    }
                } else {
                    this.modal.open = false;
                    this.toast(data.message || 'Payment Recorded Successfully!', 'success');
                    this.loadData(1);
                    this.loadAnalytics();
                    // Refresh invoices list for next modal open
                    this.invoices = this.invoices.filter(i => i.id !== this.form.invoice_id)
                        .concat(
                            data.payment?.invoice?.balance > 0
                                ? [{ ...this.invoices.find(i => String(i.id) === String(this.form.invoice_id)), balance: data.payment.invoice.balance }]
                                : []
                        );
                }
            } catch {
                this.toast('Network error. Please try again.', 'error');
            } finally {
                this.modal.submitting = false;
            }
        },

        // ── Detail Drawer ──────────────────────────────────────────────
        openDetail(payment) {
            this.detail.payment = payment;
            this.detail.open    = true;
        },

        // ── Delete ─────────────────────────────────────────────────────
        confirmDelete(payment) {
            this.deleteModal = {
                open: true, paymentId: payment.id,
                amount: payment.amount, deleting: false,
            };
        },

        async executeDelete() {
            if (this.deleteModal.deleting) return;
            this.deleteModal.deleting = true;
            try {
                const res = await fetch(`/payments/${this.deleteModal.paymentId}`, {
                    method:  'DELETE',
                    headers: {
                        'Accept':           'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const data = await res.json();
                if (res.ok) {
                    this.deleteModal.open = false;
                    this.toast(data.message || 'Payment deleted.', 'success');
                    this.loadData(this.pagination.current_page);
                    this.loadAnalytics();
                } else {
                    this.toast(data.message || 'Failed to delete payment.', 'error');
                }
            } catch {
                this.toast('Network error.', 'error');
            } finally {
                this.deleteModal.deleting = false;
            }
        },

        // ── Bulk Delete ────────────────────────────────────────────────
        async bulkDelete() {
            if (!this.selectedIds.length) return;
            if (!confirm(`Delete ${this.selectedIds.length} payment(s)? Invoice balances will be reverted. This cannot be undone.`)) return;
            const token = document.querySelector('meta[name="csrf-token"]').content;
            try {
                await Promise.all(this.selectedIds.map(id =>
                    fetch(`/payments/${id}`, {
                        method:  'DELETE',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
                    })
                ));
                this.toast(`${this.selectedIds.length} payment(s) deleted.`, 'success');
                this.selectedIds = [];
                this.loadData(1);
                this.loadAnalytics();
            } catch {
                this.toast('Bulk delete failed.', 'error');
            }
        },

        // ── Toasts ─────────────────────────────────────────────────────
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

        // ── UI Helpers ─────────────────────────────────────────────────
        methodStyle(method) {
            const styles = {
                cash:         { icon: '💵', label: 'Cash',         badge: 'bg-mint-100 text-mint-700' },
                gcash:        { icon: '📱', label: 'GCash',        badge: 'bg-blue-100 text-blue-700' },
                bank_transfer:{ icon: '🏦', label: 'Bank',         badge: 'bg-purple-100 text-purple-700' },
                credit_card:  { icon: '💳', label: 'Card',         badge: 'bg-pink-100 text-pink-700' },
                check:        { icon: '📄', label: 'Check',        badge: 'bg-amber-100 text-amber-700' },
                store_credit: { icon: '🏪', label: 'Store Credit', badge: 'bg-orange-100 text-orange-700' },
            };
            return styles[method] || { icon: '💰', label: method, badge: 'bg-beige-100 text-beige-700' };
        },

        fmt(n) {
            return Number(n ?? 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

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

        initials(name) {
            if (!name) return '?';
            return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
        },

        avatarColor(name) {
            if (!name) return '#6b7280';
            const colors = ['#10b981','#059669','#047857','#f59e0b','#d97706','#3b82f6','#2563eb','#8b5cf6','#7c3aed','#ef4444','#ec4899','#db2777'];
            let hash = 0;
            for (const ch of name) hash = ch.charCodeAt(0) + ((hash << 5) - hash);
            return colors[Math.abs(hash) % colors.length];
        },
    };
}
</script>
@endpush
