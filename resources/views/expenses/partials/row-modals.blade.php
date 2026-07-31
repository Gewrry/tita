<!-- Edit Expense Modal -->
<div x-show="editModalOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto text-center" 
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-mint-900/40 backdrop-blur-sm transition-opacity" @click="editModalOpen = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-beige-100">
            <div class="bg-white px-8 pt-8 pb-6">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-2xl font-black text-mint-950 tracking-tight">Edit Expense</h3>
                        <p class="text-[10px] font-black text-beige-400 uppercase tracking-widest mt-1">Modify record #{{ $expense->id }}</p>
                    </div>
                    <button @click="editModalOpen = false" class="p-2 text-beige-300 hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('expenses.update', $expense) }}">
                    @csrf @method('PUT')
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2 px-1">Description <span class="text-red-500">*</span></label>
                            <input type="text" name="description" value="{{ old('description', $expense->description) }}" required
                                   class="w-full px-5 py-4 bg-beige-50/50 border border-beige-100 rounded-2xl text-sm font-bold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all outline-none">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2 px-1">Amount <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-sm font-black text-mint-600">₱</span>
                                    <input type="number" name="amount" value="{{ old('amount', $expense->amount) }}" min="0.01" step="0.01" required
                                           class="w-full pl-10 pr-5 py-4 bg-beige-50/50 border border-beige-100 rounded-2xl text-sm font-black text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2 px-1">Category <span class="text-red-500">*</span></label>
                                <select name="category" required class="w-full px-5 py-4 bg-beige-50/50 border border-beige-100 rounded-2xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all outline-none appearance-none">
                                    @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category', $expense->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2 px-1">Transaction Date <span class="text-red-500">*</span></label>
                            <input type="date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required
                                   class="w-full px-5 py-4 bg-beige-50/50 border border-beige-100 rounded-2xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all outline-none uppercase">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-beige-400 uppercase tracking-widest mb-2 px-1">Internal Notes</label>
                            <textarea name="notes" rows="3"
                                      class="w-full px-5 py-4 bg-beige-50/50 border border-beige-100 rounded-2xl text-sm font-bold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all outline-none resize-none">{{ old('notes', $expense->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-10">
                        <button type="submit" class="flex-1 btn-mint py-4 px-8 shadow-xl shadow-mint-900/20 active:scale-95 transition-all text-sm justify-center">
                            Save Changes
                        </button>
                        <button type="button" @click="editModalOpen = false" class="px-8 py-4 text-[10px] font-black text-beige-400 uppercase tracking-widest hover:text-mint-900 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div x-show="deleteModalOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto text-center" 
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-red-900/20 backdrop-blur-sm transition-opacity" @click="deleteModalOpen = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-red-50">
            <div class="bg-white p-8">
                <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mb-6 mx-auto ring-4 ring-red-50/50">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                
                <div class="text-center">
                    <h3 class="text-xl font-black text-mint-950 tracking-tight">Archive Expense Record?</h3>
                    <p class="text-sm font-bold text-beige-400 mt-2">This transaction for <span class="text-mint-900">₱{{ number_format($expense->amount, 2) }}</span> will be archived. This action can be audited but will be removed from active ledgers.</p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 mt-8">
                    <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="w-full">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full py-4 px-6 bg-red-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-red-700 active:scale-95 transition-all shadow-lg shadow-red-600/20">
                            Confirm Archive
                        </button>
                    </form>
                    <button @click="deleteModalOpen = false" class="w-full py-4 px-6 text-[10px] font-black text-beige-400 uppercase tracking-widest hover:bg-beige-50 rounded-2xl transition-all">
                        Go Back
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
