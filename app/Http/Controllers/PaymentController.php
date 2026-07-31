<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\AuditTrail;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['customer', 'invoice']);

        if ($request->filled('method')) {
            $query->where('payment_method', $request->input('method'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('invoice', fn($q2) => $q2->where('invoice_number', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('payment_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('payment_date', '<=', $request->to);
        }

        $payments = $query->latest()->paginate(15);
        $invoices = Invoice::with('customer')
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->get();

        return view('payments.index', compact('payments', 'invoices'));
    }

    public function create(Request $request)
    {
        $invoices = Invoice::with('customer')
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->get();

        $selectedInvoice = null;
        if ($request->filled('invoice_id')) {
            $selectedInvoice = Invoice::with('customer')->find($request->invoice_id);
        }

        return view('payments.create', compact('invoices', 'selectedInvoice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,gcash',
            'reference_number' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        // Don't let them overpay
        $maxPayable = $invoice->balance;
        if ($validated['amount'] > $maxPayable) {
            return back()->withErrors(['amount' => "Amount exceeds remaining balance of ₱" . number_format($maxPayable, 2)])
                ->withInput();
        }

        $payment = Payment::create([
            'invoice_id' => $validated['invoice_id'],
            'customer_id' => $invoice->customer_id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'],
            'payment_date' => $validated['payment_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditTrail::log('created', $payment, null, $payment->toArray());

        return redirect()->route('payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['customer', 'invoice']);

        return view('payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        AuditTrail::log('deleted', $payment, $payment->toArray());
        $invoice = $payment->invoice;
        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
}
