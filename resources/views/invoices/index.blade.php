@extends('layouts.app')
@section('title', 'Invoices')
@section('page-title', 'Invoice Management')

@push('styles')
<style>
/* ══════════════════════════════════════════════
   INVOICE MODULE — PREMIUM DESIGN SYSTEM
══════════════════════════════════════════════ */

/* KPI Cards */
.kpi-card {
    background: #fff;
    border: 1px solid rgba(210,194,168,.4);
    border-radius: 20px;
    padding: 20px 24px;
    transition: box-shadow .2s, transform .2s;
    position: relative;
    overflow: hidden;
}
.kpi-card:hover { box-shadow: 0 8px 28px rgba(21,85,65,.08); transform: translateY(-2px); }
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 20px 20px 0 0;
}
.kpi-green::before  { background: linear-gradient(90deg, #10B981, #059669); }
.kpi-blue::before   { background: linear-gradient(90deg, #3B82F6, #2563EB); }
.kpi-amber::before  { background: linear-gradient(90deg, #F59E0B, #D97706); }
.kpi-red::before    { background: linear-gradient(90deg, #EF4444, #DC2626); }
.kpi-purple::before { background: linear-gradient(90deg, #8B5CF6, #7C3AED); }
.kpi-teal::before   { background: linear-gradient(90deg, #14B8A6, #0D9488); }
.kpi-rose::before   { background: linear-gradient(90deg, #F43F5E, #E11D48); }

/* Data Grid */
.invoice-table { border-collapse: separate; border-spacing: 0; width: 100%; }
.invoice-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #FAFAF8;
    border-bottom: 1.5px solid rgba(210,194,168,.4);
    padding: 12px 16px;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #AA8D63;
    white-space: nowrap;
    user-select: none;
}
.invoice-table thead th.sortable { cursor: pointer; }
.invoice-table thead th.sortable:hover { color: #10B981; }
.invoice-table tbody tr {
    border-bottom: 1px solid rgba(210,194,168,.2);
    transition: background .12s;
}
.invoice-table tbody tr:hover { background: #F7F4EE; }
.invoice-table tbody td { padding: 14px 16px; vertical-align: middle; }

/* Status badges */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 99px;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .06em;
    white-space: nowrap;
}
.badge-paid     { background: #D1FAE5; color: #065F46; }
.badge-unpaid   { background: #FEF3C7; color: #92400E; }
.badge-overdue  { background: #FEE2E2; color: #991B1B; }
.badge-partial  { background: #DBEAFE; color: #1E40AF; }
.badge-draft    { background: #F3F4F6; color: #6B7280; }

/* Customer avatar */
.cust-avatar {
    width: 36px;
    height: 36px;
    border-radius: 99px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 900;
    flex-shrink: 0;
    color: #fff;
}

/* Form inputs */
.field-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #8F7149;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 6px;
}
.field-label .req { color: #10B981; margin-left: 2px; }
.form-input {
    width: 100%;
    padding: 10px 14px;
    background: #F7F2E8;
    border: 1.5px solid rgba(210,194,168,.6);
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    color: #155541;
    font-family: 'Outfit', sans-serif;
    transition: border-color .15s, background .15s, box-shadow .15s;
    min-height: 42px;
}
.form-input:focus { outline: none; border-color: #10B981; background: #fff; box-shadow: 0 0 0 3px rgba(16,185,129,.12); }
.form-input.error { border-color: #ef4444; }

/* Filter pills */
.filter-pill {
    padding: 6px 14px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 800;
    border: 1.5px solid transparent;
    cursor: pointer;
    transition: all .15s;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.filter-pill.active   { background: #10B981; color: #fff; border-color: #10B981; }
.filter-pill:not(.active) { background: #fff; color: #8F7149; border-color: rgba(210,194,168,.6); }
.filter-pill:not(.active):hover { border-color: #10B981; color: #10B981; }

/* Action dropdown */
.action-menu {
    position: absolute;
    right: 0;
    top: calc(100% + 6px);
    background: #fff;
    border: 1px solid rgba(210,194,168,.5);
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,.1);
    min-width: 180px;
    z-index: 100;
    overflow: hidden;
}
.action-menu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    font-size: 13px;
    font-weight: 600;
    color: #155541;
    cursor: pointer;
    transition: background .1s;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
}
.action-menu-item:hover { background: #F7F4EE; }
.action-menu-item.danger { color: #DC2626; }
.action-menu-item.danger:hover { background: #FEF2F2; }

/* Skeleton */
@keyframes shimmer {
    0%   { background-position: -600px 0; }
    100% { background-position: 600px 0; }
}
.skeleton-row td div {
    border-radius: 8px;
    background: linear-gradient(90deg, #f0ede8 25%, #e5e1da 50%, #f0ede8 75%);
    background-size: 600px 100%;
    animation: shimmer 1.4s infinite linear;
}

/* Modal */
.inv-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(21,85,65,.5);
    backdrop-filter: blur(4px);
    z-index: 200;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 16px;
    overflow-y: auto;
}
.inv-modal {
    background: #FAFAF8;
    border-radius: 24px;
    border: 1px solid rgba(210,194,168,.4);
    box-shadow: 0 32px 80px rgba(0,0,0,.18);
    width: 100%;
    max-width: 780px;
    margin: auto;
    position: relative;
}
.inv-modal-header {
    padding: 24px 28px 20px;
    border-bottom: 1px solid rgba(210,194,168,.3);
    background: #fff;
    border-radius: 24px 24px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.inv-modal-body { padding: 28px; }
.inv-modal-footer {
    padding: 20px 28px;
    border-top: 1px solid rgba(210,194,168,.3);
    background: #fff;
    border-radius: 0 0 24px 24px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
}

/* Line items */
.line-item-row { display: grid; grid-template-columns: 1fr 80px 110px 100px 36px; gap: 8px; align-items: center; }
.line-item-row.header { grid-template-columns: 1fr 80px 110px 100px 36px; }

/* Submit button */
.submit-btn { transition: all .2s; position: relative; overflow: hidden; }
.submit-btn:not(:disabled):hover { transform: translateY(-1px); box-shadow: 0 10px 24px -4px rgba(16,185,129,.4); }
.submit-btn:disabled { opacity: .7; cursor: not-allowed; transform: none; }
@keyframes spin { to { transform: rotate(360deg); } }
.spin { animation: spin .7s linear infinite; }

/* Bulk action bar */
.bulk-bar {
    background: #155541;
    color: #fff;
    border-radius: 16px;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    animation: slideDown .2s ease;
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Confirm modal */
.confirm-modal {
    background: #fff;
    border-radius: 20px;
    padding: 32px;
    max-width: 420px;
    width: 100%;
    box-shadow: 0 32px 80px rgba(0,0,0,.2);
}

/* Responsive card layout */
@media (max-width: 768px) {
    .desktop-table { display: none; }
    .mobile-cards  { display: block; }
    .inv-modal-body { padding: 20px; }
    .line-item-row { grid-template-columns: 1fr 60px 80px 80px 36px; gap: 6px; }
}
@media (min-width: 769px) {
    .desktop-table { display: block; }
    .mobile-cards  { display: none; }
}

/* Mobile invoice card */
.mobile-inv-card {
    background: #fff;
    border: 1px solid rgba(210,194,168,.4);
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 10px;
    transition: box-shadow .15s;
}
.mobile-inv-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.07); }

/* Empty state */
.empty-state-icon {
    width: 80px; height: 80px;
    background: #F7F2E8;
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
}

/* Pagination */
.page-btn {
    min-width: 36px; height: 36px;
    border-radius: 10px;
    border: 1.5px solid rgba(210,194,168,.5);
    background: #fff;
    color: #8F7149;
    font-size: 13px;
    font-weight: 700;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: all .15s;
}
.page-btn:hover  { border-color: #10B981; color: #10B981; }
.page-btn.active { background: #10B981; border-color: #10B981; color: #fff; }
.page-btn:disabled { opacity: .4; cursor: not-allowed; }

/* Search */
.search-wrap { position: relative; }
.search-wrap .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #AA8D63; pointer-events: none; }
.search-input { padding-left: 42px !important; }

/* View detail drawer */
.detail-drawer {
    position: fixed;
    top: 0; right: 0; bottom: 0;
    width: 100%;
    max-width: 520px;
    background: #FAFAF8;
    border-left: 1px solid rgba(210,194,168,.4);
    box-shadow: -10px 0 60px rgba(0,0,0,.12);
    z-index: 300;
    overflow-y: auto;
    transform: translateX(100%);
    transition: transform .3s cubic-bezier(.4,0,.2,1);
}
.detail-drawer.open { transform: translateX(0); }
</style>
@endpush

@section('content')
<div x-data="invoiceApp()" x-init="init()" class="max-w-full">

    {{-- ════════════════════════════════
         KPI ANALYTICS CARDS
    ════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3 mb-6">
        <!-- Total Invoices -->
        <div class="kpi-card kpi-green lg:col-span-1">
            <p class="text-[10px] font-800 text-beige-400 uppercase tracking-wider mb-1">Total</p>
            <p class="text-2xl font-black text-mint-900" x-text="analytics.total_invoices ?? '—'"></p>
            <p class="text-[10px] font-semibold text-beige-400 mt-1">Invoices</p>
        </div>
        <!-- Paid -->
        <div class="kpi-card kpi-green">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                <p class="text-[10px] font-800 text-emerald-600 uppercase tracking-wider">Paid</p>
            </div>
            <p class="text-2xl font-black text-mint-900" x-text="analytics.paid_count ?? '—'"></p>
        </div>
        <!-- Pending -->
        <div class="kpi-card kpi-amber">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                <p class="text-[10px] font-800 text-amber-600 uppercase tracking-wider">Pending</p>
            </div>
            <p class="text-2xl font-black text-mint-900" x-text="analytics.unpaid_count ?? '—'"></p>
        </div>
        <!-- Overdue -->
        <div class="kpi-card kpi-red">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-red-400"></div>
                <p class="text-[10px] font-800 text-red-600 uppercase tracking-wider">Overdue</p>
            </div>
            <p class="text-2xl font-black text-mint-900" x-text="analytics.overdue_count ?? '—'"></p>
        </div>
        <!-- Total Revenue -->
        <div class="kpi-card kpi-teal lg:col-span-1">
            <p class="text-[10px] font-800 text-beige-400 uppercase tracking-wider mb-1">Revenue</p>
            <p class="text-lg font-black text-mint-900 truncate" x-text="'₱' + fmt(analytics.total_revenue)"></p>
            <p class="text-[10px] font-semibold text-beige-400 mt-1">All time</p>
        </div>
        <!-- Outstanding -->
        <div class="kpi-card kpi-rose lg:col-span-1">
            <p class="text-[10px] font-800 text-beige-400 uppercase tracking-wider mb-1">Outstanding</p>
            <p class="text-lg font-black text-red-600 truncate" x-text="'₱' + fmt(analytics.outstanding)"></p>
            <p class="text-[10px] font-semibold text-beige-400 mt-1">Unpaid</p>
        </div>
        <!-- This Month -->
        <div class="kpi-card kpi-purple lg:col-span-1">
            <p class="text-[10px] font-800 text-beige-400 uppercase tracking-wider mb-1">This Month</p>
            <p class="text-lg font-black text-mint-900 truncate" x-text="'₱' + fmt(analytics.monthly_revenue)"></p>
            <p class="text-[10px] font-semibold text-beige-400 mt-1">Collected</p>
        </div>
    </div>

    {{-- ════════════════════════════════
         TOOLBAR: FILTERS + ACTIONS
    ════════════════════════════════ --}}
    <div class="bg-white border border-beige-200/60 rounded-2xl shadow-sm mb-4">
        <div class="p-4 flex flex-wrap items-center gap-3">

            {{-- Search --}}
            <div class="search-wrap flex-1 min-w-[200px]">
                <svg class="search-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="filters.search" @input.debounce.300ms="loadData()"
                       placeholder="Search by invoice # or customer..."
                       class="form-input search-input w-full" style="min-height:40px; padding-top:8px; padding-bottom:8px;">
            </div>

            {{-- Date range --}}
            <div class="flex items-center gap-2">
                <input type="date" x-model="filters.from" @change="loadData()"
                       class="form-input text-xs" style="min-height:40px; padding:8px 10px; width:138px;" title="From date">
                <span class="text-beige-400 font-bold text-xs">to</span>
                <input type="date" x-model="filters.to" @change="loadData()"
                       class="form-input text-xs" style="min-height:40px; padding:8px 10px; width:138px;" title="To date">
            </div>

            {{-- Clear --}}
            <button x-show="filters.search || filters.from || filters.to || filters.status"
                    @click="clearFilters()"
                    class="text-xs font-bold text-beige-400 hover:text-red-500 transition-colors px-3 py-2 rounded-xl hover:bg-red-50 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                Clear
            </button>

            {{-- Spacer --}}
            <div class="flex-1 hidden sm:block"></div>

            {{-- New Invoice --}}
            <button @click="openCreate()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-mint-500 text-white font-extrabold text-sm shadow-lg shadow-mint-500/25 hover:bg-mint-600 transition-all"
                    style="min-height:40px;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                New Invoice
            </button>
        </div>

        {{-- Status filter pills --}}
        <div class="px-4 pb-4 flex flex-wrap gap-2">
            <template x-for="s in statusOptions" :key="s.value">
                <button class="filter-pill" :class="filters.status === s.value ? 'active' : ''"
                        @click="filters.status = filters.status === s.value ? '' : s.value; loadData()"
                        x-text="s.label"></button>
            </template>
        </div>
    </div>

    {{-- ════════════════════════════════
         BULK ACTION BAR
    ════════════════════════════════ --}}
    <div x-show="selectedIds.length > 0" class="bulk-bar mb-4" x-transition>
        <svg class="w-4 h-4 text-mint-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <span class="font-bold text-sm" x-text="selectedIds.length + ' selected'"></span>
        <div class="flex-1"></div>
        <button @click="bulkMarkPaid()"
                class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl bg-emerald-500 text-white font-bold text-xs hover:bg-emerald-400 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            Mark Paid
        </button>
        <button @click="bulkDelete()"
                class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl bg-red-500 text-white font-bold text-xs hover:bg-red-400 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Delete
        </button>
        <button @click="selectedIds = []" class="text-mint-300 hover:text-white text-xs font-bold ml-2">Cancel</button>
    </div>

    {{-- ════════════════════════════════
         DESKTOP TABLE
    ════════════════════════════════ --}}
    <div class="desktop-table bg-white border border-beige-200/60 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th class="w-10 pl-4">
                            <input type="checkbox" @change="toggleAll($event)"
                                   :checked="selectedIds.length === invoices.length && invoices.length > 0"
                                   class="w-4 h-4 rounded accent-mint-500">
                        </th>
                        <th class="sortable text-left" @click="sortBy('invoice_number')">
                            Invoice Ref
                            <span x-text="sort.by === 'invoice_number' ? (sort.dir === 'asc' ? ' ↑' : ' ↓') : ''"></span>
                        </th>
                        <th class="text-left">Customer</th>
                        <th class="sortable text-left" @click="sortBy('issue_date')">
                            Issued
                            <span x-text="sort.by === 'issue_date' ? (sort.dir === 'asc' ? ' ↑' : ' ↓') : ''"></span>
                        </th>
                        <th class="sortable text-left" @click="sortBy('due_date')">
                            Due
                            <span x-text="sort.by === 'due_date' ? (sort.dir === 'asc' ? ' ↑' : ' ↓') : ''"></span>
                        </th>
                        <th class="sortable text-right" @click="sortBy('total_amount')">
                            Amount
                            <span x-text="sort.by === 'total_amount' ? (sort.dir === 'asc' ? ' ↑' : ' ↓') : ''"></span>
                        </th>
                        <th class="sortable text-center" @click="sortBy('status')">
                            Status
                            <span x-text="sort.by === 'status' ? (sort.dir === 'asc' ? ' ↑' : ' ↓') : ''"></span>
                        </th>
                        <th class="text-right pr-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Skeleton rows --}}
                    <template x-if="loading">
                        <template x-for="i in 8" :key="i">
                            <tr class="skeleton-row">
                                <td class="pl-4 w-10"><div class="w-4 h-4"></div></td>
                                <td><div class="h-4 w-28"></div></td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full"></div>
                                        <div class="h-4 w-32"></div>
                                    </div>
                                </td>
                                <td><div class="h-4 w-20"></div></td>
                                <td><div class="h-4 w-20"></div></td>
                                <td class="text-right"><div class="h-4 w-20 ml-auto"></div></td>
                                <td class="text-center"><div class="h-6 w-16 rounded-full mx-auto"></div></td>
                                <td class="pr-4 text-right"><div class="h-8 w-8 rounded-xl ml-auto"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Data rows --}}
                    <template x-if="!loading">
                        <template x-for="inv in invoices" :key="inv.id">
                            <tr>
                                <td class="pl-4 w-10">
                                    <input type="checkbox" :value="inv.id"
                                           :checked="selectedIds.includes(inv.id)"
                                           @change="toggleSelect(inv.id)"
                                           class="w-4 h-4 rounded accent-mint-500">
                                </td>
                                <td>
                                    <button @click="openDetail(inv)"
                                            class="font-black text-mint-600 hover:text-mint-700 text-sm tracking-tight hover:underline">
                                        <span x-text="inv.invoice_number"></span>
                                    </button>
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="cust-avatar" :style="`background:${avatarColor(inv.customer?.name)}`"
                                             x-text="initials(inv.customer?.name)"></div>
                                        <div>
                                            <p class="text-sm font-bold text-mint-900" x-text="inv.customer?.name ?? 'N/A'"></p>
                                            <p class="text-[11px] text-beige-400 font-medium" x-text="inv.customer?.email ?? ''"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-sm font-semibold text-beige-500" x-text="fmtDate(inv.issue_date)"></td>
                                <td>
                                    <span class="text-sm font-semibold"
                                          :class="isOverdue(inv) ? 'text-red-500 font-bold' : 'text-beige-500'"
                                          x-text="fmtDate(inv.due_date)"></span>
                                </td>
                                <td class="text-right font-black text-mint-900 text-sm" x-text="'₱' + fmt(inv.total_amount)"></td>
                                <td class="text-center">
                                    <span class="badge" :class="badgeClass(inv.status)" x-text="inv.status"></span>
                                </td>
                                <td class="pr-4 text-right">
                                    <div class="relative inline-block" x-data="{ open: false }" @click.away="open = false">
                                        <button @click="open = !open"
                                                class="w-8 h-8 flex items-center justify-center rounded-xl border border-transparent hover:border-beige-200 hover:bg-beige-50 text-beige-400 hover:text-mint-600 transition-all">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                                        </button>
                                        <div class="action-menu" x-show="open" x-transition @click="open = false">
                                            <button class="action-menu-item" @click="openDetail(inv)">
                                                <svg class="w-4 h-4 text-beige-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                View Details
                                            </button>
                                            <button class="action-menu-item" @click="openEdit(inv)">
                                                <svg class="w-4 h-4 text-beige-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit Invoice
                                            </button>
                                            <template x-if="inv.status !== 'paid'">
                                                <button class="action-menu-item" @click="confirmMarkPaid(inv)">
                                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Mark as Paid
                                                </button>
                                            </template>
                                            <a class="action-menu-item" :href="`/invoices/${inv.id}/pdf`" target="_blank">
                                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                                                Download PDF
                                            </a>
                                            <div class="border-t border-beige-100 my-1"></div>
                                            <button class="action-menu-item danger" @click="confirmDelete(inv)">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>

                    {{-- Empty state --}}
                    <template x-if="!loading && invoices.length === 0">
                        <tr>
                            <td colspan="8" class="py-20 text-center">
                                <div class="empty-state-icon">
                                    <svg class="w-10 h-10 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-sm font-black text-mint-900 uppercase tracking-widest mb-2">No invoices found</p>
                                <p class="text-xs text-beige-400 font-semibold mb-5">Try adjusting your filters or create your first invoice.</p>
                                <button @click="openCreate()"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-mint-500 text-white rounded-2xl font-extrabold text-sm hover:bg-mint-600 transition-all shadow-lg shadow-mint-500/25">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    Create Invoice
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div x-show="pagination.last_page > 1" class="flex items-center justify-between px-5 py-4 border-t border-beige-100 bg-beige-50/30 flex-wrap gap-3">
            <p class="text-[11px] font-semibold text-beige-400">
                Showing <span class="font-bold text-mint-900" x-text="pagination.from ?? 0"></span>–<span class="font-bold text-mint-900" x-text="pagination.to ?? 0"></span> of <span class="font-bold text-mint-900" x-text="pagination.total ?? 0"></span>
            </p>
            <div class="flex items-center gap-1.5">
                <button class="page-btn" :disabled="pagination.current_page <= 1" @click="goToPage(pagination.current_page - 1)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <template x-for="p in pageRange()" :key="p">
                    <button class="page-btn" :class="p === pagination.current_page ? 'active' : ''"
                            @click="goToPage(p)" x-text="p"></button>
                </template>
                <button class="page-btn" :disabled="pagination.current_page >= pagination.last_page" @click="goToPage(pagination.current_page + 1)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════
         MOBILE CARD LAYOUT
    ════════════════════════════════ --}}
    <div class="mobile-cards">
        <template x-if="loading">
            <template x-for="i in 5" :key="i">
                <div class="mobile-inv-card skeleton-row">
                    <div class="flex justify-between mb-3">
                        <div class="h-4 w-28"></div>
                        <div class="h-6 w-16 rounded-full"></div>
                    </div>
                    <div class="h-4 w-40 mb-2"></div>
                    <div class="h-3 w-24"></div>
                </div>
            </template>
        </template>
        <template x-if="!loading">
            <template x-for="inv in invoices" :key="inv.id">
                <div class="mobile-inv-card">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <button @click="openDetail(inv)"
                                    class="font-black text-mint-600 text-sm tracking-tight hover:underline" x-text="inv.invoice_number"></button>
                            <p class="text-xs text-beige-400 font-semibold mt-0.5" x-text="'Due: ' + fmtDate(inv.due_date)"></p>
                        </div>
                        <span class="badge" :class="badgeClass(inv.status)" x-text="inv.status"></span>
                    </div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="cust-avatar w-7 h-7 text-[10px]" :style="`background:${avatarColor(inv.customer?.name)}`"
                             x-text="initials(inv.customer?.name)"></div>
                        <span class="text-sm font-bold text-mint-900" x-text="inv.customer?.name ?? 'N/A'"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-base font-black text-mint-900" x-text="'₱' + fmt(inv.total_amount)"></span>
                        <div class="flex items-center gap-2">
                            <button @click="openEdit(inv)" class="p-2 rounded-xl border border-beige-200 text-beige-400 hover:text-mint-600 hover:border-mint-200 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <template x-if="inv.status !== 'paid'">
                                <button @click="confirmMarkPaid(inv)" class="p-2 rounded-xl border border-beige-200 text-emerald-400 hover:border-emerald-200 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </template>
                            <button @click="confirmDelete(inv)" class="p-2 rounded-xl border border-beige-200 text-red-400 hover:border-red-200 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </template>
        <template x-if="!loading && invoices.length === 0">
            <div class="text-center py-16">
                <div class="empty-state-icon">
                    <svg class="w-10 h-10 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-black text-mint-900 uppercase tracking-widest mb-3">No invoices</p>
                <button @click="openCreate()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-mint-500 text-white rounded-2xl font-extrabold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Create Invoice
                </button>
            </div>
        </template>
    </div>

    {{-- ════════════════════════════════
         CREATE / EDIT MODAL
    ════════════════════════════════ --}}
    <div x-show="modal.open" style="display:none;"
         class="inv-modal-overlay"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="modal.open = false">
        <div class="inv-modal my-4" @click.stop
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            {{-- Header --}}
            <div class="inv-modal-header">
                <div>
                    <h2 class="text-lg font-black text-mint-900" x-text="modal.editId ? 'Edit Invoice' : 'New Invoice'"></h2>
                    <p class="text-[11px] font-semibold text-beige-400 mt-0.5" x-text="modal.editId ? 'Update invoice details and line items' : 'Create a new invoice for your customer'"></p>
                </div>
                <button @click="modal.open = false" class="w-8 h-8 flex items-center justify-center rounded-full text-beige-400 hover:text-red-500 hover:bg-red-50 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="inv-modal-body" style="max-height:72vh; overflow-y:auto;">

                {{-- Errors --}}
                <div x-show="Object.keys(formErrors).length > 0" class="mb-5 p-4 bg-red-50 border border-red-200 rounded-2xl">
                    <p class="text-sm font-bold text-red-800 mb-1">Please fix the following:</p>
                    <template x-for="(msgs, field) in formErrors" :key="field">
                        <template x-for="msg in msgs" :key="msg">
                            <p class="text-xs text-red-600 font-medium" x-text="'• ' + msg"></p>
                        </template>
                    </template>
                </div>

                {{-- Section: Customer + Invoice Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    {{-- Customer --}}
                    <div class="md:col-span-2">
                        <label class="field-label">Customer <span class="req">*</span></label>
                        <select x-model="form.customer_id" class="form-input" :class="formErrors.customer_id ? 'error' : ''">
                            <option value="">Select customer…</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}{{ $c->email ? ' — ' . $c->email : '' }}</option>
                            @endforeach
                        </select>
                        <p x-show="formErrors.customer_id" x-text="formErrors.customer_id?.[0]" class="text-xs text-red-500 font-medium mt-1"></p>
                    </div>

                    {{-- Invoice Number --}}
                    <div>
                        <label class="field-label">Invoice Number <span class="req">*</span></label>
                        <input type="text" x-model="form.invoice_number" class="form-input" :class="formErrors.invoice_number ? 'error' : ''" placeholder="INV-YYYYMM-0001">
                        <p x-show="formErrors.invoice_number" x-text="formErrors.invoice_number?.[0]" class="text-xs text-red-500 font-medium mt-1"></p>
                    </div>

                    {{-- Issue Date --}}
                    <div>
                        <label class="field-label">Issue Date <span class="req">*</span></label>
                        <input type="date" x-model="form.issue_date" class="form-input" :class="formErrors.issue_date ? 'error' : ''">
                        <p x-show="formErrors.issue_date" x-text="formErrors.issue_date?.[0]" class="text-xs text-red-500 font-medium mt-1"></p>
                    </div>

                    {{-- Due Date --}}
                    <div>
                        <label class="field-label">Due Date <span class="req">*</span></label>
                        <input type="date" x-model="form.due_date" class="form-input" :class="formErrors.due_date ? 'error' : ''">
                        <p x-show="formErrors.due_date" x-text="formErrors.due_date?.[0]" class="text-xs text-red-500 font-medium mt-1"></p>
                    </div>

                    {{-- Penalty Type --}}
                    <div>
                        <label class="field-label">Penalty Type</label>
                        <select x-model="form.penalty_type" class="form-input">
                            <option value="none">None</option>
                            <option value="flat">Flat Amount</option>
                            <option value="percentage">Percentage (%)</option>
                        </select>
                    </div>

                    {{-- Penalty Value --}}
                    <div x-show="form.penalty_type !== 'none'">
                        <label class="field-label">Penalty Value</label>
                        <input type="number" x-model="form.penalty_value" step="0.01" min="0" class="form-input" placeholder="0.00">
                    </div>
                </div>

                {{-- Line Items --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <label class="field-label mb-0">Line Items <span class="req">*</span></label>
                        <button type="button" @click="addItem()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-mint-50 text-mint-600 font-bold text-xs hover:bg-mint-100 transition-colors border border-mint-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            Add Item
                        </button>
                    </div>

                    {{-- Item header --}}
                    <div class="line-item-row header mb-2 px-1">
                        <span class="text-[10px] font-800 text-beige-400 uppercase tracking-wider">Description</span>
                        <span class="text-[10px] font-800 text-beige-400 uppercase tracking-wider text-center">Qty</span>
                        <span class="text-[10px] font-800 text-beige-400 uppercase tracking-wider text-right">Unit Price</span>
                        <span class="text-[10px] font-800 text-beige-400 uppercase tracking-wider text-right">Total</span>
                        <span></span>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(item, idx) in form.items" :key="idx">
                            <div class="line-item-row p-2 bg-beige-50 rounded-xl">
                                <input type="text" x-model="item.description"
                                       class="form-input text-sm" style="min-height:36px; padding:7px 10px;"
                                       placeholder="Description…">
                                <input type="number" x-model="item.quantity" @input="calcTotal()" min="0.01" step="0.01"
                                       class="form-input text-sm text-center" style="min-height:36px; padding:7px 8px;">
                                <input type="number" x-model="item.price" @input="calcTotal()" min="0" step="0.01"
                                       class="form-input text-sm text-right" style="min-height:36px; padding:7px 10px;"
                                       placeholder="0.00">
                                <div class="text-right font-black text-mint-900 text-sm pr-1"
                                     x-text="'₱' + fmt(item.quantity * item.price)"></div>
                                <button type="button" @click="removeItem(idx)"
                                        x-show="form.items.length > 1"
                                        class="w-8 h-8 flex items-center justify-center rounded-xl text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Grand Total --}}
                    <div class="flex justify-end mt-4 pt-4 border-t border-beige-100">
                        <div class="text-right">
                            <p class="text-[10px] font-800 text-beige-400 uppercase tracking-wider mb-1">Grand Total</p>
                            <p class="text-2xl font-black text-mint-900" x-text="'₱' + fmt(grandTotal)"></p>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="field-label">Notes</label>
                    <textarea x-model="form.notes" rows="2" class="form-input resize-none" placeholder="Optional notes for this invoice…"></textarea>
                </div>
            </div>

            {{-- Footer --}}
            <div class="inv-modal-footer">
                <button @click="modal.open = false"
                        class="px-5 py-2.5 rounded-2xl border border-beige-200 text-beige-500 font-bold text-sm hover:bg-beige-50 transition-colors"
                        :disabled="modal.submitting">
                    Cancel
                </button>
                <button @click="submitForm()"
                        :disabled="modal.submitting"
                        class="submit-btn inline-flex items-center gap-2 px-6 py-2.5 rounded-2xl bg-mint-500 text-white font-extrabold text-sm shadow-lg shadow-mint-500/25 hover:bg-mint-600">
                    <template x-if="!modal.submitting">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span x-text="modal.editId ? 'Update Invoice' : 'Create Invoice'"></span>
                        </span>
                    </template>
                    <template x-if="modal.submitting">
                        <span class="flex items-center gap-2">
                            <svg class="spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span x-text="modal.editId ? 'Updating…' : 'Creating…'"></span>
                        </span>
                    </template>
                </button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════
         DETAIL DRAWER
    ════════════════════════════════ --}}
    <div x-show="drawer.open" style="display:none;" class="fixed inset-0 z-[250]" @click="drawer.open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-mint-900/40 backdrop-blur-sm"></div>
        <div class="detail-drawer" :class="drawer.open ? 'open' : ''" @click.stop style="display:block; position:fixed;">
            <div class="sticky top-0 bg-white border-b border-beige-100 px-6 py-5 flex items-center justify-between z-10">
                <div class="flex items-center gap-3">
                    <span class="badge" :class="badgeClass(drawer.invoice?.status)" x-text="drawer.invoice?.status"></span>
                    <h3 class="font-black text-mint-900 text-lg" x-text="drawer.invoice?.invoice_number"></h3>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="openEdit(drawer.invoice)"
                            class="p-2 rounded-xl border border-beige-200 text-beige-400 hover:text-mint-600 hover:border-mint-200 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <a :href="`/invoices/${drawer.invoice?.id}/pdf`" target="_blank"
                       class="p-2 rounded-xl border border-beige-200 text-blue-400 hover:border-blue-200 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                    </a>
                    <button @click="drawer.open = false"
                            class="p-2 rounded-xl text-beige-400 hover:text-red-500 hover:bg-red-50 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-6">
                {{-- Customer --}}
                <div class="bg-white rounded-2xl border border-beige-100 p-5">
                    <p class="text-[10px] font-800 text-beige-400 uppercase tracking-wider mb-3">Billed To</p>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="cust-avatar" :style="`background:${avatarColor(drawer.invoice?.customer?.name)}`"
                             x-text="initials(drawer.invoice?.customer?.name)"></div>
                        <div>
                            <p class="font-black text-mint-900" x-text="drawer.invoice?.customer?.name ?? 'N/A'"></p>
                            <p class="text-xs text-beige-400 font-medium" x-text="drawer.invoice?.customer?.email ?? ''"></p>
                        </div>
                    </div>
                    <p x-show="drawer.invoice?.customer?.phone" class="text-sm font-semibold text-mint-700" x-text="drawer.invoice?.customer?.phone"></p>
                    <p x-show="drawer.invoice?.customer?.address" class="text-sm text-beige-500 mt-1" x-text="drawer.invoice?.customer?.address"></p>
                </div>

                {{-- Dates + amounts --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-beige-50 rounded-2xl p-4">
                        <p class="text-[10px] font-800 text-beige-400 uppercase tracking-wider mb-1">Issued</p>
                        <p class="font-black text-mint-900 text-sm" x-text="fmtDate(drawer.invoice?.issue_date)"></p>
                    </div>
                    <div class="bg-beige-50 rounded-2xl p-4">
                        <p class="text-[10px] font-800 text-beige-400 uppercase tracking-wider mb-1">Due</p>
                        <p class="font-black text-sm" :class="isOverdue(drawer.invoice) ? 'text-red-500' : 'text-mint-900'"
                           x-text="fmtDate(drawer.invoice?.due_date)"></p>
                    </div>
                    <div class="bg-mint-50 rounded-2xl p-4 col-span-2">
                        <p class="text-[10px] font-800 text-beige-400 uppercase tracking-wider mb-1">Total Amount</p>
                        <p class="text-2xl font-black text-mint-900" x-text="'₱' + fmt(drawer.invoice?.total_amount)"></p>
                    </div>
                </div>

                {{-- Line items --}}
                <div>
                    <p class="text-[10px] font-800 text-beige-400 uppercase tracking-wider mb-3">Line Items</p>
                    <div class="bg-white rounded-2xl border border-beige-100 overflow-hidden">
                        <template x-for="item in (drawer.invoice?.items ?? [])" :key="item.id">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-beige-50 last:border-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-mint-900 truncate" x-text="item.description"></p>
                                    <p class="text-xs text-beige-400 font-medium" x-text="`${item.quantity} × ₱${fmt(item.price)}`"></p>
                                </div>
                                <p class="font-black text-mint-900 text-sm ml-4" x-text="'₱' + fmt(item.quantity * item.price)"></p>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Notes --}}
                <div x-show="drawer.invoice?.notes">
                    <p class="text-[10px] font-800 text-beige-400 uppercase tracking-wider mb-2">Notes</p>
                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4">
                        <p class="text-sm text-amber-800 font-medium" x-text="drawer.invoice?.notes"></p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-2">
                    <template x-if="drawer.invoice?.status !== 'paid'">
                        <button @click="confirmMarkPaid(drawer.invoice)"
                                class="w-full py-3 rounded-2xl bg-mint-500 text-white font-extrabold text-sm hover:bg-mint-600 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Mark as Paid
                        </button>
                    </template>
                    <button @click="confirmDelete(drawer.invoice)"
                            class="w-full py-3 rounded-2xl border border-red-200 text-red-500 font-bold text-sm hover:bg-red-50 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════
         CONFIRMATION MODAL
    ════════════════════════════════ --}}
    <div x-show="confirm.open" style="display:none;"
         class="fixed inset-0 z-[400] flex items-center justify-center p-4 bg-mint-900/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="confirm-modal" @click.stop
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 mx-auto"
                 :class="confirm.type === 'danger' ? 'bg-red-100' : 'bg-amber-100'">
                <template x-if="confirm.type === 'danger'">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </template>
                <template x-if="confirm.type !== 'danger'">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </template>
            </div>
            <h3 class="text-lg font-black text-mint-900 text-center mb-2" x-text="confirm.title"></h3>
            <p class="text-sm text-beige-500 text-center font-medium mb-6" x-text="confirm.message"></p>
            <div class="flex gap-3">
                <button @click="confirm.open = false"
                        class="flex-1 py-3 rounded-2xl border border-beige-200 text-beige-500 font-bold text-sm hover:bg-beige-50 transition-colors">
                    Cancel
                </button>
                <button @click="confirm.onConfirm(); confirm.open = false"
                        class="flex-1 py-3 rounded-2xl font-extrabold text-sm transition-colors text-white"
                        :class="confirm.type === 'danger' ? 'bg-red-500 hover:bg-red-600' : 'bg-mint-500 hover:bg-mint-600'"
                        x-text="confirm.confirmText">
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function invoiceApp() {
    return {
        // State
        invoices:    [],
        analytics:   {},
        loading:     true,
        selectedIds: [],
        pagination:  { current_page: 1, last_page: 1, total: 0, per_page: 15, from: 0, to: 0 },

        // Filters & sort
        filters: { search: '', status: '', from: '', to: '' },
        sort:    { by: 'created_at', dir: 'desc' },

        // Status options
        statusOptions: [
            { value: '',        label: 'All' },
            { value: 'paid',    label: 'Paid' },
            { value: 'unpaid',  label: 'Unpaid' },
            { value: 'partial', label: 'Partial' },
            { value: 'overdue', label: 'Overdue' },
        ],

        // Modal state
        modal: {
            open: false, editId: null, submitting: false,
        },
        form: {
            customer_id: '', invoice_number: '', issue_date: '', due_date: '',
            penalty_type: 'none', penalty_value: '', notes: '',
            items: [{ description: '', quantity: 1, price: 0 }],
        },
        formErrors: {},
        grandTotal: 0,

        // Drawer
        drawer: { open: false, invoice: null },

        // Confirm modal
        confirm: { open: false, title: '', message: '', confirmText: 'Confirm', type: 'warning', onConfirm: () => {} },

        // ──────────────── INIT ────────────────
        init() {
            this.loadData();
            this.loadAnalytics();
        },

        // ──────────────── DATA LOADING ────────────────
        async loadData(page = 1) {
            this.loading = true;
            const params = new URLSearchParams({
                page,
                per_page: this.pagination.per_page,
                sort_by:  this.sort.by,
                sort_dir: this.sort.dir,
                ...Object.fromEntries(Object.entries(this.filters).filter(([,v]) => v)),
            });
            try {
                const res  = await fetch('/invoices?' + params, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const json = await res.json();
                this.invoices   = json.data.map(inv => {
                    // Ensure items array exists
                    if (!inv.items) inv.items = [];
                    return inv;
                });
                this.pagination = json.pagination;
                this.selectedIds = [];
            } catch (e) {
                this.toast('Failed to load invoices.', 'error');
            } finally {
                this.loading = false;
            }
        },

        async loadAnalytics() {
            try {
                const res  = await fetch('/invoices/analytics', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                this.analytics = await res.json();
            } catch (e) { /* silent */ }
        },

        // ──────────────── SORT ────────────────
        sortBy(col) {
            if (this.sort.by === col) {
                this.sort.dir = this.sort.dir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sort.by  = col;
                this.sort.dir = 'desc';
            }
            this.loadData();
        },

        // ──────────────── FILTERS ────────────────
        clearFilters() {
            this.filters = { search: '', status: '', from: '', to: '' };
            this.loadData();
        },

        // ──────────────── PAGINATION ────────────────
        goToPage(p) {
            if (p < 1 || p > this.pagination.last_page) return;
            this.loadData(p);
        },

        pageRange() {
            const cur  = this.pagination.current_page;
            const last = this.pagination.last_page;
            const pages = [];
            for (let i = Math.max(1, cur - 2); i <= Math.min(last, cur + 2); i++) pages.push(i);
            return pages;
        },

        // ──────────────── SELECTION ────────────────
        toggleAll(e) {
            this.selectedIds = e.target.checked ? this.invoices.map(i => i.id) : [];
        },

        toggleSelect(id) {
            if (this.selectedIds.includes(id)) {
                this.selectedIds = this.selectedIds.filter(i => i !== id);
            } else {
                this.selectedIds.push(id);
            }
        },

        // ──────────────── CREATE / EDIT ────────────────
        openCreate() {
            this.formErrors = {};
            this.modal.editId = null;
            this.form = {
                customer_id: '', invoice_number: '{{ $invoiceNumber }}',
                issue_date: new Date().toISOString().split('T')[0],
                due_date: new Date(Date.now() + 30*86400000).toISOString().split('T')[0],
                penalty_type: 'none', penalty_value: '', notes: '',
                items: [{ description: '', quantity: 1, price: 0 }],
            };
            this.calcTotal();
            this.modal.open = true;
        },

        openEdit(inv) {
            this.drawer.open = false;
            this.formErrors  = {};
            this.modal.editId = inv.id;

            // Fetch full invoice with items
            fetch(`/invoices/${inv.id}/edit`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            })
            .then(r => r.json())
            .then(data => {
                const i = data.invoice;
                this.form = {
                    customer_id:   String(i.customer_id),
                    invoice_number: i.invoice_number,
                    issue_date:    i.issue_date ? i.issue_date.split('T')[0] : '',
                    due_date:      i.due_date   ? i.due_date.split('T')[0]   : '',
                    penalty_type:  i.penalty_type  ?? 'none',
                    penalty_value: i.penalty_value ?? '',
                    notes:         i.notes         ?? '',
                    items: (i.items ?? []).map(it => ({
                        description: it.description,
                        quantity:    parseFloat(it.quantity),
                        price:       parseFloat(it.price),
                    })),
                };
                if (!this.form.items.length) this.form.items = [{ description: '', quantity: 1, price: 0 }];
                this.calcTotal();
                this.modal.open = true;
            })
            .catch(() => this.toast('Failed to load invoice data.', 'error'));
        },

        // ──────────────── FORM ITEMS ────────────────
        addItem() {
            this.form.items.push({ description: '', quantity: 1, price: 0 });
        },

        removeItem(idx) {
            this.form.items.splice(idx, 1);
            this.calcTotal();
        },

        calcTotal() {
            this.grandTotal = this.form.items.reduce((s, it) => s + (parseFloat(it.quantity)||0) * (parseFloat(it.price)||0), 0);
        },

        // ──────────────── SUBMIT ────────────────
        async submitForm() {
            if (this.modal.submitting) return;
            this.modal.submitting = true;
            this.formErrors = {};

            const isEdit = !!this.modal.editId;
            const url    = isEdit ? `/invoices/${this.modal.editId}` : '/invoices';

            const payload = {
                ...this.form,
                items: this.form.items.map(it => ({
                    description: it.description,
                    quantity:    parseFloat(it.quantity) || 0,
                    price:       parseFloat(it.price)    || 0,
                })),
            };

            try {
                const res = await fetch(url, {
                    method:  isEdit ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type':     'application/json',
                        'Accept':           'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (!res.ok) {
                    if (res.status === 422) {
                        this.formErrors = data.errors;
                        this.toast('Please fix the errors below.', 'error');
                    } else {
                        this.toast(data.message || 'An error occurred.', 'error');
                    }
                } else {
                    this.modal.open = false;
                    this.toast(data.message || 'Invoice saved!', 'success');
                    this.loadData(this.pagination.current_page);
                    this.loadAnalytics();
                }
            } catch (e) {
                this.toast('Network error. Please try again.', 'error');
            } finally {
                this.modal.submitting = false;
            }
        },

        // ──────────────── DETAIL DRAWER ────────────────
        openDetail(inv) {
            // Fetch full invoice with items
            fetch(`/invoices/${inv.id}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            })
            .then(r => r.json())
            .then(data => {
                this.drawer.invoice = data.invoice;
                this.drawer.open    = true;
            })
            .catch(() => {
                // Fallback to cached data
                this.drawer.invoice = inv;
                this.drawer.open    = true;
            });
        },

        // ──────────────── MARK PAID ────────────────
        confirmMarkPaid(inv) {
            this.confirm = {
                open:        true,
                title:       'Mark as Paid?',
                message:     `Mark ${inv.invoice_number} as fully paid? This will update the status immediately.`,
                confirmText: 'Mark Paid',
                type:        'success',
                onConfirm:   () => this.doMarkPaid(inv),
            };
        },

        async doMarkPaid(inv) {
            try {
                const res = await fetch(`/invoices/${inv.id}/mark-paid`, {
                    method:  'POST',
                    headers: {
                        'Accept':           'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const data = await res.json();
                if (res.ok) {
                    // Optimistic update
                    const found = this.invoices.find(i => i.id === inv.id);
                    if (found) found.status = 'paid';
                    if (this.drawer.invoice?.id === inv.id) this.drawer.invoice.status = 'paid';
                    this.toast('Invoice marked as paid!', 'success');
                    this.loadAnalytics();
                } else {
                    this.toast(data.message || 'Failed to mark as paid.', 'error');
                }
            } catch (e) {
                this.toast('Network error.', 'error');
            }
        },

        // ──────────────── DELETE ────────────────
        confirmDelete(inv) {
            this.confirm = {
                open:        true,
                title:       'Delete Invoice?',
                message:     `Are you sure you want to delete ${inv.invoice_number}? This action cannot be undone.`,
                confirmText: 'Yes, Delete',
                type:        'danger',
                onConfirm:   () => this.doDelete(inv.id),
            };
        },

        async doDelete(id) {
            try {
                const res = await fetch(`/invoices/${id}`, {
                    method:  'POST',
                    headers: {
                        'Content-Type':     'application/json',
                        'Accept':           'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ _method: 'DELETE' }),
                });
                const data = await res.json();
                if (res.ok) {
                    this.invoices    = this.invoices.filter(i => i.id !== id);
                    this.selectedIds = this.selectedIds.filter(i => i !== id);
                    if (this.drawer.invoice?.id === id) this.drawer.open = false;
                    this.toast('Invoice deleted.', 'success');
                    this.loadAnalytics();
                } else {
                    this.toast(data.message || 'Failed to delete.', 'error');
                }
            } catch (e) {
                this.toast('Network error.', 'error');
            }
        },

        // ──────────────── BULK ────────────────
        bulkMarkPaid() {
            this.confirm = {
                open: true,
                title: `Mark ${this.selectedIds.length} Invoices as Paid?`,
                message: 'Selected invoices will be marked as paid immediately.',
                confirmText: 'Mark All Paid',
                type: 'success',
                onConfirm: async () => {
                    await Promise.all(this.selectedIds.map(id => this.doMarkPaid({ id, invoice_number: '' })));
                    this.selectedIds = [];
                    this.loadData(this.pagination.current_page);
                },
            };
        },

        bulkDelete() {
            this.confirm = {
                open: true,
                title: `Delete ${this.selectedIds.length} Invoices?`,
                message: 'This will permanently delete all selected invoices. This cannot be undone.',
                confirmText: 'Delete All',
                type: 'danger',
                onConfirm: async () => {
                    await Promise.all(this.selectedIds.map(id => this.doDelete(id)));
                    this.selectedIds = [];
                    this.loadData(this.pagination.current_page);
                },
            };
        },

        // ──────────────── HELPERS ────────────────
        fmt(n) {
            return parseFloat(n || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        fmtDate(d) {
            if (!d) return '—';
            const dt = new Date(d);
            return dt.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
        },

        isOverdue(inv) {
            if (!inv || inv.status === 'paid') return false;
            return new Date(inv.due_date) < new Date();
        },

        badgeClass(status) {
            return {
                'paid':    'badge-paid',
                'unpaid':  'badge-unpaid',
                'partial': 'badge-partial',
                'overdue': 'badge-overdue',
            }[status] ?? 'badge-draft';
        },

        initials(name) {
            if (!name) return '?';
            return name.split(' ').slice(0,2).map(w => w[0]).join('').toUpperCase();
        },

        avatarColor(name) {
            if (!name) return '#AA8D63';
            const colors = ['#10B981','#3B82F6','#8B5CF6','#F59E0B','#EF4444','#14B8A6','#F43F5E','#6366F1'];
            let hash = 0;
            for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
            return colors[Math.abs(hash) % colors.length];
        },

        toast(msg, type = 'success') {
            window.dispatchEvent(new CustomEvent('notify', { detail: { msg, type } }));
        },
    };
}
</script>
@endpush
