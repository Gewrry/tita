<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\AuditTrail;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Analytics summary for KPI cards.
     */
    public function analytics()
    {
        $totalCollected   = Payment::sum('amount') ?? 0;
        $totalThisMonth   = Payment::whereMonth('payment_date', now()->month)
                                   ->whereYear('payment_date', now()->year)
                                   ->sum('amount') ?? 0;
        $totalPayments    = Payment::count();
        $totalOutstanding = Invoice::whereIn('status', ['unpaid', 'partial', 'overdue'])
                                   ->get()
                                   ->sum(fn($inv) => max(0, $inv->balance));

        return response()->json([
            'total_collected'   => $totalCollected,
            'total_this_month'  => $totalThisMonth,
            'total_payments'    => $totalPayments,
            'total_outstanding' => $totalOutstanding,
        ]);
    }

    /**
     * Return paginated list of payments (JSON or view).
     */
    public function index(Request $request)
    {
        $query = Payment::with(['customer', 'invoice']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('invoice',  fn($q2) => $q2->where('invoice_number', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        if ($request->filled('from')) {
            $query->whereDate('payment_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('payment_date', '<=', $request->to);
        }

        // Sorting
        $sortBy  = in_array($request->sort_by, ['payment_date', 'amount', 'payment_method', 'created_at'])
                    ? $request->sort_by : 'payment_date';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

        $perPage  = min((int) ($request->per_page ?? 15), 100);
        $payments = $query->paginate($perPage);

        // Invoices for the "Record Payment" modal dropdown
        $invoices = Invoice::with('customer')
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->orderBy('invoice_number')
            ->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'data' => $payments->map(fn($p) => $this->formatPayment($p)),
                'pagination' => [
                    'current_page' => $payments->currentPage(),
                    'last_page'    => $payments->lastPage(),
                    'per_page'     => $payments->perPage(),
                    'total'        => $payments->total(),
                    'from'         => $payments->firstItem(),
                    'to'           => $payments->lastItem(),
                ],
                'invoices' => $invoices->map(fn($i) => [
                    'id'              => $i->id,
                    'invoice_number'  => $i->invoice_number,
                    'customer_name'   => $i->customer?->name ?? 'Unknown',
                    'customer_id'     => $i->customer_id,
                    'balance'         => (float) max(0, $i->balance),
                    'total_amount'    => (float) ($i->total_amount + $i->penalty_amount),
                ]),
            ]);
        }

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

    /**
     * Record a new payment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id'       => 'required|exists:invoices,id',
            'amount'           => 'required|numeric|min:0.01',
            'payment_method'   => 'required|in:cash,bank_transfer,gcash,credit_card,check,store_credit',
            'reference_number' => 'nullable|string|max:255',
            'payment_date'     => 'required|date',
            'notes'            => 'nullable|string',
        ]);

        $invoice    = Invoice::findOrFail($validated['invoice_id']);
        $maxPayable = $invoice->balance;

        if ($validated['amount'] > $maxPayable + 0.01) {
            $msg = 'Amount exceeds remaining balance of ₱' . number_format($maxPayable, 2);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['errors' => ['amount' => [$msg]]], 422);
            }
            return back()->withErrors(['amount' => $msg])->withInput();
        }

        $payment = Payment::create([
            'invoice_id'       => $validated['invoice_id'],
            'customer_id'      => $invoice->customer_id,
            'amount'           => $validated['amount'],
            'payment_method'   => $validated['payment_method'],
            'reference_number' => $validated['reference_number'],
            'payment_date'     => $validated['payment_date'],
            'notes'            => $validated['notes'] ?? null,
        ]);

        AuditTrail::log('created', $payment, null, $payment->toArray());

        if ($request->wantsJson() || $request->ajax()) {
            $payment->load(['customer', 'invoice']);
            return response()->json([
                'message' => 'Payment recorded successfully.',
                'payment' => $this->formatPayment($payment),
            ], 201);
        }

        return redirect()->route('payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Show single payment (JSON or view).
     */
    public function show(Payment $payment)
    {
        $payment->load(['customer', 'invoice']);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['payment' => $this->formatPayment($payment)]);
        }

        return view('payments.show', compact('payment'));
    }

    /**
     * Delete a payment.
     */
    public function destroy(Payment $payment)
    {
        AuditTrail::log('deleted', $payment, $payment->toArray());
        $payment->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['message' => 'Payment deleted successfully.']);
        }

        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    /**
     * Helper: normalize a payment into a consistent array for the API.
     */
    private function formatPayment(Payment $p): array
    {
        return [
            'id'               => $p->id,
            'amount'           => (float) $p->amount,
            'payment_method'   => $p->payment_method,
            'reference_number' => $p->reference_number,
            'payment_date'     => $p->payment_date?->toDateString(),
            'notes'            => $p->notes,
            'created_at'       => $p->created_at?->toIso8601String(),
            'customer' => $p->customer ? [
                'id'    => $p->customer->id,
                'name'  => $p->customer->name,
                'email' => $p->customer->email,
            ] : null,
            'invoice' => $p->invoice ? [
                'id'             => $p->invoice->id,
                'invoice_number' => $p->invoice->invoice_number,
                'total_amount'   => (float) ($p->invoice->total_amount + $p->invoice->penalty_amount),
                'balance'        => (float) max(0, $p->invoice->balance),
                'status'         => $p->invoice->status,
            ] : null,
        ];
    }
}
