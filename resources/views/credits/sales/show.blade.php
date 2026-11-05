
@extends('layouts.credit')

@section('title', 'Invoice Details')

@section('content')

@php
    $paid = (float) $sale->allocations->sum('amount');
    $total = (float) $sale->total_amount;
    $due = max(0, $total - $paid);
@endphp

<div class="mx-auto max-w-4xl">

    <div class="mb-5 flex justify-between print:hidden">

        <a href="{{ route('credits.customers.show', $sale->customer_id) }}"
           class="text-sm font-medium text-blue-600 hover:underline">
            ← Back to Customer
        </a>

        <button onclick="window.print()"
                class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white">
            Print Invoice
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
                    Purchase Invoice
                </p>
            </div>

            <div class="text-right">
                <p class="font-bold">
                    {{ $sale->invoice_no ?? '#'.$sale->id }}
                </p>

                <p class="text-sm text-slate-500">
                    {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}
                </p>
            </div>

        </div>

        {{-- CUSTOMER --}}
        <div class="mb-7">
            <p class="text-xs uppercase text-slate-500">
                Customer
            </p>

            <h2 class="mt-1 text-lg font-bold">
                {{ $sale->customer->name ?? 'N/A' }}
            </h2>

            <p class="text-sm text-slate-500">
                {{ $sale->customer->phone ?? '' }}
            </p>
        </div>

        {{-- PRODUCTS --}}
        <div class="overflow-x-auto">

            <table class="w-full text-left text-sm">

                <thead class="border-y bg-slate-50">
                    <tr>
                        <th class="px-3 py-3">Product</th>
                        <th class="px-3 py-3">Quantity</th>
                        <th class="px-3 py-3">Rate</th>
                        <th class="px-3 py-3 text-right">Total</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @foreach($sale->items as $item)

                        <tr>
                            <td class="px-3 py-3">
                                {{ $item->product_name }}
                            </td>

                            <td class="px-3 py-3">
                                {{ $item->quantity }} {{ $item->unit }}
                            </td>

                            <td class="px-3 py-3">
                                Rs. {{ number_format($item->unit_price, 2) }}
                            </td>

                            <td class="px-3 py-3 text-right font-semibold">
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
        <div class="mt-7 ml-auto max-w-sm space-y-3 border-t pt-5">

            <div class="flex justify-between">
                <span class="text-slate-600">Invoice Total</span>
                <strong>Rs. {{ number_format($total, 2) }}</strong>
            </div>

            <div class="flex justify-between">
                <span class="text-slate-600">Paid Amount</span>
                <strong class="text-green-600">
                    Rs. {{ number_format($paid, 2) }}
                </strong>
            </div>

            <div class="flex justify-between">
                <span class="text-slate-600">Remaining Due</span>
                <strong class="{{ $due > 0 ? 'text-red-600' : 'text-green-600' }}">
                    Rs. {{ number_format($due, 2) }}
                </strong>
            </div>

            <div class="flex justify-between border-t pt-3">
                <strong>Payment Status</strong>

                @if($due <= 0)
                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                        Fully Paid
                    </span>
                @elseif($paid > 0)
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                        Partially Paid
                    </span>
                @else
                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">
                        Unpaid
                    </span>
                @endif

            </div>

        </div>

        {{-- FOOTER --}}
        <div class="mt-8 border-t pt-5 text-center text-xs text-slate-500">
            Thank you. Generated by Rabin Credit Manager.
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
