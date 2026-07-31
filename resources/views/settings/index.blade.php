@extends('layouts.app')
@section('title', 'Business Settings')
@section('page-title', 'Business Settings')

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Business Profile -->
        <div class="bg-white border border-beige-200/60 rounded-3xl p-8 mb-6">
            <h3 class="text-sm font-bold text-mint-900 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-mint-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Business Profile
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Business Name</label>
                    <input type="text" name="business_name" value="{{ old('business_name', $settings->business_name) }}"
                           class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm font-medium text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Business Type</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="business_type" value="sari_sari" class="peer sr-only" {{ $settings->business_type === 'sari_sari' ? 'checked' : '' }}>
                            <div class="p-6 rounded-2xl border-2 border-beige-200 peer-checked:border-mint-500 peer-checked:bg-mint-50 transition-all hover:border-mint-300">
                                <div class="text-3xl mb-3">🏪</div>
                                <h4 class="text-sm font-bold text-mint-900">Sari-Sari Store</h4>
                                <p class="text-xs text-beige-500 mt-1">Retail, Utang tracking, barcode support, break-bulk sales</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="business_type" value="restaurant" class="peer sr-only" {{ $settings->business_type === 'restaurant' ? 'checked' : '' }}>
                            <div class="p-6 rounded-2xl border-2 border-beige-200 peer-checked:border-mint-500 peer-checked:bg-mint-50 transition-all hover:border-mint-300">
                                <div class="text-3xl mb-3">🍽️</div>
                                <h4 class="text-sm font-bold text-mint-900">Restaurant / Cafe</h4>
                                <p class="text-xs text-beige-500 mt-1">Table management, kitchen display, order tracking</p>
                            </div>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Address</label>
                    <input type="text" name="address" value="{{ old('address', $settings->address) }}"
                           class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm font-medium text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $settings->phone) }}"
                           class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm font-medium text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Tax ID / TIN</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id', $settings->tax_id) }}"
                           class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm font-medium text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Receipt Footer</label>
                    <input type="text" name="receipt_footer" value="{{ old('receipt_footer', $settings->receipt_footer) }}" placeholder="Thank you for your purchase!"
                           class="w-full px-4 py-3 bg-beige-50 border border-beige-200 rounded-xl text-sm font-medium text-mint-900 focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-beige-600 uppercase tracking-wider mb-2">Logo</label>
                    <input type="file" name="logo" accept="image/*"
                           class="w-full px-4 py-2 bg-beige-50 border border-beige-200 rounded-xl text-sm font-medium text-beige-600 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-mint-500 file:text-white file:text-xs file:font-bold transition-all">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-8 py-3 bg-mint-500 text-white font-bold text-sm rounded-xl hover:bg-mint-600 transition-all shadow-lg shadow-mint-500/30">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
