<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CreditSale;
use App\Models\CreditPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    // ==========================================
    // CREATE CUSTOMER
    // ==========================================

    public function create()
    {
        return view('credits.customers.create');
    }


    // ==========================================
    // STORE CUSTOMER
    // ==========================================

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')
                ->store('customers', 'public');
        }

        $customer = Customer::create($data + [
            'is_active' => true,
        ]);

        return redirect()
            ->route('credits.customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }


    // ==========================================
    // SHOW CUSTOMER LEDGER
    // ==========================================

    public function show(Request $request, Customer $customer)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;


        // CREDIT INVOICES

        $salesQuery = CreditSale::where('customer_id', $customer->id)
            ->with(['items', 'allocations']);

        if ($from) {
            $salesQuery->whereDate('sale_date', '>=', $from);
        }

        if ($to) {
            $salesQuery->whereDate('sale_date', '<=', $to);
        }

        $sales = $salesQuery
            ->orderByDesc('sale_date')
            ->paginate(10, ['*'], 'sales_page')
            ->withQueryString();


        // PAYMENT HISTORY

        $paymentsQuery = CreditPayment::where('customer_id', $customer->id)
            ->with('allocations');

        if ($from) {
            $paymentsQuery->whereDate('payment_date', '>=', $from);
        }

        if ($to) {
            $paymentsQuery->whereDate('payment_date', '<=', $to);
        }

        $payments = $paymentsQuery
            ->orderByDesc('payment_date')
            ->paginate(10, ['*'], 'payments_page')
            ->withQueryString();


        return view('credits.customers.show', compact(
            'customer',
            'sales',
            'payments',
            'from',
            'to'
        ));
    }


    // ==========================================
    // EDIT CUSTOMER
    // ==========================================

    public function edit(Customer $customer)
    {
        return view('credits.customers.edit', compact('customer'));
    }


    // ==========================================
    // UPDATE CUSTOMER
    // ==========================================

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Upload new photo
        if ($request->hasFile('photo')) {

            // Delete previous photo
            if ($customer->photo) {
                Storage::disk('public')->delete($customer->photo);
            }

            $data['photo'] = $request->file('photo')
                ->store('customers', 'public');
        }

        // Update customer details
        $customer->update($data);

        return redirect()
            ->route('credits.customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }


    // ==========================================
    // DELETE CUSTOMER
    // ==========================================

    public function destroy(Customer $customer)
    {
        // Do not delete customers with credit sales or payments
        $hasSales = CreditSale::where('customer_id', $customer->id)
            ->exists();

        $hasPayments = CreditPayment::where('customer_id', $customer->id)
            ->exists();

        if ($hasSales || $hasPayments) {
            return redirect()
                ->route('credits.index')
                ->with(
                    'error',
                    'Cannot delete this customer because credit invoices or payment records exist.'
                );
        }

        // Delete customer photo
        if ($customer->photo) {
            Storage::disk('public')->delete($customer->photo);
        }

        // Delete customer
        $customer->delete();

        return redirect()
            ->route('credits.index')
            ->with('success', 'Customer deleted successfully.');
    }
}