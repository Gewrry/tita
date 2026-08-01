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
    /**
     * Analytics summary for KPI cards.
     */
    public function analytics()
    {
        $balance = SavingsTransaction::currentBalance();
        $totalDeposits = SavingsTransaction::where('type', 'deposit')->sum('amount');
        $totalWithdrawals = SavingsTransaction::where('type', 'withdrawal')->sum('amount');
        
        $monthSavings = SavingsTransaction::whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->get()
            ->sum(fn ($transaction) => $transaction->signed_amount);

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

        return response()->json([
            'balance'           => (float) $balance,
            'total_deposits'    => (float) $totalDeposits,
            'total_withdrawals' => (float) $totalWithdrawals,
            'month_savings'     => (float) $monthSavings,
            'suggested_savings' => (float) $suggestedSavings,
            'streak'            => $streak,
        ]);
    }

    public function index(Request $request)
    {
        $query = SavingsTransaction::with('goal');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', $request->to);
        }

        // Sorting
        $sortBy  = in_array($request->sort_by, ['transaction_date', 'type', 'amount', 'created_at'])
                    ? $request->sort_by : 'transaction_date';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

        $perPage      = min((int) ($request->per_page ?? 15), 100);
        $transactions = $query->paginate($perPage);

        // Goals
        $goals = SavingsGoal::where('status', 'active')->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'data' => $transactions->map(fn($t) => $this->formatTransaction($t)),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page'    => $transactions->lastPage(),
                    'per_page'     => $transactions->perPage(),
                    'total'        => $transactions->total(),
                    'from'         => $transactions->firstItem(),
                    'to'           => $transactions->lastItem(),
                ],
                'goals' => $goals->map(fn($g) => [
                    'id'            => $g->id,
                    'name'          => $g->name,
                    'target_amount' => (float) $g->target_amount,
                    'balance'       => (float) $g->currentBalance(),
                    'progress'      => (float) $g->progressPercentage(),
                    'days_left'     => $g->days_remaining,
                    'color_code'    => $g->color_code,
                    'start_date'    => $g->start_date?->toDateString(),
                    'goal_date'     => $g->goal_date?->toDateString(),
                ]),
            ]);
        }

        return view('savings.index', compact('transactions', 'goals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'savings_goal_id'  => ['nullable', 'exists:savings_goals,id'],
            'type'             => ['required', Rule::in(['deposit', 'withdrawal'])],
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'purpose'          => ['required_if:type,withdrawal', 'nullable', 'string', 'max:255'],
            'notes'            => ['nullable', 'string'],
        ]);

        if ($validated['type'] === 'withdrawal' && (float) $validated['amount'] > SavingsTransaction::currentBalance()) {
            $msg = 'Withdrawal amount cannot be greater than the current overall savings balance.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['errors' => ['amount' => [$msg]]], 422);
            }
            throw ValidationException::withMessages(['amount' => $msg]);
        }

        if ($validated['type'] === 'deposit') {
            $validated['purpose'] = null;
        }

        $transaction = SavingsTransaction::create($validated);
        AuditTrail::log('created', $transaction, null, $validated);

        $successMsg = $transaction->type === 'deposit'
            ? 'Savings deposit recorded successfully.'
            : 'Savings withdrawal recorded successfully.';

        if ($request->wantsJson() || $request->ajax()) {
            $transaction->load('goal');
            return response()->json([
                'message'     => $successMsg,
                'transaction' => $this->formatTransaction($transaction),
            ], 201);
        }

        return redirect()->route('savings.index')->with('success', $successMsg);
    }

    public function destroy(SavingsTransaction $saving)
    {
        AuditTrail::log('deleted', $saving, $saving->toArray());
        $saving->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['message' => 'Savings transaction deleted successfully.']);
        }

        return redirect()->route('savings.index')->with('success', 'Savings transaction deleted successfully.');
    }

    /**
     * Helper: normalize a transaction into a consistent array for the API.
     */
    private function formatTransaction(SavingsTransaction $t): array
    {
        return [
            'id'               => $t->id,
            'type'             => $t->type,
            'amount'           => (float) $t->amount,
            'transaction_date' => $t->transaction_date?->toDateString(),
            'purpose'          => $t->purpose,
            'notes'            => $t->notes,
            'created_at'       => $t->created_at?->toIso8601String(),
            'goal' => $t->goal ? [
                'id'         => $t->goal->id,
                'name'       => $t->goal->name,
                'color_code' => $t->goal->color_code,
            ] : null,
        ];
    }
}
