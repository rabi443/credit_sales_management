
@extends('layouts.credit')

@section('title', 'Payment Details')

@section('content')

@php
    $allocated = (float) $payment->allocations->sum('amount');
    $unallocated = max(0, (float) $payment->amount - $allocated);
@endphp

<div class="mx-auto max-w-4xl">

    <div class="mb-5 flex justify-between print:hidden">

        <a href="{{ route('credits.customers.show', $payment->customer_id) }}"
           class="text-sm font-medium text-blue-600 hover:underline">
            ← Back to Customer
        </a>

        <button onclick="window.print()"
                class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white">
            Print Receipt
        </button>

    </div>

    <div class="rounded-xl border bg-white p-6 shadow-sm sm:p-8">

        {{-- HEADER --}}
        <div class="mb-7 flex justify-between border-b pb-5">

            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Rabin Kirana Store
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Payment Receipt
                </p>
            </div>

            <div class="text-right">
                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                    Received
                </span>

                <p class="mt-2 font-bold">
                    {{ $payment->receipt_no ?? '#'.$payment->id }}
                </p>
            </div>

        </div>

        {{-- CUSTOMER --}}
        <div class="mb-7 grid grid-cols-2 gap-4">

            <div>
                <p class="text-xs uppercase text-slate-500">
                    Customer
                </p>

                <h2 class="mt-1 font-bold">
                    {{ $payment->customer->name ?? 'N/A' }}
                </h2>

                <p class="text-sm text-slate-500">
                    {{ $payment->customer->phone ?? '' }}
                </p>
            </div>

            <div class="text-right">
                <p class="text-xs uppercase text-slate-500">
                    Payment Date
                </p>

                <p class="mt-1 font-semibold">
                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                </p>

                <p class="mt-2 text-sm text-slate-500">
                    Method: {{ $payment->payment_method }}
                </p>
            </div>

        </div>

        {{-- PAYMENT AMOUNT --}}
        <div class="mb-7 rounded-xl bg-green-50 p-5">
            <p class="text-sm text-green-700">
                Amount Received
            </p>

            <h2 class="mt-1 text-3xl font-bold text-green-700">
                Rs. {{ number_format($payment->amount, 2) }}
            </h2>
        </div>

        {{-- ALLOCATED INVOICES --}}
        <h2 class="mb-4 text-lg font-bold text-slate-800">
            Allocated Transactions
        </h2>

        @forelse($payment->allocations as $allocation)

            @php
                $sale = $allocation->sale;

                $invoiceTotal = (float) ($sale?->total_amount ?? 0);

                $invoicePaid = (float) (
                    $sale?->allocations?->sum('amount') ?? 0
                );

                $invoiceDue = max(0, $invoiceTotal - $invoicePaid);
            @endphp

            <div class="mb-5 overflow-hidden rounded-xl border">

                <div class="flex justify-between gap-3 bg-slate-50 p-4">

                    <div>
                        <p class="font-bold">
                            {{ $sale?->invoice_no ?? 'Invoice not found' }}
                        </p>

                        @if($sale)
                            <p class="text-xs text-slate-500">
                                {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}
                            </p>
                        @endif
                    </div>

                    <div class="text-right">
                        <p class="text-xs text-slate-500">
                            This Payment
                        </p>

                        <p class="font-bold text-green-600">
                            Rs. {{ number_format($allocation->amount, 2) }}
                        </p>
                    </div>

                </div>

                @if($sale)

                    <div class="overflow-x-auto">

                        <table class="w-full text-sm">

                            <thead class="border-b bg-white text-left text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="px-4 py-3">Quantity</th>
                                    <th class="px-4 py-3">Rate</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">

                                @foreach($sale->items as $item)

                                    <tr>
                                        <td class="px-4 py-3">
                                            {{ $item->product_name }}
                                        </td>

                                        <td class="px-4 py-3">
                                            {{ $item->quantity }} {{ $item->unit }}
                                        </td>

                                        <td class="px-4 py-3">
                                            Rs. {{ number_format($item->unit_price, 2) }}
                                        </td>

                                        <td class="px-4 py-3 text-right">
                                            Rs. {{ number_format(
                                                $item->total ?? ($item->quantity * $item->unit_price),
                                                2
                                            ) }}
                                        </td>
                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                    <div class="space-y-2 border-t bg-slate-50 p-4 text-sm">

                        <div class="flex justify-between">
                            <span>Invoice Total</span>
                            <strong>Rs. {{ number_format($invoiceTotal, 2) }}</strong>
                        </div>

                        <div class="flex justify-between">
                            <span>Total Paid</span>
                            <strong class="text-green-600">
                                Rs. {{ number_format($invoicePaid, 2) }}
                            </strong>
                        </div>

                        <div class="flex justify-between">
                            <span>Remaining Due</span>
                            <strong class="{{ $invoiceDue > 0 ? 'text-red-600' : 'text-green-600' }}">
                                Rs. {{ number_format($invoiceDue, 2) }}
                            </strong>
                        </div>

                        <div class="pt-2 text-right">
                            <a href="{{ route('credits.sales.show', $sale->id) }}"
                               class="font-semibold text-blue-600 hover:underline">
                                View Invoice →
                            </a>
                        </div>

                    </div>

                @endif

            </div>

        @empty

            <div class="rounded-lg border border-dashed p-6 text-center text-sm text-slate-500">
                No invoice allocations found for this payment.
            </div>

        @endforelse

        {{-- SUMMARY --}}
        <div class="mt-7 space-y-3 border-t pt-5">

            <div class="flex justify-between">
                <span class="text-slate-600">Total Received</span>
                <strong>Rs. {{ number_format($payment->amount, 2) }}</strong>
            </div>

            <div class="flex justify-between">
                <span class="text-slate-600">Allocated Amount</span>
                <strong class="text-green-600">
                    Rs. {{ number_format($allocated, 2) }}
                </strong>
            </div>

            <div class="flex justify-between border-t pt-3">
                <strong>Unallocated / Advance</strong>
                <strong class="text-blue-600">
                    Rs. {{ number_format($unallocated, 2) }}
                </strong>
            </div>

        </div>

        {{-- NOTES --}}
        @if($payment->reference || $payment->notes)

            <div class="mt-6 rounded-lg bg-slate-50 p-4">

                @if($payment->reference)
                    <p class="text-sm">
                        <strong>Reference:</strong>
                        {{ $payment->reference }}
                    </p>
                @endif

                @if($payment->notes)
                    <p class="mt-2 text-sm">
                        <strong>Notes:</strong>
                        {{ $payment->notes }}
                    </p>
                @endif

            </div>

        @endif

        <div class="mt-8 border-t pt-5 text-center text-xs text-slate-500">
            Thank you for your payment. Generated by Rabin Credit Manager.
        </div>

    </div>

</div>

<style>
    @media print {
        @page {
            size: A4;
            margin: 12mm;
        }

        body {
            background: white !important;
            print-color-adjust: exact !important;
        }

        button, a, nav, header, footer {
            display: none !important;
        }

        .shadow-sm {
            box-shadow: none !important;
        }
    }
</style>

@endsection
