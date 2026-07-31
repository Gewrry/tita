@extends('layouts.app')
@section('title', 'Customer Marketability')
@section('page-title', 'Customer Marketability')

@section('content')
<div class="space-y-8">
    <form method="GET" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <input type="date" name="from" value="{{ $from }}" class="px-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-xs font-bold text-mint-800 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
            <span class="text-beige-300 font-black">to</span>
            <input type="date" name="to" value="{{ $to }}" class="px-4 py-2.5 bg-white border border-beige-200 rounded-2xl text-xs font-bold text-mint-800 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
            <button type="submit" class="px-6 py-2.5 bg-beige-100 text-mint-800 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-beige-200 border border-beige-200">Filter</button>
        </div>
        <div class="px-4 py-2 bg-mint-100/60 border border-mint-200 rounded-2xl text-[10px] font-black text-mint-700 uppercase tracking-widest">
            {{ $totalCustomers }} customers in range
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white border border-beige-200/60 rounded-[2rem] p-7 shadow-sm">
            <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2">Best Weekday</p>
            <p class="text-3xl font-black text-mint-800">{{ $bestDay ?? 'No data' }}</p>
        </div>
        <div class="bg-white border border-beige-200/60 rounded-[2rem] p-7 shadow-sm">
            <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2">Best Month</p>
            <p class="text-3xl font-black text-mint-800">{{ $bestMonth ? Carbon\Carbon::createFromFormat('Y-m', $bestMonth)->format('M Y') : 'No data' }}</p>
        </div>
        <div class="bg-white border border-beige-200/60 rounded-[2rem] p-7 shadow-sm">
            <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2">Best Year</p>
            <p class="text-3xl font-black text-mint-800">{{ $bestYear ?? 'No data' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <div class="bg-white border border-beige-200/60 rounded-[2rem] p-7 shadow-sm">
            <h3 class="text-sm font-black text-mint-950 uppercase tracking-widest mb-6">Customers by Day of Week</h3>
            <div class="h-72"><canvas id="weekdayChart"></canvas></div>
        </div>
        <div class="bg-white border border-beige-200/60 rounded-[2rem] p-7 shadow-sm">
            <h3 class="text-sm font-black text-mint-950 uppercase tracking-widest mb-6">Customers by Month</h3>
            <div class="h-72"><canvas id="monthChart"></canvas></div>
        </div>
    </div>

    <div class="bg-white border border-beige-200/60 rounded-[2rem] overflow-hidden shadow-sm">
        <div class="px-7 py-5 border-b border-beige-100">
            <h3 class="text-sm font-black text-mint-950 uppercase tracking-widest">Yearly Customer Totals</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-beige-50/50 border-b border-beige-100">
                        <th class="text-left px-7 py-4 text-[10px] font-black text-beige-500 uppercase tracking-widest">Year</th>
                        <th class="text-right px-7 py-4 text-[10px] font-black text-beige-500 uppercase tracking-widest">Customers</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-beige-100">
                    @forelse($byYear as $year => $count)
                    <tr>
                        <td class="px-7 py-4 font-black text-mint-950">{{ $year }}</td>
                        <td class="px-7 py-4 text-right font-black text-mint-700">{{ $count }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-7 py-12 text-center text-sm font-bold text-beige-400">No customer data in this date range.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const weekdayData = @json($byWeekday);
    const monthData = @json($byMonth);

    new Chart(document.getElementById('weekdayChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(weekdayData),
            datasets: [{
                data: Object.values(weekdayData),
                backgroundColor: '#10B981',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
                x: { grid: { display: false } }
            }
        }
    });

    new Chart(document.getElementById('monthChart'), {
        type: 'line',
        data: {
            labels: Object.keys(monthData).map(key => {
                const parts = key.split('-');
                return new Date(parts[0], parts[1] - 1, 1).toLocaleDateString(undefined, { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                data: Object.values(monthData),
                borderColor: '#0F766E',
                backgroundColor: 'rgba(15, 118, 110, 0.12)',
                borderWidth: 3,
                fill: true,
                tension: 0.35,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush
