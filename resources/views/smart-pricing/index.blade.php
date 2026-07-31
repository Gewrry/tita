@extends('layouts.app')
@section('title', 'Smart Pricing')
@section('page-title', 'Smart Pricing')

@php
    $profile = $selectedProduct?->pricingProfile;
    $breakdown = $recommendation['breakdown'] ?? [
        'ingredient_cost' => 0,
        'packaging_cost' => 0,
        'labor_allowance' => 0,
        'utility_allowance' => 0,
        'transportation_cost' => 0,
        'delivery_fees' => 0,
        'operating_cost' => 0,
        'waste_percentage' => 0,
        'waste_allowance' => 0,
        'complete_cost_per_serving' => 0,
    ];
    $explanation = $recommendation['explanation'] ?? [
        'previous_cost' => 0,
        'updated_cost' => 0,
        'current_selling_price' => 0,
        'current_margin' => 0,
        'target_margin' => 35,
        'expected_profit' => 0,
        'summary' => '',
    ];
    $ingredientRows = $selectedProduct
        ? $selectedProduct->pricingIngredients->map(fn ($ingredient) => [
            'name' => $ingredient->name,
            'quantity_per_serving' => (float) $ingredient->quantity_per_serving,
            'unit' => $ingredient->unit,
            'cost_per_unit' => (float) $ingredient->cost_per_unit,
            'is_estimated' => (bool) $ingredient->is_estimated,
            'notes' => $ingredient->notes,
        ])->values()->all()
        : [];
    if (count($ingredientRows) === 0) {
        $ingredientRows[] = [
            'name' => '',
            'quantity_per_serving' => 1,
            'unit' => 'serving',
            'cost_per_unit' => 0,
            'is_estimated' => false,
            'notes' => '',
        ];
    }
    $pricingData = [
        'ingredients' => $ingredientRows,
        'costs' => [
            'packaging_cost' => (float) ($profile->packaging_cost ?? 0),
            'labor_allowance' => (float) ($profile->labor_allowance ?? 0),
            'utility_allowance' => (float) ($profile->utility_allowance ?? 0),
            'transportation_cost' => (float) ($profile->transportation_cost ?? 0),
            'delivery_fees' => (float) ($profile->delivery_fees ?? 0),
            'waste_percentage' => (float) ($profile->waste_percentage ?? 0),
        ],
        'minimumMargin' => (float) ($profile->minimum_margin ?? 25),
        'desiredMargin' => (float) ($profile->desired_margin ?? 35),
        'smartRounding' => (bool) ($profile->smart_rounding ?? true),
        'activePrice' => (float) ($selectedProduct->selling_price ?? 0),
        'previousCost' => (float) ($explanation['previous_cost'] ?? 0),
        'simulatorPrice' => (float) ($recommendation['options']['recommended']['selling_price'] ?? $selectedProduct->selling_price ?? 0),
    ];
