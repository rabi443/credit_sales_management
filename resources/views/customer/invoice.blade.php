
@extends('layouts.customer')

@section('title', 'My Invoice')

@section('content')

<div class="mx-auto max-w-3xl">

    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('customer.dashboard') }}"
           class="text-sm font-medium text-blue-600 hover:underline">
            ← Back to Dashboard
        </a>

        <button onclick="window.print()"
                class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white">
            Print Invoice
        </button>
    </div>

    <div class="rounded-xl border bg-white p-6 shadow-sm sm:p-8">

        <div class="mb-8 flex justify-between gap-4 border-b pb-5">
            <div>
                <h1 class="text-2xl font-bold">Rabin Credit Manager</h1>
                <p class="mt-1 text-sm text-slate-500">Credit Purchase Invoice</p>
            </div>

            <div class="text-right">
                <p class="font-semibold">
                    {{ $sale->invoice_no ?? ('#' . $sale->id) }}
                </p>

                <p class="text-sm text-slate-500">
                    {{ \Illuminate\Support\Carbon::parse($sale->sale_date)->format('d M Y') }}
                </p>
            </div>
        </div>

        <div class="mb-7">
            <p class="text-xs font-semibold uppercase text-slate-500">
                Customer
            </p>

            <h2 class="mt-1 text-lg font-bold">
                {{ $customer->name }}
            </h2>

            <p class="text-sm text-slate-500">
                {{ $customer->phone }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">

                <thead class="border-y bg-slate-50">
                    <tr>
                        <th class="px-3 py-3">Product</th>
                        <th class="px-3 py-3">Qty</th>
                        <th class="px-3 py-3">Unit Price</th>
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
                                Rs. {{ number_format($item->price, 2) }}
                            </td>

                            <td class="px-3 py-3 text-right">
                                Rs. {{ number_format($item->total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <div class="w-full max-w-xs border-t pt-4">
                <div class="flex justify-between text-lg font-bold">
                    <span>Invoice Total</span>

                    <span>
                        Rs. {{ number_format($sale->total_amount, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="mt-8 border-t pt-5 text-center text-xs text-slate-500">
            Thank you. Please contact the shop if you notice any discrepancy.
        </div>

    </div>

</div>

<style>
    @media print {
        body { background: white !important; }
        header, footer, button, a { display: none !important; }
        main { max-width: 100% !important; padding: 0 !important; }
        .shadow-sm { box-shadow: none !important; }
    }
</style>

@endsection
