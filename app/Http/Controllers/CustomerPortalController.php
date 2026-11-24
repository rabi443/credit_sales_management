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
            'to'   => ['nullable', 'date', 'after_or_equal:from'],
        ]);
    
        $from = $filters['from'] ?? null;
        $to   = $filters['to'] ?? null;
    
        /*
        |--------------------------------------------------------------------------
        | SALES QUERY
        |--------------------------------------------------------------------------
        */
    
        $salesQuery = CreditSale::where('customer_id', $customer->id)
            ->when($from, function ($query) use ($from) {
                $query->whereDate('sale_date', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->whereDate('sale_date', '<=', $to);
            });
    
        // Get ALL filtered sales with items and allocations.
        $allSales = (clone $salesQuery)
            ->with(['items', 'allocations'])
            ->get();
    
        // Total credit transactions.
        $totalCredit = (float) $allSales->sum('total_amount');
    
        // Total amount allocated to filtered invoices.
        $totalInvoicePaid = (float) $allSales->sum(function ($sale) {
            return (float) $sale->allocations->sum('amount');
        });
    
        // Total due based on filtered invoices.
        $totalDue = (float) $allSales->sum(function ($sale) {
    
            $paid = (float) $sale->allocations->sum('amount');
    
            return max(
                0,
                (float) $sale->total_amount - $paid
            );
        });
    
        // Paginated transaction history.
        $sales = (clone $salesQuery)
            ->with('items')
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'sales_page')
            ->withQueryString();
    
    
        /*
        |--------------------------------------------------------------------------
        | PAYMENTS QUERY
        |--------------------------------------------------------------------------
        */
    
        $paymentsQuery = CreditPayment::where(
            'customer_id',
            $customer->id
        )
            ->when($from, function ($query) use ($from) {
                $query->whereDate('payment_date', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->whereDate('payment_date', '<=', $to);
            });
    
        // Get ALL filtered payments with allocations.
        $allPayments = (clone $paymentsQuery)
            ->with('allocations')
            ->get();
    
        // Total payments received.
        $totalPaid = (float) $allPayments->sum('amount');
    
        // Total advance from filtered payments.
        $totalAdvance = (float) $allPayments->sum(function ($payment) {
    
            $allocated = (float) $payment->allocations->sum('amount');
    
            return max(
                0,
                (float) $payment->amount - $allocated
            );
        });
    
        // Paginated payment history.
        $payments = (clone $paymentsQuery)
            ->with('allocations')
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'payments_page')
            ->withQueryString();
    
    
        /*
        |--------------------------------------------------------------------------
        | OUTSTANDING DUE TRANSACTIONS
        |--------------------------------------------------------------------------
        |
        | Same calculation as the admin show() method.
        |
        | 1. Use only invoices within the selected date range.
        | 2. Calculate paid amount from invoice allocations.
        | 3. Calculate remaining due for each invoice.
        | 4. Keep only invoices with a remaining balance.
        |
        */
    
        $allOutstandingSales = $allSales
            ->map(function ($sale) {
    
                // Total amount allocated to this invoice.
                $paidAmount = (float) $sale->allocations
                    ->sum('amount');
    
                // Remaining invoice balance.
                $remainingDue = max(
                    0,
                    round(
                        (float) $sale->total_amount - $paidAmount,
                        2
                    )
                );
    
                // Store calculated values for Blade.
                $sale->paid_amount_as_of = $paidAmount;
    
                $sale->remaining_due_as_of = $remainingDue;
    
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
        | NET BALANCE
        |--------------------------------------------------------------------------
        |
        | Difference between credit transactions and payments
        | within the selected date range.
        |
        */
    
        // $balance = round($totalCredit - $totalPaid, 2);
    
        // $due = $balance > 0 ? $balance : 0;
    
        // $advance = $balance < 0 ? abs($balance) : 0;
        
        $balance = round($totalOutstandingDue - $totalAdvance, 2);

        $due = max(0, $balance);

        $advance = max(0, -$balance);
    
    
        /*
        |--------------------------------------------------------------------------
        | RETURN CUSTOMER DASHBOARD
        |--------------------------------------------------------------------------
        */
    
        return view('customer.dashboard', compact(
    
            'customer',
    
            // Selected-period transaction totals
            'totalCredit',
            'totalInvoicePaid',
            'totalDue',
    
            // Selected-period payment totals
            'totalPaid',
            'totalAdvance',
    
            // Net balance
            'balance',
            'due',
            'advance',
    
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
