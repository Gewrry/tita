<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\Category;
use App\Models\PricingHistory;
use App\Models\Product;
use App\Models\ProductPricingProfile;
use App\Services\SmartPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SmartPricingController extends Controller
{
    public function index(Request $request, SmartPricingService $pricing)
    {
        $products = Product::with(['category', 'pricingProfile'])
            ->orderBy('name')
            ->get();

        $selectedProduct = null;
        if ($request->filled('product')) {
            $selectedProduct = Product::with([
                'category',
                'pricingProfile',
                'pricingIngredients',
                'pricingHistories.approver',
            ])->find($request->integer('product'));
        }

        $selectedProduct ??= Product::with([
            'category',
            'pricingProfile',
            'pricingIngredients',
            'pricingHistories.approver',
        ])->orderBy('name')->first();

        $recommendation = $selectedProduct
            ? $pricing->buildRecommendation($selectedProduct)
            : null;

        return view('smart-pricing.index', [
            'categories' => Category::orderBy('sort_order')->get(),
            'products' => $products,
            'selectedProduct' => $selectedProduct,
            'recommendation' => $recommendation,
            'units' => Product::$units,
        ]);
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'unit' => ['required', Rule::in(array_keys(Product::$units))],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'minimum_margin' => ['required', 'numeric', 'min:0', 'max:90'],
            'desired_margin' => ['required', 'numeric', 'min:0', 'max:90', 'gte:minimum_margin'],
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'] ?? null,
            'unit' => $validated['unit'],
            'cost_price' => 0,
            'selling_price' => $validated['selling_price'] ?? 0,
            'stock_quantity' => 0,
            'reorder_level' => 0,
            'is_active' => true,
            'track_stock' => false,
        ]);

        ProductPricingProfile::create([
            'product_id' => $product->id,
            'minimum_margin' => $validated['minimum_margin'],
            'desired_margin' => $validated['desired_margin'],
        ]);

        AuditTrail::log('smart_pricing_product_created', $product, null, $product->fresh()->toArray());

        return redirect()
            ->route('smart-pricing.index', ['product' => $product->id])
            ->with('success', 'Product created for Smart Pricing. Add ingredients and costs next.');
    }

    public function updateCosts(Request $request, Product $product, SmartPricingService $pricing)
    {
        $validated = $request->validate([
            'ingredients' => ['nullable', 'array'],
            'ingredients.*.name' => ['nullable', 'string', 'max:255'],
            'ingredients.*.quantity_per_serving' => ['nullable', 'numeric', 'min:0'],
            'ingredients.*.unit' => ['nullable', 'string', 'max:50'],
            'ingredients.*.cost_per_unit' => ['nullable', 'numeric', 'min:0'],
            'ingredients.*.is_estimated' => ['nullable', 'boolean'],
            'ingredients.*.notes' => ['nullable', 'string', 'max:255'],
            'packaging_cost' => ['nullable', 'numeric', 'min:0'],
            'labor_allowance' => ['nullable', 'numeric', 'min:0'],
            'utility_allowance' => ['nullable', 'numeric', 'min:0'],
            'transportation_cost' => ['nullable', 'numeric', 'min:0'],
            'delivery_fees' => ['nullable', 'numeric', 'min:0'],
            'waste_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'minimum_margin' => ['required', 'numeric', 'min:0', 'max:90'],
            'desired_margin' => ['required', 'numeric', 'min:0', 'max:90', 'gte:minimum_margin'],
            'smart_rounding' => ['nullable', 'boolean'],
        ]);

        $product->load(['pricingProfile', 'pricingIngredients']);
        $previousCost = (float) ($product->pricingProfile?->complete_cost_per_serving ?? $product->cost_price);
        $oldValues = [
            'profile' => $product->pricingProfile?->toArray(),
            'ingredients' => $product->pricingIngredients->toArray(),
            'cost_price' => $product->cost_price,
            'selling_price' => $product->selling_price,
        ];

        $recommendation = DB::transaction(function () use ($validated, $product, $pricing, $previousCost) {
            $profile = ProductPricingProfile::firstOrNew(['product_id' => $product->id]);
            $profile->fill([
                'packaging_cost' => $validated['packaging_cost'] ?? 0,
                'labor_allowance' => $validated['labor_allowance'] ?? 0,
                'utility_allowance' => $validated['utility_allowance'] ?? 0,
                'transportation_cost' => $validated['transportation_cost'] ?? 0,
                'delivery_fees' => $validated['delivery_fees'] ?? 0,
                'waste_percentage' => $validated['waste_percentage'] ?? 0,
                'minimum_margin' => $validated['minimum_margin'],
                'desired_margin' => $validated['desired_margin'],
                'smart_rounding' => (bool) ($validated['smart_rounding'] ?? false),
            ]);
            $profile->save();

            $product->pricingIngredients()->delete();
            foreach ($validated['ingredients'] ?? [] as $ingredient) {
                if (blank($ingredient['name'] ?? null)) {
                    continue;
                }

                $product->pricingIngredients()->create([
                    'name' => $ingredient['name'],
                    'quantity_per_serving' => $ingredient['quantity_per_serving'] ?? 0,
                    'unit' => $ingredient['unit'] ?? 'unit',
                    'cost_per_unit' => $ingredient['cost_per_unit'] ?? 0,
                    'is_estimated' => (bool) ($ingredient['is_estimated'] ?? false),
                    'notes' => $ingredient['notes'] ?? null,
                ]);
            }

            $product->load('pricingIngredients');
            $product->setRelation('pricingProfile', $profile->fresh());
            $recommendation = $pricing->buildRecommendation($product, $previousCost);

            $profile->update([
                'previous_cost_per_serving' => $previousCost,
                'complete_cost_per_serving' => $recommendation['breakdown']['complete_cost_per_serving'],
                'last_recommendation' => $recommendation,
                'last_recommended_at' => now(),
            ]);

            $product->forceFill([
                'cost_price' => $recommendation['breakdown']['complete_cost_per_serving'],
            ])->save();

            return $recommendation;
        });

        AuditTrail::log('smart_pricing_recalculated', $product, $oldValues, [
            'profile' => $product->pricingProfile()->first()?->toArray(),
            'ingredients' => $product->pricingIngredients()->get()->toArray(),
            'recommendation' => $recommendation,
            'selling_price_unchanged' => true,
        ]);

        return redirect()
            ->route('smart-pricing.index', ['product' => $product->id])
            ->with('success', 'Costs recalculated. Active selling price was not changed.');
    }

    public function approve(Request $request, Product $product, SmartPricingService $pricing)
    {
        $validated = $request->validate([
            'option' => ['required', Rule::in(['minimum_safe', 'recommended', 'premium'])],
            'effective_date' => ['nullable', 'date'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $recommendation = $pricing->buildRecommendation(
            $product->load(['pricingProfile', 'pricingIngredients', 'pricingHistories'])
        );
        $selectedOption = $recommendation['options'][$validated['option']];
        $previousPrice = (float) $product->selling_price;

        DB::transaction(function () use ($product, $selectedOption, $recommendation, $validated, $previousPrice) {
            $product->forceFill([
                'cost_price' => $recommendation['breakdown']['complete_cost_per_serving'],
                'selling_price' => $selectedOption['selling_price'],
            ])->save();

            PricingHistory::create([
                'product_id' => $product->id,
                'previous_price' => $previousPrice,
                'recommended_price' => $selectedOption['selling_price'],
                'approved_price' => $selectedOption['selling_price'],
                'previous_cost_per_serving' => $recommendation['explanation']['previous_cost'],
                'updated_cost_per_serving' => $recommendation['breakdown']['complete_cost_per_serving'],
                'effective_date' => $validated['effective_date'] ?? today(),
                'reason' => $validated['reason'] ?: 'Approved '.$selectedOption['label'],
                'approved_by' => auth()->id(),
                'action' => 'approved_recommendation',
                'metadata' => [
                    'option' => $validated['option'],
                    'selected_option' => $selectedOption,
                    'explanation' => $recommendation['explanation'],
                    'warnings' => $recommendation['warnings'],
                ],
            ]);
        });

        AuditTrail::log('smart_price_approved', $product, [
            'selling_price' => $previousPrice,
        ], [
            'selling_price' => $selectedOption['selling_price'],
            'option' => $validated['option'],
        ]);

        return redirect()
            ->route('smart-pricing.index', ['product' => $product->id])
            ->with('success', $selectedOption['label'].' approved as the active selling price.');
    }

    public function override(Request $request, Product $product, SmartPricingService $pricing)
    {
        $validated = $request->validate([
            'approved_price' => ['required', 'numeric', 'min:0.01'],
            'effective_date' => ['nullable', 'date'],
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $recommendation = $pricing->buildRecommendation(
            $product->load(['pricingProfile', 'pricingIngredients', 'pricingHistories'])
        );
        $previousPrice = (float) $product->selling_price;
        $approvedPrice = round((float) $validated['approved_price'], 2);
        $cost = $recommendation['breakdown']['complete_cost_per_serving'];
        $margin = $pricing->profitMargin($approvedPrice, $cost);
        $minimumMargin = (float) ($product->pricingProfile?->minimum_margin ?? 25);
        $protectionWarnings = [];

        if ($approvedPrice < $cost) {
            $protectionWarnings[] = 'Override price is below complete product cost.';
        }

        if ($margin < $minimumMargin) {
            $protectionWarnings[] = 'Override price is below the configured minimum margin.';
        }

        DB::transaction(function () use ($product, $recommendation, $validated, $previousPrice, $approvedPrice, $protectionWarnings, $margin) {
            $product->forceFill([
                'cost_price' => $recommendation['breakdown']['complete_cost_per_serving'],
                'selling_price' => $approvedPrice,
            ])->save();

            PricingHistory::create([
                'product_id' => $product->id,
                'previous_price' => $previousPrice,
                'recommended_price' => $recommendation['options']['recommended']['selling_price'],
                'approved_price' => $approvedPrice,
                'previous_cost_per_serving' => $recommendation['explanation']['previous_cost'],
                'updated_cost_per_serving' => $recommendation['breakdown']['complete_cost_per_serving'],
                'effective_date' => $validated['effective_date'] ?? today(),
                'reason' => $validated['reason'],
                'approved_by' => auth()->id(),
                'action' => 'owner_override',
                'metadata' => [
                    'explanation' => $recommendation['explanation'],
                    'warnings' => $protectionWarnings,
                    'override_margin' => $margin,
                ],
            ]);
        });

        AuditTrail::log('smart_price_overridden', $product, [
            'selling_price' => $previousPrice,
        ], [
            'selling_price' => $approvedPrice,
            'reason' => $validated['reason'],
            'warnings' => $protectionWarnings,
        ]);

        return redirect()
            ->route('smart-pricing.index', ['product' => $product->id])
            ->with('success', 'Override price saved with an approval reason.');
    }
}
