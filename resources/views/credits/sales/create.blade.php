
@extends('layouts.credit')

@section('title', 'New Credit Sale')

@section('content')

@php
    $selectedCustomerId = old('customer_id', request('customer_id'));
    $selectedCustomer = $customers->firstWhere('id', $selectedCustomerId);
@endphp

<div class="mx-auto max-w-5xl px-4 py-6">

    <a href="{{ route('credits.index') }}"
       class="text-sm font-medium text-blue-700 hover:underline">
        ← Back to dashboard
    </a>

    <div class="mt-4 rounded-xl bg-white p-5 shadow-sm sm:p-6">

        <h1 class="text-xl font-bold text-slate-800">
            New Transaction
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Add products, select payment type and save your invoice.
            This will not change stock.
        </p>

        {{-- VALIDATION ERRORS --}}
        @if($errors->any())
            <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('credits.sales.store') }}"
              class="mt-6"
              id="saleForm">

            @csrf

            {{-- ==========================================
                 CUSTOMER AND SALE DATE
            =========================================== --}}

            <div class="grid gap-5 sm:grid-cols-2">

                {{-- CUSTOMER SEARCH --}}
                <div class="relative">

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Customer *
                    </label>

                    <input
                        type="hidden"
                        name="customer_id"
                        id="customerId"
                        value="{{ $selectedCustomerId }}"
                        required
                    >

                    <div class="relative">

                        <input
                            type="text"
                            id="customerSearch"
                            autocomplete="off"
                            placeholder="Type customer name or phone..."
                            value="{{ $selectedCustomer?->name ?? '' }}"
                            class="w-full rounded-lg border border-slate-300 py-3 pl-4 pr-10 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >

                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                            🔍
                        </span>

                    </div>

                    {{-- SELECTED CUSTOMER PREVIEW --}}
                    <div id="selectedCustomerPreview"
                         class="{{ $selectedCustomer ? '' : 'hidden' }} mt-2 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 p-3">

                        <img
                            id="selectedCustomerPhoto"
                            src="{{ $selectedCustomer && $selectedCustomer->photo ? \Illuminate\Support\Facades\Storage::url($selectedCustomer->photo) : '' }}"
                            alt="Customer"
                            class="{{ $selectedCustomer && $selectedCustomer->photo ? '' : 'hidden' }} h-10 w-10 rounded-full border border-slate-200 object-cover"
                        >

                        <div
                            id="selectedCustomerInitial"
                            class="{{ $selectedCustomer && $selectedCustomer->photo ? 'hidden' : '' }} flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700"
                        >
                            {{ $selectedCustomer ? strtoupper(substr($selectedCustomer->name, 0, 1)) : '' }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <p id="selectedCustomerName" class="font-semibold text-slate-800">
                                {{ $selectedCustomer?->name }}
                            </p>

                            <p id="selectedCustomerPhone" class="text-xs text-slate-500">
                                {{ $selectedCustomer?->phone }}
                            </p>
                        </div>

                        <span class="text-xs font-semibold text-green-700">
                            Selected
                        </span>

                    </div>

                    {{-- CUSTOMER DROPDOWN --}}
                    <div
                        id="customerDropdown"
                        class="absolute left-0 right-0 top-full z-30 mt-2 hidden max-h-72 overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-xl"
                    >

                        @forelse($customers as $customer)

                            @php
                                $photo = $customer->photo
                                    ? \Illuminate\Support\Facades\Storage::url($customer->photo)
                                    : null;

                                $initial = strtoupper(substr($customer->name, 0, 1));
                            @endphp

                            <button
                                type="button"
                                class="customer-option flex w-full items-center gap-3 border-b border-slate-100 px-4 py-3 text-left transition hover:bg-blue-50"
                                data-id="{{ $customer->id }}"
                                data-name="{{ $customer->name }}"
                                data-phone="{{ $customer->phone }}"
                                data-photo="{{ $photo }}"
                                data-search="{{ strtolower($customer->name . ' ' . $customer->phone) }}"
                            >

                                @if($photo)
                                    <img
                                        src="{{ $photo }}"
                                        alt="{{ $customer->name }}"
                                        class="h-10 w-10 rounded-full border border-slate-200 object-cover"
                                    >
                                @else
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">
                                        {{ $initial }}
                                    </div>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-800">
                                        {{ $customer->name }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        {{ $customer->phone ?: 'No phone number' }}
                                    </p>
                                </div>

                                <span class="text-xs text-blue-600">
                                    Select
                                </span>

                            </button>

                        @empty

                            <div class="p-5 text-center text-sm text-slate-500">
                                No customers available.

                                <a href="{{ route('credits.customers.create') }}"
                                   class="font-semibold text-blue-600 hover:underline">
                                    Add customer
                                </a>
                            </div>

                        @endforelse

                        <div id="noCustomerFound"
                             class="hidden p-5 text-center text-sm text-slate-500">
                            No matching customer found.
                        </div>

                    </div>

                    @error('customer_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <p class="mt-1 text-xs text-slate-400">
                        Type a name or phone number to find a customer.
                    </p>

                </div>

                {{-- SALE DATE --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Sale Date *
                    </label>

                    <input
                        type="date"
                        name="sale_date"
                        value="{{ old('sale_date', now()->toDateString()) }}"
                        required
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >

                    @error('sale_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                </div>

            </div>


            {{-- ==========================================
                 PRODUCTS HEADER
            =========================================== --}}

            <div class="mt-7 flex items-center justify-between">

                <div>
                    <h2 class="font-semibold text-slate-800">
                        Products
                    </h2>

                    <p class="text-xs text-slate-500">
                        Add multiple products to this invoice.
                    </p>
                </div>

                <button
                    type="button"
                    id="addRow"
                    class="rounded-lg border border-blue-200 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50"
                >
                    + Add Product
                </button>

            </div>


            {{-- ==========================================
                 PRODUCT TABLE
            =========================================== --}}

            <div class="mt-3 overflow-x-auto rounded-lg border border-slate-200">

                <table class="min-w-[720px] w-full text-sm">

                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="p-3">Product Name</th>
                            <th class="p-3">Qty</th>
                            <th class="p-3">Unit</th>
                            <th class="p-3">Price / Qty</th>
                            <th class="p-3 text-right">Total</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>

                    <tbody id="itemsBody">

                        <tr class="item-row border-b">

                            <td class="p-2">
                                <input
                                    name="items[0][product_name]"
                                    value="{{ old('items.0.product_name') }}"
                                    required
                                    placeholder="Product name"
                                    class="w-full rounded-lg border-slate-300"
                                >
                            </td>

                            <td class="p-2">
                                <input
                                    name="items[0][quantity]"
                                    type="number"
                                    min="0.01"
                                    step="0.01"
                                    value="{{ old('items.0.quantity', 1) }}"
                                    required
                                    class="qty w-24 rounded-lg border-slate-300"
                                >
                            </td>

                            <td class="p-2">
                                <input
                                    name="items[0][unit]"
                                    required
                                    value="{{ old('items.0.unit', 'pcs') }}"
                                    class="w-20 rounded-lg border-slate-300"
                                >
                            </td>

                            <td class="p-2">
                                <input
                                    name="items[0][unit_price]"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    value="{{ old('items.0.unit_price', 0) }}"
                                    required
                                    placeholder="0.00"
                                    class="price w-32 rounded-lg border-slate-300"
                                >
                            </td>

                            <td class="p-2 text-right font-semibold line-total">
                                0.00
                            </td>

                            <td class="p-2">
                                <button type="button"
                                        class="remove-row text-red-600">
                                    Remove
                                </button>
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            {{-- ==========================================
                 GRAND TOTAL
            =========================================== --}}

            <div class="mt-5 flex justify-end">

                <div class="w-full max-w-xs rounded-lg bg-slate-50 p-4">

                    <div class="flex justify-between text-sm text-slate-500">
                        <span>Invoice Total</span>
                        <span>NPR</span>
                    </div>

                    <div class="mt-1 text-right text-2xl font-bold text-slate-800"
                         id="grandTotal">
                        0.00
                    </div>

                </div>

            </div>


            {{-- ==========================================
                 PAYMENT DETAILS
            =========================================== --}}

            <div class="mt-6 rounded-xl border border-slate-200 p-5">

                <h2 class="font-semibold text-slate-800">
                    💳 Payment Details
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Select how the customer is paying for this invoice.
                </p>

                {{-- PAYMENT TYPE --}}
                <div class="mt-4">

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Payment Type *
                    </label>

                    <select
                        name="payment_type"
                        id="paymentType"
                        required
                        class="w-full rounded-lg border border-slate-300 p-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >

                        <option value="cash" {{ old('payment_type', 'cash') == 'cash' ? 'selected' : '' }}>
                            Cash - Full Payment
                        </option>

                        <option value="online" {{ old('payment_type') == 'online' ? 'selected' : '' }}>
                            Online - Full Payment
                        </option>

                        <option value="partial" {{ old('payment_type') == 'partial' ? 'selected' : '' }}>
                            Partial Payment
                        </option>

                        <option value="credit" {{ old('payment_type') == 'credit' ? 'selected' : '' }}>
                            Credit - No Payment
                        </option>

                    </select>

                    @error('payment_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                </div>


                {{-- PAYMENT METHOD --}}
                <div id="paymentMethodSection" class="mt-4 hidden">

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Payment Method *
                    </label>

                    <select
                        name="payment_method"
                        id="paymentMethod"
                        class="w-full rounded-lg border border-slate-300 p-3 focus:border-blue-500 focus:outline-none"
                    >

                        <option value="">Select payment method</option>

                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>
                            Cash
                        </option>

                        <option value="esewa" {{ old('payment_method') == 'esewa' ? 'selected' : '' }}>
                            eSewa
                        </option>

                        <option value="khalti" {{ old('payment_method') == 'khalti' ? 'selected' : '' }}>
                            Khalti
                        </option>

                        <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>
                            Bank Transfer
                        </option>

                        <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>
                            Other
                        </option>

                    </select>

                    @error('payment_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                </div>


                {{-- PARTIAL PAYMENT AMOUNT --}}
                <div id="partialAmountSection" class="mt-4 hidden">

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Amount Received (NPR) *
                    </label>

                    <input
                        type="number"
                        name="amount_received"
                        id="amountReceived"
                        min="0.01"
                        step="0.01"
                        value="{{ old('amount_received') }}"
                        placeholder="Enter received amount"
                        class="w-full rounded-lg border border-slate-300 p-3 focus:border-blue-500 focus:outline-none"
                    >

                    @error('amount_received')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                </div>


                {{-- PAYMENT REFERENCE --}}
                <div id="paymentReferenceSection" class="mt-4 hidden">

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Transaction Reference (Optional)
                    </label>

                    <input
                        type="text"
                        name="payment_reference"
                        value="{{ old('payment_reference') }}"
                        placeholder="Transaction ID / Reference"
                        class="w-full rounded-lg border border-slate-300 p-3 focus:border-blue-500 focus:outline-none"
                    >

                </div>


                {{-- PAYMENT NOTES --}}
                <div id="paymentNotesSection" class="mt-4 hidden">

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Payment Notes (Optional)
                    </label>

                    <textarea
                        name="payment_notes"
                        rows="2"
                        placeholder="Enter payment details..."
                        class="w-full rounded-lg border border-slate-300 p-3 focus:border-blue-500 focus:outline-none"
                    >{{ old('payment_notes') }}</textarea>

                </div>


                {{-- PAYMENT SUMMARY --}}
                <div class="mt-5 rounded-lg bg-slate-50 p-4">

                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">
                            Invoice Total
                        </span>

                        <span class="font-semibold" id="paymentInvoiceTotal">
                            NPR 0.00
                        </span>
                    </div>

                    <div class="mt-3 flex justify-between text-sm">
                        <span class="text-green-700">
                            Amount Received
                        </span>

                        <span class="font-semibold text-green-700"
                              id="paymentReceived">
                            NPR 0.00
                        </span>
                    </div>

                    <div class="mt-3 flex justify-between border-t pt-3 text-lg font-bold">
                        <span>Remaining Credit</span>

                        <span id="remainingCredit" class="text-red-600">
                            NPR 0.00
                        </span>
                    </div>

                </div>

                <p class="mt-3 text-xs text-slate-500">
                    Only the unpaid amount will remain as customer credit.
                </p>

            </div>


            {{-- ==========================================
                 INVOICE NOTES
            =========================================== --}}

            <div class="mt-5">

                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Invoice Notes (Optional)
                </label>

                <textarea
                    name="notes"
                    rows="2"
                    placeholder="Enter invoice notes..."
                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                >{{ old('notes') }}</textarea>

            </div>


            {{-- ==========================================
                 FORM BUTTONS
            =========================================== --}}

            <div class="mt-6 flex justify-end gap-2">

                <a
                    href="{{ route('credits.index') }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm hover:bg-slate-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    id="saveInvoiceButton"
                    class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Save Invoice
                </button>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // CUSTOMER SEARCH
    // ==========================================

    const customerSearch = document.getElementById('customerSearch');
    const customerId = document.getElementById('customerId');
    const customerDropdown = document.getElementById('customerDropdown');
    const customerOptions = document.querySelectorAll('.customer-option');
    const noCustomerFound = document.getElementById('noCustomerFound');

    const selectedPreview = document.getElementById('selectedCustomerPreview');
    const selectedPhoto = document.getElementById('selectedCustomerPhoto');
    const selectedInitial = document.getElementById('selectedCustomerInitial');
    const selectedName = document.getElementById('selectedCustomerName');
    const selectedPhone = document.getElementById('selectedCustomerPhone');


    function openCustomerDropdown() {
        customerDropdown.classList.remove('hidden');
        filterCustomers();
    }


    function closeCustomerDropdown() {
        customerDropdown.classList.add('hidden');
    }


    function filterCustomers() {

        const search = customerSearch.value.toLowerCase().trim();

        let visibleCount = 0;

        customerOptions.forEach(option => {

            const searchText = option.dataset.search || '';

            const matches = searchText.includes(search);

            option.classList.toggle('hidden', !matches);

            if (matches) {
                visibleCount++;
            }

        });

        noCustomerFound.classList.toggle('hidden', visibleCount > 0);

    }


    function selectCustomer(option) {

        const id = option.dataset.id;
        const name = option.dataset.name;
        const phone = option.dataset.phone || 'No phone number';
        const photo = option.dataset.photo || '';

        customerId.value = id;
        customerSearch.value = name;

        selectedName.textContent = name;
        selectedPhone.textContent = phone;

        if (photo) {

            selectedPhoto.src = photo;
            selectedPhoto.classList.remove('hidden');
            selectedInitial.classList.add('hidden');

        } else {

            selectedPhoto.classList.add('hidden');

            selectedInitial.textContent = name.charAt(0).toUpperCase();
            selectedInitial.classList.remove('hidden');

        }

        selectedPreview.classList.remove('hidden');

        closeCustomerDropdown();

    }


    customerSearch.addEventListener('focus', openCustomerDropdown);
    customerSearch.addEventListener('click', openCustomerDropdown);

    customerSearch.addEventListener('input', function () {

        customerId.value = '';

        selectedPreview.classList.add('hidden');

        openCustomerDropdown();

    });


    customerOptions.forEach(option => {

        option.addEventListener('click', function () {
            selectCustomer(this);
        });

    });


    document.addEventListener('click', function (event) {

        if (
            !customerSearch.contains(event.target) &&
            !customerDropdown.contains(event.target)
        ) {
            closeCustomerDropdown();
        }

    });


    // ==========================================
    // PRODUCT ROW MANAGEMENT
    // ==========================================

    const body = document.getElementById('itemsBody');

    let rowIndex = 1;

    const money = n => Number(n || 0).toFixed(2);


    // ==========================================
    // PAYMENT ELEMENTS
    // ==========================================

    const paymentType = document.getElementById('paymentType');
    const paymentMethod = document.getElementById('paymentMethod');
    const amountReceived = document.getElementById('amountReceived');

    const paymentMethodSection = document.getElementById('paymentMethodSection');
    const partialAmountSection = document.getElementById('partialAmountSection');
    const paymentReferenceSection = document.getElementById('paymentReferenceSection');
    const paymentNotesSection = document.getElementById('paymentNotesSection');

    const paymentInvoiceTotal = document.getElementById('paymentInvoiceTotal');
    const paymentReceived = document.getElementById('paymentReceived');
    const remainingCredit = document.getElementById('remainingCredit');


    // ==========================================
    // PAYMENT CALCULATION
    // ==========================================

    function updatePaymentDetails(grand) {

        const type = paymentType.value;

        let received = 0;

        paymentMethodSection.classList.add('hidden');
        partialAmountSection.classList.add('hidden');
        paymentReferenceSection.classList.add('hidden');
        paymentNotesSection.classList.add('hidden');

        amountReceived.required = false;
        amountReceived.disabled = true;
        paymentMethod.required = false;

        if (type === 'cash') {

            received = grand;

        } else if (type === 'online') {

            received = grand;

            paymentMethodSection.classList.remove('hidden');
            paymentReferenceSection.classList.remove('hidden');
            paymentNotesSection.classList.remove('hidden');

            paymentMethod.required = true;

            if (
                !['esewa', 'khalti', 'bank', 'other'].includes(paymentMethod.value)
            ) {
                paymentMethod.value = 'esewa';
            }

        } else if (type === 'partial') {

            paymentMethodSection.classList.remove('hidden');
            partialAmountSection.classList.remove('hidden');
            paymentReferenceSection.classList.remove('hidden');
            paymentNotesSection.classList.remove('hidden');

            amountReceived.disabled = false;
            amountReceived.required = true;
            amountReceived.max = grand;

            paymentMethod.required = true;

            received = Math.min(
                Math.max(parseFloat(amountReceived.value) || 0, 0),
                grand
            );

        } else if (type === 'credit') {

            received = 0;

            paymentMethod.value = '';

        }

        const due = Math.max(0, grand - received);

        paymentInvoiceTotal.textContent = 'NPR ' + money(grand);

        paymentReceived.textContent = 'NPR ' + money(received);

        remainingCredit.textContent = 'NPR ' + money(due);

        if (due <= 0) {
            remainingCredit.classList.remove('text-red-600');
            remainingCredit.classList.add('text-green-700');
        } else {
            remainingCredit.classList.remove('text-green-700');
            remainingCredit.classList.add('text-red-600');
        }

    }


    // ==========================================
    // GRAND TOTAL CALCULATION
    // ==========================================

    function calculate() {

        let grand = 0;

        body.querySelectorAll('.item-row').forEach(row => {

            const qty = parseFloat(row.querySelector('.qty').value) || 0;

            const price = parseFloat(row.querySelector('.price').value) || 0;

            const total = qty * price;

            row.querySelector('.line-total').textContent = money(total);

            grand += total;

        });

        grand = Math.round((grand + Number.EPSILON) * 100) / 100;

        document.getElementById('grandTotal').textContent = money(grand);

        updatePaymentDetails(grand);

    }


    // ==========================================
    // ADD PRODUCT ROW
    // ==========================================

    function addRow() {

        const tr = document.createElement('tr');

        tr.className = 'item-row border-b';

        tr.innerHTML = `
            <td class="p-2">
                <input
                    name="items[${rowIndex}][product_name]"
                    required
                    placeholder="Product name"
                    class="w-full rounded-lg border-slate-300"
                >
            </td>

            <td class="p-2">
                <input
                    name="items[${rowIndex}][quantity]"
                    type="number"
                    min="0.01"
                    step="0.01"
                    required
                    value="1"
                    class="qty w-24 rounded-lg border-slate-300"
                >
            </td>

            <td class="p-2">
                <input
                    name="items[${rowIndex}][unit]"
                    required
                    value="pcs"
                    class="w-20 rounded-lg border-slate-300"
                >
            </td>

            <td class="p-2">
                <input
                    name="items[${rowIndex}][unit_price]"
                    type="number"
                    min="0"
                    step="0.01"
                    required
                    value="0"
                    class="price w-32 rounded-lg border-slate-300"
                >
            </td>

            <td class="p-2 text-right font-semibold line-total">
                0.00
            </td>

            <td class="p-2">
                <button type="button" class="remove-row text-red-600">
                    Remove
                </button>
            </td>
        `;

        body.appendChild(tr);

        rowIndex++;

        calculate();

    }


    document.getElementById('addRow').addEventListener('click', addRow);


    // ==========================================
    // PRODUCT INPUT EVENTS
    // ==========================================

    body.addEventListener('input', function (event) {

        if (event.target.matches('.qty, .price')) {
            calculate();
        }

    });


    // ==========================================
    // REMOVE PRODUCT ROW
    // ==========================================

    body.addEventListener('click', function (event) {

        if (event.target.matches('.remove-row')) {

            if (body.querySelectorAll('.item-row').length > 1) {

                event.target.closest('.item-row').remove();

                calculate();

            }

        }

    });


    // ==========================================
    // PAYMENT INPUT EVENTS
    // ==========================================

    paymentType.addEventListener('change', function () {
        calculate();
    });

    paymentMethod.addEventListener('change', function () {
        calculate();
    });

    amountReceived.addEventListener('input', function () {
        calculate();
    });


    // ==========================================
    // FORM VALIDATION
    // ==========================================

    document.getElementById('saleForm').addEventListener('submit', function (event) {

        if (!customerId.value) {

            event.preventDefault();

            customerSearch.focus();

            openCustomerDropdown();

            alert('Please select a customer.');

            return;
        }

        const grand = parseFloat(
            document.getElementById('grandTotal').textContent
        ) || 0;

        if (grand <= 0) {

            event.preventDefault();

            alert('Please add at least one product with a valid amount.');

            return;
        }

        if (paymentType.value === 'partial') {

            const received = parseFloat(amountReceived.value) || 0;

            if (received <= 0 || received > grand) {

                event.preventDefault();

                alert('Please enter a valid partial payment amount.');

                amountReceived.focus();

                return;
            }

            if (!paymentMethod.value) {

                event.preventDefault();

                alert('Please select a payment method.');

                paymentMethod.focus();

                return;
            }

        }

        if (paymentType.value === 'online' && !paymentMethod.value) {

            event.preventDefault();

            alert('Please select an online payment method.');

            paymentMethod.focus();

            return;
        }

        // Prevent accidental double submission.
        document.getElementById('saveInvoiceButton').disabled = true;

        document.getElementById('saveInvoiceButton').textContent = 'Saving...';

    });


    // ==========================================
    // INITIAL CALCULATION
    // ==========================================

    calculate();

});
</script>

@endpush
