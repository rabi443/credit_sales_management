@extends('layouts.credit')

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Temporary Bill #{{ $temporarySale->id }}</h1>

            <p class="mt-1 text-sm text-gray-500">
                Customer:
                <span class="font-semibold text-gray-800">
                    {{ $temporarySale->customer->name ?? 'Unknown Customer' }}
                </span>

                @if($temporarySale->customer?->phone)
                    | {{ $temporarySale->customer->phone }}
                @endif
            </p>
        </div>

        <a href="{{ route('credits.temporary-sales.index') }}"
           class="rounded-lg border border-gray-300 px-4 py-2 text-center text-sm font-semibold text-gray-600 hover:bg-gray-50">
            Back to Temporary List
        </a>
    </div>

    {{-- MESSAGES --}}
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CUSTOMER INFORMATION --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-xl border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Customer</p>
            <p class="mt-2 text-lg font-bold text-gray-800">
                {{ $temporarySale->customer->name ?? '-' }}
            </p>
        </div>

        <div class="rounded-xl border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Bill Date</p>
            <p class="mt-2 text-lg font-bold text-gray-800">
                {{ \Carbon\Carbon::parse($temporarySale->sale_date)->format('d M Y') }}
            </p>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <p class="text-sm text-amber-700">Temporary Total</p>
            <p class="mt-2 text-2xl font-bold text-amber-700">
                Rs. {{ number_format($temporarySale->total_amount, 2) }}
            </p>
        </div>
    </div>

    {{-- ADD PRODUCT --}}
    <div class="rounded-xl border bg-white p-5 shadow-sm">
        <div class="mb-5">
            <h2 class="text-lg font-bold text-gray-800">Add Product</h2>
            <p class="mt-1 text-sm text-gray-500">
                Add products as the customer selects them.
            </p>
        </div>

        <form action="{{ route('credits.temporary-sales.items.store', $temporarySale->id) }}"
              method="POST"
              id="addItemForm"
              class="grid grid-cols-1 gap-4 md:grid-cols-12">
            @csrf

            <div class="md:col-span-4">
                <label class="mb-1 block text-sm font-semibold text-gray-700">
                    Product Name *
                </label>

                <input type="text"
                       name="product_name"
                       required
                       placeholder="Enter product name"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold text-gray-700">
                    Quantity *
                </label>

                <input type="number"
                       name="quantity"
                       id="add_quantity"
                       min="0.01"
                       step="0.01"
                       value="1"
                       required
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold text-gray-700">
                    Unit
                </label>

                <input type="text"
                       name="unit"
                       placeholder="pcs, kg, packet"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold text-gray-700">
                    Rate *
                </label>

                <input type="number"
                       name="unit_price"
                       id="add_unit_price"
                       min="0"
                       step="0.01"
                       required
                       placeholder="0.00"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold text-gray-700">
                    Line Total
                </label>

                <input type="text"
                       id="add_line_total"
                       readonly
                       value="0.00"
                       class="w-full rounded-lg border-gray-200 bg-gray-100 font-semibold text-gray-700">
            </div>

            <div class="md:col-span-12">
                <button type="submit"
                        class="rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                    + Add Product
                </button>
            </div>
        </form>
    </div>

    {{-- PRODUCT LIST --}}
    <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
        <div class="flex items-center justify-between border-b px-5 py-4">
            <div>
                <h2 class="font-bold text-gray-800">Temporary Bill Products</h2>
                <p class="mt-1 text-xs text-gray-500">
                    Update or remove products before checkout.
                </p>
            </div>

            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                {{ $temporarySale->items->count() }} Products
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Product</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-500">Quantity</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Unit</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Rate</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($temporarySale->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 text-sm text-gray-500">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-4 py-4">
                                <span class="font-semibold text-gray-800">
                                    {{ $item->product_name }}
                                </span>
                            </td>

                            <td class="px-4 py-4 text-center text-sm text-gray-700">
                                {{ number_format($item->quantity, 2) }}
                            </td>

                            <td class="px-4 py-4 text-sm text-gray-600">
                                {{ $item->unit ?: '-' }}
                            </td>

                            <td class="px-4 py-4 text-right text-sm text-gray-700">
                                {{ number_format($item->unit_price, 2) }}
                            </td>

                            <td class="px-4 py-4 text-right font-semibold text-gray-800">
                                {{ number_format($item->line_total, 2) }}
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex justify-center gap-2">
                                    <button type="button"
                                            onclick="openEditItem(
                                                {{ $item->id }},
                                                @js($item->product_name),
                                                {{ $item->quantity }},
                                                @js($item->unit),
                                                {{ $item->unit_price }}
                                            )"
                                            class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-100">
                                        Edit
                                    </button>

                                    <form action="{{ route('credits.temporary-sales.items.destroy', [$temporarySale->id, $item->id]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Remove this product from the temporary bill?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-500">
                                No products added yet. Use the form above to add products.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-right font-bold text-gray-700">
                            Grand Total
                        </td>

                        <td class="px-4 py-4 text-right text-lg font-bold text-blue-700">
                            Rs. {{ number_format($temporarySale->total_amount, 2) }}
                        </td>

                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- EDIT PRODUCT MODAL --}}
    <div id="editItemModal"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">

        <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">Edit Product</h3>

                <button type="button"
                        onclick="closeEditItem()"
                        class="text-2xl text-gray-400 hover:text-gray-700">
                    &times;
                </button>
            </div>

            <form id="editItemForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Product Name</label>
                    <input type="text"
                           name="product_name"
                           id="edit_product_name"
                           required
                           class="w-full rounded-lg border-gray-300">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Quantity</label>
                        <input type="number"
                               name="quantity"
                               id="edit_quantity"
                               min="0.01"
                               step="0.01"
                               required
                               class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Unit</label>
                        <input type="text"
                               name="unit"
                               id="edit_unit"
                               class="w-full rounded-lg border-gray-300">
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Rate</label>
                    <input type="number"
                           name="unit_price"
                           id="edit_unit_price"
                           min="0"
                           step="0.01"
                           required
                           class="w-full rounded-lg border-gray-300">
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button"
                            onclick="closeEditItem()"
                            class="rounded-lg border px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                        Cancel
                    </button>

                    <button type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                        Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- CHECKOUT / CONFIRM TEMPORARY BILL --}}
    <div class="rounded-xl border border-green-200 bg-white p-5 shadow-sm">
        <div class="mb-5">
            <h2 class="text-lg font-bold text-gray-800">Checkout & Create Permanent Invoice</h2>
            <p class="mt-1 text-sm text-gray-500">
                Confirm this temporary bill when the customer finishes shopping.
            </p>
        </div>

        @if($temporarySale->items->count() > 0)
            <form action="{{ route('credits.sales.store') }}"
                  method="POST"
                  id="confirmTemporaryForm"
                  class="space-y-5"
                  onsubmit="return confirm('Confirm this temporary bill and create a permanent invoice?')">

                @csrf

                <input type="hidden" name="temporary_sale_id" value="{{ $temporarySale->id }}">
                <input type="hidden" name="customer_id" value="{{ $temporarySale->customer_id }}">
                <input type="hidden" name="sale_date" value="{{ $temporarySale->sale_date }}">

                @if($temporarySale->notes)
                    <input type="hidden" name="notes" value="{{ $temporarySale->notes }}">
                @endif

                {{-- Send product rows to the existing permanent sales controller --}}
                @foreach($temporarySale->items as $index => $item)
                    <input type="hidden" name="items[{{ $index }}][product_name]" value="{{ $item->product_name }}">
                    <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}">
                    <input type="hidden" name="items[{{ $index }}][unit]" value="{{ $item->unit }}">
                    <input type="hidden" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}">
                @endforeach

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Payment Type <span class="text-red-500">*</span>
                        </label>

                        <select name="payment_type"
                                id="payment_type"
                                required
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="credit">Credit / Udhar</option>
                            <option value="cash">Full Cash Payment</option>
                            <option value="online">Full Online Payment</option>
                            <option value="partial">Partial Payment</option>
                        </select>
                    </div>

                    <div id="amountReceivedWrapper" class="hidden">
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Amount Received
                        </label>

                        <input type="number"
                               name="amount_received"
                               id="amount_received"
                               min="0"
                               step="0.01"
                               value="0"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div id="paymentMethodWrapper" class="hidden">
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Payment Method
                        </label>

                        <select name="payment_method"
                                id="payment_method"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Method</option>
                            <option value="cash">Cash</option>
                            <option value="esewa">eSewa</option>
                            <option value="khalti">Khalti</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div id="referenceWrapper" class="hidden">
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Transaction Reference
                        </label>

                        <input type="text"
                               name="reference"
                               placeholder="Transaction ID (optional)"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Payment Notes (Optional)
                        </label>

                        <textarea name="payment_notes"
                                  rows="2"
                                  placeholder="Payment details..."
                                  class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-green-800">Bill Total</span>
                        <span class="text-xl font-bold text-green-800">
                            Rs. {{ number_format($temporarySale->total_amount, 2) }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('credits.temporary-sales.index') }}"
                       class="rounded-lg border border-gray-300 px-5 py-3 text-center text-sm font-semibold text-gray-600 hover:bg-gray-50">
                        Keep as Temporary
                    </a>

                    <button type="submit"
                            class="rounded-lg bg-green-600 px-6 py-3 text-sm font-bold text-white shadow hover:bg-green-700">
                        Confirm & Create Invoice
                    </button>
                </div>
            </form>
        @else
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                Add at least one product before confirming this temporary bill.
            </div>
        @endif
    </div>
