@extends('layouts.credit')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Purchase Management</h1>
            <p class="text-sm text-gray-500">Create, manage and confirm purchase bills.</p>
        </div>

        <a href="{{ route('credits.purchases.index') }}"
           class="px-4 py-2 rounded-lg bg-gray-800 text-white text-center">
            Purchase History
        </a>
    </div>

    {{-- MESSAGES --}}
    <div id="messageBox" class="hidden rounded-lg p-3 text-sm"></div>

    {{-- @if(session('success'))
        <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif --}}


    {{-- CREATE NEW BILL --}}
    @if(!isset($purchase))

        <div class="bg-white rounded-xl shadow-sm border p-5">

            <h2 class="text-lg font-bold mb-4">Start New Purchase Bill</h2>

            <form id="startPurchaseForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium mb-1">Supplier Name *</label>
                    <input type="text" name="supplier_name" required
                           class="w-full border rounded-lg p-2.5"
                           placeholder="Enter supplier name">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Supplier Invoice Number</label>
                    <input type="text" name="invoice_no"
                           class="w-full border rounded-lg p-2.5"
                           placeholder="Optional invoice number">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Purchase Date *</label>
                    <input type="date" name="purchase_date" required
                           value="{{ date('Y-m-d') }}"
                           class="w-full border rounded-lg p-2.5">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Notes</label>
                    <input type="text" name="notes"
                           class="w-full border rounded-lg p-2.5"
                           placeholder="Optional notes">
                </div>

                <div class="md:col-span-2">
                    <button type="submit" id="startPurchaseButton"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">
                        Create Temporary Bill
                    </button>
                </div>

            </form>
        </div>

    @endif


    {{-- ACTIVE PURCHASE BILL --}}
    <div id="activePurchaseSection"
         class="{{ isset($purchase) ? '' : 'hidden' }} space-y-6">

        {{-- BILL INFORMATION --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">

            <div class="flex flex-col sm:flex-row sm:justify-between gap-3">

                <div>
                    <h2 class="text-lg font-bold text-blue-900">
                        Temporary Bill #
                        <span id="purchaseNumber">{{ $purchase->purchase_number ?? '' }}</span>
                    </h2>

                    <p class="text-sm text-blue-800 mt-1">
                        Supplier:
                        <strong id="supplierName">{{ $purchase->supplier_name ?? '' }}</strong>
                    </p>

                    <p class="text-sm text-blue-800">
                        Date:
                        <span id="purchaseDate">
                            {{ isset($purchase) ? $purchase->purchase_date->format('Y-m-d') : '' }}
                        </span>
                    </p>
                </div>

                <div class="text-left sm:text-right">
                    <p class="text-sm text-blue-700">Current Bill Total</p>
                    <p class="text-2xl font-bold text-blue-900">
                        Rs. <span id="billTotal">
                            {{ $purchase->total_amount ?? '0.00' }}
                        </span>
                    </p>
                </div>

            </div>
        </div>


        {{-- ADD PRODUCTS --}}
        <div class="bg-white rounded-xl shadow-sm border p-5">

            <h2 class="text-lg font-bold mb-4">Add Purchase Products</h2>

            <form id="addProductForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Select Existing Product</label>

                    <select id="productSelect" class="w-full border rounded-lg p-2.5">
                        <option value="">-- Select product or enter new product --</option>

                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-price="{{ $product->purchase_price }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Product Name *</label>
                    <input type="text" id="productName" required
                           class="w-full border rounded-lg p-2.5"
                           placeholder="Product name">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Quantity *</label>
                    <input type="number" id="quantity" min="0.01" step="0.01"
                           required class="w-full border rounded-lg p-2.5"
                           placeholder="Quantity">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Unit *</label>
                    <select id="unit" required class="w-full border rounded-lg p-2.5">
                        <option value="pcs">Pcs</option>
                        <option value="carton">Carton</option>
                        <option value="box">Box</option>
                        <option value="pack">Pack</option>
                        <option value="jar">Jar</option>
                        <option value="set">Set</option>
                        <option value="mala">Mala</option>
                        <option value="poka">Poka</option>
                    </select>
                </div>

                <div id="piecesPerPackBox" class="hidden">
                    <label class="block text-sm font-medium mb-1">Pieces Per Unit *</label>
                    <input type="number" id="piecesPerPack" min="0.01" step="0.01"
                           value="1" class="w-full border rounded-lg p-2.5">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Purchase Price Per Unit *</label>
                    <input type="number" id="unitPrice" min="0" step="0.01"
                           required class="w-full border rounded-lg p-2.5"
                           placeholder="Price">
                </div>

                <div class="md:col-span-3 flex flex-wrap items-center gap-3">
                    <button type="submit" id="addProductButton"
                            class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold">
                        + Add Product
                    </button>

                    <span class="text-sm text-gray-500">
                        Products are saved automatically to this temporary bill.
                    </span>
                </div>

            </form>
        </div>


        {{-- PRODUCT LIST --}}
        <div class="bg-white rounded-xl shadow-sm border p-5">

            <h2 class="text-lg font-bold mb-4">Products in Current Bill</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">

                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3">#</th>
                            <th class="p-3">Product</th>
                            <th class="p-3">Quantity</th>
                            <th class="p-3">Unit</th>
                            <th class="p-3">Pieces</th>
                            <th class="p-3">Price</th>
                            <th class="p-3">Total</th>
                            <th class="p-3 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="productTableBody">

                        @if(isset($purchase))
                            @foreach($purchase->items as $index => $item)
                                <tr id="item-row-{{ $item->id }}" class="border-b">
                                    <td class="p-3">{{ $index + 1 }}</td>
                                    <td class="p-3 font-medium">{{ $item->product_name }}</td>
                                    <td class="p-3">{{ $item->quantity }}</td>
                                    <td class="p-3">{{ $item->unit }}</td>
                                    <td class="p-3">{{ $item->stock_quantity }}</td>
                                    <td class="p-3">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="p-3 font-semibold">{{ number_format($item->total_price, 2) }}</td>

                                    <td class="p-3 text-center whitespace-nowrap">
                                        <button type="button"
                                                onclick="editItem({{ $item->id }})"
                                                class="text-blue-600 font-semibold mr-2">
                                            Edit
                                        </button>

                                        <button type="button"
                                                onclick="deleteItem({{ $item->id }})"
                                                class="text-red-600 font-semibold">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                    </tbody>
                </table>
            </div>

            <div id="emptyProducts"
                 class="{{ isset($purchase) && $purchase->items->count() ? 'hidden' : '' }} text-center text-gray-400 py-8">
                No products added yet.
            </div>

            <div class="border-t mt-4 pt-4 flex justify-end">
                <div class="text-right">
                    <p class="text-gray-500 text-sm">Total Purchase Amount</p>
                    <p class="text-2xl font-bold text-gray-900">
                        Rs. <span id="bottomTotal">
                            {{ number_format($purchase->total_amount ?? 0, 2) }}
                        </span>
                    </p>
                </div>
            </div>

        </div>


        {{-- SUPPLIER BILL IMAGES --}}
        <div class="bg-white rounded-xl shadow-sm border p-5">

            <h2 class="text-lg font-bold mb-3">Supplier Bill Images</h2>

            <p class="text-sm text-gray-500 mb-4">
                Select images to preview them. They will upload only when you save the bill.
            </p>

            <div id="imageInputs" class="space-y-3">

                <div class="image-input-row flex items-center gap-3">

                    <input
                        type="file"
                        name="images[]"
                        form="saveTemporaryForm"
                        class="bill-image-input block w-full text-sm text-gray-600 border border-gray-300 rounded-lg p-2"
                        accept=".jpg,.jpeg,.png,.webp">

                    <button type="button"
                            class="remove-image-btn px-3 py-2 bg-red-100 text-red-700 rounded-lg text-sm">
                        Remove
                    </button>

                </div>

            </div>

            <button type="button" id="addMoreImageButton"
                    class="mt-3 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded-lg text-sm">
                + Add More Image
            </button>

            {{-- SELECTED IMAGE PREVIEWS --}}
            <div id="imagePreview" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4"></div>

            {{-- ALREADY SAVED IMAGES --}}
            @if(isset($purchase) && $purchase->images->count())
                <div class="mt-5">
                    <h3 class="font-semibold text-gray-700 mb-3">Previously Saved Images</h3>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($purchase->images as $image)
                            <div class="border rounded-lg p-2">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     class="w-full h-28 object-cover rounded">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>


        {{-- SAVE PURCHASE BILL --}}
        <div class="bg-white rounded-xl shadow-sm border p-5">

            <h2 class="text-lg font-bold mb-4">Save Purchase Bill</h2>

            <div class="flex flex-wrap gap-3">

                @if(isset($purchase) && $purchase)

                    {{-- NORMAL FORM SUBMISSION --}}
                    <form method="POST"
                          id="saveTemporaryForm"
                          enctype="multipart/form-data"
                          action="{{ route('credits.purchases.drafts.save', $purchase->id) }}">

                        @csrf

                        <button type="submit" id="saveTemporaryButton"
                                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">
                            Save Temporary Bill
                        </button>

                    </form>

                @endif

                <button type="button" id="savePermanentButton"
                        onclick="confirmPurchase()"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold">
                    Save Permanently
                </button>

            </div>

            <p class="text-xs text-gray-500 mt-3">
                Save Temporary Bill keeps this bill for later. Save Permanently
                records the purchase and updates product stock.
            </p>

        </div>

    </div>


    {{-- SAVED TEMPORARY PURCHASE BILLS --}}
    <div class="bg-white rounded-xl shadow-sm border p-5">

        <h2 class="text-lg font-bold mb-4">Saved Temporary Purchase Bills</h2>

        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Bill No.</th>
                        <th class="p-3">Supplier</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Products</th>
                        <th class="p-3">Total</th>
                        <th class="p-3 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody id="temporaryBillsBody">

                    @forelse($temporaryPurchases as $temp)

                        <tr id="draft-row-{{ $temp->id }}" class="border-b">

                            <td class="p-3 font-semibold">#{{ $temp->purchase_number }}</td>
                            <td class="p-3">{{ $temp->supplier_name }}</td>
                            <td class="p-3">{{ $temp->purchase_date->format('Y-m-d') }}</td>
                            <td class="p-3">{{ $temp->items->count() }}</td>

                            <td class="p-3 font-semibold">
                                Rs. {{ number_format($temp->total_amount, 2) }}
                            </td>

                            <td class="p-3 text-center whitespace-nowrap">

                                <button type="button"
                                        onclick="openDraft({{ $temp->id }})"
                                        class="text-blue-600 font-semibold mr-3">
                                    Open
                                </button>

                                <button type="button"
                                        onclick="deleteDraft({{ $temp->id }})"
                                        class="text-red-600 font-semibold">
                                    Delete
                                </button>

                            </td>
                        </tr>

                    @empty

                        <tr id="noDraftsRow">
                            <td colspan="6" class="p-6 text-center text-gray-400">
                                No temporary purchase bills found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- EDIT PRODUCT MODAL --}}
