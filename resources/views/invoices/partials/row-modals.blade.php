@php
    // Calculate values for this specific invoice
    $balance = $invoice->total_amount - $invoice->payments()->sum('amount');
@endphp

<!-- View Invoice Modal -->
<div x-show="viewModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="viewModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-mint-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="viewModalOpen = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="viewModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-beige-50 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle max-w-4xl w-full border border-beige-200/60 relative">
            <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white border-b border-beige-100 gap-4">
                <div class="flex items-center gap-4">
                    <span class="inline-flex px-4 py-1.5 text-[10px] font-black uppercase rounded-xl tracking-widest
                        @if($invoice->status === 'paid') bg-mint-100 text-mint-700 border border-mint-200/50
                        @elseif($invoice->status === 'partial') bg-amber-100 text-amber-700 border border-amber-200/50
                        @elseif($invoice->status === 'overdue') bg-red-100 text-red-700 border border-red-200/50
                        @else bg-beige-100 text-beige-600 border border-beige-200/50 @endif">
                        {{ $invoice->status }}
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black text-mint-950 tracking-tight">{{ $invoice->invoice_number }}</h3>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" class="flex-1 sm:flex-none justify-center inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-beige-200 text-mint-800 text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-beige-50 transition-all shadow-sm">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                        PDF
                    </a>
                    <button type="button" @click="viewModalOpen = false" class="text-beige-400 hover:text-red-500 transition-colors ml-2 sm:ml-4 border-l border-beige-200 pl-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            
            <div class="p-6 sm:p-8 h-[65vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Client Info -->
                    <div class="md:col-span-2 bg-white rounded-3xl p-6 border border-beige-200/60 shadow-sm">
                        <h3 class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-4">Billed To</h3>
                        <p class="text-lg font-black text-mint-950">{{ $invoice->customer->name ?? 'N/A' }}</p>
                        @if($invoice->customer && $invoice->customer->email)
                        <p class="text-sm font-bold text-mint-700 mt-1">{{ $invoice->customer->email }}</p>
                        @endif
                        @if($invoice->customer && $invoice->customer->phone)
                        <p class="text-sm font-bold text-mint-700 mt-1">{{ $invoice->customer->phone }}</p>
                        @endif
                        @if($invoice->customer && $invoice->customer->address)
                        <p class="text-sm font-semibold text-beige-500 mt-3 max-w-sm">{{ $invoice->customer->address }}</p>
                        @endif
                    </div>
                    <!-- Meta Info -->
                    <div class="bg-white rounded-3xl p-6 border border-beige-200/60 shadow-sm flex flex-col justify-center space-y-6">
                        <div>
                            <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-1.5">Issue Date</p>
                            <p class="text-sm font-black text-mint-900">{{ $invoice->issue_date->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-1.5">Due Date</p>
                            <p class="text-sm font-black {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-red-500' : 'text-mint-900' }}">{{ $invoice->due_date->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="bg-white rounded-3xl border border-beige-200/60 shadow-sm overflow-hidden mb-8">
                    <div class="border-b border-beige-100 bg-beige-50/50 px-6 py-4">
                        <h3 class="text-[10px] font-black text-mint-600 uppercase tracking-widest">Line Items</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-white border-b border-beige-100">
                                    <th class="text-left px-6 py-3.5 text-[10px] font-bold text-beige-400 uppercase tracking-widest">Description</th>
                                    <th class="text-center px-6 py-3.5 text-[10px] font-bold text-beige-400 uppercase tracking-widest w-24">QTY</th>
                                    <th class="text-right px-6 py-3.5 text-[10px] font-bold text-beige-400 uppercase tracking-widest w-32">Rate</th>
                                    <th class="text-right px-6 py-3.5 text-[10px] font-bold text-beige-400 uppercase tracking-widest w-36">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-beige-50">
                                @foreach($invoice->items as $item)
                                <tr class="hover:bg-beige-50/30 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-mint-900">{{ $item->description }}</td>
                                    <td class="px-6 py-4 text-center font-bold text-beige-500">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-mint-700">₱{{ number_format($item->price, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-black text-mint-900">₱{{ number_format($item->quantity * $item->price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-6 bg-beige-50/50 border-t border-beige-100">
                        <div class="w-full sm:w-1/2 ml-auto space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-beige-500">Subtotal</span>
                                <span class="font-black text-mint-900">₱{{ number_format($invoice->subtotal, 2) }}</span>
                            </div>
                            @if($invoice->penalty_value > 0)
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-red-500">Penalty ({{ ucfirst($invoice->penalty_type) }})</span>
                                <span class="font-black text-red-600">₱{{ number_format($invoice->penalty_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center pt-4 border-t border-beige-200">
                                <span class="text-xs font-black text-beige-500 uppercase tracking-widest">Total Amount</span>
                                <span class="text-2xl font-black text-mint-950 tracking-tight">₱{{ number_format($invoice->total_amount, 2) }}</span>
                            </div>
                            <!-- Payments Made -->
                            @if($invoice->payments()->count() > 0)
                            <div class="flex justify-between items-center pt-2">
                                <span class="text-xs font-black text-mint-600 uppercase tracking-widest">Total Paid</span>
                                <span class="text-lg font-black text-mint-600 tracking-tight">- ₱{{ number_format($invoice->payments()->sum('amount'), 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t border-mint-200/50">
                                <span class="text-xs font-black text-red-400 uppercase tracking-widest">Balance Due</span>
                                <span class="text-xl font-black text-red-600 tracking-tight">₱{{ number_format($balance, 2) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
@if($invoice->status !== 'paid')
<div x-show="paymentModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="paymentModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-mint-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="paymentModalOpen = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="paymentModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-cyan-200/60 relative">
            
            <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-5 sm:pb-6 border-b border-beige-100 flex justify-between items-center bg-cyan-50/30">
                <div>
                    <h3 class="text-xl font-black text-cyan-950" id="modal-title">Record Payment</h3>
                    <p class="text-[10px] font-bold text-cyan-700/60 uppercase tracking-widest mt-1">Receive funds for {{ $invoice->invoice_number }}</p>
                </div>
                <button type="button" @click="paymentModalOpen = false" class="text-cyan-400 hover:text-red-500 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-6 sm:p-8">
                <form method="POST" action="{{ route('payments.store') }}">
                    @csrf
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    
                    <div class="mb-6 p-5 bg-amber-50 border border-amber-100 rounded-2xl flex items-center justify-between">
                        <span class="text-[10px] font-black text-amber-700 uppercase tracking-widest">Unpaid Balance</span>
                        <span class="text-2xl font-black text-amber-700 tracking-tighter">₱{{ number_format($balance, 2) }}</span>
                    </div>

                    <div class="space-y-5 sm:space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                            <div>
                                <label class="block text-[11px] font-black text-cyan-700 uppercase tracking-widest mb-2">Collection Amount <span class="text-red-500">*</span></label>
                                <input type="number" name="amount" value="{{ old('amount', $balance) }}" min="0.01" max="{{ $balance }}" step="0.01" required placeholder="0.00"
                                       class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-black text-cyan-900 focus:ring-4 focus:ring-cyan-500/10 focus:border-cyan-500 transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-cyan-700 uppercase tracking-widest mb-2">Payment Channel <span class="text-red-500">*</span></label>
                                <select name="payment_method" required class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-bold text-cyan-900 focus:ring-4 focus:ring-cyan-500/10 focus:border-cyan-500 transition-all shadow-sm">
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="gcash">GCash</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                            <div>
                                <label class="block text-[11px] font-black text-cyan-700 uppercase tracking-widest mb-2">Posting Date <span class="text-red-500">*</span></label>
                                <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required
                                       class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-cyan-900 focus:ring-4 focus:ring-cyan-500/10 focus:border-cyan-500 transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-cyan-700 uppercase tracking-widest mb-2">Reference ID</label>
                                <input type="text" name="reference_number" value="{{ old('reference_number') }}" placeholder="e.g. Bank Ref # or Gcash ID"
                                       class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-cyan-900 placeholder-beige-300 focus:ring-4 focus:ring-cyan-500/10 focus:border-cyan-500 transition-all shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-cyan-700 uppercase tracking-widest mb-2">Transaction Notes</label>
                            <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-cyan-900 placeholder-beige-300 focus:ring-4 focus:ring-cyan-500/10 focus:border-cyan-500 transition-all shadow-sm resize-none" placeholder="Optional details for audit trail">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4 mt-6 sm:mt-8 pt-6 sm:pt-8 border-t border-beige-100">
                        <button type="submit" class="btn-mint py-3 px-6 shadow-md shadow-mint-900/10 active:scale-95 transition-all w-full sm:w-auto text-sm justify-center bg-cyan-500 border-cyan-600 hover:bg-cyan-600 focus:ring-cyan-500/20">
                            Apply Payment
                        </button>
                        <button type="button" @click="paymentModalOpen = false" class="px-6 py-3 text-xs font-black text-cyan-800 uppercase tracking-widest hover:bg-beige-100 rounded-2xl transition-all w-full sm:w-auto">Cancel</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
@endif
