<!-- SOA Modal -->
<div x-show="soaModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="soaModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-mint-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="soaModalOpen = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="soaModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-beige-50 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle max-w-4xl w-full border border-beige-200/60 relative">
            <div class="px-8 pt-6 pb-4 flex justify-between items-center bg-white border-b border-beige-100">
                <div>
                    <h3 class="text-xl font-black text-mint-950" id="modal-title">Statement of Account</h3>
                    <p class="text-[10px] font-bold text-beige-400 uppercase tracking-widest mt-1">{{ $customer->name }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('customers.soa-pdf', $customer) }}" target="_blank" class="text-xs font-black text-mint-600 hover:text-mint-800 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Download PDF
                    </a>
                    <button type="button" @click="soaModalOpen = false" class="text-beige-400 hover:text-red-500 transition-colors ml-4 border-l border-beige-200 pl-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            <div class="p-8 bg-white h-[60vh] overflow-y-auto">
                <div class="mb-8">
                    @php 
                        $modalInvoices = $customer->invoices()->latest()->get(); 
                        $totalBilled = $modalInvoices->sum('total_amount');
                        $totalPaid = $modalInvoices->where('status', 'paid')->sum('total_amount');
                        $totalDue = $totalBilled - $totalPaid;
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                        <div class="p-5 rounded-2xl bg-white border border-beige-100 shadow-sm text-center">
                            <p class="text-[9px] font-black text-beige-400 uppercase tracking-widest mb-1.5">Gross Billing</p>
                            <p class="text-xl font-black text-mint-950">₱{{ number_format($totalBilled, 2) }}</p>
                        </div>
                        <div class="p-5 rounded-2xl bg-white border border-beige-100 shadow-sm text-center">
                            <p class="text-[9px] font-black text-beige-400 uppercase tracking-widest mb-1.5">Total Received</p>
                            <p class="text-xl font-black text-mint-600">₱{{ number_format($totalPaid, 2) }}</p>
                        </div>
                        <div class="p-5 rounded-2xl {{ $totalDue > 0 ? 'bg-red-50 border-red-100' : 'bg-mint-50 border-mint-100' }} border shadow-sm text-center">
                            <p class="text-[9px] font-black {{ $totalDue > 0 ? 'text-red-400' : 'text-mint-600' }} uppercase tracking-widest mb-1.5">Outstanding</p>
                            <p class="text-xl font-black {{ $totalDue > 0 ? 'text-red-600' : 'text-mint-700' }}">₱{{ number_format($totalDue, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="border border-beige-200 rounded-2xl overflow-x-auto overflow-y-hidden">
                    <table class="w-full text-sm min-w-[500px]">
                        <thead>
                            <tr class="bg-beige-50 border-b border-beige-200">
                                <th class="text-left px-5 py-3 text-[10px] font-bold text-beige-500 uppercase tracking-widest">Date</th>
                                <th class="text-left px-5 py-3 text-[10px] font-bold text-beige-500 uppercase tracking-widest">Invoice Ref</th>
                                <th class="text-right px-5 py-3 text-[10px] font-bold text-beige-500 uppercase tracking-widest">Amount</th>
                                <th class="text-center px-5 py-3 text-[10px] font-bold text-beige-500 uppercase tracking-widest">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-beige-100">
                            @forelse($modalInvoices as $inv)
                            <tr>
                                <td class="px-5 py-4 text-xs font-bold text-beige-600">{{ $inv->created_at->format('M d, Y') }}</td>
                                <td class="px-5 py-4 font-black text-mint-900">{{ $inv->invoice_number }}</td>
                                <td class="px-5 py-4 text-right font-black text-mint-700">₱{{ number_format($inv->total_amount, 2) }}</td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest
                                        @if($inv->status === 'paid') bg-mint-100 text-mint-700
                                        @elseif($inv->status === 'partial') bg-amber-100 text-amber-700
                                        @elseif($inv->status === 'overdue') bg-red-100 text-red-700
                                        @else bg-beige-100 text-beige-600 @endif">
                                        {{ $inv->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-5 py-10 text-center text-xs font-bold text-beige-400 uppercase tracking-widest">No transaction history</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
