<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\SavingsTransaction;
use App\Models\SavingsGoal;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SavingsController extends Controller
{
    public function index(Request $request)
    {
        $query = SavingsTransaction::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', $request->to);
        }

        $transactions = $query->with('goal')->latest('transaction_date')->latest()->paginate(15)->withQueryString();
        $balance = SavingsTransaction::currentBalance();
        $totalDeposits = SavingsTransaction::where('type', 'deposit')->sum('amount');
        $totalWithdrawals = SavingsTransaction::where('type', 'withdrawal')->sum('amount');
        $monthSavings = SavingsTransaction::whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->get()
            ->sum(fn ($transaction) => $transaction->signed_amount);

        // Goals
        $goals = SavingsGoal::where('status', 'active')->get();

        // Suggested Savings (10% of today's revenue)
        $todayRevenue = Invoice::whereDate('issue_date', now()->toDateString())->sum('total_amount');
        $suggestedSavings = (float) $todayRevenue * 0.10;

        // Savings Streak
        $streak = 0;
        $checkDate = now()->toDateString();
        while (SavingsTransaction::where('type', 'deposit')->whereDate('transaction_date', $checkDate)->exists()) {
            $streak++;
            $checkDate = date('Y-m-d', strtotime($checkDate . ' -1 day'));
        }

        return view('savings.index', compact(
            'transactions',
            'balance',
            'totalDeposits',
            'totalWithdrawals',
            'monthSavings',
            'goals',
            'suggestedSavings',
            'streak'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'savings_goal_id' => ['nullable', 'exists:savings_goals,id'],
            'type' => ['required', Rule::in(['deposit', 'withdrawal'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'purpose' => ['required_if:type,withdrawal', 'nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['type'] === 'withdrawal' && (float) $validated['amount'] > SavingsTransaction::currentBalance()) {
            throw ValidationException::withMessages([
                'amount' => 'Withdrawal amount cannot be greater than the current savings balance.',
            ]);
        }

        if ($validated['type'] === 'deposit') {
            $validated['purpose'] = null;
        }

        $transaction = SavingsTransaction::create($validated);
        AuditTrail::log('created', $transaction, null, $validated);

        return redirect()->route('savings.index')
            ->with('success', $transaction->type === 'deposit'
                ? 'Savings deposit recorded successfully.'
                : 'Savings withdrawal recorded successfully.');
    }

    public function destroy(SavingsTransaction $saving)
    {
        AuditTrail::log('deleted', $saving, $saving->toArray());
        $saving->delete();

        return redirect()->route('savings.index')
            ->with('success', 'Savings transaction deleted successfully.');
    }
}
