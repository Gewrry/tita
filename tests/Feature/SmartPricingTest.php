<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use App\Models\PricingHistory;
use App\Models\Product;
use App\Models\ProductPricingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_restaurant_users_land_on_smart_dashboard_instead_of_the_full_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'owner@example.com',
        ]);
        BusinessSetting::create([
            'user_id' => $user->id,
            'business_name' => 'Tita Kitchen',
            'business_type' => 'restaurant',
            'currency' => 'PHP',
        ]);

        $loginResponse = $this->post('/login', [
            'email' => 'owner@example.com',
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect(route('profit-dashboard.index', absolute: false));

        $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));

        $dashboardResponse->assertRedirect(route('profit-dashboard.index', absolute: false));
    }

    public function test_smart_pricing_workspace_renders_for_a_restaurant_product(): void
    {
        $user = User::factory()->create();
        BusinessSetting::create([
            'user_id' => $user->id,
            'business_name' => 'Tita Kitchen',
            'business_type' => 'restaurant',
            'currency' => 'PHP',
        ]);
        $product = Product::create([
            'user_id' => $user->id,
            'name' => 'Chicken Rice Bowl',
            'cost_price' => 77,
            'selling_price' => 129,
            'stock_quantity' => 0,
            'reorder_level' => 0,
            'unit' => 'serving',
            'is_active' => true,
            'track_stock' => false,
        ]);

        $response = $this->actingAs($user)->get(route('smart-pricing.index', ['product' => $product->id]));

        $response
            ->assertOk()
            ->assertSee('Smart Pricing')
            ->assertSee('Chicken Rice Bowl');
    }

    public function test_cost_changes_recommend_a_price_without_changing_the_active_price(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'user_id' => $user->id,
            'name' => 'Chicken Rice Bowl',
            'cost_price' => 0,
            'selling_price' => 100,
            'stock_quantity' => 0,
            'reorder_level' => 0,
            'unit' => 'serving',
            'is_active' => true,
            'track_stock' => false,
        ]);

        $response = $this->actingAs($user)->put(route('smart-pricing.costs.update', $product), [
            'ingredients' => [
                [
                    'name' => 'Chicken and rice',
                    'quantity_per_serving' => 1,
                    'unit' => 'serving',
                    'cost_per_unit' => 50,
                ],
            ],
            'packaging_cost' => 5,
            'labor_allowance' => 10,
            'utility_allowance' => 5,
            'transportation_cost' => 0,
            'delivery_fees' => 0,
            'waste_percentage' => 10,
            'minimum_margin' => 25,
            'desired_margin' => 40,
            'smart_rounding' => 1,
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('smart-pricing.index', ['product' => $product->id]));

        $product->refresh();
        $profile = ProductPricingProfile::where('product_id', $product->id)->firstOrFail();

        $this->assertSame('77.00', $product->cost_price);
        $this->assertSame('100.00', $product->selling_price);
        $this->assertSame('77.00', $profile->complete_cost_per_serving);
        $this->assertEquals(129.0, $profile->last_recommendation['options']['recommended']['selling_price']);
    }

    public function test_owner_can_approve_a_recommended_price_and_record_history(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'user_id' => $user->id,
            'name' => 'Chicken Rice Bowl',
            'cost_price' => 77,
            'selling_price' => 100,
            'stock_quantity' => 0,
            'reorder_level' => 0,
            'unit' => 'serving',
            'is_active' => true,
            'track_stock' => false,
        ]);

        ProductPricingProfile::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'packaging_cost' => 5,
            'labor_allowance' => 10,
            'utility_allowance' => 5,
            'waste_percentage' => 10,
            'minimum_margin' => 25,
            'desired_margin' => 40,
            'complete_cost_per_serving' => 77,
            'previous_cost_per_serving' => 0,
            'smart_rounding' => true,
        ]);

        $product->pricingIngredients()->create([
            'user_id' => $user->id,
            'name' => 'Chicken and rice',
            'quantity_per_serving' => 1,
            'unit' => 'serving',
            'cost_per_unit' => 50,
        ]);

        $response = $this->actingAs($user)->post(route('smart-pricing.approve', $product), [
            'option' => 'recommended',
            'effective_date' => '2026-07-28',
            'reason' => 'Owner approved target margin price.',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('smart-pricing.index', ['product' => $product->id]));

        $this->assertSame('129.00', $product->refresh()->selling_price);
        $this->assertSame(1, PricingHistory::where('product_id', $product->id)->count());

        $history = PricingHistory::where('product_id', $product->id)->firstOrFail();
        $this->assertSame('100.00', $history->previous_price);
        $this->assertSame('129.00', $history->recommended_price);
        $this->assertSame('129.00', $history->approved_price);
        $this->assertSame($user->id, $history->approved_by);
    }
}
