@extends('layouts.customer')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Our Products
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Explore our products and add your favourite items to cart.
            </p>
        </div>

        <a href="{{ route('customer.cart.index') }}"
           class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-xl font-semibold transition">

            🛒 View Cart

            <span class="bg-white text-indigo-700 rounded-full px-2 py-0.5 text-xs">
                {{ collect(session('customer_cart', []))->sum('quantity') }}
            </span>

        </a>

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-700 p-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif


    {{-- ERROR MESSAGE --}}
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


    {{-- SEARCH --}}
    <form method="GET"
          action="{{ route('customer.products.index') }}"
          class="flex flex-col sm:flex-row gap-3">

        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Search products by name or SKU..."
               class="flex-1 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none">

        <button type="submit"
                class="bg-gray-800 hover:bg-black text-white px-6 py-3 rounded-xl font-semibold transition">
            🔍 Search
        </button>

        @if(request('search'))
            <a href="{{ route('customer.products.index') }}"
               class="bg-gray-200 hover:bg-gray-300 px-5 py-3 rounded-xl text-center font-semibold">
                Clear
            </a>
        @endif

    </form>


    {{-- PRODUCT COUNT --}}
    <div class="flex items-center justify-between">

        <h2 class="text-lg font-bold text-gray-800">
            All Products
        </h2>

        <span class="text-sm text-gray-500">
            {{ $products->total() }} products found
        </span>

    </div>


    {{-- PRODUCT GRID --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">

        @forelse($products as $product)

            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300">


                {{-- PRODUCT IMAGE: CLICK TO OPEN FULL IMAGE --}}
                <div class="bg-gray-100 overflow-hidden group relative">

                    @if($product->image_path)

                        <a href="{{ asset('storage/' . $product->image_path) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           title="Open full image"
                           class="block cursor-zoom-in">

                            <img src="{{ asset('storage/' . $product->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-44 object-cover transition duration-300 group-hover:scale-110"
                                 loading="lazy">

                        </a>

                    @else

                        <div class="w-full h-44 flex items-center justify-center text-gray-400">

                            <div class="text-center">
                                <div class="text-5xl">📦</div>

                                <p class="text-xs mt-2">
                                    No image available
                                </p>
                            </div>

                        </div>

                    @endif

                </div>


                {{-- PRODUCT INFORMATION --}}
                <div class="p-3 space-y-2">


                    {{-- PRODUCT NAME --}}
                    <a href="{{ route('customer.products.show', $product->id) }}"
                       class="block font-semibold text-gray-800 hover:text-indigo-600 line-clamp-2 min-h-12">

                        {{ $product->name }}

                    </a>


                    {{-- SELLING PRICE --}}
                    <div class="text-xl font-bold text-indigo-700">
                        Rs. {{ number_format($product->selling_price, 2) }}
                    </div>


                    {{-- SKU --}}
                    {{-- <p class="text-xs text-gray-500">
                        SKU: {{ $product->sku ?: 'N/A' }}
                    </p> --}}


                    {{-- STOCK STATUS --}}
                    @if($product->stock > 0)

                        <p class="text-xs text-green-600 font-semibold">

                            <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-1"></span>

                            In Stock
                            {{-- {{ $product->stock }}  --}}
                            {{-- {{ $product->unit }} --}}

                        </p>

                    @else

                        <p class="text-xs text-red-600 font-semibold">

                            <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-1"></span>

                            Out of Stock

                        </p>

                    @endif


                    {{-- ACTION BUTTONS --}}
                    <div class="grid grid-cols-2 gap-2 pt-2">


                        {{-- VIEW DETAILS --}}
                        <a href="{{ route('customer.products.show', $product->id) }}"
                           class="text-center border border-indigo-600 text-indigo-600 hover:bg-indigo-50 py-2 rounded-lg text-xs font-semibold transition">

                            View Details

                        </a>


                        {{-- ADD TO CART --}}
                        @if($product->stock > 0)

                            <form action="{{ route('customer.cart.add', $product->id) }}"
                                  method="POST">

                                @csrf

                                <input type="hidden"
                                       name="quantity"
                                       value="1">

                                <button type="submit"
                                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg text-xs font-semibold transition">

                                    🛒 Add to Cart

                                </button>

                            </form>

                        @else

                            <button disabled
                                    class="bg-gray-300 text-gray-500 py-2 rounded-lg text-xs font-semibold cursor-not-allowed">

                                Unavailable

                            </button>

                        @endif

                    </div>

                </div>

            </div>

        @empty

            {{-- NO PRODUCTS --}}
            <div class="col-span-full bg-white border border-gray-200 rounded-xl p-10 text-center">

                <div class="text-5xl mb-3">
                    🔍
                </div>

                <h3 class="text-lg font-bold text-gray-700">
                    No products found
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Try searching with another product name.
                </p>

                @if(request('search'))

                    <a href="{{ route('customer.products.index') }}"
                       class="inline-block mt-4 text-indigo-600 font-semibold hover:underline">

                        Show All Products

                    </a>

                @endif

            </div>

        @endforelse

    </div>


    {{-- PAGINATION --}}
    @if($products->hasPages())

        <div class="pt-4">
            {{ $products->links() }}
        </div>

    @endif

</div>

@endsection
