@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Overview')

@push('styles')
<style>
/* =============================================
   DASHBOARD — Premium SaaS Design System
   8px grid · Outfit font · warm green palette
   ============================================= */

/* KPI Card shimmer on hover */
.kpi-card {
    transition: box-shadow 0.3s ease, transform 0.3s ease;
    will-change: transform;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 48px -12px rgba(21,85,65,0.12), 0 4px 16px -4px rgba(21,85,65,0.06);
}
.kpi-card:hover .kpi-icon {
    transform: scale(1.12) rotate(-3deg);
}
.kpi-icon {
    transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
}

/* Section card */
.dash-card {
    background: #fff;
    border: 1px solid rgba(210,194,168,0.45);
    border-radius: 20px;
    overflow: hidden;
    transition: box-shadow 0.25s ease;
}
.dash-card:hover {
    box-shadow: 0 8px 32px -8px rgba(21,85,65,0.08);
}

/* Row hover */
.list-row {
    transition: background 0.18s ease;
}
.list-row:hover { background: #F7F2E8; }

/* Trend badge */
.trend-up   { color: #1CA074; background: #DDF6ED; }
.trend-down { color: #dc2626; background: #fee2e2; }
.trend-flat { color: #AA8D63; background: #EFE6D8; }

/* Progress bar */
.progress-bar-track {
    background: rgba(210,194,168,0.25);
    border-radius: 99px;
    overflow: hidden;
    height: 6px;
}
.progress-bar-fill {
    height: 100%;
    border-radius: 99px;
    transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Status badges */
.badge-paid    { background: #DDF6ED; color: #1B8060; }
.badge-partial { background: #FEF3C7; color: #92400E; }
.badge-overdue { background: #fee2e2; color: #991b1b; }
.badge-unpaid  { background: #EFE6D8; color: #57442D; }

/* Chart tooltip */
.chart-tooltip-custom {
    background: #fff;
    border: 1px solid rgba(210,194,168,0.6);
    border-radius: 12px;
    padding: 10px 14px;
    box-shadow: 0 8px 24px rgba(21,85,65,0.12);
    font-family: 'Outfit', sans-serif;
    font-size: 12px;
}

/* Filter selects */
.filter-select {
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23AA8D63' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 14px;
    padding-right: 32px;
    cursor: pointer;
}

/* Animated counter */
@keyframes countUp {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
.counter-anim { animation: countUp 0.5s ease both; }

/* Avatar gradient pool */
.avatar-a { background: linear-gradient(135deg, #10B981, #1CA074); }
.avatar-b { background: linear-gradient(135deg, #F59E0B, #D97706); }
.avatar-c { background: linear-gradient(135deg, #8B5CF6, #7C3AED); }
.avatar-d { background: linear-gradient(135deg, #3B82F6, #2563EB); }
.avatar-e { background: linear-gradient(135deg, #EC4899, #DB2777); }

/* Skeleton loader */
@keyframes shimmer {
    0%   { background-position: -400px 0; }
    100% { background-position: 400px 0; }
}
.skeleton {
    background: linear-gradient(90deg, #F7F2E8 25%, #EFE6D8 50%, #F7F2E8 75%);
    background-size: 800px 100%;
    animation: shimmer 1.6s ease infinite;
    border-radius: 8px;
}

/* Low-stock severity */
.severity-critical { color: #991b1b; background: #fee2e2; }
.severity-low      { color: #92400E; background: #FEF3C7; }

/* Chart container wrapper */
.chart-wrapper {
    position: relative;
    width: 100%;
}
</style>
@endpush

@section('content')
{{-- ── HEADER ROW: Period filters + quick stats label ── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    {{-- Period filters --}}
    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-3 flex-wrap" id="period-filter-form">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-beige-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <select name="month" onchange="this.form.submit()"
                    class="filter-select px-3 py-2 bg-white border border-beige-200 rounded-xl text-sm font-semibold text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all shadow-sm"
                    id="month-filter">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <select name="year" onchange="this.form.submit()"
                class="filter-select px-3 py-2 bg-white border border-beige-200 rounded-xl text-sm font-semibold text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all shadow-sm"
                id="year-filter">
            @for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>

    {{-- Period label pill --}}
    <div class="inline-flex items-center gap-2 px-4 py-2 bg-mint-500/8 rounded-full border border-mint-200/50">
        <span class="w-2 h-2 rounded-full bg-mint-500 animate-pulse"></span>
        <span class="text-[11px] font-bold text-mint-700 uppercase tracking-widest">
            {{ Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}
        </span>
    </div>
</div>

{{-- ── KPI CARDS ── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

    {{-- KPI 1: Today's Income --}}
    <div class="kpi-card bg-white border border-beige-200/50 rounded-2xl p-6 flex flex-col gap-4" id="kpi-today-income">
        <div class="flex items-start justify-between">
            <div class="kpi-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-mint-400/20 to-mint-500/10 flex items-center justify-center ring-1 ring-mint-200/50">
                <svg class="w-7 h-7 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="px-2.5 py-1 rounded-lg bg-mint-50 text-[10px] font-bold text-mint-600 uppercase tracking-widest border border-mint-100">Today</span>
        </div>
        <div>
            <p class="text-xs font-bold text-beige-400 uppercase tracking-widest mb-1.5">Today's Income</p>
            <p class="text-3xl font-black text-mint-900 counter-anim leading-none">₱{{ number_format($todayIncome, 2) }}</p>
        </div>
        <div class="flex items-center gap-2 pt-2 border-t border-beige-100">
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold trend-up">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                Payments received
            </span>
        </div>
    </div>

    {{-- KPI 2: Monthly Revenue --}}
    @php
        $incomeUp = $incomeTrend >= 0;
    @endphp
    <div class="kpi-card bg-white border border-beige-200/50 rounded-2xl p-6 flex flex-col gap-4" id="kpi-monthly-revenue">
        <div class="flex items-start justify-between">
            <div class="kpi-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-mint-500/20 to-mint-600/10 flex items-center justify-center ring-1 ring-mint-200/50">
                <svg class="w-7 h-7 text-mint-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <span class="px-2.5 py-1 rounded-lg bg-mint-50 text-[10px] font-bold text-mint-600 uppercase tracking-widest border border-mint-100">Monthly</span>
        </div>
        <div>
            <p class="text-xs font-bold text-beige-400 uppercase tracking-widest mb-1.5">Total Revenue</p>
            <p class="text-3xl font-black text-mint-900 counter-anim leading-none">₱{{ number_format($monthIncome, 2) }}</p>
        </div>
        <div class="flex items-center gap-2 pt-2 border-t border-beige-100">
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold {{ $incomeUp ? 'trend-up' : 'trend-down' }}">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $incomeUp ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"/>
                </svg>
                {{ $incomeTrend > 0 ? '+' : '' }}{{ $incomeTrend }}% vs last month
            </span>
        </div>
    </div>

    {{-- KPI 3: Pending / Receivables --}}
    <div class="kpi-card bg-white border border-beige-200/50 rounded-2xl p-6 flex flex-col gap-4" id="kpi-pending">
        <div class="flex items-start justify-between">
            <div class="kpi-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400/20 to-amber-500/10 flex items-center justify-center ring-1 ring-amber-200/50">
                <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-[10px] font-bold text-amber-600 uppercase tracking-widest border border-amber-100">Pending</span>
        </div>
        <div>
            <p class="text-xs font-bold text-beige-400 uppercase tracking-widest mb-1.5">Receivables</p>
            <p class="text-3xl font-black text-mint-900 counter-anim leading-none">₱{{ number_format($pendingPayments, 2) }}</p>
        </div>
        <div class="flex items-center gap-2 pt-2 border-t border-beige-100">
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold trend-flat">
                Awaiting collection
            </span>
        </div>
    </div>

    {{-- KPI 4: Overdue --}}
    <div class="kpi-card bg-white border {{ $overdueCount > 0 ? 'border-red-200/60' : 'border-beige-200/50' }} rounded-2xl p-6 flex flex-col gap-4" id="kpi-overdue">
        <div class="flex items-start justify-between">
            <div class="kpi-icon w-14 h-14 rounded-2xl {{ $overdueCount > 0 ? 'bg-gradient-to-br from-red-400/20 to-red-500/10 ring-1 ring-red-200/50' : 'bg-gradient-to-br from-mint-400/10 to-mint-500/5 ring-1 ring-mint-200/30' }} flex items-center justify-center">
                <svg class="w-7 h-7 {{ $overdueCount > 0 ? 'text-red-600' : 'text-mint-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <span class="px-2.5 py-1 rounded-lg {{ $overdueCount > 0 ? 'bg-red-50 text-red-600 border-red-100' : 'bg-beige-50 text-beige-500 border-beige-100' }} text-[10px] font-bold uppercase tracking-widest border">
                {{ $overdueCount > 0 ? 'Alert' : 'Clear' }}
            </span>
        </div>
        <div>
            <p class="text-xs font-bold text-beige-400 uppercase tracking-widest mb-1.5">Overdue Invoices</p>
            <p class="text-3xl font-black {{ $overdueCount > 0 ? 'text-red-600' : 'text-mint-400' }} counter-anim leading-none">
                {{ $overdueCount }} <span class="text-base font-bold text-beige-400">cases</span>
            </p>
        </div>
        <div class="flex items-center gap-2 pt-2 border-t border-beige-100">
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold {{ $overdueCount > 0 ? 'severity-critical' : 'trend-up' }}">
                ₱{{ number_format($overdueAmount, 2) }} at risk
            </span>
        </div>
    </div>

</div>

{{-- ── FINANCIAL SUMMARY + CHART ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">

    {{-- Financial Summary Card --}}
    <div class="dash-card p-6 sm:p-8 flex flex-col gap-6" id="financial-summary-card">
        <div>
            <h2 class="text-[10px] font-bold text-beige-400 uppercase tracking-widest mb-6">Financial Summary</h2>
            <div class="space-y-5">
                {{-- Income row --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-mint-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-mint-800">Total Income</span>
                        </div>
                        <span class="text-sm font-extrabold text-mint-600">₱{{ number_format($monthIncome, 2) }}</span>
                    </div>
                    <div class="progress-bar-track">
                        @php
                            $maxVal = max($monthIncome, $monthExpenses, 1);
                            $incomeWidth = min(100, round(($monthIncome / $maxVal) * 100));
                            $expensesWidth = min(100, round(($monthExpenses / $maxVal) * 100));
                        @endphp
                        <div class="progress-bar-fill bg-gradient-to-r from-mint-400 to-mint-500" style="width: {{ $incomeWidth }}%"></div>
                    </div>
                </div>

                {{-- Expenses row --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10h10"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-mint-800">Total Expenses</span>
                        </div>
                        <span class="text-sm font-extrabold text-red-500">-₱{{ number_format($monthExpenses, 2) }}</span>
                    </div>
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill bg-gradient-to-r from-red-400 to-red-500" style="width: {{ $expensesWidth }}%"></div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-beige-100"></div>

                {{-- Net Profit highlighted --}}
                <div class="rounded-2xl p-4 {{ $monthProfit >= 0 ? 'bg-mint-50 border border-mint-100' : 'bg-red-50 border border-red-100' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold {{ $monthProfit >= 0 ? 'text-mint-600' : 'text-red-600' }} uppercase tracking-widest">Net Profit</p>
                            <p class="text-2xl font-black {{ $monthProfit >= 0 ? 'text-mint-700' : 'text-red-700' }} mt-1 leading-none">
                                {{ $monthProfit < 0 ? '-' : '' }}₱{{ number_format(abs($monthProfit), 2) }}
                            </p>
                            @if($monthProfit < 0)
                            <p class="text-[10px] font-bold text-red-500 uppercase mt-1">⚠ Loss Recorded</p>
                            @endif
                        </div>
                        <div class="w-10 h-10 rounded-xl {{ $monthProfit >= 0 ? 'bg-mint-500' : 'bg-red-500' }} flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $monthProfit >= 0 ? 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' : 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z' }}"/>
                            </svg>
                        </div>
                    </div>
                    @php $profitUp = $profitTrend >= 0; @endphp
                    <div class="mt-3 flex items-center gap-1.5">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $profitUp ? 'trend-up' : 'trend-down' }}">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $profitUp ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"/></svg>
                            {{ $profitTrend > 0 ? '+' : '' }}{{ $profitTrend }}% vs last month
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Clients footer --}}
        <div class="mt-auto p-4 rounded-2xl bg-beige-50 border border-beige-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-beige-200 flex items-center justify-center">
                    <svg class="w-4 h-4 text-beige-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-[10px] font-bold text-beige-500 uppercase tracking-wider">Total Clients</span>
            </div>
            <span class="text-base font-black text-mint-900">{{ $totalCustomers }}</span>
        </div>
    </div>

    {{-- Revenue vs Expenses Chart --}}
    <div class="dash-card lg:col-span-2 p-6 sm:p-8" id="revenue-chart-card">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-sm font-extrabold text-mint-900">Revenue vs Expenses</h2>
                <p class="text-[11px] text-beige-400 font-medium mt-0.5">Last 6 months overview</p>
            </div>
            <div class="flex items-center gap-4 flex-wrap">
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-full bg-mint-500 shadow-sm shadow-mint-300/50"></div>
                    <span class="text-[11px] font-bold text-mint-900 uppercase tracking-wide">Revenue</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-full bg-red-400 shadow-sm shadow-red-300/50"></div>
                    <span class="text-[11px] font-bold text-mint-900 uppercase tracking-wide">Expenses</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-full bg-amber-400 shadow-sm shadow-amber-300/50"></div>
                    <span class="text-[11px] font-bold text-mint-900 uppercase tracking-wide">Profit</span>
                </div>
            </div>
        </div>
        <div class="chart-wrapper" style="height: 240px; min-height: 180px; max-height: 300px;">
            <canvas id="revenueChart" style="display: block; width: 100%; height: 100%;"></canvas>
        </div>
    </div>
</div>

{{-- ── RECENT INVOICES + RECENT PAYMENTS ── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">

    {{-- Recent Invoices --}}
    <div class="dash-card flex flex-col" id="recent-invoices-card">
        <div class="flex items-center justify-between px-6 py-5 border-b border-beige-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-mint-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h2 class="text-sm font-extrabold text-mint-900">Recent Invoices</h2>
            </div>
            <a href="{{ route('invoices.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-bold text-mint-600 bg-mint-50 hover:bg-mint-100 border border-mint-100 hover:border-mint-200 uppercase tracking-widest transition-all duration-200">
                View All
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="flex-1 divide-y divide-beige-100">
            @forelse($recentInvoices as $invoice)
            @php
                $initials = strtoupper(substr($invoice->customer->name ?? '?', 0, 2));
                $avatarClasses = ['avatar-a','avatar-b','avatar-c','avatar-d','avatar-e'];
                $avatarClass = $avatarClasses[$loop->index % 5];
            @endphp
            <div class="list-row flex items-center gap-4 px-6 py-4 group">
                <a href="{{ route('invoices.show', $invoice) }}" class="flex items-center gap-4 min-w-0 flex-1">
                    {{-- Avatar --}}
                    <div class="w-10 h-10 rounded-xl {{ $avatarClass }} flex items-center justify-center text-xs font-black text-white flex-shrink-0 shadow-sm">
                        {{ $initials }}
                    </div>
                    {{-- Details --}}
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-mint-900 truncate">{{ $invoice->invoice_number }}</p>
                        <p class="text-xs text-beige-400 font-semibold truncate">{{ $invoice->customer->name ?? 'N/A' }}</p>
                    </div>
                </a>
                <div class="flex items-center gap-3 flex-shrink-0 ml-auto">
                    <div class="text-right">
                        <p class="text-sm font-extrabold text-mint-900">₱{{ number_format($invoice->total_amount, 2) }}</p>
                        <span class="inline-flex px-2.5 py-0.5 text-[9px] font-black uppercase rounded-full tracking-wider
                            @if($invoice->status === 'paid') badge-paid
                            @elseif($invoice->status === 'partial') badge-partial
                            @elseif($invoice->status === 'overdue') badge-overdue
                            @else badge-unpaid @endif">
                            {{ $invoice->status }}
                        </span>
                    </div>
                    <a href="{{ route('invoices.pdf', $invoice) }}"
                       class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-beige-300 hover:text-mint-600 hover:bg-mint-50 border border-transparent hover:border-mint-100 transition-all duration-200 opacity-0 group-hover:opacity-100"
                       title="Download PDF">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                    </a>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-14 gap-3">
                <div class="w-14 h-14 rounded-2xl bg-beige-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-bold text-beige-400">No invoices yet</p>
                <a href="{{ route('invoices.create') }}" class="text-xs font-bold text-mint-600 hover:text-mint-700 transition-colors">+ Create your first invoice</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Payments --}}
    <div class="dash-card flex flex-col" id="recent-payments-card">
        <div class="flex items-center justify-between px-6 py-5 border-b border-beige-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-mint-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h2 class="text-sm font-extrabold text-mint-900">Recent Payments</h2>
            </div>
            <a href="{{ route('payments.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-bold text-mint-600 bg-mint-50 hover:bg-mint-100 border border-mint-100 hover:border-mint-200 uppercase tracking-widest transition-all duration-200">
                View All
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="flex-1 divide-y divide-beige-100">
            @forelse($recentPayments as $payment)
            @php
                $initials = strtoupper(substr($payment->customer->name ?? '?', 0, 2));
                $avatarClasses = ['avatar-a','avatar-c','avatar-b','avatar-e','avatar-d'];
                $avatarClass = $avatarClasses[$loop->index % 5];
            @endphp
            <div class="list-row flex items-center gap-4 px-6 py-4 group">
                <div class="flex items-center gap-4 min-w-0 flex-1">
                    {{-- Avatar --}}
                    <div class="w-10 h-10 rounded-xl {{ $avatarClass }} flex items-center justify-center text-xs font-black text-white flex-shrink-0 shadow-sm">
                        {{ $initials }}
                    </div>
                    {{-- Info --}}
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-mint-900 truncate">{{ $payment->customer->name ?? 'N/A' }}</p>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            {{-- Method badge --}}
                            @if($payment->payment_method === 'cash')
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase text-mint-700 bg-mint-50 border border-mint-100 rounded-full px-2 py-0.5">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Cash
                            </span>
                            @elseif($payment->payment_method === 'gcash')
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase text-blue-600 bg-blue-50 border border-blue-100 rounded-full px-2 py-0.5">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                GCash
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase text-purple-600 bg-purple-50 border border-purple-100 rounded-full px-2 py-0.5">
                                Transfer
                            </span>
                            @endif
                            <span class="text-[10px] text-beige-400 font-medium">{{ $payment->payment_date->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <p class="text-sm font-extrabold text-mint-600 text-right group-hover:scale-105 transition-transform duration-200">
                        +₱{{ number_format($payment->amount, 2) }}
                    </p>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-14 gap-3">
                <div class="w-14 h-14 rounded-2xl bg-beige-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-beige-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <p class="text-sm font-bold text-beige-400">No payments recorded</p>
                <a href="{{ route('payments.create') }}" class="text-xs font-bold text-mint-600 hover:text-mint-700 transition-colors">+ Record a payment</a>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ── BUSINESS WIDGETS ROW ── --}}
@if($lowStockProducts->count() > 0 || $bestSellers->count() > 0 || (is_sari_sari() && $topUtang->count() > 0))
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

    {{-- Low Stock Alert --}}
    @if($lowStockProducts->count() > 0)
    <div class="dash-card border-amber-200/50" id="low-stock-card">
        <div class="flex items-center justify-between px-6 py-5 border-b border-amber-100 bg-amber-50/40">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-extrabold text-amber-900">Low Stock Alert</h2>
            </div>
            <a href="{{ route('products.index', ['stock_status' => 'low']) }}"
               class="text-[10px] font-bold text-amber-600 hover:text-amber-700 uppercase tracking-widest transition-colors">
                View All
            </a>
        </div>
        <div class="divide-y divide-beige-100">
            @foreach($lowStockProducts as $product)
            @php
                $isCritical = $product->stock_quantity <= 0;
                $stockPct = $product->reorder_level > 0
                    ? min(100, round(($product->stock_quantity / ($product->reorder_level * 2)) * 100))
                    : ($product->stock_quantity > 0 ? 20 : 0);
            @endphp
            <div class="px-6 py-4">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-mint-900 truncate">{{ $product->name }}</p>
                        <p class="text-xs text-beige-400 font-medium">Reorder at {{ $product->reorder_level }}</p>
                    </div>
                    <span class="flex-shrink-0 px-2.5 py-1 rounded-lg text-[10px] font-black {{ $isCritical ? 'severity-critical' : 'severity-low' }}">
                        {{ $product->stock_quantity <= 0 ? 'Out of Stock' : $product->stock_quantity . ' left' }}
                    </span>
                </div>
                <div class="progress-bar-track">
                    <div class="progress-bar-fill {{ $isCritical ? 'bg-red-500' : 'bg-amber-400' }}" style="width: {{ $stockPct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-6 py-4 bg-amber-50/30 border-t border-amber-100">
            <a href="{{ route('products.index') }}"
               class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold uppercase tracking-wider transition-all duration-200 shadow-sm hover:shadow-amber-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Manage Inventory
            </a>
        </div>
    </div>
    @endif

    {{-- Best Sellers --}}
    @if($bestSellers->count() > 0)
    <div class="dash-card" id="best-sellers-card">
        <div class="flex items-center justify-between px-6 py-5 border-b border-beige-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <h2 class="text-sm font-extrabold text-mint-900">Best Sellers</h2>
            </div>
            <span class="text-[10px] font-bold text-beige-400 uppercase tracking-widest">This Month</span>
        </div>
        <div class="divide-y divide-beige-100">
            @foreach($bestSellers as $i => $seller)
            @php
                $rankColors = ['bg-amber-400 text-white','bg-mint-500 text-white','bg-beige-300 text-beige-700','bg-beige-100 text-beige-600','bg-beige-100 text-beige-600'];
                $rankColor = $rankColors[$i] ?? 'bg-beige-100 text-beige-500';
            @endphp
            <div class="list-row flex items-center gap-4 px-6 py-3.5">
                <span class="w-7 h-7 rounded-xl {{ $rankColor }} flex items-center justify-center text-xs font-black flex-shrink-0">
                    {{ $i + 1 }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-mint-900 truncate">{{ $seller->name }}</p>
                    <p class="text-xs text-beige-400 font-medium">{{ $seller->total_sold }} units sold</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm font-extrabold text-mint-600">₱{{ number_format($seller->total_revenue, 2) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Top Utang (Sari-sari) --}}
    @if(is_sari_sari() && $topUtang->count() > 0)
    <div class="dash-card border-red-200/50" id="top-utang-card">
        <div class="flex items-center justify-between px-6 py-5 border-b border-red-100 bg-red-50/30">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-red-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h2 class="text-sm font-extrabold text-red-900">Top Utang</h2>
            </div>
            <a href="{{ route('customers.index') }}"
               class="text-[10px] font-bold text-red-500 hover:text-red-700 uppercase tracking-widest transition-colors">
                View All
            </a>
        </div>
        <div class="divide-y divide-beige-100">
            @foreach($topUtang as $customer)
            <a href="{{ route('customers.show', $customer) }}"
               class="list-row flex items-center gap-4 px-6 py-3.5">
                <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center text-xs font-black text-red-700 flex-shrink-0">
                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                </div>
                <p class="text-sm font-bold text-mint-900 truncate flex-1">{{ $customer->name }}</p>
                <span class="text-sm font-extrabold text-red-600 flex-shrink-0">₱{{ number_format($customer->balance, 2) }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Restaurant Overview --}}
    @if(is_restaurant())
    <div class="dash-card" id="restaurant-overview-card">
        <div class="px-6 py-5 border-b border-beige-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-mint-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-mint-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8l-1 4h11m-8-4h6"/></svg>
                </div>
                <h2 class="text-sm font-extrabold text-mint-900">Restaurant Overview</h2>
            </div>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 border border-amber-100">
                <span class="text-sm font-semibold text-amber-800">Kitchen Queue</span>
                <span class="px-3 py-1 rounded-lg bg-amber-100 text-amber-700 text-sm font-black border border-amber-200">{{ $kitchenQueue }} orders</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-xl bg-beige-50 border border-beige-100">
                <span class="text-sm font-semibold text-mint-800">Today's Orders</span>
                <span class="text-sm font-black text-mint-900">{{ $todayOrders }}</span>
            </div>
            @if($activeTables)
            <div class="pt-2 border-t border-beige-100">
                <p class="text-[10px] font-bold text-beige-400 uppercase tracking-widest mb-3">Table Status</p>
                <div class="flex gap-2 flex-wrap">
                    @foreach($activeTables->take(10) as $table)
                    <span class="px-2.5 py-1.5 rounded-xl text-xs font-bold
                        {{ $table->status === 'available' ? 'bg-mint-100 text-mint-700 border border-mint-200' : '' }}
                        {{ $table->status === 'occupied'  ? 'bg-red-100 text-red-700 border border-red-200'   : '' }}
                        {{ $table->status === 'reserved'  ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
                        {{ $table->status === 'dirty'     ? 'bg-beige-100 text-beige-500 border border-beige-200' : '' }}">
                        T{{ $table->table_number }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;

    const chartData = @json($monthlyChart);

    // Create gradients
    const c = ctx.getContext('2d');
    const chartHeight = ctx.parentElement.offsetHeight || 240;

    const gradIncome = c.createLinearGradient(0, 0, 0, chartHeight);
    gradIncome.addColorStop(0,   'rgba(16, 185, 129, 0.30)');
    gradIncome.addColorStop(0.6, 'rgba(16, 185, 129, 0.06)');
    gradIncome.addColorStop(1,   'rgba(16, 185, 129, 0.00)');

    const gradExpenses = c.createLinearGradient(0, 0, 0, chartHeight);
    gradExpenses.addColorStop(0,   'rgba(248, 113, 113, 0.18)');
    gradExpenses.addColorStop(1,   'rgba(248, 113, 113, 0.00)');

    const gradProfit = c.createLinearGradient(0, 0, 0, chartHeight);
    gradProfit.addColorStop(0,   'rgba(245, 158, 11, 0.20)');
    gradProfit.addColorStop(1,   'rgba(245, 158, 11, 0.00)');

    // Custom tooltip
    const customTooltip = {
        enabled: false,
        external: function (context) {
            let tooltipEl = document.getElementById('chartjs-tooltip');
            if (!tooltipEl) {
                tooltipEl = document.createElement('div');
                tooltipEl.id = 'chartjs-tooltip';
                tooltipEl.className = 'chart-tooltip-custom';
                tooltipEl.style.cssText = 'position:absolute;pointer-events:none;transition:all .15s ease;z-index:50;';
                document.body.appendChild(tooltipEl);
            }

            const model = context.tooltip;
            if (model.opacity === 0) {
                tooltipEl.style.opacity = '0';
                return;
            }

            if (model.body) {
                const title = model.title ? model.title[0] : '';
                const lines = model.body.map(b => b.lines);
                let inner = `<p style="font-weight:800;font-size:12px;color:#155541;margin-bottom:6px;">${title}</p>`;
                const colors = ['#10B981','#f87171','#F59E0B'];
                lines.forEach((line, i) => {
                    inner += `<p style="font-size:11px;font-weight:700;color:${colors[i] || '#AA8D63'};margin:2px 0;">${line}</p>`;
                });
                tooltipEl.innerHTML = inner;
            }

            const position = context.chart.canvas.getBoundingClientRect();
            const left = position.left + window.scrollX + model.caretX;
            const top = position.top + window.scrollY + model.caretY;

            tooltipEl.style.opacity = '1';
            tooltipEl.style.left = left + 'px';
            tooltipEl.style.top  = (top - 80) + 'px';
        }
    };

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(d => d.month),
            datasets: [
                {
                    label: 'Revenue',
                    data: chartData.map(d => d.income),
                    backgroundColor: gradIncome,
                    borderColor: '#10B981',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.45,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10B981',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#10B981',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                },
                {
                    label: 'Expenses',
                    data: chartData.map(d => d.expenses),
                    backgroundColor: gradExpenses,
                    borderColor: '#f87171',
                    borderWidth: 2,
                    borderDash: [6, 4],
                    fill: true,
                    tension: 0.45,
                    pointRadius: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#f87171',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#f87171',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                },
                {
                    label: 'Profit',
                    data: chartData.map(d => d.profit),
                    backgroundColor: gradProfit,
                    borderColor: '#F59E0B',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.45,
                    pointRadius: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#F59E0B',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#F59E0B',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    hidden: true,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: { display: false },
                tooltip: customTooltip,
            },
            scales: {
                x: {
                    border: { display: false },
                    grid: { color: 'rgba(210, 194, 168, 0.15)', drawTicks: false },
                    ticks: {
                        color: '#AA8D63',
                        font: { size: 10, weight: '700', family: 'Outfit' },
                        padding: 10,
                        maxRotation: 0,
                        callback: function(value, index) {
                            // Abbreviate: "Jan 2025" → "Jan"
                            const label = this.getLabelForValue(value);
                            return label ? label.split(' ')[0] : '';
                        }
                    }
                },
                y: {
                    border: { display: false },
                    grid: { color: 'rgba(210, 194, 168, 0.15)', drawTicks: false },
                    ticks: {
                        color: '#AA8D63',
                        font: { size: 10, weight: '700', family: 'Outfit' },
                        padding: 12,
                        callback: v => {
                            if (v >= 1000000) return '₱' + (v/1000000).toFixed(1) + 'M';
                            if (v >= 1000)    return '₱' + (v/1000).toFixed(0) + 'k';
                            return '₱' + v;
                        }
                    }
                }
            },
            animation: {
                duration: 800,
                easing: 'easeInOutQuart',
            }
        }
    });

    // Toggle profit line via legend clicks
    const legendItems = document.querySelectorAll('[data-chart-legend]');
    legendItems.forEach(item => {
        item.addEventListener('click', function () {
            const chart = Chart.getChart('revenueChart');
            const index = parseInt(this.dataset.chartLegend);
            const meta = chart.getDatasetMeta(index);
            meta.hidden = !meta.hidden;
            chart.update();
        });
    });
});
</script>
@endpush