<div id="editModal"
     class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-xl p-6">

        <div class="flex justify-between items-center mb-5">
            <h2 class="text-xl font-bold">Edit Purchase Product</h2>

            <button type="button" onclick="closeEditModal()"
                    class="text-gray-500 text-2xl">
                &times;
            </button>
        </div>

        <form id="editProductForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <input type="hidden" id="editItemId">

            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Product Name</label>
                <input type="text" id="editProductName" required
                       class="w-full border rounded-lg p-2.5">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Quantity</label>
                <input type="number" id="editQuantity" min="0.01" step="0.01"
                       required class="w-full border rounded-lg p-2.5">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Unit</label>
                <select id="editUnit" class="w-full border rounded-lg p-2.5">
                    <option value="pcs">Pcs</option>
                    <option value="carton">Carton</option>
                    <option value="box">Box</option>
                    <option value="pack">Pack</option>
                    <option value="jar">Jar</option>
                    <option value="set">Set</option>
                    <option value="mala">Mala</option>
                    <option value="poka">Poka</option>
                </select>
            </div>

            <div id="editPiecesBox">
                <label class="block text-sm font-medium mb-1">Pieces Per Unit</label>
                <input type="number" id="editPieces" min="0.01" step="0.01"
                       class="w-full border rounded-lg p-2.5">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Purchase Price</label>
                <input type="number" id="editPrice" min="0" step="0.01"
                       required class="w-full border rounded-lg p-2.5">
            </div>

            <div class="md:col-span-2 flex justify-end gap-3">

                <button type="button" onclick="closeEditModal()"
                        class="px-5 py-2 border rounded-lg">
                    Cancel
                </button>

                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg">
                    Update Product
                </button>

            </div>

        </form>

    </div>
