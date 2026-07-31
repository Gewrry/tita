<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['table', 'customer', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereNotIn('status', ['completed', 'cancelled']);
        }

        $orders = $query->latest()->paginate(20);
        return view('orders.index', compact('orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'nullable|exists:restaurant_tables,id',
            'order_type' => 'required|in:dine_in,takeout,delivery',
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.special_instructions' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            $order = Order::create([
                'table_id' => $validated['table_id'] ?? null,
                'order_number' => Order::generateOrderNumber(),
                'order_type' => $validated['order_type'],
                'customer_id' => $validated['customer_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->selling_price,
                    'amount' => $item['quantity'] * $product->selling_price,
                    'special_instructions' => $item['special_instructions'] ?? null,
                ]);
            }

            $order->recalculateTotal();

            // Mark table as occupied
            if ($order->table_id) {
                RestaurantTable::where('id', $order->table_id)->update(['status' => 'occupied']);
            }

            return response()->json([
                'success' => true,
                'order' => $order->load('items.product', 'table'),
                'message' => "Order {$order->order_number} created!",
            ]);
        });
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,completed,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        // Handle table status changes
        if (in_array($validated['status'], ['completed', 'cancelled']) && $order->table_id) {
            RestaurantTable::where('id', $order->table_id)->update(['status' => 'dirty']);
        }

        return response()->json(['success' => true, 'status' => $order->status]);
    }

    /**
     * Complete order: create invoice + payment, deduct stock, free table
     */
    public function complete(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,gcash,bank_transfer',
            'amount_tendered' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($order, $validated) {
            $discount = $validated['discount'] ?? 0;
            $total = $order->total_amount - $discount;

            // Resolve customer
            $customerId = $order->customer_id;
            if (!$customerId) {
                $customer = Customer::createUnknown();
                $customerId = $customer->id;
            }

            // Create invoice
            $invoice = Invoice::create([
                'customer_id' => $customerId,
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'issue_date' => now()->toDateString(),
                'due_date' => now()->toDateString(),
                'total_amount' => $total,
                'notes' => "Order #{$order->order_number}",
                'status' => 'paid',
            ]);

            // Create invoice items from order items
            foreach ($order->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'description' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                    'amount' => $item->amount,
                ]);

                // Deduct stock
                if ($item->product->track_stock) {
                    $item->product->adjustStock(
                        -$item->quantity,
                        'sale',
                        Invoice::class,
                        $invoice->id,
                        "Order {$order->order_number}"
                    );
                }
            }

            // Record payment
            Payment::create([
                'invoice_id' => $invoice->id,
                'customer_id' => $customerId,
                'amount' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_date' => now()->toDateString(),
                'notes' => "Order #{$order->order_number}",
            ]);

            // Update order
            $order->update([
                'status' => 'completed',
                'invoice_id' => $invoice->id,
            ]);

            // Free up table
            if ($order->table_id) {
                RestaurantTable::where('id', $order->table_id)->update(['status' => 'available']);
            }

            AuditTrail::log('created', $invoice, null, $invoice->toArray());

            $change = 0;
            if (isset($validated['amount_tendered'])) {
                $change = max(0, $validated['amount_tendered'] - $total);
            }

            return response()->json([
                'success' => true,
                'invoice' => $invoice->load('items', 'customer'),
                'change' => $change,
                'message' => "Order completed! Invoice #{$invoice->invoice_number}",
            ]);
        });
    }
}
