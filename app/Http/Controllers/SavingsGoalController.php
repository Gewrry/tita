<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\SavingsGoal;
use Illuminate\Http\Request;

class SavingsGoalController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:0.01'],
            'start_date'    => ['required', 'date'],
            'goal_date'     => ['nullable', 'date', 'after_or_equal:start_date'],
            'color_code'    => ['nullable', 'string'],
            'notes'         => ['nullable', 'string'],
        ]);

        $goal = SavingsGoal::create($validated);
        AuditTrail::log('created', $goal, null, $validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Savings goal created successfully!',
                'goal'    => [
                    'id'            => $goal->id,
                    'name'          => $goal->name,
                    'target_amount' => (float) $goal->target_amount,
                    'balance'       => (float) $goal->currentBalance(),
                    'progress'      => (float) $goal->progressPercentage(),
                    'days_left'     => $goal->days_remaining,
                    'color_code'    => $goal->color_code,
                ],
            ], 201);
        }

        return redirect()->route('savings.index')->with('success', 'Savings goal created successfully!');
    }

    public function update(Request $request, SavingsGoal $savingsGoal)
    {
        // When fallback route triggers this, make sure it handles gracefully if it's a GET request masquerading
        if ($request->isMethod('get')) {
            return redirect()->route('savings.index');
        }

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:0.01'],
            'start_date'    => ['required', 'date'],
            'goal_date'     => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'        => ['required', 'in:active,completed,archived'],
            'color_code'    => ['nullable', 'string'],
            'notes'         => ['nullable', 'string'],
        ]);

        $oldData = $savingsGoal->toArray();
        $savingsGoal->update($validated);
        AuditTrail::log('updated', $savingsGoal, $oldData, $validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Savings goal updated successfully!',
                'goal'    => [
                    'id'            => $savingsGoal->id,
                    'name'          => $savingsGoal->name,
                    'target_amount' => (float) $savingsGoal->target_amount,
                    'balance'       => (float) $savingsGoal->currentBalance(),
                    'progress'      => (float) $savingsGoal->progressPercentage(),
                    'days_left'     => $savingsGoal->days_remaining,
                    'color_code'    => $savingsGoal->color_code,
                ],
            ]);
        }

        return redirect()->route('savings.index')->with('success', 'Savings goal updated successfully!');
    }

    public function destroy(Request $request, SavingsGoal $savingsGoal)
    {
        AuditTrail::log('deleted', $savingsGoal, $savingsGoal->toArray());
        $savingsGoal->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Savings goal deleted successfully!']);
        }

        return redirect()->route('savings.index')->with('success', 'Savings goal deleted successfully!');
    }
}
