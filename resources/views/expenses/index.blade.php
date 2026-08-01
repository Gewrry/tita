@extends('layouts.app')
@section('title', 'Expense Management')
@section('page-title', 'Business Expenses')

@section('content')
<div x-data="expenseDashboard()" x-init="init()" class="space-y-6">

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

        {{-- This Month --}}
        <div class="bg-gradient-to-br from-mint-500 to-mint-600 rounded-2xl p-5 shadow-sm hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-[10px] font-black text-white/60 uppercase tracking-widest">Month</span>
            </div>
            <div class="text-2xl font-black text-white leading-none" x-text="'₱' + fmt(analytics.total_this_month ?? 0)"></div>
            <div class="text-xs font-bold text-white/60 mt-1.5 uppercase tracking-widest">Expenses This Month</div>
        </div>

        {{-- Total All Time --}}
        <div class="bg-white border border-beige-200/70 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-beige-50 border border-beige-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-beige-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-black text-beige-400 uppercase tracking-widest">All Time</span>
            </div>
            <div class="text-2xl font-black text-mint-900 leading-none" x-text="'₱' + fmt(analytics.total_expenses ?? 0)"></div>
            <div class="text-xs font-bold text-beige-400 mt-1.5 uppercase tracking-widest">Total Expenses</div>
        </div>

        {{-- Top Category (Month) --}}
        <div class="bg-white border border-beige-200/70 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                </div>
                <span class="text-[10px] font-black text-beige-400 uppercase tracking-widest">Top Cat</span>
            </div>
            <div class="text-xl font-black text-mint-900 leading-none truncate" x-text="analytics.top_category ?? '—'"></div>
            <div class="text-xs font-bold text-beige-400 mt-1.5 uppercase tracking-widest">
                <span x-show="(analytics.top_category_amount ?? 0) > 0">₱<span x-text="fmt(analytics.top_category_amount)"></span></span>
                <span x-show="!(analytics.top_category_amount ?? 0 > 0)">This Month</span>
            </div>
        </div>

        {{-- Total Count --}}
        <div class="bg-white border border-beige-200/70 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-[10px] font-black text-beige-400 uppercase tracking-widest">Records</span>
            </div>
            <div class="text-2xl font-black text-mint-900 leading-none" x-text="analytics.total_count ?? '—'"></div>
            <div class="text-xs font-bold text-beige-400 mt-1.5 uppercase tracking-widest">Recorded Expenses</div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         CONTROLS: Filters + Bulk Actions + Record Button
    ══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col gap-3">
        {{-- Row 1: Search + Category + Date Range --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 flex-wrap">
            {{-- Search --}}
            <div class="relative flex-1 min-w-0 w-full sm:w-auto">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-beige-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="filters.search" @input.debounce.300ms="loadData(1)"
                       placeholder="Search expenses…"
                       class="w-full pl-11 pr-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm outline-none">
            </div>

            {{-- Category Filter --}}
            <select x-model="filters.category" @change="loadData(1)"
                    class="w-full sm:w-auto px-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-mint-800 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm outline-none">
                <option value="">All Categories</option>
                <template x-for="cat in categories" :key="cat.value">
                    <option :value="cat.value" x-text="cat.label"></option>
                </template>
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
            <template x-if="filters.search || filters.category || filters.from || filters.to">
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

            <button @click="openModal()"
                    class="btn-mint shadow-lg shadow-mint-900/10 whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Record Expense
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         DATA GRID — Desktop
    ══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-beige-200/60 rounded-3xl shadow-sm overflow-hidden">

        {{-- Desktop table --}}
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-sm min-w-[800px]">
                <thead>
                    <tr class="bg-beige-50/70 border-b border-beige-100">
                        <th class="w-10 pl-5 py-4">
                            <input type="checkbox" @change="toggleSelectAll($event)"
                                   :checked="expenses.length > 0 && selectedIds.length === expenses.length"
                                   class="w-4 h-4 rounded accent-mint-500">
                        </th>
                        <th @click="sortBy('expense_date')" class="text-left px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest cursor-pointer select-none hover:text-mint-600 transition-colors">
                            <span class="flex items-center gap-1">Date <span x-text="sortIcon('expense_date')"></span></span>
                        </th>
                        <th @click="sortBy('description')" class="text-left px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest cursor-pointer select-none hover:text-mint-600 transition-colors">
                            <span class="flex items-center gap-1">Description <span x-text="sortIcon('description')"></span></span>
                        </th>
                        <th @click="sortBy('category')" class="text-left px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest cursor-pointer select-none hover:text-mint-600 transition-colors">
                            <span class="flex items-center gap-1">Category <span x-text="sortIcon('category')"></span></span>
                        </th>
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
                                    <div class="flex flex-col gap-1.5">
                                        <div class="h-4 w-48 bg-beige-100 rounded"></div>
                                        <div class="h-3 w-32 bg-beige-100 rounded"></div>
                                    </div>
                                </td>
                                <td class="px-4 py-5"><div class="h-6 w-24 bg-beige-100 rounded-lg"></div></td>
                                <td class="px-4 py-5 text-right"><div class="h-4 w-20 bg-beige-100 rounded ml-auto"></div></td>
                                <td class="pr-5 py-5 text-right"><div class="h-8 w-20 bg-beige-100 rounded-xl ml-auto"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Empty state --}}
                    <template x-if="!loading && expenses.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="w-20 h-20 bg-beige-50 rounded-full flex items-center justify-center mx-auto mb-5">
                                    <svg class="w-10 h-10 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-base font-black text-mint-900">No expense records found</p>
                                <p class="text-sm font-medium text-beige-400 mt-1 mb-5">Expenses recorded in the system will appear here</p>
                                <button @click="openModal()" class="btn-mint text-sm px-5 py-2.5">Record First Expense</button>
                            </td>
                        </tr>
                    </template>

                    {{-- Data rows --}}
                    <template x-if="!loading">
                        <template x-for="e in expenses" :key="e.id">
                            <tr class="hover:bg-beige-50/60 transition-colors group">
                                <td class="pl-5 py-4 w-10">
                                    <input type="checkbox" :value="e.id"
                                           :checked="selectedIds.includes(e.id)"
                                           @change="toggleSelect(e.id)"
                                           class="w-4 h-4 rounded accent-mint-500">
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-bold text-mint-900" x-text="fmtDate(e.expense_date)"></div>
                                    <div class="text-xs text-beige-400 font-medium" x-text="relDate(e.expense_date)"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-mint-900 text-sm truncate max-w-[250px]" x-text="e.description"></div>
                                    <template x-if="e.notes">
                                        <div class="text-xs text-beige-400 font-medium mt-0.5 truncate max-w-[250px]" x-text="e.notes"></div>
                                    </template>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-black uppercase tracking-wide"
                                          :class="categoryStyle(e.category).badge">
                                        <span x-text="categoryStyle(e.category).icon"></span>
                                        <span x-text="e.category_name"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="font-black text-mint-600 text-sm" x-text="'₱' + fmt(e.amount)"></span>
                                </td>
                                <td class="pr-5 py-4">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button @click="openModal(e)"
                                                class="p-2 rounded-xl bg-white border border-beige-200 text-mint-600 hover:bg-mint-50 hover:border-mint-200 transition-all shadow-sm" title="Edit Expense">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button @click="confirmDelete(e)"
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
                        <div class="flex items-start gap-3">
                            <div class="flex-1 space-y-2">
                                <div class="h-4 bg-beige-100 rounded w-3/4"></div>
                                <div class="h-3 bg-beige-100 rounded w-1/2"></div>
                            </div>
                            <div class="h-5 w-16 bg-beige-100 rounded"></div>
                        </div>
                        <div class="flex gap-2">
                            <div class="h-6 w-20 bg-beige-100 rounded-lg"></div>
                            <div class="h-6 w-24 bg-beige-100 rounded-lg"></div>
                        </div>
                    </div>
                </template>
            </template>

            <template x-if="!loading && expenses.length === 0">
                <div class="px-6 py-16 text-center">
                    <div class="w-16 h-16 bg-beige-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="font-black text-mint-900">No expenses found</p>
                    <button @click="openModal()" class="btn-mint text-sm mt-4 px-5 py-2.5">Record Expense</button>
                </div>
            </template>

            <template x-if="!loading">
                <template x-for="e in expenses" :key="e.id + '-m'">
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="font-black text-mint-900 text-sm truncate" x-text="e.description"></div>
                                <div class="text-xs text-beige-400 font-medium mt-0.5 truncate" x-show="e.notes" x-text="e.notes"></div>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-black uppercase"
                                          :class="categoryStyle(e.category).badge">
                                        <span x-text="categoryStyle(e.category).icon"></span>
                                        <span x-text="e.category_name"></span>
                                    </span>
                                    <span class="text-xs text-beige-400 font-medium" x-text="fmtDate(e.expense_date)"></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-black text-mint-600 text-sm" x-text="'₱' + fmt(e.amount)"></div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-beige-100">
                            <button @click="openModal(e)" class="flex-1 py-2.5 rounded-xl bg-mint-50 text-mint-700 text-xs font-black hover:bg-mint-100 transition-colors">Edit</button>
                            <button @click="confirmDelete(e)" class="py-2.5 px-3 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
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
                    of <span class="text-mint-700" x-text="pagination.total"></span> expenses
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
         CREATE / EDIT MODAL
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
                        <h3 class="text-xl font-black text-mint-950" x-text="modal.isEdit ? 'Edit Expense' : 'Record Expense'"></h3>
                        <p class="text-[10px] font-bold text-beige-400 uppercase tracking-widest mt-0.5">Enter the details of your business expense</p>
                    </div>
                    <button @click="modal.open = false" class="w-9 h-9 rounded-xl hover:bg-red-50 text-beige-400 hover:text-red-500 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="overflow-y-auto flex-1 p-7">
                    <div class="space-y-5">
                        
                        {{-- Product (Optional) --}}
                        <div>
                            <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">
                                Product (Optional)
                            </label>
                            <select x-model="form.product_id" @change="onProductSelect()"
                                    :class="formErrors.product_id ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                                    class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 transition-all shadow-sm outline-none">
                                <option value="">— Generic Expense (Manual Description) —</option>
                                <template x-for="p in products" :key="p.id">
                                    <option :value="p.id" x-text="p.name"></option>
                                </template>
                            </select>
                            <p x-show="formErrors.product_id" x-text="formErrors.product_id?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>
                        </div>

                        {{-- Description & Amount --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="form.description" placeholder="e.g. Office Supplies, Electric Bill"
                                       :class="formErrors.description ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                                       class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 transition-all shadow-sm outline-none">
                                <p x-show="formErrors.description" x-text="formErrors.description?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">
                                    Amount <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 font-black text-beige-400 text-sm">₱</span>
                                    <input type="number" x-model="form.amount" step="0.01" min="0.01" placeholder="0.00"
                                           :class="formErrors.amount ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                                           class="w-full pl-7 pr-3 py-3 bg-white border rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 transition-all shadow-sm outline-none">
                                </div>
                                <p x-show="formErrors.amount" x-text="formErrors.amount?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>
                            </div>
                        </div>

                        {{-- Category & Date --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select x-model="form.category"
                                        :class="formErrors.category ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                                        class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 transition-all shadow-sm outline-none">
                                    <option value="">— Select Category —</option>
                                    <template x-for="cat in categories" :key="cat.value">
                                        <option :value="cat.value" x-text="cat.label"></option>
                                    </template>
                                </select>
                                <p x-show="formErrors.category" x-text="formErrors.category?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">
                                    Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" x-model="form.expense_date"
                                       :class="formErrors.expense_date ? 'border-red-400' : 'border-beige-200 focus:border-mint-500'"
                                       class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 transition-all shadow-sm outline-none">
                                <p x-show="formErrors.expense_date" x-text="formErrors.expense_date?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">Notes (Optional)</label>
                            <textarea x-model="form.notes" rows="2" placeholder="Any additional notes…"
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
                        <span x-text="modal.submitting ? 'Saving…' : (modal.isEdit ? 'Update Expense' : 'Save Expense')"></span>
                    </button>
                    <button @click="modal.open = false" class="px-6 py-3 text-xs font-black text-mint-800 uppercase tracking-widest hover:bg-beige-100 rounded-2xl transition-all w-full sm:w-auto">
                        Cancel
                    </button>
                </div>
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
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-2xl font-black text-red-950 mb-2">Delete Expense?</h3>
                    <p class="text-sm font-medium text-beige-500 mb-2">
                        Are you sure you want to delete the expense <strong class="text-red-700" x-text="deleteModal.description"></strong>? This cannot be undone.
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
                            <span x-text="deleteModal.deleting ? 'Deleting…' : 'Delete'"></span>
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
function expenseDashboard() {
    return {
        // ── State ──────────────────────────────────────────────────────
        expenses:    [],
        products:    @json($products ?? []),
        categories:  @json(collect($categories)->map(fn($l,$v)=>['value'=>$v,'label'=>$l])->values()),
        analytics:   {},
        loading:     true,
        selectedIds: [],
        pagination:  { current_page: 1, last_page: 1, total: 0, per_page: 15, from: 0, to: 0 },
        toasts:      [],
        _toastId:    0,

        filters: { search: '', category: '', from: '', to: '' },
        sort:    { by: 'expense_date', dir: 'desc' },

        modal: { open: false, isEdit: false, submitting: false, id: null },
        form: {
            product_id: '', description: '', amount: '', category: '',
            expense_date: new Date().toISOString().split('T')[0], notes: '',
        },
        formErrors: {},

        deleteModal: { open: false, id: null, description: '', deleting: false },

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
                const res  = await fetch('/expenses?' + params, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const json = await res.json();
                this.expenses   = json.data;
                this.pagination = json.pagination;
                if(json.categories) this.categories = json.categories;
                this.selectedIds = [];
            } catch {
                this.toast('Failed to load expenses.', 'error');
            } finally {
                this.loading = false;
            }
        },

        async loadAnalytics() {
            try {
                const res = await fetch('/expenses/analytics', {
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
            this.filters = { search: '', category: '', from: '', to: '' };
            this.loadData(1);
        },

        // ── Selection ──────────────────────────────────────────────────
        toggleSelect(id) {
            const idx = this.selectedIds.indexOf(id);
            if (idx === -1) this.selectedIds.push(id);
            else            this.selectedIds.splice(idx, 1);
        },
        toggleSelectAll(e) {
            this.selectedIds = e.target.checked ? this.expenses.map(p => p.id) : [];
        },

        // ── Modal: Create / Edit ───────────────────────────────────────
        openModal(expense = null) {
            this.formErrors = {};
            this.modal.isEdit = !!expense;
            if (expense) {
                this.modal.id = expense.id;
                this.form = {
                    product_id:   expense.product_id || '',
                    description:  expense.description,
                    amount:       expense.amount,
                    category:     expense.category,
                    expense_date: expense.expense_date,
                    notes:        expense.notes || '',
                };
            } else {
                this.modal.id = null;
                this.form = {
                    product_id: '', description: '', amount: '', category: '',
                    expense_date: new Date().toISOString().split('T')[0], notes: '',
                };
            }
            this.modal.open = true;
        },

        onProductSelect() {
            if (!this.form.product_id) return;
            const p = this.products.find(x => String(x.id) === String(this.form.product_id));
            if (p) {
                this.form.description = p.name;
                // Optional: this.form.amount = p.cost_price;
            }
        },

        async submitForm() {
            if (this.modal.submitting) return;
            this.modal.submitting = true;
            this.formErrors = {};

            const url = this.modal.isEdit ? `/expenses/${this.modal.id}` : '/expenses';
            const method = this.modal.isEdit ? 'PUT' : 'POST';

            try {
                const res = await fetch(url, {
                    method:  method,
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
                    this.toast(data.message || 'Expense Saved Successfully!', 'success');
                    this.loadData(this.modal.isEdit ? this.pagination.current_page : 1);
                    this.loadAnalytics();
                }
            } catch {
                this.toast('Network error. Please try again.', 'error');
            } finally {
                this.modal.submitting = false;
            }
        },

        // ── Delete ─────────────────────────────────────────────────────
        confirmDelete(expense) {
            this.deleteModal = {
                open: true, id: expense.id,
                description: expense.description, deleting: false,
            };
        },

        async executeDelete() {
            if (this.deleteModal.deleting) return;
            this.deleteModal.deleting = true;
            try {
                const res = await fetch(`/expenses/${this.deleteModal.id}`, {
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
                    this.toast(data.message || 'Expense deleted.', 'success');
                    this.loadData(this.pagination.current_page);
                    this.loadAnalytics();
                } else {
                    this.toast(data.message || 'Failed to delete expense.', 'error');
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
            if (!confirm(`Delete ${this.selectedIds.length} expense(s)? This cannot be undone.`)) return;
            const token = document.querySelector('meta[name="csrf-token"]').content;
            try {
                await Promise.all(this.selectedIds.map(id =>
                    fetch(`/expenses/${id}`, {
                        method:  'DELETE',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
                    })
                ));
                this.toast(`${this.selectedIds.length} expense(s) deleted.`, 'success');
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
        categoryStyle(category) {
            const styles = {
                supplies:       { icon: '🖇️', badge: 'bg-blue-100 text-blue-700' },
                rent:           { icon: '🏢', badge: 'bg-purple-100 text-purple-700' },
                salary:         { icon: '👩‍💼', badge: 'bg-emerald-100 text-emerald-700' },
                utilities:      { icon: '⚡', badge: 'bg-amber-100 text-amber-700' },
                transportation: { icon: '🚗', badge: 'bg-orange-100 text-orange-700' },
                food:           { icon: '🍔', badge: 'bg-red-100 text-red-700' },
                other:          { icon: '📦', badge: 'bg-beige-100 text-beige-700' },
            };
            return styles[category] || { icon: '💰', badge: 'bg-beige-100 text-beige-700' };
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
    };
}
</script>
@endpush
