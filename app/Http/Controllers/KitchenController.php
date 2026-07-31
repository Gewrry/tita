<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items.product', 'table'])
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->orderBy('created_at')
            ->get();

        return view('kitchen.index', compact('orders'));
    }

    /**
     * Update individual item status
     */
    public function updateItemStatus(Request $request, OrderItem $orderItem)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,served',
        ]);

        $orderItem->update(['status' => $validated['status']]);

        // Auto-update order status based on items
        $order = $orderItem->order;
        $allReady = $order->items->every(fn($i) => in_array($i->status, ['ready', 'served']));
        $anyPreparing = $order->items->contains(fn($i) => $i->status === 'preparing');

        if ($allReady) {
            $order->update(['status' => 'ready']);
        } elseif ($anyPreparing) {
            $order->update(['status' => 'preparing']);
        }

        return response()->json([
            'success' => true,
            'item_status' => $orderItem->status,
            'order_status' => $order->fresh()->status,
        ]);
    }

    /**
     * Mark entire order as ready
     */
    public function markOrderReady(Order $order)
    {
        $order->items()->update(['status' => 'ready']);
        $order->update(['status' => 'ready']);

        return response()->json(['success' => true, 'message' => "Order #{$order->order_number} is ready!"]);
    }

    /**
     * API endpoint for polling updates
     */
    public function poll()
    {
        $orders = Order::with(['items.product', 'table'])
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->orderBy('created_at')
            ->get();

        return response()->json($orders);
    }
}
