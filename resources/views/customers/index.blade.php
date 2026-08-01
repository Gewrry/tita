@extends('layouts.app')
@section('title', 'Customer Directory')
@section('page-title', 'Customer Directory')

@section('content')
<div x-data="customerDashboard()" x-init="init()" class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════
         TOAST SYSTEM
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
                    'bg-white border-red-200 text-red-700': t.type==='error',
                    'bg-white border-amber-200 text-amber-700': t.type==='warning',
                    'bg-white border-blue-200 text-blue-700': t.type==='info'
                 }">
                <span class="text-lg leading-none mt-0.5"
                      x-text="t.type==='success'?'✅':t.type==='error'?'❌':t.type==='warning'?'⚠️':'ℹ️'"></span>
                <span class="flex-1" x-text="t.message"></span>
                <button @click="dismissToast(t.id)" class="text-current opacity-50 hover:opacity-100 transition-opacity ml-1">✕</button>
            </div>
        </template>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         KPI ANALYTICS CARDS
    ══════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Customers --}}
        <div class="bg-white border border-beige-200/70 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-mint-50 border border-mint-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-[10px] font-black text-beige-400 uppercase tracking-widest">Total</span>
            </div>
            <div class="text-3xl font-black text-mint-900 leading-none" x-text="analytics.total ?? '—'"></div>
            <div class="text-xs font-bold text-beige-400 mt-1.5 uppercase tracking-widest">Customers</div>
        </div>

        {{-- New This Month --}}
        <div class="bg-white border border-beige-200/70 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <span class="text-[10px] font-black text-beige-400 uppercase tracking-widest">This Month</span>
            </div>
            <div class="text-3xl font-black text-mint-900 leading-none" x-text="analytics.new_this_month ?? '—'"></div>
            <div class="text-xs font-bold text-beige-400 mt-1.5 uppercase tracking-widest">New Clients</div>
        </div>

        {{-- Total Revenue --}}
        <div class="bg-gradient-to-br from-mint-500 to-mint-600 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-black text-white/60 uppercase tracking-widest">Revenue</span>
            </div>
            <div class="text-3xl font-black text-white leading-none" x-text="'₱' + fmt(analytics.total_revenue ?? 0)"></div>
            <div class="text-xs font-bold text-white/60 mt-1.5 uppercase tracking-widest">Total Billed</div>
        </div>

        {{-- Outstanding Balance --}}
        <div class="bg-white border border-beige-200/70 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-[10px] font-black text-beige-400 uppercase tracking-widest">Balance</span>
            </div>
            <div class="text-3xl font-black text-red-500 leading-none" x-text="'₱' + fmt(analytics.outstanding ?? 0)"></div>
            <div class="text-xs font-bold text-beige-400 mt-1.5 uppercase tracking-widest">Outstanding</div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         CONTROLS ROW: Search + Filters + Actions
    ══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        {{-- Search --}}
        <div class="relative flex-1 w-full">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-beige-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" x-model="filters.search" @input.debounce.300ms="loadData(1)"
                   placeholder="Search by name, email or phone…"
                   class="w-full pl-11 pr-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
        </div>

        {{-- Bulk Actions (only shown when rows selected) --}}
        <template x-if="selectedIds.length > 0">
            <div class="flex items-center gap-2 animate-fade-in">
                <span class="text-xs font-black text-mint-700 bg-mint-50 border border-mint-200 rounded-xl px-3 py-2"
                      x-text="selectedIds.length + ' selected'"></span>
                <button @click="bulkDelete()"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs font-black uppercase tracking-widest hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </div>
        </template>

        {{-- New Customer Button --}}
        <button @click="openCreate()"
                class="btn-mint shadow-lg shadow-mint-900/10 whitespace-nowrap w-full sm:w-auto justify-center flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            New Customer
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         DATA GRID (Desktop)
    ══════════════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-beige-200/60 rounded-3xl shadow-sm overflow-hidden">

        {{-- Table header - desktop --}}
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-sm min-w-[900px]">
                <thead>
                    <tr class="bg-beige-50/70 border-b border-beige-100">
                        <th class="w-10 pl-5 py-4">
                            <input type="checkbox" @change="toggleSelectAll($event)"
                                   :checked="customers.length > 0 && selectedIds.length === customers.length"
                                   class="w-4 h-4 rounded accent-mint-500">
                        </th>
                        <th @click="sortBy('name')" class="text-left px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest cursor-pointer select-none hover:text-mint-600 transition-colors">
                            <span class="flex items-center gap-1">Customer <span x-text="sortIcon('name')"></span></span>
                        </th>
                        <th class="text-left px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest">Contact</th>
                        <th @click="sortBy('total_billed')" class="text-right px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest cursor-pointer select-none hover:text-mint-600 transition-colors">
                            <span class="flex items-center justify-end gap-1">Total Billed <span x-text="sortIcon('total_billed')"></span></span>
                        </th>
                        <th class="text-right px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest">Outstanding</th>
                        <th @click="sortBy('last_invoice_date')" class="text-left px-4 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest cursor-pointer select-none hover:text-mint-600 transition-colors">
                            <span class="flex items-center gap-1">Last Invoice <span x-text="sortIcon('last_invoice_date')"></span></span>
                        </th>
                        <th class="text-right pr-5 py-4 text-[11px] font-black text-beige-500 uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-beige-100">

                    {{-- Loading skeletons --}}
                    <template x-if="loading">
                        <template x-for="i in 6" :key="i">
                            <tr class="animate-pulse">
                                <td class="pl-5 py-5"><div class="w-4 h-4 bg-beige-100 rounded"></div></td>
                                <td class="px-4 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-beige-100"></div>
                                        <div class="space-y-1.5">
                                            <div class="h-3.5 w-32 bg-beige-100 rounded"></div>
                                            <div class="h-2.5 w-20 bg-beige-100 rounded"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-5"><div class="space-y-1.5"><div class="h-3 w-36 bg-beige-100 rounded"></div><div class="h-2.5 w-24 bg-beige-100 rounded"></div></div></td>
                                <td class="px-4 py-5 text-right"><div class="h-3.5 w-20 bg-beige-100 rounded ml-auto"></div></td>
                                <td class="px-4 py-5 text-right"><div class="h-3.5 w-20 bg-beige-100 rounded ml-auto"></div></td>
                                <td class="px-4 py-5"><div class="h-3 w-24 bg-beige-100 rounded"></div></td>
                                <td class="pr-5 py-5 text-right"><div class="h-8 w-24 bg-beige-100 rounded-xl ml-auto"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Empty state --}}
                    <template x-if="!loading && customers.length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="w-20 h-20 bg-beige-50 rounded-full flex items-center justify-center mx-auto mb-5">
                                    <svg class="w-10 h-10 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <p class="text-base font-black text-mint-900">No customers found</p>
                                <p class="text-sm font-medium text-beige-400 mt-1 mb-5">Start by adding your first client to the system</p>
                                <button @click="openCreate()" class="btn-mint text-sm px-5 py-2.5">
                                    Add Your First Customer
                                </button>
                            </td>
                        </tr>
                    </template>

                    {{-- Data rows --}}
                    <template x-if="!loading">
                        <template x-for="c in customers" :key="c.id">
                            <tr class="hover:bg-beige-50/60 transition-colors group">
                                <td class="pl-5 py-4 w-10">
                                    <input type="checkbox" :value="c.id"
                                           :checked="selectedIds.includes(c.id)"
                                           @change="toggleSelect(c.id)"
                                           class="w-4 h-4 rounded accent-mint-500">
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-black text-white flex-shrink-0 transition-transform group-hover:scale-105"
                                             :style="`background: ${avatarColor(c.name)}`"
                                             x-text="initials(c.name)"></div>
                                        <div class="min-w-0">
                                            <a :href="`/customers/${c.id}`"
                                               class="block font-black text-mint-900 hover:text-mint-600 transition-colors truncate text-sm"
                                               x-text="c.name"></a>
                                            <span class="text-[11px] font-bold text-beige-400 uppercase tracking-tight"
                                                  x-text="c.invoices_count + ' invoice' + (c.invoices_count !== 1 ? 's' : '')"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-semibold text-mint-800 truncate max-w-[200px]" x-text="c.email || '—'"></p>
                                    <p class="text-xs font-bold text-beige-400 mt-0.5" x-text="c.phone || ''"></p>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="font-black text-sm text-mint-900" x-text="'₱' + fmt(c.total_billed)"></span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <span class="font-black text-sm"
                                          :class="c.outstanding > 0 ? 'text-red-500' : 'text-beige-400'"
                                          x-text="c.outstanding > 0 ? '₱' + fmt(c.outstanding) : '—'"></span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-xs font-semibold text-beige-500"
                                          x-text="c.last_invoice_date ? relDate(c.last_invoice_date) : '—'"></span>
                                </td>
                                <td class="pr-5 py-4">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a :href="`/customers/${c.id}`"
                                           class="p-2 rounded-xl bg-white border border-beige-200 text-mint-600 hover:bg-mint-50 hover:border-mint-200 hover:text-mint-700 transition-all shadow-sm" title="View Profile">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <button @click="openEdit(c)"
                                                class="p-2 rounded-xl bg-white border border-beige-200 text-beige-500 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-200 transition-all shadow-sm" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <a :href="`/customers/${c.id}/soa`"
                                           class="p-2 rounded-xl bg-white border border-beige-200 text-blue-400 hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 transition-all shadow-sm" title="Statement of Account">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </a>
                                        <button @click="confirmDelete(c)"
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

        {{-- ─── Mobile Card View ──────────────────────────────────────── --}}
        <div class="md:hidden divide-y divide-beige-100">
            <template x-if="loading">
                <template x-for="i in 4" :key="i">
                    <div class="p-4 animate-pulse">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 rounded-2xl bg-beige-100 flex-shrink-0"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-4 bg-beige-100 rounded w-3/4"></div>
                                <div class="h-3 bg-beige-100 rounded w-1/2"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="h-10 bg-beige-100 rounded-xl"></div>
                            <div class="h-10 bg-beige-100 rounded-xl"></div>
                        </div>
                    </div>
                </template>
            </template>

            <template x-if="!loading && customers.length === 0">
                <div class="px-6 py-16 text-center">
                    <div class="w-16 h-16 bg-beige-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <p class="font-black text-mint-900">No customers found</p>
                    <button @click="openCreate()" class="btn-mint text-sm mt-4 px-5 py-2.5">Add Customer</button>
                </div>
            </template>

            <template x-if="!loading">
                <template x-for="c in customers" :key="c.id + '-mobile'">
                    <div class="p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-base font-black text-white flex-shrink-0"
                                 :style="`background: ${avatarColor(c.name)}`"
                                 x-text="initials(c.name)"></div>
                            <div class="flex-1 min-w-0">
                                <a :href="`/customers/${c.id}`" class="font-black text-mint-900 hover:text-mint-600 block truncate" x-text="c.name"></a>
                                <p class="text-xs text-beige-400 font-semibold mt-0.5" x-text="c.email || c.phone || '—'"></p>
                            </div>
                            <div class="text-right">
                                <div class="font-black text-sm text-mint-900" x-text="'₱' + fmt(c.total_billed)"></div>
                                <div class="text-xs font-bold mt-0.5"
                                     :class="c.outstanding > 0 ? 'text-red-500' : 'text-beige-400'"
                                     x-text="c.outstanding > 0 ? ('₱' + fmt(c.outstanding) + ' owed') : 'Settled'"></div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-beige-100">
                            <a :href="`/customers/${c.id}`" class="flex-1 py-2.5 rounded-xl bg-mint-50 text-mint-700 text-xs font-black text-center hover:bg-mint-100 transition-colors">View</a>
                            <button @click="openEdit(c)" class="flex-1 py-2.5 rounded-xl bg-amber-50 text-amber-700 text-xs font-black hover:bg-amber-100 transition-colors">Edit</button>
                            <a :href="`/customers/${c.id}/soa`" class="flex-1 py-2.5 rounded-xl bg-blue-50 text-blue-700 text-xs font-black text-center hover:bg-blue-100 transition-colors">SOA</a>
                            <button @click="confirmDelete(c)" class="py-2.5 px-3 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
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
                    of <span class="text-mint-700" x-text="pagination.total"></span> customers
                </p>
                <div class="flex items-center gap-1.5">
                    <button @click="loadData(pagination.current_page - 1)"
                            :disabled="pagination.current_page === 1"
                            class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black border border-beige-200 bg-white text-mint-700 hover:border-mint-300 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                        ‹
                    </button>
                    <template x-for="p in pageRange" :key="p">
                        <button @click="p !== '…' && loadData(p)"
                                :class="p === pagination.current_page
                                    ? 'bg-mint-500 text-white border-mint-500 shadow-md shadow-mint-900/15'
                                    : p === '…' ? 'cursor-default text-beige-300 border-transparent' : 'bg-white text-mint-800 border-beige-200 hover:border-mint-300'"
                                class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black border transition-all"
                                x-text="p">
                        </button>
                    </template>
                    <button @click="loadData(pagination.current_page + 1)"
                            :disabled="pagination.current_page === pagination.last_page"
                            class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black border border-beige-200 bg-white text-mint-700 hover:border-mint-300 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                        ›
                    </button>
                </div>
            </div>
        </template>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         CREATE / EDIT MODAL
    ══════════════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div x-show="modal.open" style="display:none" class="fixed inset-0 z-[800] flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-mint-950/50 backdrop-blur-sm"
                 x-show="modal.open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="modal.open = false"></div>

            {{-- Panel --}}
            <div class="relative w-full max-w-xl bg-white rounded-3xl shadow-2xl border border-beige-200/60 overflow-hidden max-h-[90vh] flex flex-col"
                 x-show="modal.open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4">

                {{-- Header --}}
                <div class="px-7 pt-6 pb-5 border-b border-beige-100 bg-beige-50/60 flex items-center justify-between flex-shrink-0">
                    <div>
                        <h3 class="text-xl font-black text-mint-950"
                            x-text="modal.editId ? 'Update Client Profile' : 'New Client Enrollment'"></h3>
                        <p class="text-[10px] font-bold text-beige-400 uppercase tracking-widest mt-0.5"
                           x-text="modal.editId ? 'Edit customer details' : 'Add a new customer to the directory'"></p>
                    </div>
                    <button @click="modal.open = false" class="w-9 h-9 rounded-xl hover:bg-red-50 text-beige-400 hover:text-red-500 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="overflow-y-auto flex-1 p-7">
                    <div class="space-y-5">

                        {{-- Personal Info --}}
                        <div>
                            <p class="text-[10px] font-black text-mint-600 uppercase tracking-widest mb-3">Personal Information</p>
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">
                                    Full Name / Company <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="form.name" placeholder="e.g. John Doe or Acme Corp"
                                       :class="formErrors.name ? 'border-red-400 focus:border-red-500 focus:ring-red-500/10' : 'border-beige-200 focus:border-mint-500 focus:ring-mint-500/10'"
                                       class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 transition-all shadow-sm outline-none">
                                <p x-show="formErrors.name" x-text="formErrors.name?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>
                            </div>
                        </div>

                        {{-- Contact Info --}}
                        <div>
                            <p class="text-[10px] font-black text-mint-600 uppercase tracking-widest mb-3">Contact Information</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">Email Address</label>
                                    <input type="email" x-model="form.email" placeholder="client@example.com"
                                           :class="formErrors.email ? 'border-red-400 focus:border-red-500 focus:ring-red-500/10' : 'border-beige-200 focus:border-mint-500 focus:ring-mint-500/10'"
                                           class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 transition-all shadow-sm outline-none">
                                    <p x-show="formErrors.email" x-text="formErrors.email?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">Phone Number</label>
                                    <input type="text" x-model="form.phone" placeholder="+63 000 000 0000"
                                           :class="formErrors.phone ? 'border-red-400 focus:border-red-500 focus:ring-red-500/10' : 'border-beige-200 focus:border-mint-500 focus:ring-mint-500/10'"
                                           class="w-full px-4 py-3 bg-white border rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 transition-all shadow-sm outline-none">
                                    <p x-show="formErrors.phone" x-text="formErrors.phone?.[0]" class="text-xs text-red-500 mt-1.5 font-semibold"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Address --}}
                        <div>
                            <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">Physical Address</label>
                            <textarea x-model="form.address" rows="2" placeholder="Street, City, Province"
                                      class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm outline-none resize-none"></textarea>
                        </div>

                        {{-- Credit Settings --}}
                        <div class="bg-beige-50/50 border border-beige-200/60 rounded-2xl p-4">
                            <p class="text-[10px] font-black text-mint-600 uppercase tracking-widest mb-3">Credit Settings</p>
                            <div class="flex items-center gap-4 mb-3">
                                <label class="flex items-center gap-2.5 cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" x-model="form.is_credit_allowed" class="sr-only peer">
                                        <div class="w-10 h-5 bg-beige-200 rounded-full peer peer-checked:bg-mint-500 transition-colors"></div>
                                        <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                                    </div>
                                    <span class="text-xs font-bold text-mint-800">Allow Credit Purchases</span>
                                </label>
                            </div>
                            <template x-if="form.is_credit_allowed">
                                <div>
                                    <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">Credit Limit (₱)</label>
                                    <input type="number" x-model="form.credit_limit" placeholder="Leave blank for unlimited"
                                           class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm outline-none">
                                </div>
                            </template>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-1.5">Internal Notes</label>
                            <textarea x-model="form.notes" rows="3" placeholder="Any additional information about the client…"
                                      class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm outline-none resize-none"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-7 py-5 border-t border-beige-100 bg-beige-50/30 flex flex-col sm:flex-row items-center gap-3 flex-shrink-0">
                    <button @click="submitForm()"
                            :disabled="modal.submitting"
                            class="btn-mint shadow-md shadow-mint-900/10 w-full sm:w-auto px-6 py-3 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <template x-if="modal.submitting">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        </template>
                        <span x-text="modal.submitting ? (modal.editId ? 'Updating…' : 'Creating…') : (modal.editId ? 'Update Customer' : 'Enroll Customer')"></span>
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
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-2xl font-black text-red-950 mb-2">Delete Customer?</h3>
                    <p class="text-sm font-medium text-beige-500 mb-8">
                        This will permanently remove <strong class="text-red-700" x-text="deleteModal.customerName"></strong> and all associated data. This cannot be undone.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
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
function customerDashboard() {
    return {
        // ── State ──────────────────────────────────────────────────────
        customers:   [],
        analytics:   {},
        loading:     true,
        selectedIds: [],
        pagination:  { current_page: 1, last_page: 1, total: 0, per_page: 15, from: 0, to: 0 },
        toasts:      [],
        _toastId:    0,

        filters: { search: '' },
        sort:    { by: 'created_at', dir: 'desc' },

        modal: {
            open: false, editId: null, submitting: false,
        },
        form: {
            name: '', email: '', phone: '', address: '',
            notes: '', credit_limit: '', is_credit_allowed: false,
        },
        formErrors: {},

        deleteModal: {
            open: false, customerId: null, customerName: '', deleting: false,
        },

        // ── Init ───────────────────────────────────────────────────────
        init() {
            this.loadData();
            this.loadAnalytics();
        },

        // ── Computed ───────────────────────────────────────────────────
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
                const res  = await fetch('/customers?' + params, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const json = await res.json();
                this.customers  = json.data;
                this.pagination = json.pagination;
                this.selectedIds = [];
            } catch {
                this.toast('Failed to load customers.', 'error');
            } finally {
                this.loading = false;
            }
        },

        async loadAnalytics() {
            try {
                const res  = await fetch('/customers/analytics', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                this.analytics = await res.json();
            } catch { /* silent */ }
        },

        // ── Sorting ────────────────────────────────────────────────────
        sortBy(col) {
            if (this.sort.by === col) {
                this.sort.dir = this.sort.dir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sort.by  = col;
                this.sort.dir = 'asc';
            }
            this.loadData(1);
        },

        sortIcon(col) {
            if (this.sort.by !== col) return '↕';
            return this.sort.dir === 'asc' ? '↑' : '↓';
        },

        // ── Selection ──────────────────────────────────────────────────
        toggleSelect(id) {
            const idx = this.selectedIds.indexOf(id);
            if (idx === -1) this.selectedIds.push(id);
            else            this.selectedIds.splice(idx, 1);
        },

        toggleSelectAll(e) {
            this.selectedIds = e.target.checked ? this.customers.map(c => c.id) : [];
        },

        // ── Modal: Create ──────────────────────────────────────────────
        openCreate() {
            this.formErrors  = {};
            this.modal.editId = null;
            this.form = { name: '', email: '', phone: '', address: '', notes: '', credit_limit: '', is_credit_allowed: false };
            this.modal.open  = true;
        },

        // ── Modal: Edit ────────────────────────────────────────────────
        openEdit(customer) {
            this.formErrors   = {};
            this.modal.editId = customer.id;
            this.form = {
                name:              customer.name,
                email:             customer.email     ?? '',
                phone:             customer.phone     ?? '',
                address:           customer.address   ?? '',
                notes:             customer.notes     ?? '',
                credit_limit:      customer.credit_limit ?? '',
                is_credit_allowed: !!customer.is_credit_allowed,
            };
            this.modal.open = true;
        },

        // ── Submit (Create / Update) ───────────────────────────────────
        async submitForm() {
            if (this.modal.submitting) return;
            this.modal.submitting = true;
            this.formErrors = {};

            const isEdit = !!this.modal.editId;
            const url    = isEdit ? `/customers/${this.modal.editId}` : '/customers';

            try {
                const res = await fetch(url, {
                    method:  isEdit ? 'PUT' : 'POST',
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
                    this.toast(data.message || (isEdit ? 'Customer updated!' : 'Customer created!'), 'success');
                    this.loadData(this.pagination.current_page);
                    this.loadAnalytics();
                }
            } catch {
                this.toast('Network error. Please try again.', 'error');
            } finally {
                this.modal.submitting = false;
            }
        },

        // ── Delete ─────────────────────────────────────────────────────
        confirmDelete(customer) {
            this.deleteModal = {
                open: true, customerId: customer.id,
                customerName: customer.name, deleting: false,
            };
        },

        async executeDelete() {
            if (this.deleteModal.deleting) return;
            this.deleteModal.deleting = true;
            try {
                const res = await fetch(`/customers/${this.deleteModal.customerId}`, {
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
                    this.toast(data.message || 'Customer deleted.', 'success');
                    this.loadData(this.pagination.current_page);
                    this.loadAnalytics();
                } else {
                    this.toast(data.message || 'Failed to delete customer.', 'error');
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
            if (!confirm(`Delete ${this.selectedIds.length} customer(s)? This cannot be undone.`)) return;
            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                await Promise.all(this.selectedIds.map(id =>
                    fetch(`/customers/${id}`, {
                        method:  'DELETE',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
                    })
                ));
                this.toast(`${this.selectedIds.length} customer(s) deleted.`, 'success');
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

        // ── Helpers ────────────────────────────────────────────────────
        fmt(n) {
            return Number(n ?? 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        relDate(d) {
            if (!d) return '—';
            const diff = Date.now() - new Date(d).getTime();
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
            const colors = [
                '#10b981','#059669','#047857',
                '#f59e0b','#d97706','#b45309',
                '#3b82f6','#2563eb','#1d4ed8',
                '#8b5cf6','#7c3aed','#6d28d9',
                '#ef4444','#dc2626','#b91c1c',
                '#ec4899','#db2777','#be185d',
            ];
            let hash = 0;
            for (const ch of name) hash = ch.charCodeAt(0) + ((hash << 5) - hash);
            return colors[Math.abs(hash) % colors.length];
        },
    };
}
</script>
@endpush
