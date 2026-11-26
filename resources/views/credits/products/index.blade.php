@extends('layouts.credit')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Products</h1>
            <p class="text-sm text-gray-500 mt-1">
                Manage and view all your products
            </p>
        </div>

        <button type="button"
                onclick="openAddModal()"
                class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
            + Add Product
        </button>
    </div>


    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif


    {{-- SUMMARY --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-sm text-gray-500">Total Products</p>
            <h2 class="text-2xl font-bold text-gray-800 mt-2">
                {{ $totalProducts }}
            </h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-sm text-gray-500">Products on Current Page</p>
            <h2 class="text-2xl font-bold text-gray-800 mt-2">
                {{ $products->count() }}
            </h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm border p-5">
            <p class="text-sm text-gray-500">Total Low Stock Products</p>
            <h2 class="text-2xl font-bold text-red-600 mt-2">
                {{ $lowStockProducts }}
            </h2>
        </div>

    </div>


    {{-- SEARCH --}}
    <div class="bg-white rounded-xl shadow-sm border p-4">

        <form method="GET"
              action="{{ route('credits.products.index') }}"
              class="flex flex-col sm:flex-row gap-3">

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search product name or SKU..."
                class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">

            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Search
            </button>

            <a href="{{ route('credits.products.index') }}"
               class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-center">
                Reset
            </a>

        </form>

    </div>


    {{-- PRODUCTS TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">

        <div class="p-5 border-b">
            <h2 class="font-semibold text-gray-800">All Products</h2>
        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Image</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">SKU</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Purchase Price</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Selling Price</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Stock (PCS)</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-100">

                    @forelse($products as $product)

                        <tr class="hover:bg-gray-50">

                            {{-- IMAGE --}}
                            <td class="px-5 py-3">

                                @if($product->image_path)
                                    <img
                                        src="{{ asset('storage/' . $product->image_path) }}"
                                        alt="{{ $product->name }}"
                                        onclick="openImageViewer(this.src)"
                                        class="w-14 h-14 rounded-lg object-cover border cursor-pointer hover:opacity-80"
                                        title="Click to enlarge">
                                @else
                                    <div class="w-14 h-14 rounded-lg bg-gray-100 border flex items-center justify-center text-2xl">
                                        📦
                                    </div>
                                @endif

                            </td>


                            {{-- NAME --}}
                            <td class="px-5 py-4">
                                <div class="font-medium text-gray-800">
                                    {{ $product->name }}
                                </div>
                            </td>


                            {{-- SKU --}}
                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $product->sku ?: '-' }}
                            </td>


                            {{-- PURCHASE PRICE --}}
                            <td class="px-5 py-4 text-sm text-right text-gray-700">
                                Rs. {{ number_format((float) $product->purchase_price, 2) }}
                            </td>


                            {{-- SELLING PRICE --}}
                            <td class="px-5 py-4 text-sm text-right font-medium text-gray-800">
                                Rs. {{ number_format((float) $product->selling_price, 2) }}
                            </td>


                            {{-- STOCK --}}
                            <td class="px-5 py-4 text-center">
                                <span class="font-semibold text-gray-800">
                                    {{ number_format((float) $product->stock, 2) }}
                                </span>
                                <span class="text-xs text-gray-500">PCS</span>
                            </td>


                            {{-- STATUS --}}
                            <td class="px-5 py-4 text-center">

                                @if($product->stock <= $product->low_stock)
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                        Low Stock
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                        In Stock
                                    </span>
                                @endif

                            </td>


                            {{-- ACTIONS --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center justify-center gap-3">

                                    <button
                                        type="button"
                                        onclick="viewProduct({{ $product->id }})"
                                        class="text-green-600 hover:text-green-800 font-semibold">
                                        View
                                    </button>

                                    <button
                                        type="button"
                                        onclick="openEditModal(@js($product))"
                                        class="text-blue-600 hover:text-blue-800 font-semibold">
                                        Edit
                                    </button>

                                    <form
                                        method="POST"
                                        action="{{ route('credits.products.destroy', $product->id) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this product?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="text-red-600 hover:text-red-800 font-semibold">
                                            Delete
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">

                                <div class="text-gray-400 text-4xl mb-3">📦</div>

                                <h3 class="font-semibold text-gray-700">
                                    No Products Found
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    Add your first product using the Add Product button.
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($products->hasPages())
            <div class="p-4 border-t">
                {{ $products->links() }}
            </div>
        @endif

    </div>

</div>


{{-- ADD / EDIT PRODUCT MODAL --}}
<div id="productModal"
     class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

        <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10">

            <h2 id="productModalTitle" class="text-xl font-bold text-gray-800">
                Add Product
            </h2>

            <button type="button"
                    onclick="closeProductModal()"
                    class="text-gray-500 hover:text-red-600 text-2xl">
                &times;
            </button>

        </div>


        <form id="productForm"
              method="POST"
              enctype="multipart/form-data"
              action="{{ route('credits.products.store') }}"
              class="p-5 space-y-4">

            @csrf

            <input type="hidden" name="_method" id="productMethod" value="POST">

            <input type="hidden" name="search" value="{{ $search }}">

            {{-- PRODUCT IMAGE --}}
            <div>

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Product Image
                </label>

                <div class="flex flex-col sm:flex-row gap-4 items-start">

                    <img
                        id="productImagePreview"
                        src=""
                        class="hidden w-28 h-28 object-cover rounded-lg border cursor-pointer"
                        onclick="openImageViewer(this.src)">

                    <div class="flex-1 w-full">

                        <input
                            type="file"
                            name="image"
                            id="productImage"
                            accept=".jpg,.jpeg,.png,.webp"
                            class="w-full border rounded-lg p-2.5 text-sm">

                        <p class="text-xs text-gray-500 mt-2">
                            JPG, JPEG, PNG or WEBP. Maximum size: 5 MB.
                        </p>

                    </div>

                </div>

            </div>


            {{-- NAME AND SKU --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Product Name *
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="productName"
                        required
                        maxlength="255"
                        class="w-full border rounded-lg p-2.5"
                        placeholder="Enter product name">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        SKU
                    </label>

                    <input
                        type="text"
                        name="sku"
                        id="productSku"
                        maxlength="255"
                        class="w-full border rounded-lg p-2.5"
                        placeholder="Optional SKU">
                </div>

            </div>


            {{-- UNIT --}}
            <div>

                <label class="block text-sm font-medium mb-1">
                    Unit *
                </label>

                <select
                    name="unit"
                    id="productUnit"
                    required
                    class="w-full border rounded-lg p-2.5">

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


            {{-- PRICES --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Purchase Price *
                    </label>

                    <input
                        type="number"
                        name="purchase_price"
                        id="productPurchasePrice"
                        min="0"
                        step="0.01"
                        required
                        class="w-full border rounded-lg p-2.5"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Selling Price *
                    </label>

                    <input
                        type="number"
                        name="selling_price"
                        id="productSellingPrice"
                        min="0"
                        step="0.01"
                        required
                        class="w-full border rounded-lg p-2.5"
                        placeholder="0.00">
                </div>

            </div>


            {{-- STOCK --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Stock (PCS) *
                    </label>

                    <input
                        type="number"
                        name="stock"
                        id="productStock"
                        min="0"
                        step="0.01"
                        required
                        class="w-full border rounded-lg p-2.5"
                        placeholder="0">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Low Stock Alert *
                    </label>

                    <input
                        type="number"
                        name="low_stock"
                        id="productLowStock"
                        min="0"
                        step="0.01"
                        required
                        class="w-full border rounded-lg p-2.5"
                        placeholder="5">
                </div>

            </div>


            {{-- BUTTONS --}}
            <div class="flex justify-end gap-3 pt-4 border-t">

                <button type="button"
                        onclick="closeProductModal()"
                        class="px-5 py-2.5 border rounded-lg hover:bg-gray-100">
                    Cancel
                </button>

                <button
                    type="submit"
                    id="saveProductButton"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                    Save Product
                </button>

            </div>

        </form>

    </div>

</div>


{{-- VIEW PRODUCT MODAL --}}
<div id="viewProductModal"
     class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

        <div class="flex justify-between items-center p-5 border-b">

            <h2 class="text-xl font-bold text-gray-800">
                Product Details
            </h2>

            <button type="button"
                    onclick="closeViewModal()"
                    class="text-gray-500 hover:text-red-600 text-2xl">
                &times;
            </button>

        </div>

        <div class="p-5 space-y-4">

            <div class="flex justify-center">
                <img id="viewProductImage"
                     src=""
                     class="hidden w-40 h-40 object-cover rounded-xl border cursor-pointer"
                     onclick="openImageViewer(this.src)">
            </div>

            <div id="viewNoImage"
                 class="w-40 h-40 mx-auto rounded-xl bg-gray-100 border flex items-center justify-center text-5xl">
                📦
            </div>

            <h3 id="viewProductName"
                class="text-xl font-bold text-center text-gray-800"></h3>

            <div class="grid grid-cols-2 gap-3">

                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">SKU</p>
                    <p id="viewProductSku" class="font-semibold"></p>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Unit</p>
                    <p id="viewProductUnit" class="font-semibold"></p>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Purchase Price</p>
                    <p id="viewPurchasePrice" class="font-semibold"></p>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Selling Price</p>
                    <p id="viewSellingPrice" class="font-semibold"></p>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Stock</p>
                    <p id="viewProductStock" class="font-semibold"></p>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500">Low Stock Alert</p>
                    <p id="viewLowStock" class="font-semibold"></p>
                </div>

            </div>

            <div class="text-xs text-gray-400 space-y-1">
                <p>Created: <span id="viewCreatedAt"></span></p>
                <p>Last Updated: <span id="viewUpdatedAt"></span></p>
            </div>

        </div>

    </div>

</div>


{{-- IMAGE LIGHTBOX --}}
<div id="imageViewer"
     class="hidden fixed inset-0 z-[70] bg-black/90 flex items-center justify-center p-4"
     onclick="closeImageViewer()">

    <button type="button"
            class="absolute top-5 right-7 text-white text-4xl">
        &times;
    </button>

    <img id="largeProductImage"
         src=""
         class="max-w-full max-h-[90vh] object-contain rounded-lg"
         onclick="event.stopPropagation()">

</div>


<script>

const productIndexUrl = @json(route('credits.products.index'));
const productStoreUrl = @json(route('credits.products.store'));
const productShowBaseUrl = @json(url('/credits/products'));


// ======================================
// ADD PRODUCT MODAL
// ======================================

function openAddModal() {

    const form = document.getElementById('productForm');

    form.reset();

    form.action = productStoreUrl;

    document.getElementById('productMethod').value = 'POST';

    document.getElementById('productModalTitle').textContent = 'Add Product';

    document.getElementById('saveProductButton').textContent = 'Save Product';

    document.getElementById('productImagePreview').classList.add('hidden');

    document.getElementById('productImagePreview').src = '';

    document.getElementById('productModal').classList.remove('hidden');

}


// ======================================
// EDIT PRODUCT MODAL
// ======================================

function openEditModal(product) {

    const form = document.getElementById('productForm');

    form.reset();

    form.action = `${productShowBaseUrl}/${product.id}`;

    document.getElementById('productMethod').value = 'PUT';

    document.getElementById('productModalTitle').textContent = 'Edit Product';

    document.getElementById('saveProductButton').textContent = 'Update Product';

    document.getElementById('productName').value = product.name || '';

    document.getElementById('productSku').value = product.sku || '';

    document.getElementById('productUnit').value = product.unit || 'pcs';

    document.getElementById('productPurchasePrice').value = product.purchase_price || 0;

    document.getElementById('productSellingPrice').value = product.selling_price || 0;

    document.getElementById('productStock').value = product.stock || 0;

    document.getElementById('productLowStock').value = product.low_stock || 0;

    const preview = document.getElementById('productImagePreview');

    if (product.image_path) {

        preview.src = `/storage/${product.image_path}`;

        preview.classList.remove('hidden');

    } else {

        preview.src = '';

        preview.classList.add('hidden');

    }

    document.getElementById('productModal').classList.remove('hidden');

}


function closeProductModal() {

    document.getElementById('productModal').classList.add('hidden');

}


// ======================================
// IMAGE PREVIEW BEFORE UPLOAD
// ======================================

document.getElementById('productImage').addEventListener('change', function () {

    const file = this.files[0];

    const preview = document.getElementById('productImagePreview');

    if (!file) return;

    const allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];

    if (!allowedTypes.includes(file.type)) {

        alert('Only JPG, JPEG, PNG and WEBP images are allowed.');

        this.value = '';

        return;

    }

    if (file.size > 5 * 1024 * 1024) {

        alert('Image size must not exceed 5 MB.');

        this.value = '';

        return;

    }

    preview.src = URL.createObjectURL(file);

    preview.classList.remove('hidden');

});


// ======================================
// VIEW PRODUCT
// ======================================

async function viewProduct(id) {

    try {

        const response = await fetch(`${productShowBaseUrl}/${id}`, {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Unable to load product details.');
        }

        const product = await response.json();

        document.getElementById('viewProductName').textContent = product.name;

        document.getElementById('viewProductSku').textContent = product.sku || '-';

        document.getElementById('viewProductUnit').textContent = product.unit;

        document.getElementById('viewPurchasePrice').textContent =
            'Rs. ' + Number(product.purchase_price).toFixed(2);

        document.getElementById('viewSellingPrice').textContent =
            'Rs. ' + Number(product.selling_price).toFixed(2);

        document.getElementById('viewProductStock').textContent =
            Number(product.stock).toFixed(2) + ' PCS';

        document.getElementById('viewLowStock').textContent =
            Number(product.low_stock).toFixed(2) + ' PCS';

        document.getElementById('viewCreatedAt').textContent =
            product.created_at || '-';

        document.getElementById('viewUpdatedAt').textContent =
            product.updated_at || '-';

        const image = document.getElementById('viewProductImage');

        const noImage = document.getElementById('viewNoImage');

        if (product.image_url) {

            image.src = product.image_url;

            image.classList.remove('hidden');

            noImage.classList.add('hidden');

        } else {

            image.src = '';

            image.classList.add('hidden');

            noImage.classList.remove('hidden');

        }

        document.getElementById('viewProductModal').classList.remove('hidden');

    } catch (error) {

        alert(error.message);

    }

}


function closeViewModal() {

    document.getElementById('viewProductModal').classList.add('hidden');

}


// ======================================
// IMAGE LIGHTBOX
// ======================================

function openImageViewer(src) {

    if (!src) return;

    document.getElementById('largeProductImage').src = src;

    document.getElementById('imageViewer').classList.remove('hidden');

}


function closeImageViewer() {

    document.getElementById('imageViewer').classList.add('hidden');

    document.getElementById('largeProductImage').src = '';

}


// ======================================
// CLOSE MODALS WITH ESCAPE KEY
// ======================================

document.addEventListener('keydown', function (event) {

    if (event.key === 'Escape') {

        closeProductModal();

        closeViewModal();

        closeImageViewer();

    }

});


// ======================================
// SUBMIT BUTTON PROTECTION
// ======================================

document.getElementById('productForm').addEventListener('submit', function () {

    const button = document.getElementById('saveProductButton');

    button.disabled = true;

    button.textContent = 'Saving...';

});

</script>

@endsection
