<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTable;
use App\Models\Order;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = RestaurantTable::with('activeOrder.items.product')
            ->orderBy('sort_order')
            ->orderBy('table_number')
            ->get();

        return view('tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|integer|min:1',
            'name' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        RestaurantTable::create($validated);

        return redirect()->route('tables.index')
            ->with('success', 'Table added successfully.');
    }

    public function updateStatus(Request $request, RestaurantTable $table)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,reserved,dirty',
        ]);

        $table->update($validated);

        return response()->json(['success' => true, 'status' => $table->status]);
    }

    public function destroy(RestaurantTable $table)
    {
        $table->delete();
        return redirect()->route('tables.index')
            ->with('success', 'Table removed successfully.');
    }
}
