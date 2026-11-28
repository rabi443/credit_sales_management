@extends('layouts.customer')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

    {{-- BACK BUTTON --}}
    <a href="{{ route('customer.products.index') }}"
       class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-semibold">
        ← Back to Products
    </a>

    {{-- MESSAGES --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-700 p-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-200 text-red-700 p-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- VALIDATION ERRORS --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-200 text-red-700 p-3 rounded-lg">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-5 md:p-8">

            {{-- PRODUCT IMAGE --}}
            <div class="bg-gray-100 rounded-xl overflow-hidden flex items-center justify-center min-h-80">

                @if($product->image_path)

                    <a href="{{ asset('storage/' . $product->image_path) }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       title="Open full image"
                       class="w-full h-96 flex items-center justify-center cursor-zoom-in">

                        <img src="{{ asset('storage/' . $product->image_path) }}"
                             alt="{{ $product->name }}"
                             class="w-full h-96 object-contain hover:scale-105 transition duration-300"
                             onerror="this.parentElement.style.display='none';">

                    </a>

                @else

                    <div class="text-center text-gray-400">
                        <div class="text-8xl">📦</div>
                        <p class="mt-3">No product image available</p>
                    </div>

                @endif

            </div>


            {{-- PRODUCT INFORMATION --}}
            <div class="space-y-5">

                {{-- NAME AND SKU --}}
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        {{ $product->name }}
                    </h1>

                    <p class="text-sm text-gray-500 mt-2">
                        Product SKU: {{ $product->sku ?: 'N/A' }}
                    </p>
                </div>


                {{-- PRICE --}}
                <div class="border-y border-gray-200 py-5">

                    <p class="text-sm text-gray-500">
                        Price
                    </p>

                    <div class="text-3xl font-bold text-indigo-700 mt-1">
                        Rs. {{ number_format($product->selling_price, 2) }}
                    </div>

                </div>


                {{-- STOCK INFORMATION --}}
                <div class="space-y-3">

                    <div class="flex justify-between text-sm">

                        <span class="text-gray-500">
                            Availability
                        </span>

                        @if($product->stock > 0)

                            <span class="text-green-600 font-semibold">
                                In Stock
                            </span>

                        @else

                            <span class="text-red-600 font-semibold">
                                Out of Stock
                            </span>

                        @endif

                    </div>


                    {{-- <div class="flex justify-between text-sm">

                        <span class="text-gray-500">
                            Available Quantity
                        </span>

                        <span class="font-semibold text-gray-800">
                            {{ $product->stock }} {{ $product->unit }}
                        </span>

                    </div> --}}


                    {{-- <div class="flex justify-between text-sm">

                        <span class="text-gray-500">
                            Unit
                        </span>

                        <span class="font-semibold text-gray-800">
                            {{ $product->unit }}
                        </span>

                    </div> --}}

                </div>


                {{-- ADD TO CART --}}
                @if($product->stock > 0)

                    <form action="{{ route('customer.cart.add', $product->id) }}"
                          method="POST"
                          class="space-y-4 border-t border-gray-200 pt-5">

                        @csrf

                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Select Quantity
                            </label>

                            <input type="number"
                                   name="quantity"
                                   value="1"
                                   min="1"
                                   max="{{ (int) $product->stock }}"
                                   required
                                   class="w-32 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">

                            <p class="text-xs text-gray-500 mt-1">
                                Maximum available: {{ $product->stock }}
                            </p>

                        </div>


                        <button type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold transition">

                            🛒 Add to Cart

                        </button>

                    </form>

                @else

                    <button disabled
                            class="w-full bg-gray-300 text-gray-500 py-3 rounded-xl font-bold cursor-not-allowed">

                        Out of Stock

                    </button>

                @endif


                {{-- VIEW CART --}}
                <a href="{{ route('customer.cart.index') }}"
                   class="block text-center border border-gray-300 hover:bg-gray-50 py-3 rounded-xl font-semibold text-gray-700 transition">

                    🛒 View Shopping Cart

                </a>


                {{-- CONTINUE SHOPPING --}}
                <a href="{{ route('customer.products.index') }}"
                   class="block text-center bg-gray-100 hover:bg-gray-200 py-3 rounded-xl font-semibold text-gray-700 transition">

                    Continue Shopping

                </a>

            </div>

        </div>

    </div>

</div>

@endsection
