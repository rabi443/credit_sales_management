@extends('layouts.customer')

@section('title', 'My Credit Dashboard')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">
        Welcome, {{ $customer->name }} 👋
    </h1>

    <p class="mt-1 text-sm text-slate-500">
        Here you can track your credit purchases and payments.
    </p>
</div>


{{-- =========================
    SUMMARY CARDS
========================= --}}

<div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">

    {{-- TOTAL CREDIT --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">
            Total Credit
        </p>

        <h2 class="mt-2 text-2xl font-bold text-blue-700">
            Rs. {{ number_format($totalCredit, 2) }}
        </h2>
    </div>


    {{-- TOTAL PAID --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">
            Total Paid
        </p>

        <h2 class="mt-2 text-2xl font-bold text-green-700">
            Rs. {{ number_format($totalPaid, 2) }}
        </h2>
    </div>


    {{-- REMAINING / ADVANCE BALANCE --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

        <p class="text-sm text-slate-500">
            {{ $balance < 0 ? 'Advance Balance' : 'Remaining Balance' }}
        </p>

        <h2 class="mt-2 text-2xl font-bold text-orange-600">
            Rs. {{ number_format(abs($balance), 2) }}
        </h2>

        @if($balance < 0)
            <p class="mt-1 text-xs text-slate-500">
                You have paid more than your recorded credit.
            </p>
        @endif

    </div>

</div>


{{-- =========================
    DATE FILTER
========================= --}}

<div class="mb-8 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

    <h2 class="mb-4 text-lg font-bold text-slate-800">
        Filter Transactions
    </h2>

    <form
        method="GET"
        action="{{ route('customer.dashboard') }}"
        class="grid grid-cols-1 items-end gap-4 sm:grid-cols-3"
    >

        {{-- FROM DATE --}}
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
                From Date
            </label>

            <input
                type="date"
                name="from"
                value="{{ $from }}"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >
        </div>


        {{-- TO DATE --}}
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
                To Date
            </label>

            <input
                type="date"
                name="to"
                value="{{ $to }}"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >
        </div>


        {{-- BUTTONS --}}
        <div class="flex gap-2">

            <button
                type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white transition hover:bg-blue-700"
            >
                Apply
            </button>

            <a
                href="{{ route('customer.dashboard') }}"
                class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 transition hover:bg-slate-50"
            >
                Clear
            </a>

        </div>

    </form>

    <p class="mt-3 text-xs text-slate-500">
        The date filter applies to the transaction lists.
        Summary cards show your overall totals.
    </p>

</div>



{{-- =========================
    CREDIT PURCHASES
========================= --}}

<div class="mb-8 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

    {{-- HEADER --}}
    <div class="flex flex-col justify-between gap-2 border-b px-5 py-4 sm:flex-row sm:items-center">

        <div>
            <h2 class="text-lg font-bold text-slate-800">
                My Credit Purchases
            </h2>

            <p class="text-sm text-slate-500">
                Your credit sale invoices.
            </p>
        </div>

        <div class="text-xs text-slate-500">
            {{ $sales->total() }} invoices
        </div>

    </div>


    {{-- SCROLLABLE TABLE --}}
    <div class="max-h-80 overflow-x-auto overflow-y-auto">

        <table class="w-full min-w-[600px] text-left text-sm">

            {{-- STICKY TABLE HEADER --}}
            <thead class="sticky top-0 z-10 bg-slate-50 text-slate-600 shadow-sm">

                <tr>
                    <th class="px-5 py-3 font-semibold">
                        Invoice
                    </th>

                    <th class="px-5 py-3 font-semibold">
                        Date
                    </th>

                    <th class="px-5 py-3 font-semibold">
                        Amount
                    </th>

                    <th class="px-5 py-3 font-semibold">
                        Action
                    </th>
                </tr>

            </thead>


            {{-- TABLE BODY --}}
            <tbody class="divide-y divide-slate-100">

                @forelse($sales as $sale)

                    <tr class="transition hover:bg-slate-50">

                        {{-- INVOICE NUMBER --}}
                        <td class="px-5 py-4 font-medium text-slate-800">
                            {{ $sale->invoice_no ?? ('#' . $sale->id) }}
                        </td>


                        {{-- DATE --}}
                        <td class="px-5 py-4 whitespace-nowrap text-slate-600">
                            {{ \Illuminate\Support\Carbon::parse($sale->sale_date)->format('d M Y') }}
                        </td>


                        {{-- AMOUNT --}}
                        <td class="px-5 py-4 whitespace-nowrap font-semibold text-slate-800">
                            Rs. {{ number_format($sale->total_amount, 2) }}
                        </td>


                        {{-- ACTION --}}
                        <td class="px-5 py-4">

                            <a
                                href="{{ route('customer.invoices.show', $sale) }}"
                                class="inline-flex items-center gap-1 font-semibold text-blue-600 hover:text-blue-800 hover:underline"
                            >
                                View Invoice
                                <span>→</span>
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td
                            colspan="4"
                            class="px-5 py-10 text-center text-slate-500"
                        >
                            <div class="mb-2 text-3xl">📄</div>

                            <p class="font-medium">
                                No credit purchases found.
                            </p>

                            <p class="mt-1 text-xs">
                                Your credit invoices will appear here.
                            </p>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION OUTSIDE SCROLL AREA --}}
    <div class="border-t border-slate-200 px-5 py-3">

        {{ $sales->links() }}

    </div>

</div>



{{-- =========================
    PAYMENT HISTORY
========================= --}}

<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

    {{-- HEADER --}}
    <div class="flex flex-col justify-between gap-2 border-b px-5 py-4 sm:flex-row sm:items-center">

        <div>
            <h2 class="text-lg font-bold text-slate-800">
                My Payment History
            </h2>

            <p class="text-sm text-slate-500">
                Payments recorded by the shop.
            </p>
        </div>

        <div class="text-xs text-slate-500">
            {{ $payments->total() }} payments
        </div>

    </div>


    {{-- SCROLLABLE TABLE --}}
    <div class="max-h-80 overflow-x-auto overflow-y-auto">

        <table class="w-full min-w-[600px] text-left text-sm">

            {{-- STICKY TABLE HEADER --}}
            <thead class="sticky top-0 z-10 bg-slate-50 text-slate-600 shadow-sm">

                <tr>

                    <th class="px-5 py-3 font-semibold">
                        Date
                    </th>

                    <th class="px-5 py-3 font-semibold">
                        Payment Method
                    </th>

                    <th class="px-5 py-3 font-semibold">
                        Amount Paid
                    </th>

                </tr>

            </thead>


            {{-- TABLE BODY --}}
            <tbody class="divide-y divide-slate-100">

                @forelse($payments as $payment)

                    <tr class="transition hover:bg-slate-50">

                        {{-- PAYMENT DATE --}}
                        <td class="px-5 py-4 whitespace-nowrap text-slate-600">
                            {{ \Illuminate\Support\Carbon::parse($payment->payment_date)->format('d M Y') }}
                        </td>


                        {{-- PAYMENT METHOD --}}
                        <td class="px-5 py-4 text-slate-700">
                            {{ $payment->payment_method ?? 'Cash' }}
                        </td>


                        {{-- PAYMENT AMOUNT --}}
                        <td class="px-5 py-4 whitespace-nowrap font-semibold text-green-700">
                            Rs. {{ number_format($payment->amount, 2) }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td
                            colspan="3"
                            class="px-5 py-10 text-center text-slate-500"
                        >
                            <div class="mb-2 text-3xl">💰</div>

                            <p class="font-medium">
                                No payments found.
                            </p>

                            <p class="mt-1 text-xs">
                                Your payment history will appear here.
                            </p>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION OUTSIDE SCROLL AREA --}}
    <div class="border-t border-slate-200 px-5 py-3">

        {{ $payments->links() }}

    </div>

</div>


@endsection