</div>


<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const draftBaseUrl = @json(url('/credits/purchases/drafts'));

let activePurchaseId = @json($purchase->id ?? null);

let purchaseItems = @json(
    isset($purchase) ? $purchase->items->values() : []
);


// ========================================
// COMMON FUNCTIONS
// ========================================

function showMessage(message, type = 'success') {

    const box = document.getElementById('messageBox');

    box.textContent = message;

    box.className = 'rounded-lg p-3 text-sm ' +
        (type === 'success'
            ? 'bg-green-100 text-green-800'
            : 'bg-red-100 text-red-800');

    box.classList.remove('hidden');

    setTimeout(() => box.classList.add('hidden'), 5000);
}


async function sendRequest(url, method = 'GET', body = null) {

    const options = {
        method,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    };

    if (body instanceof FormData) {
        options.body = body;
    } else if (body !== null) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(body);
    }

    const response = await fetch(url, options);

    const data = await response.json();

    if (!response.ok) {

        const errors = data.errors
            ? Object.values(data.errors).flat().join(' ')
            : data.message || 'Something went wrong.';

        throw new Error(errors);
    }

    return data;
}


function money(value) {
    return Number(value || 0).toFixed(2);
}


function updateTotals(total) {

    document.getElementById('billTotal').textContent = money(total);
    document.getElementById('bottomTotal').textContent = money(total);

}


