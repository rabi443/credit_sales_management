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
use Illuminate\Validation\ValidationException;

class CreditSaleController extends Controller
{
    public function create()
    {
        $customers = Customer::where('is_active', true)
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

            'payment_type' => [
                'required',
                'in:cash,online,credit,partial',
            ],

            'amount_received' => [
                'nullable',
                'numeric',
                'gte:0',
            ],

            'payment_method' => [
                'nullable',
                'in:cash,esewa,khalti,bank,other',
            ],

            'payment_reference' => [
                'nullable',
                'string',
                'max:100',
            ],

            'payment_notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $total = collect($data['items'])->sum(
            fn ($item) => round(
                (float) $item['quantity'] * (float) $item['unit_price'],
                2
            )
        );

        $total = round($total, 2);

        // Determine how much money was received.
        $paymentType = $data['payment_type'];

        $amountReceived = 0;
        $paymentMethod = null;

        if ($paymentType === 'cash') {
            $amountReceived = $total;
            $paymentMethod = 'cash';
        }

        if ($paymentType === 'online') {
            if (
                empty($data['payment_method']) ||
                !in_array($data['payment_method'], [
                    'esewa',
                    'khalti',
                    'bank',
                    'other',
                ])
            ) {
                throw ValidationException::withMessages([
                    'payment_method' => 'Please select an online payment method.',
                ]);
            }

            $amountReceived = $total;
            $paymentMethod = $data['payment_method'];
        }

        if ($paymentType === 'partial') {
            $amountReceived = round(
                (float) ($data['amount_received'] ?? 0),
                2
            );

            if ($amountReceived <= 0) {
                throw ValidationException::withMessages([
                    'amount_received' => 'Please enter the amount received.',
                ]);
            }

            if ($amountReceived > $total) {
                throw ValidationException::withMessages([
                    'amount_received' => 'Received amount cannot exceed the invoice total.',
                ]);
            }

            if (empty($data['payment_method'])) {
                throw ValidationException::withMessages([
                    'payment_method' => 'Please select how the partial payment was received.',
                ]);
            }

            $paymentMethod = $data['payment_method'];
        }

        $sale = DB::transaction(function () use (
            $data,
            $total,
            $amountReceived,
            $paymentMethod
        ) {
            $customer = Customer::whereKey($data['customer_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Create invoice.
            $sale = CreditSale::create([
                'customer_id' => $customer->id,
                'invoice_no' => 'CR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'sale_date' => $data['sale_date'],
                'total_amount' => $total,
                'notes' => $data['notes'] ?? null,
            ]);

            // Save invoice products.
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

            // First allocate any existing customer advance.
            $this->allocateAvailablePayments($customer->id);

            // Record payment only when money was actually received.
            if ($amountReceived > 0) {
                $payment = CreditPayment::create([
                    'customer_id' => $customer->id,
                    'receipt_no' => 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'payment_date' => $data['sale_date'],
                    'amount' => $amountReceived,
                    'payment_method' => $paymentMethod,
                    'reference' => $data['payment_reference'] ?? null,
                    'notes' => $data['payment_notes'] ?? null,
                ]);

                // Allocate this new payment directly to this invoice.
                $alreadyPaid = (float) CreditPaymentAllocation::where(
                    'credit_sale_id',
                    $sale->id
                )->sum('amount');

                $due = max(
                    0,
                    round((float) $sale->total_amount - $alreadyPaid, 2)
                );

                $allocate = min($amountReceived, $due);

                if ($allocate > 0) {
                    CreditPaymentAllocation::create([
                        'credit_payment_id' => $payment->id,
                        'credit_sale_id' => $sale->id,
                        'amount' => $allocate,
                    ]);
                }
            }

            return $sale;
        });

        $message = $amountReceived >= $total
            ? 'Invoice saved and fully paid.'
            : ($amountReceived > 0
                ? 'Invoice saved with partial payment.'
                : 'Credit invoice saved. No payment received.');

        return redirect()
            ->route('credits.customers.show', $sale->customer_id)
            ->with('success', $message);
    }

    /**
     * Allocate available customer payments to oldest unpaid invoices.
     */
    private function allocateAvailablePayments(int $customerId): void
    {
        $payments = CreditPayment::where('customer_id', $customerId)
            ->orderBy('payment_date')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $sales = CreditSale::where('customer_id', $customerId)
            ->orderBy('sale_date')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($payments as $payment) {

            $alreadyAllocated = (float) CreditPaymentAllocation::where(
                'credit_payment_id',
                $payment->id
            )->sum('amount');

            $available = round(
                (float) $payment->amount - $alreadyAllocated,
                2
            );

            if ($available <= 0) {
                continue;
            }

            foreach ($sales as $sale) {

                $alreadyPaid = (float) CreditPaymentAllocation::where(
                    'credit_sale_id',
                    $sale->id
                )->sum('amount');

                $due = round(
                    (float) $sale->total_amount - $alreadyPaid,
                    2
                );

                if ($due <= 0) {
                    continue;
                }

                $allocate = min($available, $due);

                if ($allocate <= 0) {
                    continue;
                }

                CreditPaymentAllocation::create([
                    'credit_payment_id' => $payment->id,
                    'credit_sale_id' => $sale->id,
                    'amount' => $allocate,
                ]);

                $available = round($available - $allocate, 2);

                if ($available <= 0) {
                    break;
                }
            }
        }
    }
}
