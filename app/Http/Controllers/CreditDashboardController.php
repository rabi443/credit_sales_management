<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CreditSale;
use App\Models\CreditPayment;
use Illuminate\Http\Request;

class CreditDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get search input
        $search = trim((string) $request->query('search', ''));

        // Customer query
        $customers = Customer::query()
            ->withSum('creditSales as total_credit', 'total_amount')
            ->withSum('creditPayments as total_paid', 'amount')
            ->withCount('creditSales')

            // Search by customer name or phone
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            })

            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Overall dashboard totals (not affected by customer search)
        $totalCredit = (float) CreditSale::sum('total_amount');
        $totalPaid = (float) CreditPayment::sum('amount');

        // Overall outstanding balance
        $totalOutstanding = $totalCredit - $totalPaid;

        return view('credits.index', compact(
            'customers',
            'search',
            'totalCredit',
            'totalPaid',
            'totalOutstanding'
        ));
    }
}