// ========================================
// UNIT FIELDS
// ========================================

function updateUnitFields() {

    const unit = document.getElementById('unit').value;

    document.getElementById('piecesPerPackBox')
        .classList.toggle('hidden', unit === 'pcs');

    if (unit === 'pcs') {
        document.getElementById('piecesPerPack').value = 1;
    }
}


function updateEditUnitFields() {

    const unit = document.getElementById('editUnit').value;

    document.getElementById('editPiecesBox')
        .classList.toggle('hidden', unit === 'pcs');

    if (unit === 'pcs') {
        document.getElementById('editPieces').value = 1;
    }
}


document.getElementById('unit').addEventListener('change', updateUnitFields);
document.getElementById('editUnit').addEventListener('change', updateEditUnitFields);


document.getElementById('productSelect').addEventListener('change', function () {

    const option = this.options[this.selectedIndex];

    if (!this.value) return;

    document.getElementById('productName').value = option.dataset.name || '';
    document.getElementById('unitPrice').value = option.dataset.price || 0;

});


// ========================================
// CREATE TEMPORARY BILL
// ========================================

const startForm = document.getElementById('startPurchaseForm');

if (startForm) {

    startForm.addEventListener('submit', async function (event) {

        event.preventDefault();

        const button = document.getElementById('startPurchaseButton');

        button.disabled = true;
        button.textContent = 'Creating...';

        try {

            const formData = new FormData(this);

            const data = await sendRequest(
                draftBaseUrl,
                'POST',
                formData
            );

            activePurchaseId = data.purchase.id;

            window.location.href = draftBaseUrl + '/' + activePurchaseId;

        } catch (error) {

            showMessage(error.message, 'error');

        } finally {

            button.disabled = false;
            button.textContent = 'Create Temporary Bill';

        }

    });

}


// ========================================
// ADD PRODUCT WITHOUT REFRESH
// ========================================

const addProductForm = document.getElementById('addProductForm');

if (addProductForm) {

    addProductForm.addEventListener('submit', async function (event) {

        event.preventDefault();

        if (!activePurchaseId) {
            showMessage('Please create a temporary purchase bill first.', 'error');
            return;
        }

        const button = document.getElementById('addProductButton');

        button.disabled = true;
        button.textContent = 'Adding...';

        const selectedProduct = document.getElementById('productSelect').value;

        const payload = {
            product_id: selectedProduct || null,
            product_name: document.getElementById('productName').value,
            quantity: document.getElementById('quantity').value,
            unit: document.getElementById('unit').value,
            pieces_per_pack: document.getElementById('piecesPerPack').value || 1,
            unit_price: document.getElementById('unitPrice').value
        };

        try {

            const data = await sendRequest(
                `${draftBaseUrl}/${activePurchaseId}/items`,
                'POST',
                payload
            );

            purchaseItems.push(data.item);

            renderProducts();
            updateTotals(data.total_amount);

            this.reset();

            document.getElementById('piecesPerPack').value = 1;

            updateUnitFields();

            showMessage(data.message);

        } catch (error) {

            showMessage(error.message, 'error');

        } finally {

            button.disabled = false;
            button.textContent = '+ Add Product';

        }

    });

}


// ========================================
// RENDER PRODUCT TABLE
// ========================================

