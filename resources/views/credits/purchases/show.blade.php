@extends('layouts.credit')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Purchase Details</h1>
            <p class="text-sm text-gray-500 mt-1">
                Purchase #{{ $purchase->id }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('credits.purchases.index') }}"
               class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
                Back to Purchases
            </a>

            <button onclick="window.print()"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                Print
            </button>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green-100 text-green-800 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- Purchase Information --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:justify-between gap-6">

            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    Supplier Information
                </h2>

                <div class="space-y-2 text-sm">
                    <p>
                        <span class="font-medium text-gray-600">Supplier:</span>
                        <span class="text-gray-900">{{ $purchase->supplier_name }}</span>
                    </p>

                    <p>
                        <span class="font-medium text-gray-600">Invoice Number:</span>
                        <span class="text-gray-900">
                            {{ $purchase->invoice_no ?: 'N/A' }}
                        </span>
                    </p>

                    <p>
                        <span class="font-medium text-gray-600">Purchase Date:</span>
                        <span class="text-gray-900">
                            {{ $purchase->purchase_date->format('d M Y') }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="md:text-right">
                <p class="text-sm text-gray-500">Purchase Number</p>
                <h2 class="text-2xl font-bold text-gray-800">
                    #{{ $purchase->id }}
                </h2>

                <p class="text-sm text-gray-500 mt-3">Total Purchase Amount</p>
                <h2 class="text-3xl font-bold text-green-700">
                    Rs. {{ number_format($purchase->total_amount, 2) }}
                </h2>
            </div>

        </div>

        @if($purchase->notes)
            <div class="mt-6 pt-5 border-t border-gray-200">
                <h3 class="font-semibold text-gray-700 mb-2">Notes</h3>
                <p class="text-sm text-gray-600 whitespace-pre-line">
                    {{ $purchase->notes }}
                </p>
            </div>
        @endif
    </div>

    {{-- Purchased Items --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                Purchased Items
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Products included in this purchase
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Product</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Unit</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Unit Price</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($purchase->items as $item)
                        <tr>
                            <td class="px-5 py-4 text-sm text-gray-500">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-5 py-4 text-sm font-medium text-gray-800">
                                {{ $item->product_name }}
                            </td>

                            <td class="px-5 py-4 text-sm text-right text-gray-700">
                                {{ number_format((float) $item->quantity, 2) }}
                            </td>

                            <td class="px-5 py-4 text-sm text-gray-700">
                                {{ $item->unit }}
                            </td>

                            <td class="px-5 py-4 text-sm text-right text-gray-700">
                                Rs. {{ number_format((float) $item->unit_price, 2) }}
                            </td>

                            <td class="px-5 py-4 text-sm text-right font-semibold text-gray-800">
                                Rs. {{ number_format((float) $item->total_price, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                No purchase items found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-5 py-4 text-right font-bold text-gray-700">
                            Grand Total
                        </td>
                        <td class="px-5 py-4 text-right font-bold text-green-700">
                            Rs. {{ number_format((float) $purchase->total_amount, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Bill Images --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h2 class="text-lg font-semibold text-gray-800">
            Bill Images
        </h2>

        <p class="text-sm text-gray-500 mt-1 mb-5">
            Uploaded supplier bills and purchase receipts
        </p>

        @if($purchase->images->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($purchase->images as $image)
                    <a href="{{ asset('storage/' . $image->image_path) }}"
                       target="_blank"
                       class="block group">

                        <div class="border border-gray-200 rounded-lg overflow-hidden bg-gray-50">
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                 alt="Purchase bill"
                                 class="w-full h-64 object-contain group-hover:scale-105 transition duration-200">

                            <div class="px-3 py-2 text-center text-sm text-blue-600">
                                View Full Image
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="rounded-lg bg-gray-50 border border-dashed border-gray-300 p-8 text-center">
                <p class="text-gray-500">No bill images uploaded for this purchase.</p>
            </div>
        @endif
    </div>

</div>

{{-- Print Styling --}}
<style>
    @media print {
        body {
            background: white !important;
        }

        nav,
        aside,
        button,
        a[href*="purchases"] {
            display: none !important;
        }

        .shadow-sm {
            box-shadow: none !important;
        }

        .max-w-7xl {
            max-width: 100% !important;
        }

        @page {
            size: A4;
            margin: 12mm;
        }
    }
</style>
@endsection
