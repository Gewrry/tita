@extends('layouts.app')
@section('title', 'Point of Sale')
@section('page-title', 'Point of Sale')

@section('content')
<div x-data="posApp()" class="flex flex-col lg:flex-row gap-4 -mt-2" style="height: calc(100vh - 140px);">
    <!-- Left: Products -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Search & Category Tabs -->
        <div class="mb-3">
            <input type="text" x-model="search" @input="filterProducts()" placeholder="🔍 Search by name or scan barcode..." class="w-full px-4 py-3 bg-white border border-beige-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500">
        </div>
        <div class="flex gap-2 mb-3 overflow-x-auto pb-1 flex-shrink-0">
            <button @click="selectedCategory = null; filterProducts()" :class="!selectedCategory ? 'bg-mint-500 text-white' : 'bg-white text-mint-900 border border-beige-200'" class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all">All</button>
            @foreach($categories as $cat)
            <button @click="selectedCategory = {{ $cat->id }}; filterProducts()" :class="selectedCategory === {{ $cat->id }} ? 'bg-mint-500 text-white' : 'bg-white text-mint-900 border border-beige-200'" class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all">{{ $cat->name }}</button>
            @endforeach
        </div>
        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 content-start">
            <template x-for="product in filteredProducts" :key="product.id">
                <button @click="addToCart(product)" 
                        :disabled="product.track_stock && product.stock_quantity <= 0"
                        :class="product.track_stock && product.stock_quantity <= 0 ? 'opacity-50 cursor-not-allowed grayscale' : 'hover:border-mint-400 hover:shadow-md'"
                        class="bg-white border border-beige-200/60 rounded-xl p-3 text-left transition-all group">
                    <div class="text-sm font-bold text-mint-900 truncate" x-text="product.name"></div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm font-black text-mint-600" x-text="'₱' + parseFloat(product.selling_price).toFixed(2)"></span>
                        <span class="text-[10px] font-bold text-beige-400" x-text="product.track_stock ? (product.stock_quantity <= 0 ? 'Out of Stock' : 'Stk: ' + product.stock_quantity) : '∞'"></span>
                    </div>
                </button>
            </template>
        </div>
    </div>

    <!-- Right: Cart -->
    <div class="w-full lg:w-96 flex flex-col bg-white border border-beige-200/60 rounded-2xl overflow-hidden flex-shrink-0">
        <!-- Cart Header -->
        <div class="p-4 border-b border-beige-100 bg-mint-900 text-white">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold">Current Sale</h3>
                <button @click="clearCart()" class="text-[10px] font-bold text-mint-300 hover:text-white uppercase">Clear</button>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto divide-y divide-beige-100">
            <template x-if="cart.length === 0">
                <div class="p-8 text-center text-sm text-beige-400 font-medium">No items yet. Tap a product to add.</div>
            </template>
            <template x-for="(item, index) in cart" :key="index">
                <div class="px-4 py-3 flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-mint-900 truncate" x-text="item.name"></div>
                        <div class="text-xs text-beige-500" x-text="'₱' + parseFloat(item.price).toFixed(2) + ' × ' + item.qty"></div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button @click="updateQty(index, -1)" class="w-7 h-7 rounded-lg bg-beige-100 text-beige-600 font-bold text-sm hover:bg-red-100 hover:text-red-600 transition-colors">−</button>
                        <span class="w-8 text-center text-sm font-bold text-mint-900" x-text="item.qty"></span>
                        <button @click="updateQty(index, 1)" 
                                :disabled="item.track_stock && item.qty >= item.stock"
                                :class="item.track_stock && item.qty >= item.stock ? 'opacity-30 cursor-not-allowed' : 'hover:bg-mint-100 hover:text-mint-600'"
                                class="w-7 h-7 rounded-lg bg-beige-100 text-beige-600 font-bold text-sm transition-colors">+</button>
                    </div>
                    <div class="text-sm font-black text-mint-900 w-20 text-right" x-text="'₱' + (item.price * item.qty).toFixed(2)"></div>
                </div>
            </template>
        </div>

        <!-- Cart Footer -->
        <div class="border-t border-beige-200 p-4 space-y-3 bg-beige-50/50">
            <div class="flex justify-between text-sm"><span class="text-beige-500">Subtotal</span><span class="font-bold text-mint-900" x-text="'₱' + subtotal.toFixed(2)"></span></div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-beige-500">Discount</span>
                <input type="number" x-model.number="discount" min="0" step="0.01" class="flex-1 px-3 py-1.5 bg-white border border-beige-200 rounded-lg text-sm text-right">
            </div>
            <div class="flex justify-between text-lg font-black"><span class="text-mint-900">Total</span><span class="text-mint-600" x-text="'₱' + total.toFixed(2)"></span></div>

            <!-- Customer & Payment -->
            <select x-model="customerId" class="w-full px-3 py-2 bg-white border border-beige-200 rounded-xl text-sm">
                <option value="">Walk-in Customer</option>
                @foreach($customers as $cust)
                    <option value="{{ $cust->id }}">{{ $cust->name }} @if($cust->balance > 0)(Utang: ₱{{ number_format($cust->balance, 2) }})@endif</option>
                @endforeach
            </select>

            <div class="grid grid-cols-3 gap-2">
                <button @click="paymentMethod = 'cash'" :class="paymentMethod === 'cash' ? 'bg-mint-500 text-white' : 'bg-white border border-beige-200 text-mint-900'" class="py-2 rounded-xl text-xs font-bold transition-all">💵 Cash</button>
                <button @click="paymentMethod = 'gcash'" :class="paymentMethod === 'gcash' ? 'bg-blue-500 text-white' : 'bg-white border border-beige-200 text-mint-900'" class="py-2 rounded-xl text-xs font-bold transition-all">📱 GCash</button>
                <button @click="paymentMethod = 'bank_transfer'" :class="paymentMethod === 'bank_transfer' ? 'bg-purple-500 text-white' : 'bg-white border border-beige-200 text-mint-900'" class="py-2 rounded-xl text-xs font-bold transition-all">🏦 Bank</button>
            </div>

            @if(is_sari_sari())
            <label class="flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl cursor-pointer">
                <input type="checkbox" x-model="isCredit" class="rounded border-amber-300 text-amber-500 focus:ring-amber-500">
                <span class="text-sm font-bold text-amber-700">📝 I-Utang (Credit)</span>
            </label>
            @endif

            <template x-if="paymentMethod === 'cash' && !isCredit">
                <div>
                    <label class="block text-xs font-bold text-beige-500 mb-1">Amount Tendered</label>
                    <input type="number" x-model.number="amountTendered" min="0" step="0.01" class="w-full px-3 py-2 bg-white border border-beige-200 rounded-xl text-sm text-right font-bold">
                    <div x-show="amountTendered > 0" class="mt-1 text-right text-sm font-black" :class="change >= 0 ? 'text-mint-600' : 'text-red-500'" x-text="'Change: ₱' + change.toFixed(2)"></div>
                </div>
            </template>

            <button @click="processCheckout()" :disabled="cart.length === 0 || processing" class="w-full py-3.5 bg-mint-500 text-white font-bold text-sm rounded-xl hover:bg-mint-600 transition-all shadow-lg shadow-mint-500/30 disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!processing" x-text="isCredit ? '📝 Record Utang' : '💰 Complete Sale'"></span>
                <span x-show="processing">Processing...</span>
            </button>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div x-show="showReceipt" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 m-4">
            <div class="text-center mb-4">
                <h3 class="text-lg font-black text-mint-900">✅ Sale Complete!</h3>
                <p class="text-sm text-beige-500" x-text="receiptMessage"></p>
            </div>
            <template x-if="receiptData">
                <div class="border border-beige-200 rounded-xl p-4 text-sm space-y-2 mb-4">
                    <div class="text-center font-bold text-mint-900" x-text="receiptData.invoice?.invoice_number"></div>
                    <div class="border-t border-dashed border-beige-200 pt-2">
                        <template x-for="item in receiptData.invoice?.items" :key="item.id">
                            <div class="flex justify-between text-xs">
                                <span x-text="item.description + ' ×' + item.quantity"></span>
                                <span class="font-bold" x-text="'₱' + parseFloat(item.amount).toFixed(2)"></span>
                            </div>
                        </template>
                    </div>
                    <div class="border-t border-beige-200 pt-2 flex justify-between font-bold">
                        <span>Total</span>
                        <span x-text="'₱' + parseFloat(receiptData.invoice?.total_amount).toFixed(2)"></span>
                    </div>
                    <div x-show="receiptData.change > 0" class="flex justify-between text-mint-600 font-bold">
                        <span>Change</span>
                        <span x-text="'₱' + parseFloat(receiptData.change).toFixed(2)"></span>
                    </div>
                </div>
            </template>
            <button @click="showReceipt = false" class="w-full py-3 bg-mint-500 text-white font-bold text-sm rounded-xl hover:bg-mint-600 transition-all">Done</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function posApp() {
    return {
        allProducts: @json($products),
        filteredProducts: @json($products),
        selectedCategory: null,
        search: '',
        cart: [],
        customerId: '',
        paymentMethod: 'cash',
        isCredit: false,
        discount: 0,
        amountTendered: 0,
        processing: false,
        showReceipt: false,
        receiptData: null,
        receiptMessage: '',

        get subtotal() { return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0); },
        get total() { return Math.max(0, this.subtotal - this.discount); },
        get change() { return this.amountTendered - this.total; },

        filterProducts() {
            this.filteredProducts = this.allProducts.filter(p => {
                const matchCat = !this.selectedCategory || p.category_id === this.selectedCategory;
                const matchSearch = !this.search || p.name.toLowerCase().includes(this.search.toLowerCase()) || (p.barcode && p.barcode === this.search);
                return matchCat && matchSearch;
            });
            // Auto-add on exact barcode match
            if (this.search && this.filteredProducts.length === 1 && this.filteredProducts[0].barcode === this.search) {
                this.addToCart(this.filteredProducts[0]);
                this.search = '';
                this.filterProducts();
            }
        },

        addToCart(product) {
            const existing = this.cart.find(i => i.product_id === product.id);
            if (existing) {
                if (product.track_stock && existing.qty >= product.stock_quantity) {
                    return;
                }
                existing.qty++;
            }
            else {
                if (product.track_stock && product.stock_quantity <= 0) {
                    return;
                }
                this.cart.push({ 
                    product_id: product.id, 
                    name: product.name, 
                    price: parseFloat(product.selling_price), 
                    qty: 1,
                    stock: product.stock_quantity,
                    track_stock: product.track_stock
                });
            }
        },

        updateQty(index, delta) {
            const item = this.cart[index];
            if (delta > 0 && item.track_stock && item.qty >= item.stock) {
                return;
            }
            item.qty += delta;
            if (item.qty <= 0) this.cart.splice(index, 1);
        },

        clearCart() { this.cart = []; this.discount = 0; this.amountTendered = 0; },

        async processCheckout() {
            if (this.cart.length === 0 || this.processing) return;
            this.processing = true;
            try {
                const res = await fetch('{{ route("pos.checkout") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        items: this.cart.map(i => ({ product_id: i.product_id, quantity: i.qty, price: i.price })),
                        customer_id: this.customerId || null,
                        payment_method: this.paymentMethod,
                        amount_tendered: this.amountTendered,
                        is_credit: this.isCredit,
                        discount: this.discount,
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.receiptData = data;
                    this.receiptMessage = data.message;
                    this.showReceipt = true;
                    this.clearCart();
                    this.customerId = '';
                    this.isCredit = false;
                } else { alert(data.message || 'Checkout failed.'); }
            } catch (e) { alert('Error: ' + e.message); }
            this.processing = false;
        }
    }
}
</script>
@endpush
