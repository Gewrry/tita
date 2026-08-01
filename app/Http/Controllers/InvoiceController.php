<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return $this->jsonIndex($request);
        }

        $customers = Customer::orderBy('name')->get(['id', 'name', 'email', 'phone']);
        $products  = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'description', 'selling_price']);
        $invoiceNumber = Invoice::generateInvoiceNumber();

        return view('invoices.index', compact('customers', 'products', 'invoiceNumber'));
    }

    private function jsonIndex(Request $request)
    {
        $query = Invoice::with('customer')->withCount('payments');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range
        if ($request->filled('from')) {
            $query->whereDate('issue_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('issue_date', '<=', $request->to);
        }

        // Sorting
        $sortBy  = in_array($request->sort_by, ['invoice_number','issue_date','due_date','total_amount','status']) ? $request->sort_by : 'created_at';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

        $invoices = $query->paginate((int)($request->per_page ?? 15));

        return response()->json([
            'data'       => $invoices->items(),
            'pagination' => [
                'current_page' => $invoices->currentPage(),
                'last_page'    => $invoices->lastPage(),
                'total'        => $invoices->total(),
                'per_page'     => $invoices->perPage(),
                'from'         => $invoices->firstItem(),
                'to'           => $invoices->lastItem(),
            ],
        ]);
    }

    public function analytics(Request $request)
    {
        $base = Invoice::query();
        $thisMonth = Carbon::now()->startOfMonth();

        $totalInvoices  = (clone $base)->count();
        $totalRevenue   = (clone $base)->sum('total_amount');
        $paidCount      = (clone $base)->where('status', 'paid')->count();
        $unpaidCount    = (clone $base)->whereIn('status', ['unpaid', 'partial'])->count();
        $overdueCount   = (clone $base)->where('status', 'overdue')->count();
        $outstanding    = (clone $base)->whereIn('status', ['unpaid', 'partial', 'overdue'])->sum('total_amount');
        $monthlyRevenue = (clone $base)->where('status', 'paid')->whereDate('updated_at', '>=', $thisMonth)->sum('total_amount');

        return response()->json([
            'total_invoices'  => $totalInvoices,
            'total_revenue'   => (float)$totalRevenue,
            'paid_count'      => $paidCount,
            'unpaid_count'    => $unpaidCount,
            'overdue_count'   => $overdueCount,
            'outstanding'     => (float)$outstanding,
            'monthly_revenue' => (float)$monthlyRevenue,
        ]);
    }

    public function create()
    {
        $customers     = Customer::orderBy('name')->get();
        $invoiceNumber = Invoice::generateInvoiceNumber();

        return view('invoices.create', compact('customers', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'          => 'required_without:use_unknown_customer|nullable|exists:customers,id',
            'use_unknown_customer' => 'nullable|boolean',
            'invoice_number'       => [
                'required',
                Rule::unique('invoices', 'invoice_number')->where('user_id', auth()->id())
            ],
            'issue_date'           => 'required|date',
            'due_date'             => 'required|date|after_or_equal:issue_date',
            'penalty_type'         => 'in:none,flat,percentage',
            'penalty_value'        => 'nullable|numeric|min:0',
            'notes'                => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'nullable|exists:products,id',
            'items.*.description'  => 'required|string|max:255',
            'items.*.quantity'     => 'required|numeric|min:0.01',
            'items.*.price'        => 'required|numeric|min:0',
        ]);

        if ($request->boolean('use_unknown_customer')) {
            $customer   = Customer::createUnknown();
            AuditTrail::log('created', $customer, null, $customer->toArray());
            $customerId = $customer->id;
        } else {
            $customerId = $validated['customer_id'];
        }

        $invoice = Invoice::create([
            'customer_id'   => $customerId,
            'invoice_number'=> $validated['invoice_number'],
            'issue_date'    => $validated['issue_date'],
            'due_date'      => $validated['due_date'],
            'penalty_type'  => $validated['penalty_type'] ?? 'none',
            'penalty_value' => $validated['penalty_value'] ?? 0,
            'notes'         => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                'product_id'  => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'price'       => $item['price'],
                'amount'      => $item['quantity'] * $item['price'],
            ]);
        }

        $invoice->recalculateTotal();
        $invoice->updateStatus();
        AuditTrail::log('created', $invoice, null, $invoice->toArray());

        if ($request->expectsJson()) {
            $invoice->load('customer');
            return response()->json([
                'message' => 'Invoice created successfully.',
                'invoice' => $invoice,
            ], 201);
        }

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'payments']);

        if (request()->expectsJson()) {
            return response()->json(['invoice' => $invoice]);
        }

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $customers = Customer::orderBy('name')->get();

        if (request()->expectsJson()) {
            return response()->json(['invoice' => $invoice, 'customers' => $customers]);
        }

        return view('invoices.edit', compact('invoice', 'customers'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'customer_id'         => 'required|exists:customers,id',
            'invoice_number'      => [
                'required',
                Rule::unique('invoices', 'invoice_number')
                    ->where('user_id', auth()->id())
                    ->ignore($invoice->id)
            ],
            'issue_date'          => 'required|date',
            'due_date'            => 'required|date|after_or_equal:issue_date',
            'penalty_type'        => 'in:none,flat,percentage',
            'penalty_value'       => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'nullable|exists:products,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.price'       => 'required|numeric|min:0',
        ]);

        $oldValues = $invoice->toArray();

        $invoice->update([
            'customer_id'   => $validated['customer_id'],
            'issue_date'    => $validated['issue_date'],
            'due_date'      => $validated['due_date'],
            'penalty_type'  => $validated['penalty_type'] ?? 'none',
            'penalty_value' => $validated['penalty_value'] ?? 0,
            'notes'         => $validated['notes'] ?? null,
        ]);

        $invoice->items()->delete();

        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                'product_id'  => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'price'       => $item['price'],
                'amount'      => $item['quantity'] * $item['price'],
            ]);
        }

        $invoice->recalculateTotal();
        $invoice->updateStatus();
        AuditTrail::log('updated', $invoice, $oldValues, $invoice->fresh()->toArray());

        if ($request->expectsJson()) {
            $invoice->load('customer');
            return response()->json([
                'message' => 'Invoice updated successfully.',
                'invoice' => $invoice,
            ]);
        }

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        AuditTrail::log('deleted', $invoice, $invoice->toArray());
        $invoice->items()->delete();
        $invoice->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Invoice deleted successfully.']);
        }

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function markPaid(Invoice $invoice)
    {
        $oldValues       = $invoice->toArray();
        $invoice->status = 'paid';
        $invoice->save();

        AuditTrail::log('marked_paid', $invoice, $oldValues, $invoice->toArray());

        if (request()->expectsJson()) {
            $invoice->load('customer');
            return response()->json([
                'message' => 'Invoice marked as paid.',
                'invoice' => $invoice,
            ]);
        }

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice marked as paid.');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'payments']);
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));

        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }
}
