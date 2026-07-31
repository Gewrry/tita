<!-- Create Invoice Modal -->
<div x-show="createModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="createModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-mint-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="createModalOpen = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div x-show="createModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-beige-50 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle max-w-5xl w-full border border-beige-200/60 relative" x-data="invoiceForm({{ old('use_unknown_customer') ? 'true' : 'false' }})" x-init="calcTotal()">
            
            <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-5 sm:pb-6 border-b border-beige-100 flex justify-between items-center bg-white">
                <div>
                    <h3 class="text-xl font-black text-mint-950" id="modal-title">Create New Invoice</h3>
                    <p class="text-[10px] font-bold text-beige-400 uppercase tracking-widest mt-1">Bill a customer for products or services</p>
                </div>
                <button type="button" @click="createModalOpen = false" class="text-beige-400 hover:text-red-500 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="h-[75vh] overflow-y-auto p-6 sm:p-8">
                <form method="POST" action="{{ route('invoices.store') }}" id="create-invoice-form">
                    @csrf
                    <!-- Top section from old create form -->
                    <div class="bg-white border border-beige-200/60 rounded-3xl p-6 sm:p-8 mb-6 shadow-sm">
                        <h3 class="text-[11px] font-black text-mint-600 uppercase tracking-widest mb-6">Invoice Details</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Customer <span class="text-red-500">*</span></label>
                                <select name="customer_id" :required="!unknownCustomer" :disabled="unknownCustomer" class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm disabled:bg-beige-50 disabled:text-beige-400">
                                    <option value="">Select customer</option>
                                    @foreach(App\Models\Customer::orderBy('name')->get() as $cust)
                                    <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>
                                    @endforeach
                                </select>
                                <label class="mt-3 flex items-start gap-2 text-xs font-bold text-beige-500">
                                    <input type="checkbox" name="use_unknown_customer" value="1" x-model="unknownCustomer" class="mt-0.5 rounded border-beige-300 text-mint-600 focus:ring-mint-500">
                                    <span>Customer name is unknown. Create the next Unknown Customer automatically.</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Invoice #</label>
                                <input type="text" name="invoice_number" value="{{ old('invoice_number', 'INV-'.strtoupper(Str::random(6))) }}" 
                                       class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Issue Date <span class="text-red-500">*</span></label>
                                <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required
                                       class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Due Date <span class="text-red-500">*</span></label>
                                <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required
                                       class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Line Items -->
                    <div class="bg-white border border-beige-200/60 rounded-3xl p-6 sm:p-8 mb-6 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-[11px] font-black text-mint-600 uppercase tracking-widest">Line Items</h3>
                            <button type="button" @click="addItem()" class="inline-flex items-center gap-2 px-4 py-2 bg-mint-50 text-mint-600 text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-mint-100 transition-all border border-mint-200/20 shadow-sm shadow-mint-900/5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                Add Item
                            </button>
                        </div>
                        <div class="space-y-4">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex flex-col md:flex-row items-start md:items-center gap-4 p-5 bg-beige-50/50 border border-beige-100 rounded-2xl group transition-all hover:bg-white hover:border-beige-200 hover:shadow-sm">
                                    <div class="flex-1 w-full md:min-w-0">
                                        <input type="text" :name="'items['+index+'][description]'" x-model="item.description" placeholder="Description of service or product" required
                                               class="w-full px-4 py-2.5 bg-white border border-beige-200 rounded-xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all">
                                    </div>
                                    <div class="w-full md:w-24">
                                        <input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" min="1" placeholder="Qty" required
                                               @input="calcTotal()"
                                               class="w-full px-4 py-2.5 bg-white border border-beige-200 rounded-xl text-sm font-black text-mint-900 md:text-center focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all">
                                    </div>
                                    <div class="w-full md:w-36">
                                        <input type="number" :name="'items['+index+'][price]'" x-model.number="item.price" min="0" step="0.01" placeholder="Price" required
                                               @input="calcTotal()"
                                               class="w-full px-4 py-2.5 bg-white border border-beige-200 rounded-xl text-sm font-black text-mint-900 md:text-right focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all">
                                    </div>
                                    <div class="w-full md:w-36 flex items-center justify-between md:justify-end px-2">
                                        <span class="text-[10px] font-bold text-beige-400 uppercase tracking-widest md:hidden">Subtotal:</span>
                                        <span class="text-base font-black text-mint-600 tracking-tight" x-text="'₱' + (item.quantity * item.price).toLocaleString('en-PH', {minimumFractionDigits: 2})"></span>
                                    </div>
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="self-end md:self-auto p-2.5 rounded-xl text-beige-300 hover:text-red-500 hover:bg-white hover:border-beige-200 border border-transparent transition-all shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-beige-100">
                            <div class="flex items-baseline gap-4">
                                <span class="text-[11px] font-black text-beige-400 uppercase tracking-widest">Total Amount</span>
                                <span class="text-3xl font-black text-mint-900 tracking-tighter" x-text="'₱' + grandTotal.toLocaleString('en-PH', {minimumFractionDigits: 2})"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Options -->
                    <div class="bg-white border border-beige-200/60 rounded-3xl p-6 sm:p-8 mb-6 shadow-sm">
                        <h3 class="text-[11px] font-black text-mint-600 uppercase tracking-widest mb-6">Additional Options</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Penalty Type</label>
                                <select name="penalty_type" class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                                    <option value="none">None</option>
                                    <option value="flat">Flat Fee</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Penalty Value</label>
                                <input type="number" name="penalty_value" value="{{ old('penalty_value', 0) }}" min="0" step="0.01"
                                       class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-mint-600 uppercase tracking-widest mb-2">Internal Notes</label>
                                <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Optional notes reference"
                                       class="w-full px-4 py-3 bg-white border border-beige-200 rounded-2xl text-sm font-semibold text-mint-900 placeholder-beige-300 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500 transition-all shadow-sm">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="px-6 sm:px-8 py-5 sm:py-6 border-t border-beige-100 flex flex-col sm:flex-row items-center gap-3 sm:gap-4 bg-white">
                <button type="submit" form="create-invoice-form" class="btn-mint py-3.5 px-8 shadow-xl shadow-mint-900/10 active:scale-95 transition-all w-full sm:w-auto text-sm justify-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Authorize & Create
                </button>
                <button type="button" @click="createModalOpen = false" class="px-8 py-3.5 text-[11px] font-black text-mint-800 uppercase tracking-widest hover:bg-beige-100 rounded-2xl transition-all w-full sm:w-auto">Cancel</button>
            </div>
            
        </div>
    </div>
</div>
