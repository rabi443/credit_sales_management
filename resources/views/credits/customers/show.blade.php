
@extends('layouts.credit')

@section('title', $customer->name . ' Account')

@section('content')

{{-- CUSTOMER HEADER --}}
<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-start">

    <div>
        <a href="{{ route('credits.index') }}"
           class="text-sm font-medium text-blue-700">
            ← All customers
        </a>

        <h1 class="mt-2 text-2xl font-bold text-slate-800">
            {{ $customer->name }}
        </h1>

        <p class="text-sm text-slate-500">
            {{ $customer->phone ?: 'No phone' }}
            ·
            {{ $customer->address ?: 'No address' }}
        </p>
    </div>

    <div class="flex flex-wrap gap-2">

        <a href="{{ route('credits.customers.account.edit', $customer) }}"
        class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
            🔐 Reset Login
        </a>
        

        <a href="{{ route('credits.sales.create', ['customer_id' => $customer->id]) }}"
           class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
            + New Sale
        </a>

        <a href="{{ route('credits.payments.create', $customer) }}"
           class="rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
            + Receive Payment
        </a>

        <button onclick="window.print()"
                class="rounded-lg border bg-white px-4 py-2.5 text-sm font-semibold hover:bg-slate-50">
            Print
        </button>

    </div>
</div>


{{-- DATE FILTER --}}
<div class="mb-6 rounded-xl bg-white p-5 shadow-sm">

    <div class="mb-4">
        <h2 class="font-semibold text-slate-800">
            Filter Account History
        </h2>

        <p class="mt-1 text-xs text-slate-500">
            Select a date range to filter credit invoices and payments.
        </p>
    </div>

    <form method="GET"
          action="{{ route('credits.customers.show', $customer) }}"
          class="flex flex-col gap-4 sm:flex-row sm:items-end">

        {{-- FROM DATE --}}
        <div class="flex-1">
            <label class="mb-1 block text-sm font-medium text-slate-700">
                From Date (AD)
            </label>

            <input
                type="date"
                name="from"
                value="{{ $from ?? request('from') }}"
                class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-blue-500 focus:ring-blue-500"
            >
        </div>

        {{-- TO DATE --}}
        <div class="flex-1">
            <label class="mb-1 block text-sm font-medium text-slate-700">
                To Date (AD)
            </label>

            <input
                type="date"
                name="to"
                value="{{ $to ?? request('to') }}"
                min="{{ $from ?? request('from') }}"
                class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-blue-500 focus:ring-blue-500"
            >
        </div>

        {{-- BUTTONS --}}
        <div class="flex gap-2">

            <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2.5 font-semibold text-white hover:bg-blue-700">
                Filter
            </button>

            <a href="{{ route('credits.customers.show', $customer) }}"
               class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 font-semibold text-slate-700 hover:bg-slate-50">
                Reset
            </a>

        </div>

    </form>

    @if($from || $to)
        <div class="mt-4 rounded-lg bg-blue-50 p-3 text-sm text-blue-800">

            <span class="font-semibold">Active Filter:</span>

            {{ $from ? \Carbon\Carbon::parse($from)->format('d M Y') : 'Beginning' }}

            →

            {{ $to ? \Carbon\Carbon::parse($to)->format('d M Y') : 'Today' }}

        </div>
    @endif

</div>


{{-- CUSTOMER SUMMARY --}}
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

    {{-- TOTAL CREDIT --}}
    <div class="rounded-xl bg-white p-4 shadow-sm">
        <p class="text-xs text-slate-500">Total Credit</p>

        <p class="mt-1 text-xl font-bold text-slate-800">
            Rs. {{ number_format($customer->total_credit, 2) }}
        </p>
    </div>

    {{-- TOTAL PAID --}}
    <div class="rounded-xl bg-white p-4 shadow-sm">
        <p class="text-xs text-slate-500">Total Paid</p>

        <p class="mt-1 text-xl font-bold text-green-700">
            Rs. {{ number_format($customer->total_paid, 2) }}
        </p>
    </div>

    {{-- OUTSTANDING --}}
    <div class="rounded-xl bg-white p-4 shadow-sm">
        <p class="text-xs text-slate-500">Outstanding</p>

        <p class="mt-1 text-xl font-bold {{ $customer->balance > 0 ? 'text-red-600' : 'text-green-700' }}">
            Rs. {{ number_format(max(0, $customer->balance), 2) }}
        </p>
    </div>

    {{-- ADVANCE --}}
    <div class="rounded-xl bg-white p-4 shadow-sm">
        <p class="text-xs text-slate-500">Advance Credit</p>

        <p class="mt-1 text-xl font-bold text-blue-700">
            Rs. {{ number_format($customer->advance, 2) }}
        </p>
    </div>

</div>


