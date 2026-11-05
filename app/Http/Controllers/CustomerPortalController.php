<?php

namespace App\Http\Controllers;

use App\Models\CreditSale;
use App\Models\CreditPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerPortalController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CUSTOMER DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        */

        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;


        /*
        |--------------------------------------------------------------------------
        | CREDIT SALES / TRANSACTIONS
        |--------------------------------------------------------------------------
        */

        $salesQuery = CreditSale::where(
            'customer_id',
            $customer->id
        )->with('items');

        if ($from) {
            $salesQuery->whereDate('sale_date', '>=', $from);
        }

        if ($to) {
            $salesQuery->whereDate('sale_date', '<=', $to);
        }

        // Total transaction amount for the complete filtered period.
        $totalCredit = (float) (clone $salesQuery)->sum('total_amount');

        // Paginated transaction list.
        $sales = (clone $salesQuery)
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'sales_page')
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER PAYMENT HISTORY
        |--------------------------------------------------------------------------
        */

        $paymentsQuery = CreditPayment::where(
            'customer_id',
            $customer->id
        );

        if ($from) {
            $paymentsQuery->whereDate('payment_date', '>=', $from);
        }

        if ($to) {
            $paymentsQuery->whereDate('payment_date', '<=', $to);
        }

        // Total payments for the complete filtered period.
        $totalPaid = (float) (clone $paymentsQuery)->sum('amount');

        // Paginated payment history.
        $payments = (clone $paymentsQuery)
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'payments_page')
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | FILTERED BALANCE
        |--------------------------------------------------------------------------
        */

        // Net balance movement for the selected date range.
        $balance = $totalCredit - $totalPaid;

        // Positive balance = due.
        $due = max($balance, 0);

        // Negative balance = advance.
        $advance = max(-$balance, 0);


        /*
        |--------------------------------------------------------------------------
        | RETURN CUSTOMER DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view('customer.dashboard', compact(
            'customer',
            'totalCredit',
            'totalPaid',
            'balance',
            'due',
            'advance',
            'sales',
            'payments',
            'from',
            'to'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER INVOICE DETAILS
    |--------------------------------------------------------------------------
    */

    public function invoice(CreditSale $sale)
    {
        $customer = Auth::guard('customer')->user();

        // Only allow the logged-in customer to view their own invoice.
        $sale = CreditSale::where('customer_id', $customer->id)
            ->with('items')
            ->findOrFail($sale->id);

        return view('customer.invoice', compact(
            'customer',
            'sale'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER PAYMENT DETAILS
    |--------------------------------------------------------------------------
    */

    public function payment($id)
    {
        $customer = Auth::guard('customer')->user();

        // Only allow the customer to view their own payment.
        $payment = CreditPayment::with([
            'allocations.sale.items',
            'allocations.sale.allocations',
        ])
            ->where('customer_id', $customer->id)
            ->findOrFail($id);

        return view('customer.payment-details', compact(
            'customer',
            'payment'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER PROFILE
    |--------------------------------------------------------------------------
    */

    public function profile()
    {
        $customer = Auth::guard('customer')->user();

        return view('customer.profile', compact(
            'customer'
        ));
    }
}