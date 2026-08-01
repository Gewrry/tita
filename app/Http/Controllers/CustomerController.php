<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\AuditTrail;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerController extends Controller
{
    /**
     * Return real-time analytics for the KPI cards.
     */
    public function analytics()
    {
        $total      = Customer::count();
        $newThisMonth = Customer::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count();

        $totalRevenue    = Invoice::sum('total_amount') ?? 0;
        $totalPaid       = Payment::sum('amount') ?? 0;
        $outstanding     = max(0, $totalRevenue - $totalPaid);

        return response()->json([
            'total'         => $total,
            'new_this_month' => $newThisMonth,
            'total_revenue'  => $totalRevenue,
            'outstanding'    => $outstanding,
        ]);
    }

    /**
     * Display the customer list (JSON for AJAX, view for browser).
     */
    public function index(Request $request)
    {
        $query = Customer::query()
            ->withCount('invoices')
            ->withSum('invoices as total_billed', 'total_amount')
            ->withSum('payments as total_paid_sum', 'amount')
            ->withMax('invoices as last_invoice_date', 'created_at');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy  = in_array($request->sort_by, ['name', 'created_at', 'total_billed', 'last_invoice_date'])
                    ? $request->sort_by : 'created_at';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'total_billed') {
            $query->orderBy('total_billed', $sortDir);
        } elseif ($sortBy === 'last_invoice_date') {
            $query->orderBy('last_invoice_date', $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $perPage   = min((int) ($request->per_page ?? 15), 100);
        $customers = $query->paginate($perPage);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'data' => $customers->map(function ($c) {
                    return [
                        'id'                => $c->id,
                        'name'              => $c->name,
                        'email'             => $c->email,
                        'phone'             => $c->phone,
                        'address'           => $c->address,
                        'notes'             => $c->notes,
                        'credit_limit'      => $c->credit_limit,
                        'is_credit_allowed' => $c->is_credit_allowed,
                        'invoices_count'    => $c->invoices_count,
                        'total_billed'      => (float) ($c->total_billed ?? 0),
                        'total_paid_sum'    => (float) ($c->total_paid_sum ?? 0),
                        'outstanding'       => max(0, (float) ($c->total_billed ?? 0) - (float) ($c->total_paid_sum ?? 0)),
                        'last_invoice_date' => $c->last_invoice_date,
                        'created_at'        => $c->created_at?->toIso8601String(),
                    ];
                }),
                'pagination' => [
                    'current_page' => $customers->currentPage(),
                    'last_page'    => $customers->lastPage(),
                    'per_page'     => $customers->perPage(),
                    'total'        => $customers->total(),
                    'from'         => $customers->firstItem(),
                    'to'           => $customers->lastItem(),
                ],
            ]);
        }

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:50',
            'address'          => 'nullable|string',
            'notes'            => 'nullable|string',
            'credit_limit'     => 'nullable|numeric|min:0',
            'is_credit_allowed'=> 'nullable|boolean',
        ]);

        $validated['is_credit_allowed'] = $request->boolean('is_credit_allowed');

        $customer = Customer::create($validated);
        AuditTrail::log('created', $customer, null, $validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message'  => 'Customer created successfully.',
                'customer' => [
                    'id'                => $customer->id,
                    'name'              => $customer->name,
                    'email'             => $customer->email,
                    'phone'             => $customer->phone,
                    'address'           => $customer->address,
                    'notes'             => $customer->notes,
                    'credit_limit'      => $customer->credit_limit,
                    'is_credit_allowed' => $customer->is_credit_allowed,
                    'invoices_count'    => 0,
                    'total_billed'      => 0,
                    'total_paid_sum'    => 0,
                    'outstanding'       => 0,
                    'last_invoice_date' => null,
                    'created_at'        => $customer->created_at?->toIso8601String(),
                ],
            ], 201);
        }

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['invoices.payments', 'payments']);
        $invoices = $customer->invoices()->latest()->paginate(10);

        return view('customers.show', compact('customer', 'invoices'));
    }

    public function edit(Customer $customer)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['customer' => $customer]);
        }

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:50',
            'address'          => 'nullable|string',
            'notes'            => 'nullable|string',
            'credit_limit'     => 'nullable|numeric|min:0',
            'is_credit_allowed'=> 'nullable|boolean',
        ]);

        $validated['is_credit_allowed'] = $request->boolean('is_credit_allowed');

        $oldValues = $customer->toArray();
        $customer->update($validated);
        AuditTrail::log('updated', $customer, $oldValues, $validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message'  => 'Customer updated successfully.',
                'customer' => $customer->fresh(),
            ]);
        }

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        AuditTrail::log('deleted', $customer, $customer->toArray());
        $customer->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['message' => 'Customer deleted successfully.']);
        }

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    public function soa(Customer $customer)
    {
        $customer->load(['invoices.payments', 'payments']);
        $invoices = $customer->invoices()->with('payments')->latest()->get();

        return view('customers.soa', compact('customer', 'invoices'));
    }

    public function downloadSoaPdf(Customer $customer)
    {
        $customer->load(['invoices.payments', 'payments']);
        $invoices = $customer->invoices()->with('payments')->latest()->get();

        $pdf = Pdf::loadView('pdf.soa', compact('customer', 'invoices'));

        return $pdf->download("SOA-{$customer->name}.pdf");
    }
}
