<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use App\Models\FoodSale;
use App\Models\Product;
use App\Models\ProductPricingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfitDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_restaurant_owner_can_record_a_simple_sale_and_view_profit_dashboard(): void
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
            'name' => 'Classic Hamburger',
            'cost_price' => 75.60,
            'selling_price' => 129,
            'stock_quantity' => 0,
            'reorder_level' => 0,
            'unit' => 'serving',
            'is_active' => true,
            'track_stock' => false,
        ]);
        ProductPricingProfile::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'complete_cost_per_serving' => 75.60,
            'minimum_margin' => 25,
            'desired_margin' => 40,
        ]);

        $response = $this->actingAs($user)->post(route('profit-dashboard.sales.store'), [
            'product_id' => $product->id,
            'sale_date' => '2026-07-28',
            'quantity' => 10,
            'selling_price' => 129,
            'channel' => 'walk_in',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profit-dashboard.index'));

        $sale = FoodSale::firstOrFail();
        $this->assertSame('1290.00', $sale->total_revenue);
        $this->assertSame('756.00', $sale->total_cost);
        $this->assertSame('534.00', $sale->gross_profit);

        $dashboard = $this->actingAs($user)->get(route('profit-dashboard.index', [
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-31',
        ]));

        $dashboard
            ->assertOk()
            ->assertSee('Smart Dashboard')
            ->assertSee('Classic Hamburger')
            ->assertSee('Menu Engineering');
    }
}
