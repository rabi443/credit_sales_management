@extends('layouts.credit')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Purchase History
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                View and manage your confirmed purchases.
            </p>
        </div>

        <a href="{{ route('credits.purchases.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
            <span class="text-lg">+</span>
            New Purchase
        </a>

    </div>


    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="rounded-lg border border-green-300 bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg border border-red-300 bg-red-100 px-4 py-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif


    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">
                Total Purchases (Current Page)
            </p>

            <h2 class="mt-2 text-2xl font-bold text-gray-800">
                {{ $purchases->total() }}
            </h2>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">
                Purchase Amount (Current Page)
            </p>

            <h2 class="mt-2 text-2xl font-bold text-blue-700">
                Rs. {{ number_format($purchases->getCollection()->sum('total_amount'), 2) }}
            </h2>
        </div>

    </div>


    {{-- SEARCH AND DATE FILTER --}}
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

        <form action="{{ route('credits.purchases.index') }}"
              method="GET">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <div class="lg:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Search Supplier / Invoice Number
                    </label>

                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search supplier or invoice..."
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        From Date
                    </label>

                    <input type="date"
                           name="from"
                           value="{{ request('from') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        To Date
                    </label>

                    <input type="date"
                           name="to"
                           value="{{ request('to') }}"
                           min="{{ request('from') }}"
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

            </div>

            <div class="mt-4 flex flex-wrap gap-2">

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                    Apply Filters
                </button>

                <a href="{{ route('credits.purchases.index') }}"
                   class="rounded-lg bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                    Reset
                </a>

            </div>

        </form>

    </div>


    {{-- PURCHASE TABLE --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-gray-200 p-5">

            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    Purchase Records
                </h2>

                <p class="text-sm text-gray-500">
                    {{ $purchases->total() }} purchase records found.
                </p>
            </div>

        </div>


        @if($purchases->count() > 0)

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">

                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                #
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                Purchase Date
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                Supplier
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                                Invoice No.
                            </th>

                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">
                                Items
                            </th>

                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">
                                Total Amount
                            </th>

                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">
                                Action
                            </th>
                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @foreach($purchases as $index => $purchase)

                            <tr class="transition hover:bg-gray-50">

                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-600">
                                    {{ $purchases->firstItem() + $index }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
                                    {{ $purchase->purchase_date->format('d M Y') }}
                                </td>

                                <td class="px-4 py-4 text-sm font-semibold text-gray-800">
                                    {{ $purchase->supplier_name }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-600">
                                    {{ $purchase->invoice_no ?: '—' }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-center text-sm text-gray-700">
                                    {{ $purchase->items->count() }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-right text-sm font-bold text-gray-800">
                                    Rs. {{ number_format($purchase->total_amount, 2) }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-center">

                                    <a href="{{ route('credits.purchases.show', $purchase->id) }}"
                                       class="inline-flex items-center rounded-lg bg-blue-100 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-200">
                                        View Details
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>


                    {{-- CURRENT PAGE TOTAL --}}
                    <tfoot class="bg-gray-50">

                        <tr>

                            <td colspan="5"
                                class="px-4 py-4 text-right font-bold text-gray-800">
                                Current Page Total
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 text-right font-bold text-blue-700">
                                Rs. {{ number_format($purchases->getCollection()->sum('total_amount'), 2) }}
                            </td>

                            <td></td>

                        </tr>

                    </tfoot>

                </table>

            </div>


            {{-- PAGINATION --}}
            <div class="border-t border-gray-200 p-5">
                {{ $purchases->links() }}
            </div>

        @else

            <div class="p-12 text-center">

                <div class="mb-3 text-5xl">📦</div>

                <h3 class="text-lg font-semibold text-gray-700">
                    No purchases found
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    No confirmed purchases match your search or date filters.
                </p>

                <a href="{{ route('credits.purchases.create') }}"
                   class="mt-5 inline-flex rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                    Record New Purchase
                </a>

            </div>

        @endif

    </div>

</div>

@endsection
