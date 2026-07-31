<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->withCount('invoices', 'payments')
            ->latest()
            ->paginate(15);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);
        AuditTrail::log('created', $customer, null, $validated);

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
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $customer->toArray();
        $customer->update($validated);
        AuditTrail::log('updated', $customer, $oldValues, $validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        AuditTrail::log('deleted', $customer, $customer->toArray());
        $customer->delete();

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
