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

    /**
     * Display sale details.
     */
    public function show($id)
    {
        $sale = CreditSale::with([
            'customer',
            'items',
            'allocations',
        ])->findOrFail($id);

        return view('credits.sales.show', compact('sale'));
    }

    /**
     * Store sale and optionally record payment.
     * Supports confirming a temporary sale.
     */
    public function store(Request $request)
    {
        // ==========================================
        // VALIDATION
        // ==========================================

        $data = $request->validate([
            'customer_id' => [
                'required',
                'exists:customers,id',
            ],

            'sale_date' => [
                'required',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'temporary_sale_id' => [
                'nullable',
                'exists:temporary_sales,id',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_name' => [
                'required',
                'string',
                'max:255',
            ],

            'items.*.unit' => [
                'required',
                'string',
                'max:30',
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'gt:0',
                'max:99999999',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'gte:0',
                'max:999999999',
            ],

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

        // ==========================================
        // VERIFY TEMPORARY BILL
        // ==========================================

        if (!empty($data['temporary_sale_id'])) {

            $temporarySale = \App\Models\TemporarySale::with('items')
                ->findOrFail($data['temporary_sale_id']);

            if ((int) $temporarySale->customer_id !== (int) $data['customer_id']) {
                throw ValidationException::withMessages([
                    'customer_id' => 'The selected customer does not match the temporary bill.',
                ]);
            }

            if ($temporarySale->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'The temporary bill does not contain any products.',
                ]);
            }
        }

        // ==========================================
        // CALCULATE INVOICE TOTAL
        // ==========================================

        $total = collect($data['items'])->sum(
            fn ($item) => round(
                (float) $item['quantity'] *
                (float) $item['unit_price'],
                2
            )
        );

        $total = round($total, 2);

        if ($total <= 0) {
            throw ValidationException::withMessages([
                'items' => 'Invoice total must be greater than zero.',
            ]);
        }

        // ==========================================
        // DETERMINE PAYMENT
        // ==========================================

        $paymentType = $data['payment_type'];

        $amountReceived = 0;
        $paymentMethod = null;

        // FULL CASH PAYMENT
        if ($paymentType === 'cash') {

            $amountReceived = $total;
            $paymentMethod = 'cash';
        }

        // FULL ONLINE PAYMENT
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

        // PARTIAL PAYMENT
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

        // CREDIT PAYMENT
        if ($paymentType === 'credit') {

            $amountReceived = 0;
            $paymentMethod = null;
        }

        // ==========================================
        // SAVE INVOICE AND PAYMENT
        // ==========================================

        $sale = DB::transaction(function () use (
            $data,
            $total,
            $amountReceived,
            $paymentMethod
        ) {

            // Lock customer
            $customer = Customer::whereKey($data['customer_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // ======================================
            // VERIFY AND LOCK TEMPORARY BILL
            // ======================================

            $temporarySale = null;

            if (!empty($data['temporary_sale_id'])) {

                $temporarySale = \App\Models\TemporarySale::whereKey(
                    $data['temporary_sale_id']
                )
                    ->where('customer_id', $customer->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Verify that submitted items match the temporary bill.
                $temporaryItems = $temporarySale->items()
                    ->orderBy('id')
                    ->get();

                if ($temporaryItems->isEmpty()) {
                    throw ValidationException::withMessages([
                        'items' => 'This temporary bill has no products.',
                    ]);
                }
            }

            // ======================================
            // CREATE PERMANENT INVOICE
            // ======================================

            $sale = CreditSale::create([
                'customer_id' => $customer->id,

                'invoice_no' => 'CR-' .
                    now()->format('Ymd') .
                    '-' .
                    strtoupper(Str::random(6)),

                'sale_date' => $data['sale_date'],

                'total_amount' => $total,

                'notes' => $data['notes'] ?? null,
            ]);

            // ======================================
            // SAVE INVOICE PRODUCTS
            // ======================================

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

            // ======================================
            // ALLOCATE EXISTING CUSTOMER ADVANCE
            // ======================================

            $this->allocateAvailablePayments($customer->id);

            // ======================================
            // RECORD NEW PAYMENT
            // ======================================

            if ($amountReceived > 0) {

                $payment = CreditPayment::create([
                    'customer_id' => $customer->id,

                    'receipt_no' => 'PAY-' .
                        now()->format('Ymd') .
                        '-' .
                        strtoupper(Str::random(6)),

                    'payment_date' => $data['sale_date'],

                    'amount' => $amountReceived,

                    'payment_method' => $paymentMethod,

                    'reference' => $data['payment_reference'] ?? null,

                    'notes' => $data['payment_notes'] ?? null,
                ]);

                // Calculate invoice amount already paid.
                $alreadyPaid = (float) CreditPaymentAllocation::where(
                    'credit_sale_id',
                    $sale->id
                )->sum('amount');

                $due = max(
                    0,
                    round(
                        (float) $sale->total_amount - $alreadyPaid,
                        2
                    )
                );

                $allocate = min($amountReceived, $due);

                // Allocate new payment to this invoice.
                if ($allocate > 0) {

                    CreditPaymentAllocation::create([
                        'credit_payment_id' => $payment->id,

                        'credit_sale_id' => $sale->id,

                        'amount' => $allocate,
                    ]);
                }
            }

            // ======================================
            // DELETE TEMPORARY BILL
            // ======================================

            if ($temporarySale) {
                $temporarySale->delete();
            }

            return $sale;
        });

        // ==========================================
        // SUCCESS MESSAGE
        // ==========================================

        $message = $amountReceived >= $total
            ? 'Invoice saved and fully paid.'
            : (
                $amountReceived > 0
                    ? 'Invoice saved with partial payment.'
                    : 'Credit invoice saved. No payment received.'
            );

        if (!empty($data['temporary_sale_id'])) {
            $message .= ' Temporary bill has been cleared.';
        }

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
