<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CreditSale;
use App\Models\CreditPayment;
use App\Models\CreditPaymentAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreditPaymentController extends Controller
{
    public function create(Customer $customer)
    {
        $customer->load('creditSales.allocations');
        return view('credits.payments.create', compact('customer'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:999999999'],
            'payment_method' => ['required', 'in:cash,esewa,khalti,bank,other'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $payment = DB::transaction(function () use ($data) {
            $customer = Customer::whereKey($data['customer_id'])->lockForUpdate()->firstOrFail();

            $payment = CreditPayment::create([
                'customer_id' => $customer->id,
                'receipt_no' => 'RC-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'payment_date' => $data['payment_date'],
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $available = (float) $payment->amount;

            $sales = CreditSale::where('customer_id', $customer->id)
                ->with('allocations')->orderBy('sale_date')->orderBy('id')
                ->lockForUpdate()->get();

            foreach ($sales as $sale) {
                $paid = (float) $sale->allocations->sum('amount');
                $due = round((float) $sale->total_amount - $paid, 2);
                if ($due <= 0 || $available <= 0) continue;

                $allocate = min($available, $due);

                CreditPaymentAllocation::create([
                    'credit_payment_id' => $payment->id,
                    'credit_sale_id' => $sale->id,
                    'amount' => $allocate,
                ]);

                $available = round($available - $allocate, 2);
            }

            return $payment;
        });

        return redirect()->route('credits.customers.show', $payment->customer_id)
            ->with('success', "Payment {$payment->receipt_no} recorded successfully.");
    }
}
