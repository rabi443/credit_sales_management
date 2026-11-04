
@extends('layouts.customer')

@section('title', 'My Invoice')

@section('content')

@php
    // Calculate payment summary
    // $paidAmount = $sale->paymentAllocations->sum('amount');
    $paidAmount = $sale->allocations->sum('amount');

    $invoiceTotal = (float) $sale->total_amount;

    $dueAmount = max($invoiceTotal - $paidAmount, 0);

    if ($dueAmount <= 0) {
        $paymentStatus = 'Fully Paid';
        $statusColor = 'green';
    } elseif ($paidAmount > 0) {
        $paymentStatus = 'Partially Paid';
        $statusColor = 'orange';
    } else {
        $paymentStatus = 'Not Paid';
        $statusColor = 'red';
    }
@endphp

<div class="mx-auto max-w-3xl">

    {{-- TOP ACTIONS --}}
    <div class="mb-5 flex items-center justify-between print:hidden">

        <a href="{{ route('customer.dashboard') }}"
           class="text-sm font-medium text-blue-600 hover:underline">
            ← Back to Dashboard
        </a>

        <button onclick="window.print()"
                class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            Print Invoice
        </button>

    </div>


    {{-- INVOICE --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

        {{-- HEADER --}}
        <div class="mb-8 flex justify-between gap-4 border-b border-slate-200 pb-5">

            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Rabin Kirana Store
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Purchase Invoice
                </p>
            </div>

            <div class="text-right">

                <p class="font-semibold text-slate-900">
                    {{ $sale->invoice_no ?? ('#' . $sale->id) }}
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    {{ \Illuminate\Support\Carbon::parse($sale->sale_date)->format('d M Y') }}
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Invoice Date
                </p>

            </div>

        </div>


        {{-- CUSTOMER DETAILS --}}
        <div class="mb-7">

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


        {{-- PRODUCT TABLE --}}
        <div class="overflow-x-auto">

            <table class="w-full text-left text-sm">

                <thead class="border-y border-slate-200 bg-slate-50">

                    <tr>
                        <th class="px-3 py-3 font-semibold text-slate-700">
                            Product
                        </th>

                        <th class="px-3 py-3 font-semibold text-slate-700">
                            Qty
                        </th>

                        <th class="px-3 py-3 font-semibold text-slate-700">
                            Unit Price
                        </th>

                        <th class="px-3 py-3 text-right font-semibold text-slate-700">
                            Total
                        </th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @foreach($sale->items as $item)

                        <tr>

                            <td class="px-3 py-3 text-slate-800">
                                {{ $item->product_name }}
                            </td>

                            <td class="px-3 py-3 text-slate-600">
                                {{ $item->quantity }} {{ $item->unit }}
                            </td>

                            <td class="px-3 py-3 text-slate-600">
                                Rs. {{ number_format($item->unit_price, 2) }}
                            </td>

                            <td class="px-3 py-3 text-right font-medium text-slate-800">
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


        {{-- PAYMENT SUMMARY --}}
        <div class="mt-8 flex justify-end">

            <div class="w-full max-w-sm space-y-4">

                {{-- INVOICE TOTAL --}}
                <div class="flex justify-between text-sm">

                    <span class="text-slate-600">
                        Invoice Total
                    </span>

                    <span class="font-semibold text-slate-900">
                        Rs. {{ number_format($invoiceTotal, 2) }}
                    </span>

                </div>


                {{-- PAID AMOUNT --}}
                <div class="flex justify-between text-sm">

                    <span class="text-slate-600">
                        Paid Amount
                    </span>

                    <span class="font-semibold text-green-600">
                        Rs. {{ number_format($paidAmount, 2) }}
                    </span>

                </div>


                {{-- REMAINING DUE --}}
                <div class="flex justify-between text-sm">

                    <span class="text-slate-600">
                        Remaining Due
                    </span>

                    <span class="font-semibold {{ $dueAmount > 0 ? 'text-red-600' : 'text-green-600' }}">
                        Rs. {{ number_format($dueAmount, 2) }}
                    </span>

                </div>


                {{-- PAYMENT STATUS --}}
                <div class="flex items-center justify-between border-t border-slate-200 pt-4">

                    <span class="font-bold text-slate-800">
                        Payment Status
                    </span>

                    <span class="rounded-full px-3 py-1.5 text-xs font-bold

                        {{ $statusColor === 'green' ? 'bg-green-100 text-green-700' : '' }}

                        {{ $statusColor === 'orange' ? 'bg-orange-100 text-orange-700' : '' }}

                        {{ $statusColor === 'red' ? 'bg-red-100 text-red-700' : '' }}">

                        {{ $paymentStatus }}

                    </span>

                </div>

            </div>

        </div>


        {{-- PAYMENT MESSAGE --}}
        <div class="mt-6 rounded-lg p-4

            {{ $statusColor === 'green' ? 'bg-green-50 text-green-700' : '' }}

            {{ $statusColor === 'orange' ? 'bg-orange-50 text-orange-700' : '' }}

            {{ $statusColor === 'red' ? 'bg-red-50 text-red-700' : '' }}">

            @if($paymentStatus === 'Fully Paid')

                <p class="text-sm font-semibold">
                    ✓ Payment Completed
                </p>

                <p class="mt-1 text-xs">
                    Thank you! Your invoice has been fully paid.
                </p>

            @elseif($paymentStatus === 'Partially Paid')

                <p class="text-sm font-semibold">
                    Partial Payment Received
                </p>

                <p class="mt-1 text-xs">
                    You have paid Rs. {{ number_format($paidAmount, 2) }}.
                    Please pay the remaining Rs. {{ number_format($dueAmount, 2) }}.
                </p>

            @else

                <p class="text-sm font-semibold">
                    Payment Pending
                </p>

                <p class="mt-1 text-xs">
                    No payment has been received for this invoice.
                    Total outstanding amount: Rs. {{ number_format($dueAmount, 2) }}.
                </p>

            @endif

        </div>


        {{-- FOOTER --}}
        <div class="mt-8 border-t border-slate-200 pt-5 text-center">

            <p class="text-sm font-semibold text-slate-700">
                Thank You!
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Please contact the shop if you notice any discrepancy.
            </p>

            <p class="mt-3 text-xs text-slate-400">
                Generated by Rabin Credit Manager
            </p>

        </div>

    </div>

</div>


{{-- PRINT STYLES --}}
<style>

    @media print {

        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            background: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        header,
        footer,
        button,
        a,
        nav {
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
            border: none !important;
        }

        table {
            width: 100% !important;
        }

    }

</style>

@endsection
