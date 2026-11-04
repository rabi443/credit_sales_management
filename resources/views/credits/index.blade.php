@extends('layouts.credit')

@section('title', 'Customer Credit Dashboard')

@section('content')

<div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">

    <div class="mx-auto max-w-7xl">

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif


        {{-- HEADER --}}
        <div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

            <div>
                <h1 class="text-2xl font-bold text-slate-800 sm:text-3xl">
                    Admin Dashboard
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Manage customers, sales and payments.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                <a href="{{ route('credits.customers.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                    <span>＋</span>
                    Add Customer
                </a>

                <a href="{{ route('credits.sales.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
                    <span>＋</span>
                    New Sale
                </a>

            </div>

        </div>


        {{-- SUMMARY CARDS --}}
        <div class="mb-7 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

            {{-- TOTAL CREDIT --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Total Sales
                        </p>

                        <h2 class="mt-2 text-2xl font-bold text-slate-800">
                            NPR {{ number_format($totalCredit, 2) }}
                        </h2>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-2xl">
                        🧾
                    </div>

                </div>

            </div>


            {{-- TOTAL PAID --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Total Received
                        </p>

                        <h2 class="mt-2 text-2xl font-bold text-green-600">
                            NPR {{ number_format($totalPaid, 2) }}
                        </h2>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-2xl">
                        💰
                    </div>

                </div>

            </div>


            {{-- OUTSTANDING --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Outstanding Receivable Balance
                        </p>

                        <h2 class="mt-2 text-2xl font-bold {{ $totalOutstanding >= 0 ? 'text-red-600' : 'text-green-600' }}">
                            NPR {{ number_format($totalOutstanding, 2) }}
                        </h2>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 text-2xl">
                        📊
                    </div>

                </div>

            </div>

        </div>


        {{-- CUSTOMER LIST --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

            {{-- TABLE HEADER --}}
            <div class="flex flex-col justify-between gap-4 border-b border-slate-200 p-5 sm:flex-row sm:items-center">

                <div>
                    <h2 class="text-lg font-bold text-slate-800">
                        Customers
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $customers->total() }} registered customers
                    </p>
                </div>


                {{-- SEARCH FORM --}}
                <form
                    id="customerSearchForm"
                    action="{{ route('credits.index') }}"
                    method="GET"
                    class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row"
                >

                    <div class="relative w-full sm:w-80">

                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            🔍
                        </span>

                        <input
                            type="search"
                            id="customerSearch"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search name or phone..."
                            autocomplete="off"
                            class="w-full rounded-lg border border-slate-300 py-2.5 pl-10 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                    </div>

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Search
                    </button>

                    @if($search)
                        <a
                            href="{{ route('credits.index') }}"
                            class="rounded-lg border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold text-slate-600 hover:bg-slate-50"
                        >
                            Clear
                        </a>
                    @endif

                </form>

            </div>


            {{-- AUTO SEARCH MESSAGE --}}
            <div class="border-b border-slate-100 bg-slate-50 px-5 py-2">

                <p class="text-xs text-slate-500">
                    Search automatically while typing.
                </p>

            </div>


            {{-- TABLE SCROLL AREA --}}
            <div class="max-h-[500px] overflow-auto">

                <table class="min-w-full divide-y divide-slate-200">

                    {{-- TABLE HEAD --}}
                    <thead class="sticky top-0 z-10 bg-slate-100">

                        <tr>

                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-600">
                                Customer
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-600">
                                Phone
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-600">
                                Total Credit
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-600">
                                Total Paid
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-slate-600">
                                Balance
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 text-center text-xs font-bold uppercase tracking-wider text-slate-600">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    {{-- TABLE BODY --}}
                    <tbody class="divide-y divide-slate-100 bg-white">

                        @forelse($customers as $customer)

                            @php

                                $credit = (float) ($customer->total_credit ?? 0);

                                $paid = (float) ($customer->total_paid ?? 0);

                                $balance = $credit - $paid;

                                $customerPhoto = $customer->photo
                                    ? \Illuminate\Support\Facades\Storage::url($customer->photo)
                                    : null;

                                $initials = collect(explode(' ', trim($customer->name)))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                    ->implode('');

                            @endphp


                            <tr class="transition hover:bg-slate-50">

                                {{-- CUSTOMER NAME AND PHOTO --}}
                                <td class="whitespace-nowrap px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        {{-- CLICKABLE PHOTO --}}
                                        @if($customerPhoto)

                                            <a
                                                href="{{ $customerPhoto }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                title="Click to view full image"
                                                class="shrink-0"
                                            >

                                                <img
                                                    src="{{ $customerPhoto }}"
                                                    alt="{{ $customer->name }}"
                                                    class="h-11 w-11 cursor-pointer rounded-full border border-slate-200 object-cover transition hover:scale-110 hover:ring-2 hover:ring-blue-500"
                                                >

                                            </a>

                                        @else

                                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">
                                                {{ $initials ?: 'C' }}
                                            </div>

                                        @endif


                                        {{-- CUSTOMER DETAILS --}}
                                        <div>

                                            <a
                                                href="{{ route('credits.customers.show', $customer) }}"
                                                class="font-semibold text-slate-800 hover:text-blue-600"
                                            >
                                                {{ $customer->name }}
                                            </a>

                                            <p class="mt-0.5 text-xs text-slate-400">
                                                {{ $customer->credit_sales_count }} credit sales
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- PHONE --}}
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                    {{ $customer->phone ?: 'N/A' }}
                                </td>


                                {{-- TOTAL CREDIT --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-slate-700">
                                    NPR {{ number_format($credit, 2) }}
                                </td>


                                {{-- TOTAL PAID --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-semibold text-green-600">
                                    NPR {{ number_format($paid, 2) }}
                                </td>


                                {{-- BALANCE --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right">

                                    <span class="font-bold {{ $balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        NPR {{ number_format($balance, 2) }}
                                    </span>

                                    @if($balance < 0)

                                        <p class="text-xs text-green-600">
                                            Advance
                                        </p>

                                    @elseif($balance == 0)

                                        <p class="text-xs text-green-600">
                                            Cleared
                                        </p>

                                    @else

                                        <p class="text-xs text-red-500">
                                            Due
                                        </p>

                                    @endif

                                </td>


                                {{-- ACTIONS --}}
                                <td class="whitespace-nowrap px-5 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        {{-- VIEW --}}
                                        <a
                                            href="{{ route('credits.customers.show', $customer) }}"
                                            title="View customer ledger"
                                            class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100"
                                        >
                                            View
                                        </a>


                                        {{-- EDIT --}}
                                        <a
                                            href="{{ route('credits.customers.edit', $customer) }}"
                                            title="Edit customer"
                                            class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-100"
                                        >
                                            Edit
                                        </a>


                                        {{-- DELETE --}}
                                        {{-- <form
                                            action="{{ route('credits.customers.destroy', $customer) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete {{ $customer->name }}?')"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                title="Delete customer"
                                                class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100"
                                            >
                                                Delete
                                            </button>

                                        </form> --}}

                                    </div>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="6" class="px-5 py-12 text-center">

                                    <div class="text-4xl">
                                        👥
                                    </div>

                                    <h3 class="mt-3 font-semibold text-slate-700">
                                        No customers found
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-500">

                                        @if($search)

                                            No customers match "{{ $search }}".

                                        @else

                                            Add your first customer to get started.

                                        @endif

                                    </p>

                                    @if($search)

                                        <a
                                            href="{{ route('credits.index') }}"
                                            class="mt-4 inline-block text-sm font-semibold text-blue-600 hover:underline"
                                        >
                                            Clear search
                                        </a>

                                    @endif

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            @if($customers->hasPages())

                <div class="border-t border-slate-200 px-5 py-4">

                    {{ $customers->links() }}

                </div>

            @endif

        </div>

    </div>

</div>


{{-- AUTO SEARCH JAVASCRIPT --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('customerSearch');

    const searchForm = document.getElementById('customerSearchForm');

    if (!searchInput || !searchForm) {
        return;
    }

    let searchTimer;

    searchInput.addEventListener('input', function () {

        clearTimeout(searchTimer);

        searchTimer = setTimeout(function () {

            searchForm.requestSubmit();

        }, 400);

    });

    searchInput.addEventListener('keydown', function (event) {

        if (event.key === 'Enter') {

            clearTimeout(searchTimer);

            searchForm.requestSubmit();

        }

    });

});

</script>

@endsection