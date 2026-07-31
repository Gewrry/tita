<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Order;
use App\Models\RestaurantTable;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (business()?->isRestaurant()) {
            return redirect()->route('profit-dashboard.index');
        }

        $today = Carbon::today();
        
        // Month filter
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);
        
        $currentMonth = Carbon::createFromDate($year, $month, 1);
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        // Today's income (always current day)
        $todayIncome = Payment::whereDate('payment_date', $today)->sum('amount');

        // Month income
        $monthIncome = Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])->sum('amount');

        // Month expenses
        $monthExpenses = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])->sum('amount');

        // Profit
        $monthProfit = $monthIncome - $monthExpenses;

        // Pending payments (unpaid + partial invoices)
        $pendingPayments = Invoice::whereIn('status', ['unpaid', 'partial'])->sum('total_amount')
            - Payment::whereHas('invoice', fn($q) => $q->whereIn('status', ['unpaid', 'partial']))->sum('amount');

        // Overdue accounts count
        $overdueCount = Invoice::where('status', 'overdue')->count();

        // Overdue amount
        $overdueAmount = Invoice::where('status', 'overdue')->get()->sum(function ($inv) {
            return $inv->balance;
        });

        // Total customers
        $totalCustomers = Customer::count();

        // Recent invoices
        $recentInvoices = Invoice::with('customer')
            ->latest()
            ->take(5)
            ->get();

        // Recent payments
        $recentPayments = Payment::with(['customer', 'invoice'])
            ->latest()
            ->take(5)
            ->get();

        // Monthly income chart (last 6 months)
        $monthlyChart = collect();
        for ($i = 5; $i >= 0; $i--) {
            $chartMonth = Carbon::now()->subMonths($i);
            $income = Payment::whereYear('payment_date', $chartMonth->year)
                ->whereMonth('payment_date', $chartMonth->month)
                ->sum('amount');
            $expenses = Expense::whereYear('expense_date', $chartMonth->year)
                ->whereMonth('expense_date', $chartMonth->month)
                ->sum('amount');
            $monthlyChart->push([
                'month' => $chartMonth->format('M Y'),
                'income' => (float) $income,
                'expenses' => (float) $expenses,
                'profit' => (float) ($income - $expenses),
            ]);
        }

        // === NEW: Business-mode-aware widgets ===
        $biz = business();
        
        // Low Stock Products
        $lowStockProducts = Product::where('track_stock', true)
            ->whereColumn('stock_quantity', '<=', 'reorder_level')
            ->orderBy('stock_quantity')
            ->take(5)
            ->get();

        // Best Sellers (top 5 by invoice items this month)
        $bestSellers = \DB::table('invoice_items')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.user_id', auth()->id())
            ->whereBetween('invoices.issue_date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('invoice_items.product_id')
            ->select('products.name', \DB::raw('SUM(invoice_items.quantity) as total_sold'), \DB::raw('SUM(invoice_items.amount) as total_revenue'))
            ->groupBy('products.name', 'invoice_items.product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Top Utang (for sari-sari)
        $topUtang = Customer::where('is_credit_allowed', true)
            ->get()
            ->sortByDesc('balance')
            ->take(5)
            ->filter(fn($c) => $c->balance > 0);

        // Restaurant-specific
        $activeTables = null;
        $kitchenQueue = 0;
        $todayOrders = 0;
        if ($biz && $biz->isRestaurant()) {
            $activeTables = RestaurantTable::withCount(['orders' => fn($q) => $q->whereNotIn('status', ['completed', 'cancelled'])])->get();
            $kitchenQueue = Order::whereIn('status', ['pending', 'preparing'])->count();
            $todayOrders = Order::whereDate('created_at', $today)->count();
        }

        return view('dashboard', compact(
            'todayIncome',
            'monthIncome',
            'monthExpenses',
            'monthProfit',
            'pendingPayments',
            'overdueCount',
            'overdueAmount',
            'totalCustomers',
            'recentInvoices',
            'recentPayments',
            'monthlyChart',
            'month',
            'year',
            'lowStockProducts',
            'bestSellers',
            'topUtang',
            'activeTables',
            'kitchenQueue',
            'todayOrders'
        ));
    }
}
