<!-- View Payment Details Modal -->
<div x-show="viewModalOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto text-center" 
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-mint-900/40 backdrop-blur-sm transition-opacity" @click="viewModalOpen = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-beige-100">
            <div class="bg-white p-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-2xl font-black text-mint-950 tracking-tight">Payment Details</h3>
                        <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mt-1">Transaction Log #{{ $payment->id }}</p>
                    </div>
                    <button @click="viewModalOpen = false" class="p-2 text-beige-300 hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-y-8 gap-x-6 mb-8">
                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-1.5">Customer</p>
                        <p class="text-sm font-black text-mint-900">{{ $payment->customer->name }}</p>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-1.5">Invoice Reference</p>
                        <p class="text-sm font-black text-mint-600 bg-mint-50 px-2 py-0.5 rounded-lg inline-block border border-mint-100">{{ $payment->invoice->invoice_number }}</p>
                    </div>
                    
                    <div>
                        <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-1.5">Amount Paid</p>
                        <p class="text-2xl font-black text-mint-950 tracking-tighter">₱{{ number_format($payment->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-1.5">Payment Method</p>
                        <span class="inline-flex px-3 py-1 text-[10px] font-black uppercase rounded-xl tracking-widest
                            @if($payment->payment_method === 'cash') bg-mint-100 text-mint-700
                            @elseif($payment->payment_method === 'gcash') bg-blue-100 text-blue-700
                            @else bg-purple-100 text-purple-700 @endif">
                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-1.5">Date Processed</p>
                        <p class="text-sm font-bold text-mint-900 uppercase">{{ $payment->payment_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-1.5">Reference ID</p>
                        <p class="text-sm font-mono font-bold text-beige-300">{{ $payment->reference_number ?? '—' }}</p>
                    </div>
                </div>

                @if($payment->notes)
                <div class="p-5 bg-beige-50/50 border border-beige-100 rounded-2xl mb-8">
                    <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2 px-1">Internal Notes</p>
                    <p class="text-sm font-semibold text-mint-800 italic leading-relaxed px-1">"{{ $payment->notes }}"</p>
                </div>
                @endif

                <div class="flex items-center gap-4 border-t border-beige-100 pt-8">
                    <button @click="viewModalOpen = false" class="flex-1 py-4 px-6 bg-beige-100 text-mint-900 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-beige-200 active:scale-95 transition-all">
                        Close Log
                    </button>
                    @if(auth()->user()->is_admin) {{-- Assuming only admins can delete records --}}
                    <form method="POST" action="{{ route('payments.destroy', $payment) }}" onsubmit="return confirm('Archive this payment record for audit?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-4 text-beige-300 hover:text-red-500 transition-colors" title="Archive Record">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
