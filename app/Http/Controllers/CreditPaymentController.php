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
    /**
     * Display receive payment form.
     */
    public function create(Customer $customer)
    {
        $customer->load('creditSales.allocations');

        return view('credits.payments.create', compact('customer'));
    }

    /**
     * Store payment and allocate it to unpaid invoices.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => [
                'required',
                'exists:customers,id',
            ],

            'payment_date' => [
                'required',
                'date',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
                'max:999999999',
            ],

            'payment_method' => [
                'required',
                'in:cash,esewa,khalti,bank,other',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:100',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $payment = DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Lock Customer
            |--------------------------------------------------------------------------
            */

            $customer = Customer::whereKey($data['customer_id'])
                ->lockForUpdate()
                ->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | Create Payment Record
            |--------------------------------------------------------------------------
            */

            $payment = CreditPayment::create([
                'customer_id' => $customer->id,

                'receipt_no' => 'RC-' .
                    now()->format('Ymd') .
                    '-' .
                    strtoupper(Str::random(6)),

                'payment_date' => $data['payment_date'],

                'amount' => round((float) $data['amount'], 2),

                'payment_method' => $data['payment_method'],

                'reference' => $data['reference'] ?? null,

                'notes' => $data['notes'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Available Payment Amount
            |--------------------------------------------------------------------------
            */

            $available = (float) $payment->amount;

            /*
            |--------------------------------------------------------------------------
            | Get Customer Unpaid Invoices
            |--------------------------------------------------------------------------
            | Oldest invoice gets payment first.
            |--------------------------------------------------------------------------
            */

            $sales = CreditSale::where('customer_id', $customer->id)
                ->orderBy('sale_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($sales as $sale) {

                if ($available <= 0) {
                    break;
                }

                /*
                |--------------------------------------------------------------------------
                | Calculate Already Paid Amount
                |--------------------------------------------------------------------------
                */

                $alreadyPaid = (float) CreditPaymentAllocation::where(
                    'credit_sale_id',
                    $sale->id
                )->sum('amount');

                /*
                |--------------------------------------------------------------------------
                | Calculate Remaining Due
                |--------------------------------------------------------------------------
                */

                $due = round(
                    (float) $sale->total_amount - $alreadyPaid,
                    2
                );

                if ($due <= 0) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Allocate Payment
                |--------------------------------------------------------------------------
                */

                $allocate = min($available, $due);

                CreditPaymentAllocation::create([
                    'credit_payment_id' => $payment->id,
                    'credit_sale_id' => $sale->id,
                    'amount' => $allocate,
                ]);

                $available = round($available - $allocate, 2);
            }

            /*
            |--------------------------------------------------------------------------
            | Remaining Amount
            |--------------------------------------------------------------------------
            | If all invoices are paid, remaining amount stays as customer advance.
            |--------------------------------------------------------------------------
            */

            return $payment;
        });

        return redirect()
            ->route('credits.customers.show', $payment->customer_id)
            ->with(
                'success',
                "Payment {$payment->receipt_no} recorded successfully."
            );
    }
}
