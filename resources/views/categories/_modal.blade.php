<!-- Category Modal Component -->
<div x-show="showCategoryModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-mint-900/60 backdrop-blur-sm" @click="showCategoryModal = false"></div>

        <div class="inline-block w-full max-w-md p-6 my-4 md:p-8 md:my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-[2rem] md:rounded-[3rem]">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-xl font-black text-mint-950 uppercase tracking-widest">New Category</h3>
                <button @click="showCategoryModal = false" type="button" class="p-2 text-beige-400 hover:text-mint-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="submitCategory" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Category Name *</label>
                    <input type="text" x-model="newCatName" required placeholder="e.g., Beverages, Snacks" class="w-full px-5 py-4 bg-beige-50/50 border border-beige-200 rounded-3xl text-sm font-bold text-mint-900 focus:ring-4 focus:ring-mint-500/10 focus:border-mint-500">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-beige-500 uppercase tracking-widest mb-2">Color Label</label>
                    <div class="flex items-center gap-4">
                        <input type="color" x-model="newCatColor" class="w-14 h-14 rounded-2xl cursor-pointer border-0 p-1 bg-beige-50/50">
                        <span class="text-sm font-bold text-mint-900" x-text="newCatColor"></span>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row gap-4 pt-4">
                    <button type="button" @click="showCategoryModal = false" class="w-full sm:flex-1 px-6 py-4 bg-beige-100 text-beige-600 text-[10px] font-black uppercase tracking-widest rounded-3xl hover:bg-beige-200 transition-all">Cancel</button>
                    <button type="submit" :disabled="isSubmitting" class="w-full sm:flex-[2] px-6 py-4 bg-mint-700 text-white text-[10px] font-black uppercase tracking-widest rounded-3xl hover:bg-mint-800 transition-all shadow-lg shadow-mint-700/20 disabled:opacity-50">
                        <span x-show="!isSubmitting">Create Category</span>
                        <span x-show="isSubmitting">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Categories Modal -->
<div x-show="showManageModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-mint-900/60 backdrop-blur-sm" @click="showManageModal = false"></div>

        <div class="inline-block w-full max-w-lg p-6 my-4 md:p-8 md:my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-[2rem] md:rounded-[3rem]">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-mint-950 uppercase tracking-widest">Manage Categories</h3>
                <button @click="showManageModal = false" type="button" class="p-2 text-beige-400 hover:text-mint-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar pr-2">
                @forelse($categories as $cat)
                <div class="flex items-center justify-between p-4 bg-beige-50/50 rounded-2xl border border-beige-200 hover:border-mint-200 transition-all">
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full shadow-sm" style="background-color: {{ $cat->color }}"></span>
                        <span class="font-bold text-mint-900 text-sm">{{ $cat->name }}</span>
                    </div>
                    <form action="{{ route('categories.destroy', $cat) }}" method="POST" x-ref="catDeleteForm_{{ $cat->id }}"
                          @submit.prevent="
                              $dispatch('confirm', {
                                  title: 'Delete Category?',
                                  message: 'Are you sure you want to delete this category? Products in this category will become uncategorized.',
                                  confirmText: 'Yes, Delete',
                                  confirmType: 'danger',
                                  onConfirm: () => $refs['catDeleteForm_{{ $cat->id }}'].submit()
                              })
                          ">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 text-red-300 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
                @empty
                <div class="text-center py-8">
                    <p class="text-sm font-bold text-beige-400">No categories found.</p>
                </div>
                @endforelse
            </div>

            <div class="pt-6 mt-6 border-t border-beige-100 flex justify-end">
                <button type="button" @click="showManageModal = false; showCategoryModal = true" class="px-6 py-3 bg-mint-50 text-mint-700 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-mint-100 transition-all">
                    + Add New
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function categoryModalHandler() {
    return {
        showCategoryModal: false,
        showManageModal: false,
        newCatName: '',
        newCatColor: '#20c997',
        isSubmitting: false,
        submitCategory() {
            if (!this.newCatName.trim()) return;
            
            this.isSubmitting = true;
            fetch('{{ route('categories.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: this.newCatName,
                    color: this.newCatColor
                })
            })
            .then(res => res.json())
            .then(data => {
                this.isSubmitting = false;
                if (data.success) {
                    // Find any element with id category_id (create or edit form)
                    const select = document.getElementById('category_id');
                    if (select) {
                        if (select.tomselect) {
                            select.tomselect.addOption({value: data.category.id, text: data.category.name});
                            select.tomselect.addItem(data.category.id);
                        } else {
                            const option = new Option(data.category.name, data.category.id, true, true);
                            select.add(option);
                            // Trigger change event to notify Alpine of the change
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                    
                    this.showCategoryModal = false;
                    this.newCatName = '';
                    
                    // Show toast notification
                    window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'Category created successfully!', type: 'success' } }));
                } else {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'Validation error: Please check your input.', type: 'error' } }));
                }
            })
            .catch(err => {
                this.isSubmitting = false;
                window.dispatchEvent(new CustomEvent('notify', { detail: { msg: 'An error occurred. Please try again.', type: 'error' } }));
            });
        }
    }
}
</script>
