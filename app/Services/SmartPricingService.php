<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPricingProfile;

class SmartPricingService
{
    public function buildRecommendation(Product $product, ?float $previousCost = null): array
    {
        $product->loadMissing(['pricingIngredients', 'pricingProfile']);

        $profile = $this->profileFor($product);
        $breakdown = $this->costBreakdown($product, $profile);
        $options = $this->priceOptions(
            $breakdown['complete_cost_per_serving'],
            (float) $profile->minimum_margin,
            (float) $profile->desired_margin,
            (bool) $profile->smart_rounding
        );

        $currentPrice = (float) $product->selling_price;
        $currentMargin = $this->profitMargin($currentPrice, $breakdown['complete_cost_per_serving']);
        $recommended = $options['recommended'];
        $minimumMargin = (float) $profile->minimum_margin;

        $warnings = [];
        if ($currentPrice > 0 && $currentPrice < $breakdown['complete_cost_per_serving']) {
            $warnings[] = 'Current selling price is below the complete cost per serving.';
        }

        if ($currentPrice > 0 && $currentMargin < $minimumMargin) {
            $warnings[] = 'Current selling price does not meet the configured minimum profit margin.';
        }

        if ($currentPrice <= 0) {
            $warnings[] = 'No active selling price has been approved for this product yet.';
        }

        return [
            'breakdown' => $breakdown,
            'options' => $options,
            'warnings' => $warnings,
            'explanation' => [
                'previous_cost' => round($previousCost ?? (float) ($profile->previous_cost_per_serving ?? $product->cost_price), 2),
                'updated_cost' => $breakdown['complete_cost_per_serving'],
                'current_selling_price' => round($currentPrice, 2),
                'current_margin' => $currentMargin,
                'target_margin' => round((float) $profile->desired_margin, 2),
                'expected_profit' => $recommended['expected_profit'],
                'summary' => 'Recommended Price uses the target profit margin formula: selling price = complete cost / (1 - target margin). Markup is profit divided by cost; profit margin is profit divided by selling price.',
            ],
        ];
    }

    public function costBreakdown(Product $product, ProductPricingProfile $profile): array
    {
        $ingredientCost = $product->pricingIngredients->sum(function ($ingredient) {
            return (float) $ingredient->quantity_per_serving * (float) $ingredient->cost_per_unit;
        });

        $packaging = (float) $profile->packaging_cost;
        $labor = (float) $profile->labor_allowance;
        $utility = (float) $profile->utility_allowance;
        $transportation = (float) $profile->transportation_cost;
        $delivery = (float) $profile->delivery_fees;
        $operatingCost = $packaging + $labor + $utility + $transportation + $delivery;
        $subtotalBeforeWaste = $ingredientCost + $operatingCost;
        $wasteAllowance = $subtotalBeforeWaste * ((float) $profile->waste_percentage / 100);
        $completeCost = $subtotalBeforeWaste + $wasteAllowance;

        return [
            'ingredient_cost' => round($ingredientCost, 2),
            'packaging_cost' => round($packaging, 2),
            'labor_allowance' => round($labor, 2),
            'utility_allowance' => round($utility, 2),
            'transportation_cost' => round($transportation, 2),
            'delivery_fees' => round($delivery, 2),
            'operating_cost' => round($operatingCost, 2),
            'waste_percentage' => round((float) $profile->waste_percentage, 2),
            'waste_allowance' => round($wasteAllowance, 2),
            'complete_cost_per_serving' => round($completeCost, 2),
        ];
    }

    public function priceOptions(float $cost, float $minimumMargin, float $desiredMargin, bool $smartRounding): array
    {
        $minimumMargin = $this->boundedMargin($minimumMargin);
        $desiredMargin = max($minimumMargin, $this->boundedMargin($desiredMargin));
        $premiumMargin = min(90, max($desiredMargin + 10, $minimumMargin + 10));

        return [
            'minimum_safe' => $this->option('Minimum Safe Price', $cost, $minimumMargin, $smartRounding),
            'recommended' => $this->option('Recommended Price', $cost, $desiredMargin, $smartRounding),
            'premium' => $this->option('Premium Price', $cost, $premiumMargin, $smartRounding),
        ];
    }

    public function profitMargin(float $price, float $cost): float
    {
        if ($price <= 0) {
            return 0;
        }

        return round((($price - $cost) / $price) * 100, 2);
    }

    public function markup(float $price, float $cost): float
    {
        if ($cost <= 0) {
            return 0;
        }

        return round((($price - $cost) / $cost) * 100, 2);
    }

    private function option(string $label, float $cost, float $targetMargin, bool $smartRounding): array
    {
        $marginRate = $this->boundedMargin($targetMargin) / 100;
        $rawPrice = $cost <= 0 ? 0 : $cost / (1 - $marginRate);
        $sellingPrice = $smartRounding ? $this->friendlyPrice($rawPrice) : round($rawPrice, 2);

        return [
            'label' => $label,
            'target_margin' => round($targetMargin, 2),
            'selling_price' => round($sellingPrice, 2),
            'cost_per_serving' => round($cost, 2),
            'expected_profit' => round($sellingPrice - $cost, 2),
            'markup_percentage' => $this->markup($sellingPrice, $cost),
            'profit_margin_percentage' => $this->profitMargin($sellingPrice, $cost),
            'raw_price' => round($rawPrice, 2),
            'rounded' => $smartRounding,
        ];
    }

    private function friendlyPrice(float $rawPrice): float
    {
        if ($rawPrice <= 0) {
            return 0;
        }

        if ($rawPrice <= 9) {
            return 9;
        }

        $candidate = ceil(($rawPrice + 1) / 10) * 10 - 1;

        return round(max($candidate, $rawPrice), 2);
    }

    private function boundedMargin(float $margin): float
    {
        return min(90, max(0, $margin));
    }

    private function profileFor(Product $product): ProductPricingProfile
    {
        return $product->pricingProfile ?: new ProductPricingProfile([
            'packaging_cost' => 0,
            'labor_allowance' => 0,
            'utility_allowance' => 0,
            'transportation_cost' => 0,
            'delivery_fees' => 0,
            'waste_percentage' => 0,
            'minimum_margin' => 25,
            'desired_margin' => 35,
            'complete_cost_per_serving' => (float) $product->cost_price,
            'previous_cost_per_serving' => (float) $product->cost_price,
            'smart_rounding' => true,
        ]);
    }
}
