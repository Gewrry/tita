<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('activeProducts')->orderBy('sort_order')->get();
        $products = Product::where('is_active', true)->with('category')->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('pos.index', compact('categories', 'products', 'customers'));
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,gcash,bank_transfer',
            'amount_tendered' => 'nullable|numeric|min:0',
            'is_credit' => 'nullable|boolean',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // Resolve customer
            $customerId = $validated['customer_id'];
            if (!$customerId) {
                $customer = Customer::createUnknown();
                $customerId = $customer->id;
            } else {
                $customer = Customer::find($customerId);
            }

            $isCredit = $request->boolean('is_credit');
            $discount = $validated['discount'] ?? 0;

            // Calculate total
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }
            $total = $subtotal - $discount;

            // Check credit if utang
            if ($isCredit && !$customer->canCredit($total)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer has exceeded credit limit. Current balance: ₱' . number_format($customer->balance, 2) . 
                                 ($customer->credit_limit ? ', Limit: ₱' . number_format($customer->credit_limit, 2) : ''),
                ], 422);
            }

            // Create invoice
            $invoice = Invoice::create([
                'customer_id' => $customerId,
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'issue_date' => now()->toDateString(),
                'due_date' => $isCredit ? now()->addDays(30)->toDateString() : now()->toDateString(),
                'total_amount' => $total,
                'notes' => $validated['notes'] ?? 'POS Transaction',
                'status' => $isCredit ? 'unpaid' : 'paid',
            ]);

            // Create invoice items and deduct stock
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);

                $invoice->items()->create([
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'amount' => $item['quantity'] * $item['price'],
                ]);

                // Deduct stock
                if ($product->track_stock) {
                    $product->adjustStock(
                        -$item['quantity'],
                        'sale',
                        Invoice::class,
                        $invoice->id,
                        "POS Sale - {$invoice->invoice_number}"
                    );
                }
            }

            // Record payment if not credit
            if (!$isCredit) {
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'customer_id' => $customerId,
                    'amount' => $total,
                    'payment_method' => $validated['payment_method'],
                    'payment_date' => now()->toDateString(),
                    'notes' => 'POS Payment',
                ]);
            }

            AuditTrail::log('created', $invoice, null, $invoice->toArray());

            $change = 0;
            if (!$isCredit && isset($validated['amount_tendered'])) {
                $change = max(0, $validated['amount_tendered'] - $total);
            }

            return response()->json([
                'success' => true,
                'invoice' => $invoice->load('items', 'customer'),
                'change' => $change,
                'message' => $isCredit 
                    ? "Utang recorded for {$customer->name}. Balance: ₱" . number_format($customer->balance + $total, 2)
                    : "Sale completed! Invoice #{$invoice->invoice_number}",
            ]);
        });
    }

    /**
     * Quick search products by name/barcode for POS
     */
    public function searchProducts(Request $request)
    {
        $search = $request->get('q', '');

        $products = Product::where('is_active', true)
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', $search)
                  ->orWhere('sku', 'like', "%{$search}%");
            })
            ->with('category')
            ->limit(20)
            ->get();

        return response()->json($products);
    }
}
