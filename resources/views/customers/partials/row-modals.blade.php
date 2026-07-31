<!-- Edit Modal -->
<div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-mint-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="editModalOpen = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-beige-200/60 relative">
            <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-5 sm:pb-6 border-b border-beige-100 flex justify-between items-center bg-beige-50/50">
                <div>
                    <h3 class="text-xl font-black text-mint-950" id="modal-title">Update Client Profile</h3>
                    <p class="text-[10px] font-bold text-beige-400 uppercase tracking-widest mt-1">Edit customer details</p>
                </div>
                <button type="button" @click="editModalOpen = false" class="text-beige-400 hover:text-red-500 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6 sm:p-8 pb-8 sm:pb-10">
                <form method="POST" action="{{ route('customers.update', $customer) }}">
                    @csrf @method('PUT')
                    <div class="space-y-5 sm:space-y-6">
                        <div>
                            <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Full Name / Company <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $customer->name) }}" required class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Email Address</label>
                                <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Physical Address</label>
                            <textarea name="address" rows="3" class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm resize-none">{{ old('address', $customer->address) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Internal Notes</label>
                            <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm resize-none">{{ old('notes', $customer->notes) }}</textarea>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4 mt-6 sm:mt-8 pt-6 sm:pt-8 border-t border-beige-100">
                        <button type="submit" class="btn-mint py-3 px-6 shadow-md shadow-mint-900/10 active:scale-95 transition-all w-full sm:w-auto text-sm">Update Profile</button>
                        <button type="button" @click="editModalOpen = false" class="px-6 py-3 text-xs font-black text-mint-800 uppercase tracking-widest hover:bg-beige-100 rounded-2xl transition-all w-full sm:w-auto">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div x-show="deleteModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-red-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="deleteModalOpen = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-red-200 relative">
            <div class="p-8 pb-10 text-center">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-2xl font-black text-red-950 mb-2">Delete Customer?</h3>
                <p class="text-sm font-medium text-beige-500 mb-8">This action cannot be undone. This will permanently delete <strong>{{ $customer->name }}</strong>.</p>
                <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="flex flex-col sm:flex-row gap-3">
                    @csrf @method('DELETE')
                    <button type="button" @click="deleteModalOpen = false" class="flex-1 px-6 py-4 rounded-xl text-sm font-black text-beige-600 bg-beige-50 hover:bg-beige-100 transition-colors uppercase tracking-widest">Cancel</button>
                    <button type="submit" class="flex-1 px-6 py-4 rounded-xl text-sm font-black text-white bg-red-500 hover:bg-red-600 shadow-lg shadow-red-500/20 transition-all active:scale-95 uppercase tracking-widest">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