function renderProducts() {

    const tbody = document.getElementById('productTableBody');
    const empty = document.getElementById('emptyProducts');

    if (!tbody || !empty) return;

    tbody.innerHTML = '';

    empty.classList.toggle('hidden', purchaseItems.length > 0);

    purchaseItems.forEach((item, index) => {

        const row = document.createElement('tr');

        row.id = `item-row-${item.id}`;
        row.className = 'border-b';

        const values = [
            index + 1,
            item.product_name,
            item.quantity,
            item.unit,
            item.stock_quantity,
            money(item.unit_price),
            money(item.total_price)
        ];

        values.forEach(value => {

            const cell = document.createElement('td');

            cell.className = 'p-3';
            cell.textContent = value;

            row.appendChild(cell);

        });

        const actionCell = document.createElement('td');

        actionCell.className = 'p-3 text-center whitespace-nowrap';

        const editButton = document.createElement('button');

        editButton.type = 'button';
        editButton.textContent = 'Edit';
        editButton.className = 'text-blue-600 font-semibold mr-3';
        editButton.onclick = () => editItem(item.id);

        const deleteButton = document.createElement('button');

        deleteButton.type = 'button';
        deleteButton.textContent = 'Delete';
        deleteButton.className = 'text-red-600 font-semibold';
        deleteButton.onclick = () => deleteItem(item.id);

        actionCell.append(editButton, deleteButton);

        row.appendChild(actionCell);
        tbody.appendChild(row);

    });

}


// ========================================
// EDIT PRODUCT
// ========================================

function editItem(id) {

    const item = purchaseItems.find(
        product => Number(product.id) === Number(id)
    );

    if (!item) return;

    document.getElementById('editItemId').value = item.id;
    document.getElementById('editProductName').value = item.product_name;
    document.getElementById('editQuantity').value = item.quantity;
    document.getElementById('editUnit').value = item.unit;
    document.getElementById('editPieces').value = item.pieces_per_pack;
    document.getElementById('editPrice').value = item.unit_price;

    updateEditUnitFields();

    document.getElementById('editModal').classList.remove('hidden');

}


function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}


document.getElementById('editProductForm').addEventListener('submit', async function (event) {

    event.preventDefault();

    const id = document.getElementById('editItemId').value;

    const payload = {
        product_name: document.getElementById('editProductName').value,
        quantity: document.getElementById('editQuantity').value,
        unit: document.getElementById('editUnit').value,
        pieces_per_pack: document.getElementById('editPieces').value || 1,
        unit_price: document.getElementById('editPrice').value
    };

    try {

        const data = await sendRequest(
            `${draftBaseUrl}/${activePurchaseId}/items/${id}`,
            'PUT',
            payload
        );

        const index = purchaseItems.findIndex(
            item => Number(item.id) === Number(id)
        );

        if (index !== -1) {
            purchaseItems[index] = data.item;
        }

        renderProducts();
        updateTotals(data.total_amount);

        closeEditModal();

        showMessage(data.message);

    } catch (error) {

        showMessage(error.message, 'error');

    }

});


// ========================================
// DELETE PRODUCT
// ========================================

async function deleteItem(id) {

    if (!confirm('Are you sure you want to delete this product?')) return;

    try {

        const data = await sendRequest(
            `${draftBaseUrl}/${activePurchaseId}/items/${id}`,
            'DELETE'
        );

        purchaseItems = purchaseItems.filter(
            item => Number(item.id) !== Number(id)
        );

        renderProducts();
        updateTotals(data.total_amount);

        showMessage(data.message);

    } catch (error) {

        showMessage(error.message, 'error');

    }

}


// ========================================
// OPEN TEMPORARY BILL
// ========================================

function openDraft(id) {
    window.location.href = draftBaseUrl + '/' + id;
}


// ========================================
// DELETE TEMPORARY BILL
// ========================================

async function deleteDraft(id) {

    if (!confirm('Delete this entire temporary purchase bill?')) return;

    try {

        const data = await sendRequest(
            `${draftBaseUrl}/${id}`,
            'DELETE'
        );

        document.getElementById(`draft-row-${id}`)?.remove();

        showMessage(data.message);

        if (activePurchaseId && Number(activePurchaseId) === Number(id)) {
            window.location.href = @json(route('credits.purchases.create'));
        }

    } catch (error) {

        showMessage(error.message, 'error');

    }

}


// ========================================
// IMAGE SELECTION AND PREVIEW
// ========================================

const imageInputs = document.getElementById('imageInputs');
const imagePreview = document.getElementById('imagePreview');
const addMoreImageButton = document.getElementById('addMoreImageButton');


