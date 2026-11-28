@extends('layouts.customer')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Shopping Cart</h1>
            <p class="text-sm text-gray-500 mt-1">
                Review your selected products.
            </p>
        </div>

        <a href="{{ route('customer.products.index') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl font-semibold text-center">
            Continue Shopping
        </a>
    </div>

    {{-- MESSAGES --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @php
        $totalQuantity = 0;
    @endphp

    @forelse($products as $product)

        @php
            $quantity = (int) ($cart[$product->id]['quantity'] ?? 0);
            $lineTotal = $product->selling_price * $quantity;
            $totalQuantity += $quantity;
        @endphp

    @empty
    @endforelse

    @if($products->isEmpty())

        <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center">
            <div class="text-7xl mb-4">🛒</div>

            <h2 class="text-xl font-bold text-gray-800">
                Your cart is empty
            </h2>

            <p class="text-gray-500 mt-2">
                You haven't added any products yet.
            </p>

            <a href="{{ route('customer.products.index') }}"
               class="inline-block mt-5 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold">
                Explore Products
            </a>
        </div>

    @else

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- CART PRODUCTS --}}
            <div class="lg:col-span-2 space-y-4">

                @foreach($products as $product)

                    @php
                        $quantity = (int) ($cart[$product->id]['quantity'] ?? 0);
                        $lineTotal = $product->selling_price * $quantity;
                    @endphp

                    <div class="bg-white border border-gray-200 rounded-2xl p-4">

                        <div class="flex flex-col sm:flex-row gap-4">

                            {{-- IMAGE --}}
                            <div class="w-full sm:w-32 h-32 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">

                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover"
                                        onerror="this.style.display='none';">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-5xl">
                                        📦
                                    </div>
                                @endif

                            </div>

                            {{-- DETAILS --}}
                            <div class="flex-1 space-y-3">

                                <div class="flex justify-between gap-3">
                                    <div>
                                        <h3 class="font-bold text-gray-800">
                                            {{ $product->name }}
                                        </h3>

                                        {{-- <p class="text-xs text-gray-500 mt-1">
                                            SKU: {{ $product->sku ?: 'N/A' }}
                                        </p> --}}
                                    </div>

                                    <div class="text-right">
                                        <p class="text-sm text-gray-500">Unit Price</p>
                                        <p class="font-semibold text-indigo-700">
                                            Rs. {{ number_format($product->selling_price, 2) }}
                                        </p>
                                    </div>
                                </div>

                                {{-- UPDATE QUANTITY --}}
                                <div class="flex flex-wrap justify-between items-end gap-3">

                                    <form action="{{ route('customer.cart.update', $product->id) }}"
                                          method="POST"
                                          class="flex items-end gap-2">

                                        @csrf
                                        @method('PUT')

                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">
                                                Quantity 
                                                {{-- ({{ $product->unit }}) --}}
                                            </label>

                                            <input type="number"
                                                   name="quantity"
                                                   value="{{ $quantity }}"
                                                   min="1"
                                                   max="{{ (int) $product->stock }}"
                                                   required
                                                   class="w-24 border border-gray-300 rounded-lg px-3 py-2">
                                        </div>

                                        <button type="submit"
                                                class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-lg text-sm">
                                            Update
                                        </button>

                                    </form>

                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">Subtotal</p>
                                        <p class="font-bold text-gray-800">
                                            Rs. {{ number_format($lineTotal, 2) }}
                                        </p>
                                    </div>

                                </div>

                                {{-- REMOVE --}}
                                <form action="{{ route('customer.cart.remove', $product->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Remove this product from cart?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="text-red-600 hover:text-red-800 text-sm font-semibold">
                                        Remove
                                    </button>

                                </form>

                            </div>
                        </div>

                    </div>

                @endforeach

            </div>

            {{-- ORDER SUMMARY --}}
            <div class="lg:col-span-1">

                <div class="bg-white border border-gray-200 rounded-2xl p-5 sticky top-5 space-y-5">

                    <h2 class="text-lg font-bold text-gray-800">
                        Cart Summary
                    </h2>

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total Items</span>
                        <span class="font-semibold">{{ $totalQuantity }}</span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-semibold">
                            Rs. {{ number_format($subtotal, 2) }}
                        </span>
                    </div>

                    <div class="border-t border-gray-200 pt-4 flex justify-between">
                        <span class="font-bold text-gray-800">Total</span>
                        <span class="text-xl font-bold text-indigo-700">
                            Rs. {{ number_format($subtotal, 2) }}
                        </span>
                    </div>

                    {{-- DISABLED CHECKOUT --}}
                    <button type="button"
                            disabled
                            class="w-full bg-gray-300 text-gray-500 py-3 rounded-xl font-bold cursor-not-allowed">
                        Checkout (Coming Soon)
                    </button>

                    <p class="text-xs text-center text-gray-400">
                        Checkout is currently unavailable.
                    </p>

                    <a href="{{ route('customer.products.index') }}"
                       class="block text-center text-indigo-600 hover:text-indigo-800 font-semibold text-sm">
                        Continue Shopping
                    </a>

                </div>

            </div>

        </div>

    @endif

</div>

@endsection
