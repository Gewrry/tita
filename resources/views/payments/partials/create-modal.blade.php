<!-- Record Payment Modal -->
<div x-show="createModalOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;"
     x-data="paymentForm()">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-mint-900/40 backdrop-blur-sm transition-opacity" @click="createModalOpen = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-beige-100">
            <div class="bg-white px-8 pt-8 pb-6">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-2xl font-black text-mint-950 tracking-tight">Record Payment</h3>
                        <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mt-1">Settle outstanding invoice</p>
                    </div>
                    <button @click="createModalOpen = false" class="p-2 text-beige-300 hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('payments.store') }}">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2 px-1">Target Invoice <span class="text-red-500">*</span></label>
                            <select name="invoice_id" required x-model="selectedInvoice" @change="onInvoiceChange()"
                                    class="w-full px-5 py-4 bg-beige-50/50 border border-beige-100 rounded-2xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all outline-none appearance-none">
                                <option value="">Select outstanding invoice</option>
                                @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}" data-balance="{{ $inv->balance }}">
                                    {{ $inv->invoice_number }} — {{ $inv->customer->name }} (Balance: ₱{{ number_format($inv->balance, 2) }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="balance > 0" class="p-6 bg-amber-50 border border-amber-100 rounded-2xl flex items-center justify-between">
                            <span class="text-[10px] font-black text-amber-700 uppercase tracking-widest">Unpaid Balance:</span>
                            <span class="text-2xl font-black text-amber-700 tracking-tighter" x-text="'₱' + balance.toLocaleString('en-PH', {minimumFractionDigits: 2})"></span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2 px-1">Amount <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-sm font-black text-mint-600">₱</span>
                                    <input type="number" name="amount" value="{{ old('amount') }}" min="0.01" step="0.01" required :max="balance" placeholder="0.00"
                                           class="w-full pl-10 pr-5 py-4 bg-beige-50/50 border border-beige-100 rounded-2xl text-sm font-black text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2 px-1">Channel <span class="text-red-500">*</span></label>
                                <select name="payment_method" required class="w-full px-5 py-4 bg-beige-50/50 border border-beige-100 rounded-2xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all outline-none appearance-none">
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="gcash">GCash</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2 px-1">Posting Date <span class="text-red-500">*</span></label>
                                <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required
                                       class="w-full px-5 py-4 bg-beige-50/50 border border-beige-100 rounded-2xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all outline-none uppercase">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2 px-1">Reference ID</label>
                                <input type="text" name="reference_number" value="{{ old('reference_number') }}" placeholder="Bank Ref / GCash ID"
                                       class="w-full px-5 py-4 bg-beige-50/50 border border-beige-100 rounded-2xl text-sm font-bold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2 px-1">Notes</label>
                            <textarea name="notes" rows="3" placeholder="Optional audit details..."
                                      class="w-full px-5 py-4 bg-beige-50/50 border border-beige-100 rounded-2xl text-sm font-bold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all outline-none resize-none">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-10">
                        <button type="submit" class="flex-1 btn-mint py-4 px-8 shadow-xl shadow-mint-900/20 active:scale-95 transition-all text-sm justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Record Payment
                        </button>
                        <button type="button" @click="createModalOpen = false" class="px-8 py-4 text-[10px] font-black text-beige-400 uppercase tracking-widest hover:text-mint-900 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
