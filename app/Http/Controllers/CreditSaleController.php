<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CreditSale;
use App\Models\CreditSaleItem;
use App\Models\CreditPayment;
use App\Models\CreditPaymentAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreditSaleController extends Controller
{
    public function create()
    {
        $customers = \App\Models\Customer::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('credits.sales.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'sale_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.unit' => ['required', 'string', 'max:30'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'items.*.unit_price' => ['required', 'numeric', 'gte:0', 'max:999999999'],
        ]);

        $sale = DB::transaction(function () use ($data) {
            $customer = Customer::whereKey($data['customer_id'])->lockForUpdate()->firstOrFail();

            $total = collect($data['items'])->sum(
                fn ($item) => round((float) $item['quantity'] * (float) $item['unit_price'], 2)
            );

            $sale = CreditSale::create([
                'customer_id' => $customer->id,
                'invoice_no' => 'CR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'sale_date' => $data['sale_date'],
                'total_amount' => $total,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];

                CreditSaleItem::create([
                    'credit_sale_id' => $sale->id,
                    'product_name' => $item['product_name'],
                    'unit' => $item['unit'],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'line_total' => round($qty * $price, 2),
                ]);
            }

            // Apply any existing customer advance to the oldest unpaid invoices.
            $this->allocateAvailablePayments($customer->id);

            return $sale;
        });

        return redirect()->route('credits.customers.show', $sale->customer_id)
            ->with('success', "Credit invoice {$sale->invoice_no} saved.");
    }

    private function allocateAvailablePayments(int $customerId): void
    {
        $payments = CreditPayment::where('customer_id', $customerId)
            ->orderBy('payment_date')->orderBy('id')
            ->lockForUpdate()->get();

        $sales = CreditSale::where('customer_id', $customerId)
            ->orderBy('sale_date')->orderBy('id')
            ->lockForUpdate()->get();

        foreach ($payments as $payment) {
            // Query the database each time so allocations created earlier in this
            // transaction are included when processing the next payment.
            $alreadyAllocated = (float) CreditPaymentAllocation::where(
                'credit_payment_id', $payment->id
            )->sum('amount');
            $available = round((float) $payment->amount - $alreadyAllocated, 2);
            if ($available <= 0) continue;

            foreach ($sales as $sale) {
                $alreadyPaid = (float) CreditPaymentAllocation::where(
                    'credit_sale_id', $sale->id
                )->sum('amount');
                $due = round((float) $sale->total_amount - $alreadyPaid, 2);
                if ($due <= 0) continue;

                $allocate = min($available, $due);
                if ($allocate <= 0) continue;

                CreditPaymentAllocation::create([
                    'credit_payment_id' => $payment->id,
                    'credit_sale_id' => $sale->id,
                    'amount' => $allocate,
                ]);

                $available = round($available - $allocate, 2);
                if ($available <= 0) break;
            }
        }
    }
}
