<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CreditSale;
use App\Models\CreditPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerPortalController extends Controller
{
    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $filters['from'] ?? null;
        $to = $filters['to'] ?? null;

        $totalCredit = (float) CreditSale::where(
            'customer_id', $customer->id
        )->sum('total_amount');

        $totalPaid = (float) CreditPayment::where(
            'customer_id', $customer->id
        )->sum('amount');

        $balance = $totalCredit - $totalPaid;

        $salesQuery = CreditSale::where(
            'customer_id', $customer->id
        )->with('items');

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

        $paymentsQuery = CreditPayment::where(
            'customer_id', $customer->id
        );

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

        return view('customer.dashboard', compact(
            'customer',
            'totalCredit',
            'totalPaid',
            'balance',
            'sales',
            'payments',
            'from',
            'to'
        ));
    }

    public function invoice(CreditSale $sale)
    {
        $customer = Auth::guard('customer')->user();

        // Critical: only allow this customer to view their own invoice.
        $sale = CreditSale::where('customer_id', $customer->id)
            ->with('items')
            ->findOrFail($sale->id);

        return view('customer.invoice', compact('customer', 'sale'));
    }
}
