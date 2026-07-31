<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('customer');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('issue_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('issue_date', '<=', $request->to);
        }

        $invoices = $query->latest()->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $invoiceNumber = Invoice::generateInvoiceNumber();

        return view('invoices.create', compact('customers', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required_without:use_unknown_customer|nullable|exists:customers,id',
            'use_unknown_customer' => 'nullable|boolean',
            'invoice_number' => [
                'required',
                Rule::unique('invoices', 'invoice_number')->where('user_id', auth()->id())
            ],
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'penalty_type' => 'in:none,flat,percentage',
            'penalty_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        if ($request->boolean('use_unknown_customer')) {
            $customer = Customer::createUnknown();
            AuditTrail::log('created', $customer, null, $customer->toArray());
            $customerId = $customer->id;
        } else {
            $customerId = $validated['customer_id'];
        }

        $invoice = Invoice::create([
            'customer_id' => $customerId,
            'invoice_number' => $validated['invoice_number'],
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'penalty_type' => $validated['penalty_type'] ?? 'none',
            'penalty_value' => $validated['penalty_value'] ?? 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'amount' => $item['quantity'] * $item['price'],
            ]);
        }

        $invoice->recalculateTotal();
        AuditTrail::log('created', $invoice, null, $invoice->toArray());

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'payments']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $customers = Customer::orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'customers'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_number' => [
                'required',
                Rule::unique('invoices', 'invoice_number')
                    ->where('user_id', auth()->id())
                    ->ignore($invoice->id)
            ],
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'penalty_type' => 'in:none,flat,percentage',
            'penalty_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $oldValues = $invoice->toArray();

        $invoice->update([
            'customer_id' => $validated['customer_id'],
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'penalty_type' => $validated['penalty_type'] ?? 'none',
            'penalty_value' => $validated['penalty_value'] ?? 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Delete old items and create new ones
        $invoice->items()->delete();

        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'amount' => $item['quantity'] * $item['price'],
            ]);
        }

        $invoice->recalculateTotal();
        $invoice->updateStatus();
        AuditTrail::log('updated', $invoice, $oldValues, $invoice->fresh()->toArray());

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        AuditTrail::log('deleted', $invoice, $invoice->toArray());
        $invoice->items()->delete();
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'payments']);
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));
        
        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }
}