@endphp

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 xl:grid-cols-[320px_minmax(0,1fr)] gap-6">
        <aside class="space-y-4">
            <div class="bg-white border border-beige-200/60 rounded-2xl p-5">
                <h2 class="text-sm font-black text-mint-900 mb-4">Create Product</h2>
                <form method="POST" action="{{ route('smart-pricing.products.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="smart-product-name">Product Name</label>
                        <input id="smart-product-name" name="name" type="text" required value="{{ old('name') }}" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm focus:ring-2 focus:ring-mint-500/20 focus:border-mint-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="smart-product-category">Category</label>
                        <select id="smart-product-category" name="category_id" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                            <option value="">No Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="smart-product-unit">Unit</label>
                            <select id="smart-product-unit" name="unit" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                                @foreach($units as $value => $label)
                                    <option value="{{ $value }}" {{ old('unit', 'serving') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="smart-product-price">Active Price</label>
                            <input id="smart-product-price" name="selling_price" type="number" min="0" step="0.01" value="{{ old('selling_price', 0) }}" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="smart-min-margin">Min Margin</label>
                            <input id="smart-min-margin" name="minimum_margin" type="number" min="0" max="90" step="0.01" value="{{ old('minimum_margin', 25) }}" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="smart-desired-margin">Desired</label>
                            <input id="smart-desired-margin" name="desired_margin" type="number" min="0" max="90" step="0.01" value="{{ old('desired_margin', 35) }}" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                        </div>
                    </div>
                    <button type="submit" class="w-full min-h-[44px] px-4 py-2.5 bg-mint-500 text-white font-bold text-sm rounded-xl hover:bg-mint-600 transition-all shadow-lg shadow-mint-500/25 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create
                    </button>
                </form>
            </div>

            <div class="bg-white border border-beige-200/60 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-beige-100 flex items-center justify-between">
                    <h2 class="text-sm font-black text-mint-900">Pricing Products</h2>
                    <span class="text-xs font-bold text-beige-500">{{ $products->count() }}</span>
                </div>
                <div class="max-h-[460px] overflow-y-auto divide-y divide-beige-100">
                    @forelse($products as $product)
                        @php $isSelected = $selectedProduct?->id === $product->id; @endphp
                        <a href="{{ route('smart-pricing.index', ['product' => $product->id]) }}" class="block px-5 py-4 transition-colors {{ $isSelected ? 'bg-mint-50' : 'hover:bg-beige-50' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-mint-900 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-beige-500 truncate">{{ $product->category?->name ?? 'Uncategorized' }}</p>
                                </div>
                                <span class="text-sm font-black text-mint-700 tabular-nums">&#8369;{{ number_format($product->selling_price, 2) }}</span>
                            </div>
                            <div class="mt-2 flex items-center justify-between text-[11px] font-semibold text-beige-500">
                                <span>Cost &#8369;{{ number_format($product->pricingProfile?->complete_cost_per_serving ?? $product->cost_price, 2) }}</span>
                                <span>{{ $product->pricingProfile?->last_recommended_at ? 'Recalculated' : 'No profile' }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-center">
                            <p class="text-sm font-bold text-beige-500">No products yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </aside>

        @if($selectedProduct)
        <section x-data="smartPricing({{ Js::from($pricingData) }})" class="space-y-6">
            <div class="bg-white border border-beige-200/60 rounded-2xl p-5">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                    <div>
                        <p class="text-xs font-black text-beige-500 uppercase tracking-widest">Active Product</p>
                        <h2 class="text-2xl font-black text-mint-900 mt-1">{{ $selectedProduct->name }}</h2>
                        <p class="text-sm text-beige-600 mt-1">{{ $selectedProduct->category?->name ?? 'Uncategorized' }} &middot; {{ $units[$selectedProduct->unit] ?? $selectedProduct->unit }}</p>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="px-4 py-3 bg-mint-50 border border-mint-100 rounded-xl">
                            <p class="text-[10px] font-black text-mint-600 uppercase">Active Price</p>
                            <p class="text-lg font-black text-mint-900 tabular-nums">&#8369;{{ number_format($selectedProduct->selling_price, 2) }}</p>
                        </div>
                        <div class="px-4 py-3 bg-beige-50 border border-beige-100 rounded-xl">
                            <p class="text-[10px] font-black text-beige-500 uppercase">Saved Cost</p>
                            <p class="text-lg font-black text-mint-900 tabular-nums">&#8369;{{ number_format($breakdown['complete_cost_per_serving'], 2) }}</p>
                        </div>
                        <div class="px-4 py-3 bg-white border border-beige-200 rounded-xl">
                            <p class="text-[10px] font-black text-beige-500 uppercase">Current Margin</p>
                            <p class="text-lg font-black text-mint-900 tabular-nums">{{ number_format($explanation['current_margin'], 2) }}%</p>
                        </div>
                        <div class="px-4 py-3 bg-white border border-beige-200 rounded-xl">
                            <p class="text-[10px] font-black text-beige-500 uppercase">Target Margin</p>
                            <p class="text-lg font-black text-mint-900 tabular-nums">{{ number_format($explanation['target_margin'], 2) }}%</p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 md:grid-cols-5 gap-2">
                    @foreach(['Enter Product Costs', 'Set Target Margin', 'Review Smart Price Options', 'Simulate Profit', 'Approve Selling Price'] as $step)
                        <div class="px-3 py-2 bg-beige-50 border border-beige-100 rounded-lg text-[11px] font-black text-mint-800 uppercase tracking-wide">{{ $step }}</div>
                    @endforeach
                </div>
            </div>

            <template x-if="activeWarnings.length">
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4" role="alert">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z"/></svg>
                        <div>
                            <p class="text-sm font-black text-red-700">Profit Protection</p>
                            <template x-for="warning in activeWarnings" :key="warning">
                                <p class="text-sm text-red-700 mt-1" x-text="warning"></p>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            <form method="POST" action="{{ route('smart-pricing.costs.update', $selectedProduct) }}" @input="dirty = true" @change="dirty = true" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 2xl:grid-cols-[minmax(0,1fr)_360px] gap-6">
                    <div class="bg-white border border-beige-200/60 rounded-2xl overflow-hidden">
                        <div class="px-5 py-4 border-b border-beige-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-black text-mint-900">Enter Product Costs</h3>
                                <p class="text-xs text-beige-500 mt-1">Ingredient rows use quantity per serving multiplied by ingredient cost.</p>
                            </div>
                            <button type="button" @click="addIngredient(); dirty = true" class="min-h-[44px] px-4 py-2 bg-white border border-mint-200 text-mint-700 font-bold text-sm rounded-xl hover:bg-mint-50 transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Ingredient
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[760px] text-sm">
                                <thead class="bg-beige-50 text-[11px] uppercase tracking-wider text-beige-500">
                                    <tr>
                                        <th class="text-left font-black px-4 py-3 w-[28%]">Ingredient</th>
                                        <th class="text-left font-black px-4 py-3">Qty/Serving</th>
                                        <th class="text-left font-black px-4 py-3">Unit</th>
                                        <th class="text-left font-black px-4 py-3">Cost/Unit</th>
                                        <th class="text-left font-black px-4 py-3">Line Cost</th>
                                        <th class="text-left font-black px-4 py-3">Estimate</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-beige-100">
                                    <template x-for="(ingredient, index) in ingredients" :key="index">
                                        <tr>
                                            <td class="px-4 py-3">
                                                <input type="text" :name="`ingredients[${index}][name]`" x-model="ingredient.name" class="w-full px-3 py-2 bg-beige-50 border border-beige-200 rounded-lg text-sm" placeholder="Chicken breast">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" min="0" step="0.0001" :name="`ingredients[${index}][quantity_per_serving]`" x-model.number="ingredient.quantity_per_serving" class="w-full px-3 py-2 bg-beige-50 border border-beige-200 rounded-lg text-sm">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="text" :name="`ingredients[${index}][unit]`" x-model="ingredient.unit" class="w-full px-3 py-2 bg-beige-50 border border-beige-200 rounded-lg text-sm" placeholder="g">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" min="0" step="0.0001" :name="`ingredients[${index}][cost_per_unit]`" x-model.number="ingredient.cost_per_unit" class="w-full px-3 py-2 bg-beige-50 border border-beige-200 rounded-lg text-sm">
                                            </td>
                                            <td class="px-4 py-3 font-black text-mint-900 tabular-nums" x-text="currency(lineCost(ingredient))"></td>
                                            <td class="px-4 py-3">
                                                <label class="inline-flex min-h-[44px] items-center gap-2 cursor-pointer">
                                                    <input type="checkbox" value="1" :name="`ingredients[${index}][is_estimated]`" x-model="ingredient.is_estimated" class="rounded border-beige-300 text-mint-500 focus:ring-mint-500">
                                                    <span class="text-xs font-bold text-beige-600">Yes</span>
                                                </label>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <button type="button" @click="removeIngredient(index); dirty = true" class="min-w-[44px] min-h-[44px] inline-flex items-center justify-center text-beige-400 hover:text-red-500 rounded-lg transition-colors" aria-label="Remove ingredient">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white border border-beige-200/60 rounded-2xl p-5 space-y-5">
                        <div>
                            <h3 class="text-sm font-black text-mint-900">Set Target Margin</h3>
                            <p class="text-xs text-beige-500 mt-1">Minimum protects the floor. Desired drives the recommendation.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="minimum_margin">Minimum %</label>
                                <input id="minimum_margin" name="minimum_margin" type="number" min="0" max="90" step="0.01" x-model.number="minimumMargin" required class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="desired_margin">Desired %</label>
                                <input id="desired_margin" name="desired_margin" type="number" min="0" max="90" step="0.01" x-model.number="desiredMargin" required class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="packaging_cost">Packaging</label>
                                <input id="packaging_cost" name="packaging_cost" type="number" min="0" step="0.01" x-model.number="costs.packaging_cost" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="labor_allowance">Labor</label>
                                <input id="labor_allowance" name="labor_allowance" type="number" min="0" step="0.01" x-model.number="costs.labor_allowance" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="utility_allowance">Utility</label>
                                <input id="utility_allowance" name="utility_allowance" type="number" min="0" step="0.01" x-model.number="costs.utility_allowance" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="transportation_cost">Transport</label>
                                <input id="transportation_cost" name="transportation_cost" type="number" min="0" step="0.01" x-model.number="costs.transportation_cost" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="delivery_fees">Delivery Fees</label>
                                <input id="delivery_fees" name="delivery_fees" type="number" min="0" step="0.01" x-model.number="costs.delivery_fees" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="waste_percentage">Waste %</label>
                                <input id="waste_percentage" name="waste_percentage" type="number" min="0" max="100" step="0.01" x-model.number="costs.waste_percentage" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                            </div>
                        </div>

                        <label class="flex min-h-[44px] items-center justify-between gap-3 px-3 py-2 bg-mint-50 border border-mint-100 rounded-xl cursor-pointer">
                            <span class="text-sm font-bold text-mint-900">Smart rounding</span>
                            <span>
                                <input type="hidden" name="smart_rounding" value="0">
                                <input type="checkbox" name="smart_rounding" value="1" x-model="smartRounding" class="rounded border-beige-300 text-mint-500 focus:ring-mint-500">
                            </span>
                        </label>

                        <div class="space-y-2 pt-2 border-t border-beige-100">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-beige-600">Ingredients</span>
                                <strong class="text-mint-900 tabular-nums" x-text="currency(ingredientCost())"></strong>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-beige-600">Operating</span>
                                <strong class="text-mint-900 tabular-nums" x-text="currency(operatingCost())"></strong>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-beige-600">Waste allowance</span>
                                <strong class="text-mint-900 tabular-nums" x-text="currency(wasteAllowance())"></strong>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-beige-100">
                                <span class="text-sm font-black text-mint-900">Complete cost</span>
                                <strong class="text-xl font-black text-mint-700 tabular-nums" x-text="currency(completeCost())"></strong>
                            </div>
                        </div>

                        <button type="submit" class="w-full min-h-[48px] px-5 py-3 bg-mint-500 text-white font-black text-sm rounded-xl hover:bg-mint-600 transition-all shadow-lg shadow-mint-500/25 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M20 9A8 8 0 006.34 4.34L4 6.68M4 15a8 8 0 0013.66 4.66L20 17.32"/></svg>
                            Recalculate Options
                        </button>
                    </div>
                </div>
            </form>

            <div class="bg-white border border-beige-200/60 rounded-2xl p-5">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-5">
                    <div>
                        <h3 class="text-sm font-black text-mint-900">Review Smart Price Options</h3>
                        <p class="text-xs text-beige-500 mt-1">Target margin formula: price = complete cost / (1 - target margin).</p>
                    </div>
                    <div x-show="dirty" class="px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl text-xs font-bold text-amber-700">
                        Recalculate before approval.
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    @foreach([
                        'minimum_safe' => 'minimumOption',
                        'recommended' => 'recommendedOption',
                        'premium' => 'premiumOption',
                    ] as $optionKey => $alpineOption)
                        <div class="border border-beige-200 rounded-2xl p-4 {{ $optionKey === 'recommended' ? 'bg-mint-50/60 ring-1 ring-mint-200' : 'bg-white' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-wider {{ $optionKey === 'recommended' ? 'text-mint-700' : 'text-beige-500' }}" x-text="{{ $alpineOption }}.label"></p>
                                    <p class="text-3xl font-black text-mint-900 tabular-nums mt-2" x-text="currency({{ $alpineOption }}.selling_price)"></p>
                                </div>
                                @if($optionKey === 'recommended')
                                    <span class="px-2.5 py-1 bg-mint-500 text-white text-[10px] font-black uppercase rounded-lg">Target</span>
                                @endif
                            </div>

                            <dl class="mt-5 space-y-2 text-sm">
                                <div class="flex justify-between gap-3">
                                    <dt class="text-beige-600">Cost per serving</dt>
                                    <dd class="font-black text-mint-900 tabular-nums" x-text="currency({{ $alpineOption }}.cost_per_serving)"></dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-beige-600">Expected profit</dt>
                                    <dd class="font-black text-mint-900 tabular-nums" x-text="currency({{ $alpineOption }}.expected_profit)"></dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-beige-600">Markup</dt>
                                    <dd class="font-black text-mint-900 tabular-nums" x-text="percent({{ $alpineOption }}.markup_percentage)"></dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-beige-600">Profit margin</dt>
                                    <dd class="font-black text-mint-900 tabular-nums" x-text="percent({{ $alpineOption }}.profit_margin_percentage)"></dd>
                                </div>
                            </dl>

                            <form method="POST" action="{{ route('smart-pricing.approve', $selectedProduct) }}" class="mt-5 space-y-3">
                                @csrf
                                <input type="hidden" name="option" value="{{ $optionKey }}">
                                <div>
                                    <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="effective-{{ $optionKey }}">Effective Date</label>
                                    <input id="effective-{{ $optionKey }}" name="effective_date" type="date" value="{{ today()->format('Y-m-d') }}" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="reason-{{ $optionKey }}">Reason</label>
                                    <input id="reason-{{ $optionKey }}" name="reason" type="text" placeholder="Optional approval note" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                                </div>
                                <button type="submit" :disabled="dirty" class="w-full min-h-[44px] px-4 py-2.5 bg-mint-500 text-white font-bold text-sm rounded-xl hover:bg-mint-600 transition-all disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Approve
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 2xl:grid-cols-2 gap-6">
                <div class="bg-white border border-beige-200/60 rounded-2xl p-5">
                    <h3 class="text-sm font-black text-mint-900 mb-4">Recommendation Explanation</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="px-4 py-3 bg-beige-50 border border-beige-100 rounded-xl">
                            <dt class="text-[10px] font-black text-beige-500 uppercase">Previous Cost</dt>
                            <dd class="text-lg font-black text-mint-900 tabular-nums">&#8369;{{ number_format($explanation['previous_cost'], 2) }}</dd>
                        </div>
                        <div class="px-4 py-3 bg-beige-50 border border-beige-100 rounded-xl">
                            <dt class="text-[10px] font-black text-beige-500 uppercase">Updated Cost</dt>
                            <dd class="text-lg font-black text-mint-900 tabular-nums" x-text="currency(completeCost())"></dd>
                        </div>
                        <div class="px-4 py-3 bg-beige-50 border border-beige-100 rounded-xl">
                            <dt class="text-[10px] font-black text-beige-500 uppercase">Current Price</dt>
                            <dd class="text-lg font-black text-mint-900 tabular-nums">&#8369;{{ number_format($explanation['current_selling_price'], 2) }}</dd>
                        </div>
                        <div class="px-4 py-3 bg-beige-50 border border-beige-100 rounded-xl">
                            <dt class="text-[10px] font-black text-beige-500 uppercase">Current Margin</dt>
                            <dd class="text-lg font-black text-mint-900 tabular-nums" x-text="percent(currentMargin())"></dd>
                        </div>
                        <div class="px-4 py-3 bg-beige-50 border border-beige-100 rounded-xl">
                            <dt class="text-[10px] font-black text-beige-500 uppercase">Target Margin</dt>
                            <dd class="text-lg font-black text-mint-900 tabular-nums" x-text="percent(targetMargin())"></dd>
                        </div>
                        <div class="px-4 py-3 bg-beige-50 border border-beige-100 rounded-xl">
                            <dt class="text-[10px] font-black text-beige-500 uppercase">Expected Profit</dt>
                            <dd class="text-lg font-black text-mint-900 tabular-nums" x-text="currency(recommendedOption.expected_profit)"></dd>
                        </div>
                    </dl>
                    <p class="mt-4 text-sm leading-6 text-beige-700">{{ $explanation['summary'] }}</p>
                </div>

                <div class="bg-white border border-beige-200/60 rounded-2xl p-5">
                    <h3 class="text-sm font-black text-mint-900 mb-4">Simulate Profit</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="simulator-price">Selling Price</label>
                            <input id="simulator-price" type="number" min="0" step="0.01" x-model.number="simulatorPrice" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="simulator-quantity">Expected Sales Qty</label>
                            <input id="simulator-quantity" type="number" min="0" step="1" x-model.number="simulatorQuantity" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                        </div>
                    </div>
                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="px-4 py-3 bg-mint-50 border border-mint-100 rounded-xl">
                            <p class="text-[10px] font-black text-mint-600 uppercase">Revenue</p>
                            <p class="text-xl font-black text-mint-900 tabular-nums" x-text="currency(projectedRevenue())"></p>
                        </div>
                        <div class="px-4 py-3 bg-beige-50 border border-beige-100 rounded-xl">
                            <p class="text-[10px] font-black text-beige-500 uppercase">Total Cost</p>
                            <p class="text-xl font-black text-mint-900 tabular-nums" x-text="currency(projectedTotalCost())"></p>
                        </div>
                        <div class="px-4 py-3 bg-white border border-beige-200 rounded-xl">
                            <p class="text-[10px] font-black text-beige-500 uppercase">Gross Profit</p>
                            <p class="text-xl font-black text-mint-900 tabular-nums" x-text="currency(projectedGrossProfit())"></p>
                        </div>
                        <div class="px-4 py-3 bg-white border border-beige-200 rounded-xl">
                            <p class="text-[10px] font-black text-beige-500 uppercase">Profit Margin</p>
                            <p class="text-xl font-black text-mint-900 tabular-nums" x-text="percent(projectedMargin())"></p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('smart-pricing.override', $selectedProduct) }}" class="mt-5 pt-5 border-t border-beige-100 space-y-3">
                        @csrf
                        <h4 class="text-sm font-black text-mint-900">Owner Override</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="override-price">Override Price</label>
                                <input id="override-price" name="approved_price" type="number" min="0.01" step="0.01" x-model.number="simulatorPrice" required class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="override-effective">Effective Date</label>
                                <input id="override-effective" name="effective_date" type="date" value="{{ today()->format('Y-m-d') }}" class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-beige-600 uppercase mb-1" for="override-reason">Required Reason</label>
                            <textarea id="override-reason" name="reason" rows="2" required class="w-full px-3 py-2.5 bg-beige-50 border border-beige-200 rounded-xl text-sm" placeholder="Reason for approving a custom price"></textarea>
                        </div>
                        <button type="submit" :disabled="dirty" class="w-full min-h-[44px] px-4 py-2.5 bg-beige-800 text-white font-bold text-sm rounded-xl hover:bg-beige-900 transition-all disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Save Override
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white border border-beige-200/60 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-beige-100">
                    <h3 class="text-sm font-black text-mint-900">Pricing History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[920px] text-sm">
                        <thead class="bg-beige-50 text-[11px] uppercase tracking-wider text-beige-500">
                            <tr>
                                <th class="text-left font-black px-4 py-3">Effective</th>
                                <th class="text-left font-black px-4 py-3">Previous</th>
                                <th class="text-left font-black px-4 py-3">Recommended</th>
                                <th class="text-left font-black px-4 py-3">Approved</th>
                                <th class="text-left font-black px-4 py-3">Cost</th>
                                <th class="text-left font-black px-4 py-3">Reason</th>
                                <th class="text-left font-black px-4 py-3">Approved By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-beige-100">
                            @forelse($selectedProduct->pricingHistories as $history)
                                <tr>
                                    <td class="px-4 py-3 font-bold text-mint-900">{{ $history->effective_date->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 tabular-nums">&#8369;{{ number_format($history->previous_price, 2) }}</td>
                                    <td class="px-4 py-3 tabular-nums">
                                        @if(is_null($history->recommended_price))
                                            None
                                        @else
                                            &#8369;{{ number_format($history->recommended_price, 2) }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-black text-mint-900 tabular-nums">&#8369;{{ number_format($history->approved_price, 2) }}</td>
                                    <td class="px-4 py-3 tabular-nums">&#8369;{{ number_format($history->updated_cost_per_serving, 2) }}</td>
                                    <td class="px-4 py-3 max-w-sm">
                                        <span class="block truncate" title="{{ $history->reason }}">{{ $history->reason ?: ucfirst(str_replace('_', ' ', $history->action)) }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $history->approver?->name ?? 'Unknown' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-sm font-bold text-beige-500">No approved price changes yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        @else
        <section class="bg-white border border-beige-200/60 rounded-2xl p-10 text-center">
            <h2 class="text-xl font-black text-mint-900">Create a product to start Smart Pricing.</h2>
            <p class="text-sm text-beige-600 mt-2">The pricing workspace will appear after the first product is created.</p>
        </section>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function smartPricing(data) {
    return {
        dirty: false,
        ingredients: data.ingredients,
        costs: data.costs,
        minimumMargin: data.minimumMargin,
        desiredMargin: data.desiredMargin,
        smartRounding: data.smartRounding,
        activePrice: Number(data.activePrice || 0),
        previousCost: Number(data.previousCost || 0),
        simulatorPrice: Number(data.simulatorPrice || data.activePrice || 0),
        simulatorQuantity: 100,
        currency(value) {
            return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(Number(value || 0));
        },
        percent(value) {
            return `${Number(value || 0).toFixed(2)}%`;
        },
        number(value) {
            return Number(value || 0);
        },
        addIngredient() {
            this.ingredients.push({
                name: '',
                quantity_per_serving: 1,
                unit: 'serving',
                cost_per_unit: 0,
                is_estimated: false,
                notes: ''
            });
        },
        removeIngredient(index) {
            if (this.ingredients.length === 1) {
                this.ingredients[0] = {
                    name: '',
                    quantity_per_serving: 1,
                    unit: 'serving',
                    cost_per_unit: 0,
                    is_estimated: false,
                    notes: ''
                };
                return;
            }
            this.ingredients.splice(index, 1);
        },
        lineCost(ingredient) {
            return this.number(ingredient.quantity_per_serving) * this.number(ingredient.cost_per_unit);
        },
        ingredientCost() {
            return this.ingredients.reduce((total, ingredient) => total + this.lineCost(ingredient), 0);
        },
        operatingCost() {
            return this.number(this.costs.packaging_cost)
                + this.number(this.costs.labor_allowance)
                + this.number(this.costs.utility_allowance)
                + this.number(this.costs.transportation_cost)
                + this.number(this.costs.delivery_fees);
        },
        wasteAllowance() {
            return (this.ingredientCost() + this.operatingCost()) * (this.number(this.costs.waste_percentage) / 100);
        },
        completeCost() {
            return this.round(this.ingredientCost() + this.operatingCost() + this.wasteAllowance());
        },
        targetMargin() {
            return Math.max(this.number(this.minimumMargin), this.number(this.desiredMargin));
        },
        option(label, margin) {
            const cost = this.completeCost();
            const target = Math.min(90, Math.max(0, this.number(margin)));
            const rawPrice = cost <= 0 ? 0 : cost / (1 - (target / 100));
            const price = this.smartRounding ? this.friendlyPrice(rawPrice) : this.round(rawPrice);
            const profit = this.round(price - cost);
            return {
                label: label,
                target_margin: target,
                selling_price: price,
                cost_per_serving: cost,
                expected_profit: profit,
                markup_percentage: cost > 0 ? this.round((profit / cost) * 100) : 0,
                profit_margin_percentage: price > 0 ? this.round((profit / price) * 100) : 0
            };
        },
        friendlyPrice(rawPrice) {
            if (rawPrice <= 0) return 0;
            if (rawPrice <= 9) return 9;
            return this.round(Math.ceil((rawPrice + 1) / 10) * 10 - 1);
        },
        round(value) {
            return Math.round((Number(value || 0) + Number.EPSILON) * 100) / 100;
        },
        currentMargin() {
            const price = this.activePrice;
            const cost = this.completeCost();
            if (price <= 0) return 0;
            return this.round(((price - cost) / price) * 100);
        },
        projectedRevenue() {
            return this.round(this.number(this.simulatorPrice) * this.number(this.simulatorQuantity));
        },
        projectedTotalCost() {
            return this.round(this.completeCost() * this.number(this.simulatorQuantity));
        },
        projectedGrossProfit() {
            return this.round(this.projectedRevenue() - this.projectedTotalCost());
        },
        projectedMargin() {
            const revenue = this.projectedRevenue();
            if (revenue <= 0) return 0;
            return this.round((this.projectedGrossProfit() / revenue) * 100);
        },
        get minimumOption() {
            return this.option('Minimum Safe Price', this.minimumMargin);
        },
        get recommendedOption() {
            return this.option('Recommended Price', this.targetMargin());
        },
        get premiumOption() {
            return this.option('Premium Price', Math.min(90, this.targetMargin() + 10));
        },
        get activeWarnings() {
            const warnings = [];
            const cost = this.completeCost();
            if (this.activePrice <= 0) {
                warnings.push('No active selling price has been approved for this product yet.');
            }
            if (this.activePrice > 0 && this.activePrice < cost) {
                warnings.push('Current selling price is below the complete cost per serving.');
            }
            if (this.activePrice > 0 && this.currentMargin() < this.number(this.minimumMargin)) {
                warnings.push('Current selling price does not meet the configured minimum profit margin.');
            }
            return warnings;
        }
    };
}
</script>
@endpush
