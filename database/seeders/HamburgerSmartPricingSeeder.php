<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\FoodSale;
use App\Models\PricingHistory;
use App\Models\Product;
use App\Models\ProductPricingProfile;
use App\Models\User;
use App\Services\SmartPricingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HamburgerSmartPricingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->orderBy('id')->first()
            ?: User::create([
                'name' => 'Demo Owner',
                'email' => 'demo@tita.local',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

        BusinessSetting::withoutGlobalScopes()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'business_name' => 'TITA Burger Kitchen',
                'business_type' => 'restaurant',
                'currency' => 'PHP',
            ]
        );

        $category = Category::withoutGlobalScopes()->updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Burgers'],
            [
                'description' => 'Smart pricing burger samples',
                'icon' => 'burger',
                'color' => '#10B981',
                'sort_order' => 1,
            ]
        );

        $product = Product::withoutGlobalScopes()->firstOrNew([
            'user_id' => $user->id,
            'name' => 'Classic Hamburger',
        ]);
        $previousPrice = (float) ($product->exists ? $product->selling_price : 0);

        $product->fill([
            'category_id' => $category->id,
            'sku' => 'BURG-CLASSIC',
            'barcode' => null,
            'description' => 'Sample hamburger product for Smart Pricing.',
            'cost_price' => 75.60,
            'stock_quantity' => 0,
            'reorder_level' => 0,
            'unit' => 'serving',
            'is_active' => true,
            'track_stock' => false,
        ]);
        $product->selling_price = $previousPrice;
        $product->save();

        $profile = ProductPricingProfile::withoutGlobalScopes()->updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $product->id],
            [
                'packaging_cost' => 6,
                'labor_allowance' => 5,
                'utility_allowance' => 2,
                'transportation_cost' => 1,
                'delivery_fees' => 0,
                'waste_percentage' => 5,
                'minimum_margin' => 25,
                'desired_margin' => 40,
                'smart_rounding' => true,
                'previous_cost_per_serving' => 0,
            ]
        );

        $product->pricingIngredients()->delete();

        foreach ($this->hamburgerIngredients() as $ingredient) {
            $product->pricingIngredients()->create([
                'user_id' => $user->id,
                'name' => $ingredient['name'],
                'quantity_per_serving' => $ingredient['quantity_per_serving'],
                'unit' => $ingredient['unit'],
                'cost_per_unit' => $ingredient['cost_per_unit'],
                'is_estimated' => $ingredient['is_estimated'],
                'notes' => $ingredient['is_estimated'] ? 'Estimated sample cost' : 'Actual sample cost',
            ]);
        }

        $product = Product::withoutGlobalScopes()
            ->with(['pricingProfile', 'pricingIngredients'])
            ->findOrFail($product->id);

        $recommendation = app(SmartPricingService::class)->buildRecommendation($product, 0);
        $approvedPrice = $recommendation['options']['recommended']['selling_price'];

        $profile->update([
            'complete_cost_per_serving' => $recommendation['breakdown']['complete_cost_per_serving'],
            'last_recommendation' => $recommendation,
            'last_recommended_at' => now(),
        ]);

        $product->forceFill([
            'cost_price' => $recommendation['breakdown']['complete_cost_per_serving'],
            'selling_price' => $approvedPrice,
        ])->save();

        PricingHistory::withoutGlobalScopes()->updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'action' => 'sample_hamburger_approved',
            ],
            [
                'previous_price' => $previousPrice,
                'recommended_price' => $approvedPrice,
                'approved_price' => $approvedPrice,
                'previous_cost_per_serving' => 0,
                'updated_cost_per_serving' => $recommendation['breakdown']['complete_cost_per_serving'],
                'effective_date' => today(),
                'reason' => 'Sample Hamburger Smart Pricing data approved for review.',
                'approved_by' => $user->id,
                'metadata' => [
                    'option' => 'recommended',
                    'selected_option' => $recommendation['options']['recommended'],
                    'explanation' => $recommendation['explanation'],
                    'warnings' => $recommendation['warnings'],
                ],
            ]
        );

        FoodSale::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('channel', 'sample')
            ->delete();

        foreach ([14, 18, 12, 22, 16] as $daysAgo => $quantity) {
            $saleDate = today()->subDays(4 - $daysAgo);
            $totalRevenue = round($quantity * $approvedPrice, 2);
            $unitCost = $recommendation['breakdown']['complete_cost_per_serving'];
            $totalCost = round($quantity * $unitCost, 2);

            FoodSale::withoutGlobalScopes()->create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'sale_date' => $saleDate,
                'quantity' => $quantity,
                'selling_price' => $approvedPrice,
                'unit_cost' => $unitCost,
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'gross_profit' => round($totalRevenue - $totalCost, 2),
                'channel' => 'sample',
                'notes' => 'Sample Hamburger sale for profit dashboard review',
            ]);
        }

        $this->command?->info("Classic Hamburger sample added for {$user->email}.");
        $this->command?->line('Cost per serving: PHP '.$recommendation['breakdown']['complete_cost_per_serving']);
        $this->command?->line('Minimum Safe Price: PHP '.$recommendation['options']['minimum_safe']['selling_price']);
        $this->command?->line('Recommended Price: PHP '.$recommendation['options']['recommended']['selling_price']);
        $this->command?->line('Premium Price: PHP '.$recommendation['options']['premium']['selling_price']);
        $this->command?->line('Sample sales added: 82 units');
    }

    private function hamburgerIngredients(): array
    {
        return [
            ['name' => 'Burger bun', 'quantity_per_serving' => 1, 'unit' => 'piece', 'cost_per_unit' => 10, 'is_estimated' => false],
            ['name' => 'Beef patty', 'quantity_per_serving' => 1, 'unit' => 'piece', 'cost_per_unit' => 32, 'is_estimated' => false],
            ['name' => 'Cheese slice', 'quantity_per_serving' => 1, 'unit' => 'piece', 'cost_per_unit' => 7, 'is_estimated' => false],
            ['name' => 'Lettuce', 'quantity_per_serving' => 15, 'unit' => 'g', 'cost_per_unit' => 0.10, 'is_estimated' => true],
            ['name' => 'Tomato', 'quantity_per_serving' => 20, 'unit' => 'g', 'cost_per_unit' => 0.10, 'is_estimated' => true],
            ['name' => 'Onion', 'quantity_per_serving' => 10, 'unit' => 'g', 'cost_per_unit' => 0.07, 'is_estimated' => true],
            ['name' => 'Pickles', 'quantity_per_serving' => 10, 'unit' => 'g', 'cost_per_unit' => 0.12, 'is_estimated' => true],
            ['name' => 'Burger sauce', 'quantity_per_serving' => 20, 'unit' => 'ml', 'cost_per_unit' => 0.12, 'is_estimated' => true],
            ['name' => 'Seasoning and cooking oil', 'quantity_per_serving' => 1, 'unit' => 'serving', 'cost_per_unit' => 1.20, 'is_estimated' => true],
        ];
    }
}
