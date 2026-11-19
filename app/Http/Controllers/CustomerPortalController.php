<?php

namespace App\Http\Controllers;

use App\Models\CreditSale;
use App\Models\CreditPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

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

        // Calculate the actual account balance as of this date.
        // If no end date is selected, use today's date.
        $asOfDate = $to ?? now()->toDateString();


        /*
        |--------------------------------------------------------------------------
        | DATE-WISE CREDIT TRANSACTIONS
        |--------------------------------------------------------------------------
        */

        $salesQuery = CreditSale::where(
            'customer_id',
            $customer->id
        );

        if ($from) {
            $salesQuery->whereDate('sale_date', '>=', $from);
        }

        if ($to) {
            $salesQuery->whereDate('sale_date', '<=', $to);
        }

        // Credit transactions within the selected date range.
        $totalCredit = (float) (clone $salesQuery)
            ->sum('total_amount');

        // Transaction history.
        $sales = (clone $salesQuery)
            ->with('items')
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'sales_page')
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | DATE-WISE PAYMENT HISTORY
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

        // Payments received within the selected date range.
        $totalPaid = (float) (clone $paymentsQuery)
            ->sum('amount');

        // Payment history.
        $payments = (clone $paymentsQuery)
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'payments_page')
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | ACTUAL CUSTOMER BALANCE AS OF SELECTED END DATE
        |--------------------------------------------------------------------------
        |
        | Include ALL previous credit transactions and payments.
        | Do not use only the selected period for this calculation.
        |
        */

        $balanceCredit = (float) CreditSale::where(
            'customer_id',
            $customer->id
        )
            ->whereDate('sale_date', '<=', $asOfDate)
            ->sum('total_amount');

        $balancePaid = (float) CreditPayment::where(
            'customer_id',
            $customer->id
        )
            ->whereDate('payment_date', '<=', $asOfDate)
            ->sum('amount');

        $balance = round($balanceCredit - $balancePaid, 2);

        // Positive balance = Remaining Due.
        $due = $balance > 0 ? $balance : 0;

        // Negative balance = Advance Paid.
        $advance = $balance < 0 ? abs($balance) : 0;


        /*
        |--------------------------------------------------------------------------
        | PAYMENT IDS UP TO SELECTED END DATE
        |--------------------------------------------------------------------------
        |
        | These IDs are used to calculate how much has been allocated
        | to each invoice by the selected end date.
        |
        */

        $paymentIds = CreditPayment::where(
            'customer_id',
            $customer->id
        )
            ->whereDate('payment_date', '<=', $asOfDate)
            ->pluck('id');


        /*
        |--------------------------------------------------------------------------
        | OUTSTANDING DUE TRANSACTIONS AS OF SELECTED END DATE
        |--------------------------------------------------------------------------
        |
        | Include previous invoices too, not only invoices created
        | within the selected from/to period.
        |
        | Payments after the selected end date are not counted.
        |
        */

        $allOutstandingSales = CreditSale::where(
            'customer_id',
            $customer->id
        )
            ->whereDate('sale_date', '<=', $asOfDate)
            ->with('allocations')
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->get()
            ->map(function ($sale) use ($paymentIds) {

                // Count only payments made on or before the end date.
                $paidAmount = (float) $sale->allocations
                    ->whereIn('credit_payment_id', $paymentIds)
                    ->sum('amount');

                $remainingDue = round(
                    (float) $sale->total_amount - $paidAmount,
                    2
                );

                $sale->paid_amount_as_of = $paidAmount;
                $sale->remaining_due_as_of = max(0, $remainingDue);

                return $sale;
            })
            ->filter(function ($sale) {
                return $sale->remaining_due_as_of > 0.009;
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | TOTAL OUTSTANDING DUE
        |--------------------------------------------------------------------------
        */

        $totalOutstandingDue = (float) $allOutstandingSales
            ->sum('remaining_due_as_of');


        /*
        |--------------------------------------------------------------------------
        | OUTSTANDING DUE PAGINATION
        |--------------------------------------------------------------------------
        */

        $perPage = 10;

        $outstandingPage = LengthAwarePaginator::resolveCurrentPage(
            'outstanding_page'
        );

        $outstandingSales = new LengthAwarePaginator(
            $allOutstandingSales
                ->forPage($outstandingPage, $perPage)
                ->values(),

            $allOutstandingSales->count(),

            $perPage,

            $outstandingPage,

            [
                'path' => $request->url(),
                'pageName' => 'outstanding_page',
            ]
        );

        $outstandingSales->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | RETURN DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view('customer.dashboard', compact(
            'customer',

            // Selected-period activity totals
            'totalCredit',
            'totalPaid',

            // Actual balance as of selected end date
            'balanceCredit',
            'balancePaid',
            'balance',
            'due',
            'advance',
            'asOfDate',

            // Transaction and payment history
            'sales',
            'payments',

            // Outstanding invoices
            'outstandingSales',
            'totalOutstandingDue',

            // Date filters
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

        $sale = CreditSale::where('customer_id', $customer->id)
            ->with([
                'items',
                'allocations',
            ])
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
