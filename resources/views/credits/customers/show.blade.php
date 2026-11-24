@extends('layouts.credit')

@section('title', 'Customer Account')

@section('content')
@php
    $formatMoney = fn ($amount) => 'Rs. ' . number_format((float) $amount, 2);

    $totalCredit = $totalCredit ?? 0;
    $totalReceived = $totalReceived ?? 0;
    $outstanding = $outstanding ?? 0;
    $advance = $advance ?? 0;
    $totalInvoicePaid = $totalInvoicePaid ?? 0;
    $totalDue = $totalDue ?? 0;
    $totalAdvance = $totalAdvance ?? 0;
@endphp

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Customer Account
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Sales and payment details
            </p>
        </div>

        <div class="flex flex-wrap gap-2 no-print">
            <a href="{{ route('credits.sales.create', ['customer_id' => $customer->id]) }}"
               class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                + New Sale
            </a>

            <a href="{{ route('credits.payments.create', ['customer' => $customer->id]) }}"
               class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                + Receive Payment
            </a>

            <button onclick="window.print()"
                    class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">
                Print
            </button>
        </div>
    </div>


    {{-- CUSTOMER INFORMATION --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">

            {{-- CLICKABLE CUSTOMER PHOTO --}}
            <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-blue-100 text-2xl font-bold text-blue-700">
                @if($customer->photo)
                    <img
                        src="{{ asset('storage/' . $customer->photo) }}"
                        alt="{{ $customer->name }}"
                        onclick="openCustomerPhoto(this.src)"
                        class="h-full w-full cursor-zoom-in object-cover transition hover:opacity-80"
                        title="Click to view full size"
                    >
                @else
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                @endif
            </div>

            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-800">
                    {{ $customer->name }}
                </h2>

                <div class="mt-2 flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-500">
                    <span>
                        <strong>Phone:</strong>
                        {{ $customer->phone ?? 'N/A' }}
                    </span>

                    <span>
                        <strong>Address:</strong>
                        {{ $customer->address ?? 'N/A' }}
                    </span>
                </div>
            </div>

            <div class="no-print">
                <a href="{{ route('credits.customers.account.edit', $customer->id) }}"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Reset Customer password
                </a>
            </div>

        </div>
    </div>


    {{-- DATE FILTER --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm no-print">

        <form action="{{ route('credits.customers.show', $customer->id) }}"
              method="GET"
              class="flex flex-col items-end gap-3 sm:flex-row">

            <div class="w-full flex-1">
                <label for="from" class="mb-1 block text-sm font-medium text-gray-700">
                    From Date
                </label>

                <input type="date"
                       name="from"
                       id="from"
                       value="{{ $from }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>

            <div class="w-full flex-1">
                <label for="to" class="mb-1 block text-sm font-medium text-gray-700">
                    To Date
                </label>

                <input type="date"
                       name="to"
                       id="to"
                       value="{{ $to }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>

            <div class="flex w-full gap-2 sm:w-auto">
                <button type="submit"
                        class="flex-1 rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Filter
                </button>

                <a href="{{ route('credits.customers.show', $customer->id) }}"
                   class="flex-1 rounded-lg border border-gray-300 px-5 py-2 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Reset
                </a>
            </div>

        </form>

        @if($from || $to)
            <p class="mt-3 text-sm text-blue-600">
                Showing records
                {{ $from ? 'from ' . \Carbon\Carbon::parse($from)->format('d M Y') : 'from beginning' }}
                {{ $to ? 'to ' . \Carbon\Carbon::parse($to)->format('d M Y') : 'to today' }}
            </p>
        @endif
    </div>


    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- TOTAL CREDIT --}}
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-5">
            <p class="text-sm font-medium text-blue-700">
                Total Purchases
            </p>

            <h3 class="mt-2 text-2xl font-bold text-blue-900">
                {{ $formatMoney($totalCredit) }}
            </h3>
        </div>

        {{-- TOTAL PAID --}}
        <div class="rounded-xl border border-green-100 bg-green-50 p-5">
            <p class="text-sm font-medium text-green-700">
                Total Paid
            </p>

            <h3 class="mt-2 text-2xl font-bold text-green-900">
                {{ $formatMoney($totalReceived) }}
            </h3>
        </div>

        {{-- OUTSTANDING --}}
        <div class="rounded-xl border border-red-100 bg-red-50 p-5">
            <p class="text-sm font-medium text-red-700">
                Outstanding Dues
            </p>

            <h3 class="mt-2 text-2xl font-bold text-red-900">
                {{ $formatMoney($outstanding) }}
            </h3>
        </div>

        {{-- ADVANCE --}}
        <div class="rounded-xl border border-purple-100 bg-purple-50 p-5">
            <p class="text-sm font-medium text-purple-700">
                Advance Paid
            </p>

            <h3 class="mt-2 text-2xl font-bold text-purple-900">
                {{ $formatMoney($advance) }}
            </h3>
        </div>

    </div>


    {{-- CREDIT SALES / INVOICES --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <div class="flex flex-col justify-between gap-2 border-b border-gray-200 p-5 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    Purchases / Invoices
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Customer purchase history
                </p>
            </div>

            <span class="text-sm font-medium text-gray-500">
                {{ $sales->total() }} invoices
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">
                            Invoice No.
                        </th>

                        <th class="px-4 py-3 text-left font-semibold text-gray-600">
                            Date
                        </th>

                        <th class="px-4 py-3 text-left font-semibold text-gray-600">
                            Products
                        </th>

                        <th class="px-4 py-3 text-right font-semibold text-gray-600">
                            Total
                        </th>

                        <th class="px-4 py-3 text-right font-semibold text-gray-600">
                            Paid
                        </th>

                        <th class="px-4 py-3 text-right font-semibold text-gray-600">
                            Due
                        </th>

                        <th class="px-4 py-3 text-center font-semibold text-gray-600 no-print">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse($sales as $sale)

                        @php
                            $paid = (float) $sale->allocations->sum('amount');

                            $due = max(
                                0,
                                (float) $sale->total_amount - $paid
                            );
                        @endphp

                        <tr class="hover:bg-gray-50">

                            <td class="whitespace-nowrap px-4 py-3 font-semibold text-blue-600">
                                {{ $sale->invoice_no }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}
                            </td>

                            <td class="px-4 py-3 text-gray-600">
                                @forelse($sale->items as $item)
                                    <div>
                                        {{ $item->product_name }}
                                        <span class="text-gray-400">
                                            ({{ $item->quantity }} {{ $item->unit ?? '' }})
                                        </span>
                                    </div>
                                @empty
                                    <span class="text-gray-400">No products</span>
                                @endforelse
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-gray-800">
                                {{ $formatMoney($sale->total_amount) }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right font-medium text-green-600">
                                {{ $formatMoney($paid) }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold {{ $due > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $formatMoney($due) }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-center no-print">
                                <a href="{{ route('credits.sales.show', $sale->id) }}"
                                   class="font-semibold text-blue-600 hover:text-blue-800">
                                    View
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                No credit sales found for this period.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

                {{-- INVOICE TOTAL FOOTER --}}
                <tfoot class="bg-gray-100">
                    <tr class="font-bold text-gray-800">

                        <td colspan="3" class="px-4 py-4 text-right">
                            TOTAL:
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-right">
                            {{ $formatMoney($totalCredit) }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-right text-green-700">
                            {{ $formatMoney($totalInvoicePaid) }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-right text-red-700">
                            {{ $formatMoney($totalDue) }}
                        </td>

                        <td class="no-print"></td>

                    </tr>
                </tfoot>

            </table>
        </div>

        <div class="border-t border-gray-200 p-4 no-print">
            {{ $sales->appends(request()->query())->links() }}
        </div>

    </div>


    {{-- PAYMENT HISTORY --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <div class="flex flex-col justify-between gap-2 border-b border-gray-200 p-5 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    Payment History
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Customer payment and advance records
                </p>
            </div>

            <span class="text-sm font-medium text-gray-500">
                {{ $payments->total() }} payments
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">
                            Receipt No.
                        </th>

                        <th class="px-4 py-3 text-left font-semibold text-gray-600">
                            Date
                        </th>

                        <th class="px-4 py-3 text-left font-semibold text-gray-600">
                            Payment Method
                        </th>

                        <th class="px-4 py-3 text-left font-semibold text-gray-600">
                            Reference / Notes
                        </th>

                        <th class="px-4 py-3 text-right font-semibold text-gray-600">
                            Received
                        </th>

                        <th class="px-4 py-3 text-right font-semibold text-gray-600">
                            Advance Left
                        </th>

                        <th class="px-4 py-3 text-center font-semibold text-gray-600 no-print">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse($payments as $payment)

                        @php
                            $allocated = (float) $payment->allocations->sum('amount');

                            $advanceLeft = max(
                                0,
                                (float) $payment->amount - $allocated
                            );
                        @endphp

                        <tr class="hover:bg-gray-50">

                            <td class="whitespace-nowrap px-4 py-3 font-semibold text-blue-600">
                                {{ $payment->receipt_no }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                            </td>

                            <td class="px-4 py-3 text-gray-600">
                                {{ ucfirst($payment->payment_method ?? 'N/A') }}
                            </td>

                            <td class="px-4 py-3 text-gray-600">
                                <div>
                                    {{ $payment->reference ?? '-' }}
                                </div>

                                @if($payment->notes)
                                    <div class="mt-1 text-xs text-gray-400">
                                        {{ $payment->notes }}
                                    </div>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-green-600">
                                {{ $formatMoney($payment->amount) }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold {{ $advanceLeft > 0 ? 'text-purple-600' : 'text-gray-500' }}">
                                {{ $formatMoney($advanceLeft) }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-center no-print">
                                <a href="{{ route('credits.payments.show', $payment->id) }}"
                                   class="font-semibold text-blue-600 hover:text-blue-800">
                                    View
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                No payment records found for this period.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

                {{-- PAYMENT TOTAL FOOTER --}}
                <tfoot class="bg-gray-100">
                    <tr class="font-bold text-gray-800">

                        <td colspan="4" class="px-4 py-4 text-right">
                            TOTAL:
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-right text-green-700">
                            {{ $formatMoney($totalReceived) }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-right text-purple-700">
                            {{ $formatMoney($totalAdvance) }}
                        </td>

                        <td class="no-print"></td>

                    </tr>
                </tfoot>

            </table>
        </div>

        <div class="border-t border-gray-200 p-4 no-print">
            {{ $payments->appends(request()->query())->links() }}
        </div>

    </div>

</div>


{{-- CUSTOMER PHOTO FULL-SIZE MODAL --}}
<div id="customerPhotoModal"
     class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/90 p-4"
     onclick="closeCustomerPhoto()">

    {{-- CLOSE BUTTON --}}
    <button type="button"
            onclick="closeCustomerPhoto()"
            class="absolute right-5 top-5 flex h-11 w-11 items-center justify-center rounded-full bg-white/20 text-3xl text-white transition hover:bg-white/40"
            aria-label="Close photo preview">
        &times;
    </button>

    {{-- FULL-SIZE IMAGE --}}
    <img id="customerFullSizePhoto"
         src=""
         alt="Customer full size photo"
         class="max-h-[90vh] max-w-full rounded-lg object-contain shadow-2xl"
         onclick="event.stopPropagation()">

</div>


{{-- PHOTO PREVIEW JAVASCRIPT --}}
<script>
    function openCustomerPhoto(imageSrc) {
        if (!imageSrc) return;

        const modal = document.getElementById('customerPhotoModal');
        const image = document.getElementById('customerFullSizePhoto');

        image.src = imageSrc;

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        document.body.style.overflow = 'hidden';
    }

    function closeCustomerPhoto() {
        const modal = document.getElementById('customerPhotoModal');
        const image = document.getElementById('customerFullSizePhoto');

        modal.classList.add('hidden');
        modal.classList.remove('flex');

        image.src = '';

        document.body.style.overflow = '';
    }

    // Close photo preview using Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCustomerPhoto();
        }
    });
</script>


{{-- PRINT STYLES --}}
<style>
    @media print {

        body {
            background: white !important;
            color: black !important;
            font-size: 11px !important;
        }

        .no-print,
        nav,
        aside,
        header,
        footer {
            display: none !important;
        }

        main,
        .container,
        .space-y-6 {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .rounded-xl,
        .shadow-sm {
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        .overflow-x-auto {
            overflow: visible !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        th,
        td {
            border: 1px solid #ccc !important;
            padding: 7px !important;
            color: black !important;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        tr {
            page-break-inside: avoid;
        }

        .grid {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        }

        .bg-blue-50,
        .bg-green-50,
        .bg-red-50,
        .bg-purple-50,
        .bg-gray-100 {
            background: white !important;
        }

        a {
            text-decoration: none !important;
            color: black !important;
        }
    }
</style>
@endsection