</div>

<script>
/* =====================================
   ADD PRODUCT LINE TOTAL PREVIEW
===================================== */
const addQuantity = document.getElementById('add_quantity');
const addUnitPrice = document.getElementById('add_unit_price');
const addLineTotal = document.getElementById('add_line_total');

function calculateAddLineTotal() {
    const quantity = parseFloat(addQuantity?.value) || 0;
    const price = parseFloat(addUnitPrice?.value) || 0;

    if (addLineTotal) {
        addLineTotal.value = (quantity * price).toFixed(2);
    }
}

addQuantity?.addEventListener('input', calculateAddLineTotal);
addUnitPrice?.addEventListener('input', calculateAddLineTotal);


/* =====================================
   EDIT PRODUCT MODAL
===================================== */
const editModal = document.getElementById('editItemModal');
const editForm = document.getElementById('editItemForm');

function openEditItem(id, name, quantity, unit, price) {
    document.getElementById('edit_product_name').value = name;
    document.getElementById('edit_quantity').value = quantity;
    document.getElementById('edit_unit').value = unit || '';
    document.getElementById('edit_unit_price').value = price;

    editForm.action =
        @js(url('/credits/temporary-sales/' . $temporarySale->id . '/items')) + '/' + id;

    editModal.classList.remove('hidden');
    editModal.classList.add('flex');
}

