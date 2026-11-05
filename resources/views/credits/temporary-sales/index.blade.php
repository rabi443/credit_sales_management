@extends('layouts.credit')

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-6">

    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Temporary Sales</h1>
            <p class="mt-1 text-sm text-gray-500">
                Manage customers' temporary bills before creating permanent invoices.
            </p>
        </div>

        <a href="{{ route('credits.temporary-sales.create') }}"
           class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-blue-700">
            + New Temporary Bill
        </a>
    </div>

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

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Active Temporary Bills</p>
            <p class="mt-2 text-2xl font-bold text-gray-800">
                {{ $temporarySales->count() }}
            </p>
        </div>

        <div class="rounded-xl border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total Temporary Amount</p>
            <p class="mt-2 text-2xl font-bold text-amber-600">
                Rs. {{ number_format($temporarySales->sum('total_amount'), 2) }}
            </p>
        </div>

        <div class="rounded-xl border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total Products</p>
            <p class="mt-2 text-2xl font-bold text-gray-800">
                {{ $temporarySales->sum(fn($sale) => $sale->items->count()) }}
            </p>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
        <div class="flex flex-col justify-between gap-3 border-b px-5 py-4 sm:flex-row sm:items-center">
            <h2 class="font-semibold text-gray-800">Temporary Bill List</h2>

            <input type="text"
                   id="temporarySearch"
                   placeholder="Search customer, phone or bill..."
                   class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 sm:max-w-xs">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="temporaryTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Customer</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Phone</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Date</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-gray-500">Products</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-500">Total</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($temporarySales as $sale)
                        <tr class="temporary-row hover:bg-gray-50">
                            <td class="px-5 py-4 text-sm text-gray-500">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-semibold text-gray-800">
                                    {{ $sale->customer->name ?? 'Unknown Customer' }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    Temporary Bill #{{ $sale->id }}
                                </div>
                            </td>

                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $sale->customer->phone ?? '-' }}
                            </td>

                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}
                            </td>

                            <td class="px-5 py-4 text-center text-sm text-gray-600">
                                {{ $sale->items->count() }}
                            </td>

                            <td class="px-5 py-4 text-right font-semibold text-gray-800">
                                Rs. {{ number_format($sale->total_amount, 2) }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('credits.temporary-sales.edit', $sale->id) }}"
                                       class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                        Open
                                    </a>

                                    <form action="{{ route('credits.temporary-sales.destroy', $sale->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this temporary bill and all its items?')">
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
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="text-gray-400">
                                    <p class="text-lg font-semibold">No temporary bills found</p>
                                    <p class="mt-1 text-sm">Create a temporary bill to get started.</p>
                                </div>

                                <a href="{{ route('credits.temporary-sales.create') }}"
                                   class="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                    + Create Temporary Bill
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById('temporarySearch')?.addEventListener('input', function () {
    const search = this.value.toLowerCase();

    document.querySelectorAll('.temporary-row').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(search) ? '' : 'none';
    });
});
</script>
@endsection