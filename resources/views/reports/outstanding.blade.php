@extends('layouts.app')
@section('title', 'Outstanding Balances')
@section('page-title', 'Outstanding Balances')

@section('content')
<div class="bg-slate-900 border border-slate-800/50 rounded-2xl p-5 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Outstanding</p>
            <p class="text-2xl font-bold text-amber-400 mt-1">₱{{ number_format($totalOutstanding, 2) }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-slate-400">{{ $invoices->count() }} invoice(s)</p>
        </div>
    </div>
</div>

<div class="bg-slate-900 border border-slate-800/50 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-800/50">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Invoice</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Customer</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Due Date</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Total</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Paid</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Balance</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Status</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-slate-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/30">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-slate-800/20 transition-colors">
                    <td class="px-5 py-3"><a href="{{ route('invoices.show', $invoice) }}" class="text-emerald-400 hover:text-emerald-300 font-medium">{{ $invoice->invoice_number }}</a></td>
                    <td class="px-5 py-3 text-slate-300">{{ $invoice->customer->name }}</td>
                    <td class="px-5 py-3 {{ $invoice->due_date->isPast() ? 'text-red-400' : 'text-slate-400' }}">{{ $invoice->due_date->format('M d, Y') }}</td>
                    <td class="px-5 py-3 text-right text-slate-200">₱{{ number_format($invoice->total_amount, 2) }}</td>
                    <td class="px-5 py-3 text-right text-emerald-400">₱{{ number_format($invoice->total_paid, 2) }}</td>
                    <td class="px-5 py-3 text-right font-bold text-amber-400">₱{{ number_format($invoice->balance, 2) }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex px-2.5 py-0.5 text-[10px] font-semibold uppercase rounded-full
                            @if($invoice->status === 'partial') bg-amber-500/10 text-amber-400
                            @elseif($invoice->status === 'overdue') bg-red-500/10 text-red-400
                            @else bg-slate-700/50 text-slate-400 @endif">
                            {{ $invoice->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-500/10 text-emerald-400 text-xs font-medium rounded-lg hover:bg-emerald-500/20 transition-all">
                            Pay
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-8 text-center text-slate-500">No outstanding balances! 🎉</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

