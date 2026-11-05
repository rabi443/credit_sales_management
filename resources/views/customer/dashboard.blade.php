@extends('layouts.customer')

@section('title', 'My Credit Dashboard')

@section('content')

<div class="space-y-6">

    {{-- =========================
        PAGE HEADER
    ========================= --}}

    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            Welcome, {{ $customer->name }} 👋
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Here you can track your purchases and payments with Rabin Kirana Store.
            Use the date filters to view your transactions and payment history.
        </p>
    </div>


    {{-- =========================
        FILTERED SUMMARY CARDS
    ========================= --}}

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        {{-- TOTAL TRANSACTIONS --}}

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">
                    Total Transaction Amount
                </p>

                <span class="rounded-lg bg-blue-50 p-2 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 14l2 2 4-4m5-5H4m16 0v11a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2z"/>
                    </svg>
                </span>
            </div>

            <h2 class="mt-3 text-2xl font-bold text-blue-700">
                Rs. {{ number_format($totalCredit, 2) }}
            </h2>

            <p class="mt-1 text-xs text-slate-400">
                Total purchases in selected period
            </p>
        </div>


        {{-- TOTAL PAID --}}

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">
                    Total Paid
                </p>

                <span class="rounded-lg bg-green-50 p-2 text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
            </div>

            <h2 class="mt-3 text-2xl font-bold text-green-700">
                Rs. {{ number_format($totalPaid, 2) }}
            </h2>

            <p class="mt-1 text-xs text-slate-400">
                Paid in selected period
            </p>
        </div>


        {{-- DUE / ADVANCE --}}

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">
                    {{ $balance < 0 ? 'Advance Paid' : 'Remaining Due Balance' }}
                </p>

                <span class="rounded-lg {{ $balance > 0 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }} p-2">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 2v8m0 0v2m8-6a8 8 0 11-16 0 8 8 0 0116 0z"/>
                    </svg>
                </span>
            </div>

            <h2 class="mt-3 text-2xl font-bold {{ $balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                Rs. {{ number_format(abs($balance), 2) }}
            </h2>

            @if($balance > 0)
                <p class="mt-1 text-xs text-red-500">
                    Outstanding due amount for selected period
                </p>
            @elseif($balance < 0)
                <p class="mt-1 text-xs text-green-600">
                    Payments exceed transactions in selected period
                </p>
            @else
                <p class="mt-1 text-xs text-green-600">
                    Transactions and payments are equal
                </p>
            @endif
        </div>

    </div>


    {{-- =========================
        DATE FILTER
    ========================= --}}

    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

        <div class="mb-4">
            <h2 class="text-lg font-bold text-slate-800">
                Filter Transactions
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Select a date range to calculate your transaction amount, paid amount and balance.
            </p>
        </div>

        <form method="GET"
              action="{{ route('customer.dashboard') }}"
              class="grid grid-cols-1 items-end gap-4 sm:grid-cols-3">

            {{-- FROM DATE --}}

            <div>
                <label for="from"
                       class="mb-1 block text-sm font-medium text-slate-700">
                    From Date
                </label>

                <input type="date"
                       name="from"
                       id="from"
                       value="{{ $from }}"
                       max="{{ $to ?: date('Y-m-d') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>


            {{-- TO DATE --}}

            <div>
                <label for="to"
                       class="mb-1 block text-sm font-medium text-slate-700">
                    To Date
                </label>

                <input type="date"
                       name="to"
                       id="to"
                       value="{{ $to }}"
                       min="{{ $from }}"
                       max="{{ date('Y-m-d') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>


            {{-- BUTTONS --}}

            <div class="flex gap-2">

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Apply Filter
                </button>

                <a href="{{ route('customer.dashboard') }}"
                   class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Clear
                </a>

            </div>

        </form>

        {{-- FILTER STATUS --}}

        <div class="mt-4 rounded-lg bg-blue-50 px-4 py-3 text-xs text-blue-700">

            @if($from || $to)

                <strong>Selected period:</strong>

                {{ $from ? \Carbon\Carbon::parse($from)->format('d M Y') : 'Beginning' }}

                to

                {{ $to ? \Carbon\Carbon::parse($to)->format('d M Y') : 'Today' }}

            @else

                Showing all transactions and payments.

            @endif

            <p class="mt-1">
                Summary cards and table totals are calculated using this date filter.
            </p>

        </div>

    </div>


    {{-- =========================
        CREDIT TRANSACTIONS
    ========================= --}}

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        {{-- HEADER --}}

        <div class="flex flex-col justify-between gap-2 border-b px-5 py-4 sm:flex-row sm:items-center">

            <div>
                <h2 class="text-lg font-bold text-slate-800">
                    My Transactions
                </h2>

                <p class="text-sm text-slate-500">
                    Purchase invoices.
                </p>
            </div>

            <div class="text-xs text-slate-500">
                {{ $sales->total() }} invoices found
            </div>

        </div>


        {{-- SCROLLABLE TABLE --}}

        <div class="max-h-80 overflow-x-auto overflow-y-auto">

            <table class="w-full min-w-[600px] text-left text-sm">

                {{-- TABLE HEADER --}}

                <thead class="sticky top-0 z-10 bg-slate-50 text-slate-600 shadow-sm">

                    <tr>
                        <th class="px-5 py-3 font-semibold">
                            Invoice
                        </th>

                        <th class="px-5 py-3 font-semibold">
                            Date
                        </th>

                        <th class="px-5 py-3 text-right font-semibold">
                            Amount
                        </th>

                        <th class="px-5 py-3 text-center font-semibold">
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

                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                {{ \Illuminate\Support\Carbon::parse($sale->sale_date)->format('d M Y') }}
                            </td>


                            {{-- AMOUNT --}}

                            <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-800">
                                Rs. {{ number_format($sale->total_amount, 2) }}
                            </td>


                            {{-- ACTION --}}

                            <td class="px-5 py-4 text-center">

                                <a href="{{ route('customer.invoices.show', $sale) }}"
                                   class="inline-flex items-center gap-1 font-semibold text-blue-600 hover:text-blue-800 hover:underline">

                                    View Invoice
                                    <span>→</span>

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4"
                                class="px-5 py-10 text-center text-slate-500">

                                <div class="mb-2 text-3xl">
                                    📄
                                </div>

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


                {{-- TRANSACTION TOTAL FOOTER --}}

                <tfoot class="sticky bottom-0 z-10 border-t-2 border-blue-200 bg-blue-50">

                    <tr>

                        <td colspan="2"
                            class="px-5 py-4 text-right font-bold text-slate-700">

                            Total Transaction Amount

                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right text-base font-bold text-blue-700">

                            Rs. {{ number_format($totalCredit, 2) }}

                        </td>

                        <td class="px-5 py-4"></td>

                    </tr>

                </tfoot>

            </table>

        </div>


        {{-- PAGINATION --}}

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
                    Payment History
                </h2>

                <p class="text-sm text-slate-500">
                    Payments recorded by the shop.
                </p>
            </div>

            <div class="text-xs text-slate-500">
                {{ $payments->total() }} payments found
            </div>

        </div>


        {{-- SCROLLABLE TABLE --}}

        <div class="max-h-80 overflow-x-auto overflow-y-auto">

            <table class="w-full min-w-[750px] text-left text-sm">

                {{-- TABLE HEADER --}}

                <thead class="sticky top-0 z-10 bg-slate-50 text-slate-600 shadow-sm">

                    <tr>

                        <th class="px-5 py-3 font-semibold">
                            Payment ID
                        </th>

                        <th class="px-5 py-3 font-semibold">
                            Date
                        </th>

                        <th class="px-5 py-3 font-semibold">
                            Payment Method
                        </th>

                        <th class="px-5 py-3 text-right font-semibold">
                            Amount Paid
                        </th>

                        <th class="px-5 py-3 text-center font-semibold">
                            Action
                        </th>

                    </tr>

                </thead>


                {{-- TABLE BODY --}}

                <tbody class="divide-y divide-slate-100">

                    @forelse($payments as $payment)

                        <tr class="transition hover:bg-slate-50">

                            {{-- PAYMENT ID --}}

                            <td class="px-5 py-4 font-medium text-slate-800">
                                #{{ $payment->id }}
                            </td>


                            {{-- DATE --}}

                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                {{ \Illuminate\Support\Carbon::parse($payment->payment_date)->format('d M Y') }}
                            </td>


                            {{-- PAYMENT METHOD --}}

                            <td class="px-5 py-4 capitalize text-slate-700">
                                {{ $payment->payment_method ?? 'Cash' }}
                            </td>


                            {{-- AMOUNT --}}

                            <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-green-700">
                                Rs. {{ number_format($payment->amount, 2) }}
                            </td>


                            {{-- ACTION --}}

                            <td class="px-5 py-4 text-center">

                                <a href="{{ route('customer.payments.show', $payment->id) }}"
                                   class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-4 w-4"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor"
                                         stroke-width="2">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>

                                    </svg>

                                    View Details

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="px-5 py-10 text-center text-slate-500">

                                <div class="mb-2 text-3xl">
                                    💰
                                </div>

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


                {{-- PAYMENT TOTAL FOOTER --}}

                <tfoot class="sticky bottom-0 z-10 border-t-2 border-green-200 bg-green-50">

                    <tr>

                        <td colspan="3"
                            class="px-5 py-4 text-right font-bold text-slate-700">

                            Total Payments

                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right text-base font-bold text-green-700">

                            Rs. {{ number_format($totalPaid, 2) }}

                        </td>

                        <td class="px-5 py-4"></td>

                    </tr>

                </tfoot>

            </table>

        </div>


        {{-- PAGINATION --}}

        <div class="border-t border-slate-200 px-5 py-3">

            {{ $payments->links() }}

        </div>

    </div>


    {{-- =========================
        FILTERED BALANCE SUMMARY
    ========================= --}}

    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

        <h2 class="mb-4 text-lg font-bold text-slate-800">
            Account Summary
        </h2>

        <div class="space-y-3">

            <div class="flex justify-between gap-3 text-sm">
                <span class="text-slate-600">
                    Total Transactions
                </span>

                <span class="font-semibold text-blue-700">
                    Rs. {{ number_format($totalCredit, 2) }}
                </span>
            </div>

            <div class="flex justify-between gap-3 text-sm">
                <span class="text-slate-600">
                    Total Paid
                </span>

                <span class="font-semibold text-green-700">
                    Rs. {{ number_format($totalPaid, 2) }}
                </span>
            </div>

            <div class="border-t border-dashed border-slate-300 pt-3">

                <div class="flex justify-between gap-3">
                    <span class="font-bold text-slate-800">
                        {{ $balance < 0 ? 'Net Advance' : 'Net Due' }}
                    </span>

                    <span class="text-lg font-bold {{ $balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                        Rs. {{ number_format(abs($balance), 2) }}
                    </span>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection