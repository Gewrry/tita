<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AuditTrail;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', "%{$request->search}%");
        }

        if ($request->filled('from')) {
            $query->whereDate('expense_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('expense_date', '<=', $request->to);
        }

        $expenses = $query->latest()->paginate(15);
        $categories = Expense::$categories;

        // Summary
        $totalThisMonth = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        return view('expenses.index', compact('expenses', 'categories', 'totalThisMonth'));
    }

    public function create()
    {
        $categories = Expense::$categories;
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|in:' . implode(',', array_keys(Expense::$categories)),
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $expense = Expense::create($validated);
        AuditTrail::log('created', $expense, null, $validated);

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
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|in:' . implode(',', array_keys(Expense::$categories)),
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $expense->toArray();
        $expense->update($validated);
        AuditTrail::log('updated', $expense, $oldValues, $validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        AuditTrail::log('deleted', $expense, $expense->toArray());
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
