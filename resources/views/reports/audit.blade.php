@extends('layouts.app')
@section('title', 'Audit Trail')
@section('page-title', 'Audit Trail')

@section('content')
<div class="bg-slate-900 border border-slate-800/50 rounded-2xl p-5 mb-6">
    <form method="GET" class="flex items-center gap-4">
        <select name="model_type" onchange="this.form.submit()" class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm text-slate-300 focus:outline-none focus:border-emerald-500/50 transition-all">
            <option value="">All Types</option>
            <option value="App\Models\Invoice" {{ request('model_type') === 'App\Models\Invoice' ? 'selected' : '' }}>Invoices</option>
            <option value="App\Models\Payment" {{ request('model_type') === 'App\Models\Payment' ? 'selected' : '' }}>Payments</option>
            <option value="App\Models\Customer" {{ request('model_type') === 'App\Models\Customer' ? 'selected' : '' }}>Customers</option>
            <option value="App\Models\Expense" {{ request('model_type') === 'App\Models\Expense' ? 'selected' : '' }}>Expenses</option>
        </select>
    </form>
</div>

<div class="bg-slate-900 border border-slate-800/50 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800/50">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Timestamp</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">User</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Action</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Model</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">ID</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/30">
                @forelse($trails as $trail)
                <tr class="hover:bg-slate-800/20 transition-colors">
                    <td class="px-5 py-3 text-slate-400 text-xs">{{ $trail->created_at->format('M d, Y H:i:s') }}</td>
                    <td class="px-5 py-3 text-slate-300">{{ $trail->user->name ?? 'System' }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-lg
                            @if($trail->action === 'created') bg-emerald-500/10 text-emerald-400
                            @elseif($trail->action === 'updated') bg-amber-500/10 text-amber-400
                            @else bg-red-500/10 text-red-400 @endif">
                            {{ $trail->action }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-slate-400 text-xs">{{ class_basename($trail->model_type) }}</td>
                    <td class="px-5 py-3 text-slate-400">#{{ $trail->model_id }}</td>
                    <td class="px-5 py-3 text-slate-500 text-xs">{{ $trail->ip_address }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">No audit records</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($trails->hasPages())
    <div class="px-5 py-4 border-t border-slate-800/50">{{ $trails->links() }}</div>
    @endif
</div>
@endsection

