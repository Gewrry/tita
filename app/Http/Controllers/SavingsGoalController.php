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
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:0.01'],
            'start_date' => ['required', 'date'],
            'goal_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'color_code' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $goal = SavingsGoal::create($validated);
        AuditTrail::log('created', $goal, null, $validated);

        return redirect()->route('savings.index')->with('success', 'Savings goal created successfully!');
    }

    public function update(Request $request, SavingsGoal $savingsGoal)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:0.01'],
            'start_date' => ['required', 'date'],
            'goal_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:active,completed,archived'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldData = $savingsGoal->toArray();
        $savingsGoal->update($validated);
        AuditTrail::log('updated', $savingsGoal, $oldData, $validated);

        return redirect()->route('savings.index')->with('success', 'Savings goal updated successfully!');
    }

    public function destroy(SavingsGoal $savingsGoal)
    {
        AuditTrail::log('deleted', $savingsGoal, $savingsGoal->toArray());
        $savingsGoal->delete();

        return redirect()->route('savings.index')->with('success', 'Savings goal deleted successfully!');
    }
}
