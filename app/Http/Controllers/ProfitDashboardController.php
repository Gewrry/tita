<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\FoodSale;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProfitDashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->startOfMonth();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfMonth();

        $products = Product::with('pricingProfile')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $sales = FoodSale::with('product')
            ->whereBetween('sale_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->latest('sale_date')
            ->latest()
            ->get();

        $revenue = (float) $sales->sum('total_revenue');
        $totalCost = (float) $sales->sum('total_cost');
        $grossProfit = (float) $sales->sum('gross_profit');
        $profitMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

        $insights = $this->menuEngineering($sales);
        $marginAlerts = $this->marginAlerts($products);
        $pendingPriceUpdates = $this->pendingPriceUpdates($products);

        return view('profit-dashboard.index', [
            'products' => $products,
            'recentSales' => $sales->take(12),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'revenue' => $revenue,
            'totalCost' => $totalCost,
            'grossProfit' => $grossProfit,
            'profitMargin' => $profitMargin,
            'unitsSold' => (float) $sales->sum('quantity'),
            'averageProfitPerSale' => $sales->count() > 0 ? $grossProfit / $sales->count() : 0,
            'dailyTrend' => $this->dailyTrend($sales, $startDate, $endDate),
            'menuInsights' => $insights,
            'marginAlerts' => $marginAlerts,
            'pendingPriceUpdates' => $pendingPriceUpdates,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'sale_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'channel' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::with('pricingProfile')->findOrFail($validated['product_id']);
        $quantity = round((float) $validated['quantity'], 2);
        $sellingPrice = round((float) ($validated['selling_price'] ?? $product->selling_price), 2);
        $unitCost = round((float) ($product->pricingProfile?->complete_cost_per_serving ?? $product->cost_price), 2);
        $totalRevenue = round($quantity * $sellingPrice, 2);
        $totalCost = round($quantity * $unitCost, 2);
        $grossProfit = round($totalRevenue - $totalCost, 2);

        $sale = FoodSale::create([
            'product_id' => $product->id,
            'sale_date' => $validated['sale_date'],
            'quantity' => $quantity,
            'selling_price' => $sellingPrice,
            'unit_cost' => $unitCost,
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'gross_profit' => $grossProfit,
            'channel' => $validated['channel'] ?? 'manual',
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditTrail::log('food_sale_recorded', $sale, null, $sale->toArray());

        return redirect()
            ->route('profit-dashboard.index')
            ->with('success', 'Sale recorded. Profit dashboard updated.');
    }

    public function destroy(FoodSale $sale)
    {
        AuditTrail::log('food_sale_deleted', $sale, $sale->toArray());
        $sale->delete();

        return redirect()
            ->route('profit-dashboard.index')
            ->with('success', 'Sale entry removed.');
    }

    private function menuEngineering(Collection $sales): Collection
    {
        if ($sales->isEmpty()) {
            return collect();
        }

        $grouped = $sales->groupBy('product_id')->map(function (Collection $rows) {
            $quantity = (float) $rows->sum('quantity');
            $revenue = (float) $rows->sum('total_revenue');
            $grossProfit = (float) $rows->sum('gross_profit');

            return [
                'product' => $rows->first()->product,
                'quantity' => $quantity,
                'revenue' => $revenue,
                'gross_profit' => $grossProfit,
                'contribution_margin' => $quantity > 0 ? $grossProfit / $quantity : 0,
                'profit_margin' => $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0,
            ];
        });

        $totalQuantity = max(1, (float) $grouped->sum('quantity'));
        $averageContribution = (float) $grouped->avg('contribution_margin');
        $popularityThreshold = $grouped->count() > 0 ? (100 / $grouped->count()) * 0.7 : 0;

        return $grouped->map(function (array $item) use ($totalQuantity, $averageContribution, $popularityThreshold) {
            $popularity = ($item['quantity'] / $totalQuantity) * 100;
            $highPopularity = $popularity >= $popularityThreshold;
            $highProfit = $item['contribution_margin'] >= $averageContribution;

            $item['popularity'] = $popularity;
            $item['classification'] = match (true) {
                $highPopularity && $highProfit => 'Star',
                $highPopularity && ! $highProfit => 'Price Risk',
                ! $highPopularity && $highProfit => 'Promote',
                default => 'Review',
            };
            $item['recommendation'] = match ($item['classification']) {
                'Star' => 'Keep visible and protect margin.',
                'Price Risk' => 'Popular item with weak profit. Recheck costs or raise price.',
                'Promote' => 'Good profit but low demand. Test bundles or photos.',
                default => 'Low demand and low profit. Consider repricing or removing.',
            };

            return $item;
        })->sortByDesc('gross_profit')->values();
    }

    private function marginAlerts(Collection $products): Collection
    {
        return $products->map(function (Product $product) {
            $cost = (float) ($product->pricingProfile?->complete_cost_per_serving ?? $product->cost_price);
            $price = (float) $product->selling_price;
            $minimumMargin = (float) ($product->pricingProfile?->minimum_margin ?? 25);
            $margin = $price > 0 ? (($price - $cost) / $price) * 100 : 0;

            return [
                'product' => $product,
                'cost' => $cost,
                'price' => $price,
                'margin' => $margin,
                'minimum_margin' => $minimumMargin,
                'warning' => $price <= 0
                    ? 'No active selling price'
                    : ($price < $cost ? 'Below cost' : ($margin < $minimumMargin ? 'Below minimum margin' : null)),
            ];
        })->filter(fn (array $item) => filled($item['warning']))->values();
    }

    private function pendingPriceUpdates(Collection $products): Collection
    {
        return $products->map(function (Product $product) {
            $recommendation = $product->pricingProfile?->last_recommendation;
            $recommendedPrice = $recommendation['options']['recommended']['selling_price'] ?? null;

            if (is_null($recommendedPrice)) {
                return null;
            }

            $priceDifference = round((float) $recommendedPrice - (float) $product->selling_price, 2);
            if (abs($priceDifference) < 0.01) {
                return null;
            }

            return [
                'product' => $product,
                'current_price' => (float) $product->selling_price,
                'recommended_price' => (float) $recommendedPrice,
                'price_difference' => $priceDifference,
            ];
        })->filter()->values();
    }

    private function dailyTrend(Collection $sales, Carbon $startDate, Carbon $endDate): Collection
    {
        $grouped = $sales->groupBy(fn (FoodSale $sale) => $sale->sale_date->format('Y-m-d'));
        $days = collect();
        $cursor = $startDate->copy();

        while ($cursor->lte($endDate) && $days->count() < 31) {
            $key = $cursor->format('Y-m-d');
            $rows = $grouped->get($key, collect());
            $days->push([
                'label' => $cursor->format('M d'),
                'revenue' => (float) $rows->sum('total_revenue'),
                'profit' => (float) $rows->sum('gross_profit'),
            ]);
            $cursor->addDay();
        }

        return $days;
    }
}