{{-- TRANSACTION INVOICES --}}
<div class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm">

    {{-- HEADER --}}
    <div class="flex items-center justify-between border-b px-5 py-4">

        <div>
            <h2 class="font-semibold text-slate-800">
                Transaction Invoices
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                {{ $sales->total() }} invoice(s) found
            </p>
        </div>

    </div>

    {{-- SCROLLABLE INVOICE TABLE --}}
    <div class="max-h-[400px] overflow-auto">

        <table class="min-w-full divide-y divide-slate-200 text-sm">

            {{-- STICKY TABLE HEADER --}}
            <thead class="sticky top-0 z-10 bg-slate-50 text-left text-xs uppercase text-slate-500">

                <tr>
                    <th class="whitespace-nowrap px-5 py-3">Invoice / Date</th>
                    <th class="px-5 py-3">Products</th>
                    <th class="whitespace-nowrap px-5 py-3 text-right">Total</th>
                    <th class="whitespace-nowrap px-5 py-3 text-right">Paid</th>
                    <th class="whitespace-nowrap px-5 py-3 text-right">Due</th>
                    <th class="whitespace-nowrap px-5 py-3">Status</th>
                </tr>

            </thead>

            <tbody class="divide-y divide-slate-100">

                @forelse($sales as $sale)

                    @php
                        $paid = (float) $sale->allocations->sum('amount');
                        $due = max(0, (float) $sale->total_amount - $paid);
                    @endphp

                    <tr class="hover:bg-slate-50">

                        {{-- INVOICE --}}
                        <td class="whitespace-nowrap px-5 py-4">

                            <div class="font-semibold text-slate-800">
                                {{ $sale->invoice_no }}
                            </div>

                            <div class="text-xs text-slate-500">
                                {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}
                            </div>

                        </td>

                        {{-- PRODUCTS --}}
                        <td class="px-5 py-4">

                            <div class="space-y-1">

                                @foreach($sale->items as $item)

                                    <div>
                                        {{ $item->product_name }}

                                        <span class="text-slate-400">
                                            ({{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}
                                            {{ $item->unit }}
                                            ×
                                            {{ number_format((float) $item->unit_price, 2) }})
                                        </span>
                                    </div>

                                @endforeach

                            </div>

                        </td>

                        {{-- TOTAL --}}
                        <td class="whitespace-nowrap px-5 py-4 text-right font-semibold">
                            {{ number_format((float) $sale->total_amount, 2) }}
                        </td>

                        {{-- PAID --}}
                        <td class="whitespace-nowrap px-5 py-4 text-right text-green-700">
                            {{ number_format($paid, 2) }}
                        </td>

                        {{-- DUE --}}
                        <td class="whitespace-nowrap px-5 py-4 text-right font-semibold {{ $due > 0 ? 'text-red-600' : 'text-green-700' }}">
                            {{ number_format($due, 2) }}
                        </td>

                        {{-- STATUS --}}
                        <td class="whitespace-nowrap px-5 py-4">

                            @if($due == 0)

                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                    Paid
                                </span>

                            @elseif($paid > 0)

                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                    Partially Paid
                                </span>

                            @else

                                <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                    Unpaid
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6"
                            class="px-5 py-8 text-center text-slate-500">
                            No credit sales found for this date range.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINATION --}}
    <div class="border-t px-5 py-4">
        {{ $sales->links() }}
    </div>

</div>


{{-- PAYMENT HISTORY --}}
<div class="overflow-hidden rounded-xl bg-white shadow-sm">

    {{-- HEADER --}}
    <div class="border-b px-5 py-4">

        <h2 class="font-semibold text-slate-800">
            Payment History
        </h2>

        <p class="mt-1 text-xs text-slate-500">
            {{ $payments->total() }} payment(s) found
        </p>

    </div>

    {{-- SCROLLABLE PAYMENT TABLE --}}
    <div class="max-h-[400px] overflow-auto">

        <table class="min-w-full divide-y divide-slate-200 text-sm">

            {{-- STICKY TABLE HEADER --}}
            <thead class="sticky top-0 z-10 bg-slate-50 text-left text-xs uppercase text-slate-500">

                <tr>
                    <th class="whitespace-nowrap px-5 py-3">Receipt / Date</th>
                    <th class="whitespace-nowrap px-5 py-3">Method</th>
                    <th class="px-5 py-3">Reference / Notes</th>
                    <th class="whitespace-nowrap px-5 py-3 text-right">Received</th>
                    <th class="whitespace-nowrap px-5 py-3 text-right">Advance Left</th>
                </tr>

            </thead>

            <tbody class="divide-y divide-slate-100">

                @forelse($payments as $payment)

                    @php
                        $allocated = (float) $payment->allocations->sum('amount');
                    @endphp

                    <tr class="hover:bg-slate-50">

                        {{-- RECEIPT --}}
                        <td class="whitespace-nowrap px-5 py-4">

                            <div class="font-semibold text-slate-800">
                                {{ $payment->receipt_no }}
                            </div>

                            <div class="text-xs text-slate-500">
                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                            </div>

                        </td>

                        {{-- METHOD --}}
                        <td class="whitespace-nowrap px-5 py-4 capitalize">
                            {{ $payment->payment_method }}
                        </td>

                        {{-- REFERENCE --}}
                        <td class="px-5 py-4 text-slate-500">
                            {{ $payment->reference ?: $payment->notes ?: '—' }}
                        </td>

                        {{-- RECEIVED --}}
                        <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-green-700">
                            {{ number_format((float) $payment->amount, 2) }}
                        </td>

                        {{-- ADVANCE LEFT --}}
                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            {{ number_format(max(0, (float) $payment->amount - $allocated), 2) }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5"
                            class="px-5 py-8 text-center text-slate-500">
                            No payments found for this date range.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINATION --}}
    <div class="border-t px-5 py-4">
        {{ $payments->links() }}
    </div>

</div>


{{-- PRINT STYLE --}}
<style>
    @media print {
        body {
            background: white !important;
        }

        button,
        a,
        form {
            display: none !important;
        }

        .shadow-sm {
            box-shadow: none !important;
        }
    }
</style>

@endsection