// Create preview for selected files
function renderSelectedImages() {

    imagePreview.innerHTML = '';

    const rows = imageInputs.querySelectorAll('.image-input-row');

    rows.forEach(row => {

        const input = row.querySelector('.bill-image-input');

        if (!input.files || !input.files.length) return;

        Array.from(input.files).forEach((file, fileIndex) => {

            if (!file.type.startsWith('image/')) return;

            const previewCard = document.createElement('div');

            previewCard.className = 'border rounded-lg p-2';

            const image = document.createElement('img');

            image.src = URL.createObjectURL(file);
            image.className = 'w-full h-28 object-cover rounded';

            const fileName = document.createElement('p');

            fileName.className = 'text-xs text-gray-600 mt-2 truncate';
            fileName.textContent = file.name;

            previewCard.append(image, fileName);

            imagePreview.appendChild(previewCard);

        });

    });

}


// Add more image input
addMoreImageButton?.addEventListener('click', function () {

    const row = document.createElement('div');

    row.className = 'image-input-row flex items-center gap-3';

    row.innerHTML = `
        <input
            type="file"
            name="images[]"
            form="saveTemporaryForm"
            class="bill-image-input block w-full text-sm text-gray-600 border border-gray-300 rounded-lg p-2"
            accept=".jpg,.jpeg,.png,.webp">

        <button type="button"
                class="remove-image-btn px-3 py-2 bg-red-100 text-red-700 rounded-lg text-sm">
            Remove
        </button>
    `;

    imageInputs.appendChild(row);

});


// Preview selected images only. No upload here.
imageInputs?.addEventListener('change', function (event) {

    if (!event.target.classList.contains('bill-image-input')) return;

    const files = Array.from(event.target.files || []);

    for (const file of files) {

        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {

            showMessage('Only JPG, JPEG, PNG and WEBP images are allowed.', 'error');

            event.target.value = '';
            renderSelectedImages();

            return;
        }

        if (file.size > 5 * 1024 * 1024) {

            showMessage('Each image must be smaller than 5 MB.', 'error');

            event.target.value = '';
            renderSelectedImages();

            return;
        }

    }

    renderSelectedImages();

});


// Remove selected image input
imageInputs?.addEventListener('click', function (event) {

    const button = event.target.closest('.remove-image-btn');

    if (!button) return;

    const rows = imageInputs.querySelectorAll('.image-input-row');

    // Keep at least one input field
    if (rows.length === 1) {

        const input = rows[0].querySelector('.bill-image-input');

        input.value = '';

    } else {

        button.closest('.image-input-row').remove();

    }

    renderSelectedImages();

});


// Collect selected images for permanent purchase upload
function getSelectedImageFiles() {

    const files = [];

    imageInputs.querySelectorAll('.bill-image-input').forEach(input => {

        Array.from(input.files || []).forEach(file => {
            files.push(file);
        });

    });

    return files;

}


// Upload selected images only when permanently confirming
async function uploadSelectedImages() {

    const files = getSelectedImageFiles();

    if (!files.length) return;

    const formData = new FormData();

    files.forEach(file => {
        formData.append('images[]', file);
    });

    await sendRequest(
        `${draftBaseUrl}/${activePurchaseId}/images`,
        'POST',
        formData
    );

}


// ========================================
// SAVE PERMANENT PURCHASE
// ========================================

async function confirmPurchase() {

    if (!activePurchaseId) {
        showMessage('No active purchase bill.', 'error');
        return;
    }

    if (!purchaseItems.length) {
        showMessage('Please add at least one product.', 'error');
        return;
    }

    if (!confirm('Confirm this purchase permanently? Product stock will be updated.')) {
        return;
    }

    const button = document.getElementById('savePermanentButton');

    button.disabled = true;
    button.textContent = 'Saving...';

    try {

        // Upload selected images first
        await uploadSelectedImages();

        // Then confirm purchase
        const data = await sendRequest(
            `${draftBaseUrl}/${activePurchaseId}/confirm`,
            'POST'
        );

        showMessage(data.message);

        window.location.href = data.redirect_url;

    } catch (error) {

        showMessage(error.message, 'error');

    } finally {

        button.disabled = false;
        button.textContent = 'Save Permanently';

    }

}


// ========================================
// INITIAL DISPLAY
// ========================================

renderProducts();
updateUnitFields();
renderSelectedImages();

</script>

@endsection