function closeEditItem() {
    editModal.classList.add('hidden');
    editModal.classList.remove('flex');
}

editModal?.addEventListener('click', function(event) {
    if (event.target === editModal) {
        closeEditItem();
    }
});


/* =====================================
   PAYMENT TYPE HANDLING
===================================== */
const paymentType = document.getElementById('payment_type');
const amountWrapper = document.getElementById('amountReceivedWrapper');
const methodWrapper = document.getElementById('paymentMethodWrapper');
const referenceWrapper = document.getElementById('referenceWrapper');
const amountInput = document.getElementById('amount_received');
const methodInput = document.getElementById('payment_method');

function updatePaymentFields() {
    const type = paymentType.value;

    amountWrapper.classList.add('hidden');
    methodWrapper.classList.add('hidden');
    referenceWrapper.classList.add('hidden');

    amountInput.required = false;
    methodInput.required = false;

    if (type === 'credit') {
        amountInput.value = '0';
    }

    if (type === 'cash') {
        amountInput.value = {{ (float) $temporarySale->total_amount }};
        methodInput.value = 'cash';
    }

    if (type === 'online') {
        amountInput.value = {{ (float) $temporarySale->total_amount }};
        amountWrapper.classList.remove('hidden');
        methodWrapper.classList.remove('hidden');
        referenceWrapper.classList.remove('hidden');

        methodInput.required = true;
    }

    if (type === 'partial') {
        amountInput.value = '';
        amountWrapper.classList.remove('hidden');
        methodWrapper.classList.remove('hidden');
        referenceWrapper.classList.remove('hidden');

        amountInput.required = true;
        methodInput.required = true;
    }
}

paymentType?.addEventListener('change', updatePaymentFields);

updatePaymentFields();
</script>
@endsection