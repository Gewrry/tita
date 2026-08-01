<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Product;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * Analytics summary for KPI cards.
     */
    public function analytics()
    {
        $totalExpenses  = Expense::sum('amount') ?? 0;
        $totalThisMonth = Expense::whereMonth('expense_date', now()->month)
                                 ->whereYear('expense_date', now()->year)
                                 ->sum('amount') ?? 0;
        $totalCount     = Expense::count();

        // Top Category This Month
        $topCategoryRow = Expense::select('category', DB::raw('SUM(amount) as total'))
                                 ->whereMonth('expense_date', now()->month)
                                 ->whereYear('expense_date', now()->year)
                                 ->groupBy('category')
                                 ->orderByDesc('total')
                                 ->first();
        
        $topCategory = $topCategoryRow ? Expense::$categories[$topCategoryRow->category] ?? $topCategoryRow->category : 'None';
        $topCategoryAmount = $topCategoryRow ? (float)$topCategoryRow->total : 0;

        return response()->json([
            'total_expenses'      => (float) $totalExpenses,
            'total_this_month'    => (float) $totalThisMonth,
            'total_count'         => $totalCount,
            'top_category'        => $topCategory,
            'top_category_amount' => $topCategoryAmount,
        ]);
    }

    public function index(Request $request)
    {
        $query = Expense::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', "%{$request->search}%")
                  ->orWhere('notes', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('from')) {
            $query->whereDate('expense_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('expense_date', '<=', $request->to);
        }

        // Sorting
        $sortBy  = in_array($request->sort_by, ['expense_date', 'description', 'category', 'amount', 'created_at'])
                    ? $request->sort_by : 'expense_date';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

        $perPage  = min((int) ($request->per_page ?? 15), 100);
        $expenses = $query->paginate($perPage);

        $categories = Expense::$categories;

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'data' => $expenses->map(fn($e) => $this->formatExpense($e)),
                'pagination' => [
                    'current_page' => $expenses->currentPage(),
                    'last_page'    => $expenses->lastPage(),
                    'per_page'     => $expenses->perPage(),
                    'total'        => $expenses->total(),
                    'from'         => $expenses->firstItem(),
                    'to'           => $expenses->lastItem(),
                ],
                'categories' => collect($categories)->map(fn($label, $value) => ['value' => $value, 'label' => $label])->values(),
            ]);
        }

        // Summary for Blade view if needed
        $totalThisMonth = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'cost_price']);

        return view('expenses.index', compact('expenses', 'categories', 'totalThisMonth', 'products'));
    }

    public function create()
    {
        $categories = Expense::$categories;
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'   => 'nullable|exists:products,id',
            'description'  => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'category'     => 'required|in:' . implode(',', array_keys(Expense::$categories)),
            'expense_date' => 'required|date',
            'notes'        => 'nullable|string',
        ]);

        $expense = Expense::create($validated);
        AuditTrail::log('created', $expense, null, $validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Expense recorded successfully.',
                'expense' => $this->formatExpense($expense),
            ], 201);
        }

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        $categories = Expense::$categories;
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'product_id'   => 'nullable|exists:products,id',
            'description'  => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'category'     => 'required|in:' . implode(',', array_keys(Expense::$categories)),
            'expense_date' => 'required|date',
            'notes'        => 'nullable|string',
        ]);

        $oldValues = $expense->toArray();
        $expense->update($validated);
        AuditTrail::log('updated', $expense, $oldValues, $validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Expense updated successfully.',
                'expense' => $this->formatExpense($expense),
            ]);
        }

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        AuditTrail::log('deleted', $expense, $expense->toArray());
        $expense->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['message' => 'Expense deleted successfully.']);
        }

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    /**
     * Helper: normalize an expense into a consistent array for the API.
     */
    private function formatExpense(Expense $e): array
    {
        return [
            'id'           => $e->id,
            'product_id'   => $e->product_id,
            'description'  => $e->description,
            'amount'       => (float) $e->amount,
            'category'     => $e->category,
            'category_name'=> Expense::$categories[$e->category] ?? ucfirst($e->category),
            'expense_date' => $e->expense_date?->toDateString(),
            'notes'        => $e->notes,
            'created_at'   => $e->created_at?->toIso8601String(),
        ];
    }
}
