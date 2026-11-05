@extends('layouts.credit')

@section('content')
<div class="mx-auto max-w-3xl space-y-6 px-4 py-6">

    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Create Temporary Bill</h1>
            <p class="mt-1 text-sm text-gray-500">Start a temporary bill for a customer.</p>
        </div>

        <a href="{{ route('credits.temporary-sales.index') }}"
           class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50">
            Back to List
        </a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl border bg-white p-6 shadow-sm">
        <form action="{{ route('credits.temporary-sales.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="customer_id" class="mb-2 block text-sm font-semibold text-gray-700">
                    Select Customer <span class="text-red-500">*</span>
                </label>

                <select name="customer_id"
                        id="customer_id"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Choose customer</option>

                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}"
                            {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                            @if($customer->phone)
                                - {{ $customer->phone }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sale_date" class="mb-2 block text-sm font-semibold text-gray-700">
                    Bill Date <span class="text-red-500">*</span>
                </label>

                <input type="date"
                       name="sale_date"
                       id="sale_date"
                       value="{{ old('sale_date', date('Y-m-d')) }}"
                       required
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="notes" class="mb-2 block text-sm font-semibold text-gray-700">
                    Notes (Optional)
                </label>

                <textarea name="notes"
                          id="notes"
                          rows="3"
                          placeholder="Enter any notes..."
                          class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>

            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                <strong>Note:</strong> This is a temporary bill. It will not affect the customer's
                credit balance, payment history, or permanent invoices until you confirm it.
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('credits.temporary-sales.index') }}"
                   class="rounded-lg border border-gray-300 px-5 py-3 text-center text-sm font-semibold text-gray-600 hover:bg-gray-50">
                    Cancel
                </a>

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                    Create Temporary Bill
                </button>
            </div>
        </form>
    </div>
</div>
@endsection