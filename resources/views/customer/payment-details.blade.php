
@extends('layouts.customer')

@section('title', 'Payment Details')

@section('content')

@php
    $totalAllocated = (float) $payment->allocations->sum('amount');

    $unallocated = max(
        (float) $payment->amount - $totalAllocated,
        0
    );
@endphp

<div class="mx-auto max-w-4xl">

    {{-- TOP ACTIONS --}}
    <div class="mb-5 flex items-center justify-between print:hidden">

        <a href="{{ route('customer.dashboard') }}"
           class="text-sm font-medium text-blue-600 hover:underline">
            ← Back to Dashboard
        </a>

        <button onclick="window.print()"
                class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            Print Receipt
        </button>

    </div>


    {{-- RECEIPT --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

        {{-- HEADER --}}
        <div class="mb-7 flex items-start justify-between gap-4 border-b border-slate-200 pb-5">

            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Rabin Kirana Store
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Customer Payment Receipt
                </p>
            </div>

            <div class="text-right">
                <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                    Payment Received
                </span>

                <p class="mt-2 text-sm font-semibold text-slate-800">
                    Payment #{{ $payment->id }}
                </p>
            </div>

        </div>


        {{-- CUSTOMER DETAILS --}}
        <div class="mb-7 grid grid-cols-1 gap-5 sm:grid-cols-2">

            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                    Customer
                </p>

                <h2 class="mt-1 text-lg font-bold text-slate-900">
                    {{ $customer->name }}
                </h2>

                <p class="text-sm text-slate-500">
                    {{ $customer->phone }}
                </p>
            </div>

            <div class="sm:text-right">

                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                    Payment Date
                </p>

                <p class="mt-1 font-semibold text-slate-800">
                    {{ \Illuminate\Support\Carbon::parse($payment->payment_date)->format('d M Y') }}
                </p>

                <p class="mt-2 text-sm text-slate-500">
                    Method:
                    <span class="font-semibold text-slate-700">
                        {{ $payment->payment_method ?? 'Cash' }}
                    </span>
                </p>

            </div>

        </div>


        {{-- PAYMENT AMOUNT --}}
        <div class="mb-8 rounded-xl border border-green-200 bg-green-50 p-5">

            <p class="text-sm font-medium text-green-700">
                Total Amount Paid
            </p>

            <h2 class="mt-2 text-3xl font-bold text-green-700">
                Rs. {{ number_format($payment->amount, 2) }}
            </h2>

        </div>


        {{-- ALLOCATED TRANSACTIONS --}}
        <div class="mb-5">

            <h2 class="text-lg font-bold text-slate-800">
                Payment Transactions
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                These are the invoices and products against which this payment was recorded.
            </p>

        </div>


        @forelse($payment->allocations as $index => $allocation)

            @php
                $sale = $allocation->sale;

                $invoiceTotal = (float) ($sale?->total_amount ?? 0);

                $invoicePaid = (float) (
                    $sale?->allocations?->sum('amount') ?? 0
                );

                $invoiceDue = max(
                    $invoiceTotal - $invoicePaid,
                    0
                );

                $thisPayment = (float) $allocation->amount;
            @endphp

            <div class="mb-5 overflow-hidden rounded-xl border border-slate-200">

                {{-- INVOICE HEADER --}}
                <div class="flex flex-col justify-between gap-3 bg-slate-50 px-4 py-4 sm:flex-row sm:items-center">

                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">
                            Transaction {{ $index + 1 }}
                        </p>

                        <h3 class="mt-1 font-bold text-slate-800">
                            {{ $sale?->invoice_no ?? ('Invoice #' . ($sale?->id ?? 'N/A')) }}
                        </h3>

                        @if($sale)
                            <p class="mt-1 text-xs text-slate-500">
                                {{ \Illuminate\Support\Carbon::parse($sale->sale_date)->format('d M Y') }}
                            </p>
                        @endif
                    </div>

                    <div class="sm:text-right">
                        <p class="text-xs text-slate-500">
                            Amount from this payment
                        </p>

                        <p class="text-lg font-bold text-green-600">
                            Rs. {{ number_format($thisPayment, 2) }}
                        </p>
                    </div>

                </div>


                {{-- PRODUCTS --}}
                @if($sale)

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[450px] text-left text-sm">

                            <thead class="border-b bg-white text-slate-500">

                                <tr>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="px-4 py-3">Quantity</th>
                                    <th class="px-4 py-3">Rate</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                </tr>

                            </thead>

                            <tbody class="divide-y divide-slate-100">

                                @forelse($sale->items as $item)

                                    <tr>

                                        <td class="px-4 py-3 font-medium text-slate-800">
                                            {{ $item->product_name }}
                                        </td>

                                        <td class="px-4 py-3 text-slate-600">
                                            {{ $item->quantity }} {{ $item->unit }}
                                        </td>

                                        <td class="px-4 py-3 text-slate-600">
                                            Rs. {{ number_format($item->unit_price, 2) }}
                                        </td>

                                        <td class="px-4 py-3 text-right font-medium text-slate-800">
                                            Rs. {{ number_format(
                                                $item->total ?? ($item->quantity * $item->unit_price),
                                                2
                                            ) }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="4"
                                            class="px-4 py-5 text-center text-slate-500">
                                            No products found for this invoice.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>


                    {{-- INVOICE PAYMENT SUMMARY --}}
                    <div class="space-y-3 border-t border-slate-200 bg-slate-50 p-4">

                        <div class="flex justify-between text-sm">

                            <span class="text-slate-600">
                                Invoice Total
                            </span>

                            <span class="font-semibold text-slate-800">
                                Rs. {{ number_format($invoiceTotal, 2) }}
                            </span>

                        </div>

                        <div class="flex justify-between text-sm">

                            <span class="text-slate-600">
                                Total Paid (All Payments)
                            </span>

                            <span class="font-semibold text-green-600">
                                Rs. {{ number_format($invoicePaid, 2) }}
                            </span>

                        </div>

                        <div class="flex justify-between text-sm">

                            <span class="text-slate-600">
                                Remaining Due
                            </span>

                            <span class="font-bold {{ $invoiceDue > 0 ? 'text-red-600' : 'text-green-600' }}">
                                Rs. {{ number_format($invoiceDue, 2) }}
                            </span>

                        </div>

                        <div class="flex justify-end pt-2">

                            <a href="{{ route('customer.invoices.show', $sale) }}"
                               class="text-sm font-semibold text-blue-600 hover:underline">
                                View Full Invoice →
                            </a>

                        </div>

                    </div>

                @else

                    <div class="p-5 text-sm text-red-600">
                        The invoice associated with this payment could not be found.
                    </div>

                @endif

            </div>

        @empty

            <div class="rounded-xl border border-dashed border-slate-300 p-8 text-center">

                <div class="mb-2 text-3xl">📄</div>

                <p class="font-semibold text-slate-700">
                    No invoice allocation found.
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    Please contact the shop for payment allocation details.
                </p>

            </div>

        @endforelse


        {{-- PAYMENT TOTAL SUMMARY --}}
        <div class="mt-7 rounded-xl border border-slate-200 p-5">

            <h3 class="mb-4 font-bold text-slate-800">
                Payment Summary
            </h3>

            <div class="space-y-3">

                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">
                        Total Paid Amount
                    </span>

                    <span class="font-semibold text-slate-800">
                        Rs. {{ number_format($payment->amount, 2) }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">
                        Allocated to Invoices
                    </span>

                    <span class="font-semibold text-green-600">
                        Rs. {{ number_format($totalAllocated, 2) }}
                    </span>
                </div>

                <div class="flex justify-between border-t pt-3 text-sm">
                    <span class="font-semibold text-slate-700">
                        Unallocated Amount
                    </span>

                    <span class="font-bold text-blue-600">
                        Rs. {{ number_format($unallocated, 2) }}
                    </span>
                </div>

            </div>

        </div>


        {{-- FOOTER --}}
        <div class="mt-8 border-t border-slate-200 pt-5 text-center">

            <p class="font-semibold text-slate-700">
                Thank You!
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Thank you for your payment. Please contact the shop if you notice any discrepancy.
            </p>

            <p class="mt-3 text-xs text-slate-400">
                Generated by Rabin Credit Manager
            </p>

        </div>

    </div>

</div>


{{-- PRINT CSS --}}
<style>
    @media print {

        @page {
            size: A4;
            margin: 12mm;
        }

        body {
            background: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        header,
        footer,
        nav,
        button,
        a,
        .print\:hidden {
            display: none !important;
        }

        main {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .shadow-sm {
            box-shadow: none !important;
        }

        .rounded-xl {
            break-inside: avoid;
        }

        table {
            width: 100% !important;
        }

    }
</style>

@endsection
