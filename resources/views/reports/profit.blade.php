@extends('layouts.app')
@section('title', 'Profit & Loss')
@section('page-title', 'Profit & Loss')

@section('content')
<div class="bg-slate-900 border border-slate-800/50 rounded-2xl p-5 mb-6">
    <form method="GET" class="flex items-end gap-4 flex-wrap">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
            <input type="date" name="from" value="{{ $from }}" class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-emerald-500/50 transition-all">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
            <input type="date" name="to" value="{{ $to }}" class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm text-slate-200 focus:outline-none focus:border-emerald-500/50 transition-all">
        </div>
        <button type="submit" class="px-4 py-2 bg-cyan-500/10 text-cyan-400 text-sm font-medium rounded-xl hover:bg-cyan-500/20 transition-all">Apply Filter</button>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
    <div class="bg-slate-900 border border-slate-800/50 rounded-2xl p-5">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Total Income</p>
        <p class="text-2xl font-bold text-emerald-400">₱{{ number_format($totalIncome, 2) }}</p>
        <p class="text-xs text-slate-500 mt-1">From payments received</p>
    </div>
    <div class="bg-slate-900 border border-slate-800/50 rounded-2xl p-5">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Total Expenses</p>
        <p class="text-2xl font-bold text-red-400">₱{{ number_format($totalExpenses, 2) }}</p>
        <p class="text-xs text-slate-500 mt-1">Business costs</p>
    </div>
    <div class="bg-slate-900 border border-emerald-500/20 rounded-2xl p-5 bg-gradient-to-br from-slate-900 to-emerald-900/10">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Net Profit</p>
        <p class="text-2xl font-bold {{ $profit >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
            {{ $profit >= 0 ? '' : '-' }}₱{{ number_format(abs($profit), 2) }}
        </p>
        <p class="text-xs {{ $profit >= 0 ? 'text-emerald-500' : 'text-red-500' }} mt-1">
            {{ $profit >= 0 ? 'Profit' : 'Loss' }}
        </p>
    </div>
</div>

<!-- Monthly Chart -->
@if($monthlyData->count())
<div class="bg-slate-900 border border-slate-800/50 rounded-2xl p-6 mb-6">
    <h3 class="text-sm font-medium text-white mb-4">Monthly Breakdown</h3>
    <div class="h-64">
        <canvas id="profitChart"></canvas>
    </div>
</div>

<!-- Monthly Table -->
<div class="bg-slate-900 border border-slate-800/50 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800/50">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Month</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Income</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Expenses</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Profit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/30">
                @foreach($monthlyData as $row)
                <tr>
                    <td class="px-5 py-3 text-slate-300">{{ $row['month'] }}</td>
                    <td class="px-5 py-3 text-right text-emerald-400">₱{{ number_format($row['income'], 2) }}</td>
                    <td class="px-5 py-3 text-right text-red-400">₱{{ number_format($row['expenses'], 2) }}</td>
                    <td class="px-5 py-3 text-right font-bold {{ $row['profit'] >= 0 ? 'text-emerald-400' : 'text-red-400' }}">₱{{ number_format($row['profit'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const data = @json($monthlyData);
    const ctx = document.getElementById('profitChart')?.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.month),
            datasets: [
                { label: 'Income', data: data.map(d => d.income), borderColor: 'rgb(16, 185, 129)', backgroundColor: 'rgba(16, 185, 129, 0.1)', fill: true, tension: 0.3 },
                { label: 'Expenses', data: data.map(d => d.expenses), borderColor: 'rgb(239, 68, 68)', backgroundColor: 'rgba(239, 68, 68, 0.1)', fill: true, tension: 0.3 },
                { label: 'Profit', data: data.map(d => d.profit), borderColor: 'rgb(6, 182, 212)', borderDash: [5, 5], tension: 0.3 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#94a3b8' } } },
            scales: {
                x: { ticks: { color: '#64748b' }, grid: { color: 'rgba(51,65,85,0.3)' } },
                y: { ticks: { color: '#64748b', callback: v => '₱' + v.toLocaleString() }, grid: { color: 'rgba(51,65,85,0.3)' } }
            }
        }
    });
});
</script>
@endpush
@endsection

