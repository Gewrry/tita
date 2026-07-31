<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\AuditTrail;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function income(Request $request)
    {
        $from = $request->get('from', Carbon::now()->startOfMonth()->toDateString());
        $to = $request->get('to', Carbon::now()->toDateString());

        $payments = Payment::with(['customer', 'invoice'])
            ->whereBetween('payment_date', [$from, $to])
            ->orderBy('payment_date', 'desc')
            ->get();

        $totalIncome = $payments->sum('amount');

        // Group by payment method
        $byMethod = $payments->groupBy('payment_method')->map(fn($group) => $group->sum('amount'));

        // Group by day
        $dailyIncome = $payments->groupBy(fn($p) => $p->payment_date->format('Y-m-d'))
            ->map(fn($group) => $group->sum('amount'));

        return view('reports.income', compact('payments', 'totalIncome', 'byMethod', 'dailyIncome', 'from', 'to'));
    }

    public function expenses(Request $request)
    {
        $from = $request->get('from', Carbon::now()->startOfMonth()->toDateString());
        $to = $request->get('to', Carbon::now()->toDateString());

        $expenses = Expense::whereBetween('expense_date', [$from, $to])
            ->orderBy('expense_date', 'desc')
            ->get();

        $totalExpenses = $expenses->sum('amount');

        // Group by category
        $byCategory = $expenses->groupBy('category')->map(fn($group) => $group->sum('amount'));

        return view('reports.expenses', compact('expenses', 'totalExpenses', 'byCategory', 'from', 'to'));
    }

    public function profit(Request $request)
    {
        $from = $request->get('from', Carbon::now()->startOfMonth()->toDateString());
        $to = $request->get('to', Carbon::now()->toDateString());

        $totalIncome = Payment::whereBetween('payment_date', [$from, $to])->sum('amount');
        $totalExpenses = Expense::whereBetween('expense_date', [$from, $to])->sum('amount');
        $profit = $totalIncome - $totalExpenses;

        // Monthly breakdown
        $monthlyData = collect();
        $start = Carbon::parse($from)->startOfMonth();
        $end = Carbon::parse($to)->endOfMonth();

        while ($start <= $end) {
            $mIncome = Payment::whereYear('payment_date', $start->year)
                ->whereMonth('payment_date', $start->month)
                ->sum('amount');
            $mExpense = Expense::whereYear('expense_date', $start->year)
                ->whereMonth('expense_date', $start->month)
                ->sum('amount');

            $monthlyData->push([
                'month' => $start->format('M Y'),
                'income' => (float) $mIncome,
                'expenses' => (float) $mExpense,
                'profit' => (float) ($mIncome - $mExpense),
            ]);

            $start->addMonth();
        }

        return view('reports.profit', compact('totalIncome', 'totalExpenses', 'profit', 'monthlyData', 'from', 'to'));
    }

    public function outstanding()
    {
        $invoices = Invoice::with('customer')
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->orderBy('due_date')
            ->get();

        $totalOutstanding = $invoices->sum(fn($inv) => $inv->balance);

        return view('reports.outstanding', compact('invoices', 'totalOutstanding'));
    }

    public function customers(Request $request)
    {
        $from = $request->get('from', Carbon::now()->startOfYear()->toDateString());
        $to = $request->get('to', Carbon::now()->toDateString());

        $customers = Customer::whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ])
            ->orderBy('created_at')
            ->get();

        $weekdayOrder = collect(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
        $byWeekday = $weekdayOrder->mapWithKeys(function ($day) use ($customers) {
            return [$day => $customers->filter(fn ($customer) => $customer->created_at->format('l') === $day)->count()];
        });

        $byMonth = $customers
            ->groupBy(fn ($customer) => $customer->created_at->format('Y-m'))
            ->map(fn ($group) => $group->count());

        $byYear = $customers
            ->groupBy(fn ($customer) => $customer->created_at->format('Y'))
            ->map(fn ($group) => $group->count());

        $totalCustomers = $customers->count();
        $bestDay = $totalCustomers > 0 ? $byWeekday->sortDesc()->keys()->first() : null;
        $bestMonth = $byMonth->sortDesc()->keys()->first();
        $bestYear = $byYear->sortDesc()->keys()->first();

        return view('reports.customers', compact(
            'from',
            'to',
            'byWeekday',
            'byMonth',
            'byYear',
            'bestDay',
            'bestMonth',
            'bestYear',
            'totalCustomers'
        ));
    }

    public function auditTrail(Request $request)
    {
        $query = AuditTrail::with('user')->latest();

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        $trails = $query->paginate(20);

        return view('reports.audit', compact('trails'));
    }

    public function inventory(Request $request)
    {
        $query = \App\Models\Product::with('category');
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->orderBy('name')->get();
        $totalValue = $products->sum(fn($p) => $p->stock_quantity * $p->cost_price);
        $totalRetailValue = $products->sum(fn($p) => $p->stock_quantity * $p->selling_price);
        $lowStockCount = $products->filter(fn($p) => $p->isLowStock())->count();
        $outOfStockCount = $products->filter(fn($p) => $p->isOutOfStock())->count();
        $categories = \App\Models\Category::orderBy('sort_order')->get();

        return view('reports.inventory', compact('products', 'totalValue', 'totalRetailValue', 'lowStockCount', 'outOfStockCount', 'categories'));
    }

    public function bestSellers(Request $request)
    {
        $from = $request->get('from', Carbon::now()->startOfMonth()->toDateString());
        $to = $request->get('to', Carbon::now()->toDateString());

        $sellers = \DB::table('invoice_items')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.user_id', auth()->id())
            ->whereBetween('invoices.issue_date', [$from, $to])
            ->whereNotNull('invoice_items.product_id')
            ->select('products.name', 'products.selling_price', 'products.cost_price',
                \DB::raw('SUM(invoice_items.quantity) as total_sold'),
                \DB::raw('SUM(invoice_items.amount) as total_revenue'))
            ->groupBy('products.name', 'products.selling_price', 'products.cost_price', 'invoice_items.product_id')
            ->orderByDesc('total_sold')
            ->get();

        return view('reports.best-sellers', compact('sellers', 'from', 'to'));
    }
}
