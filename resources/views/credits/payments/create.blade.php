
@extends('layouts.credit')

@section('title', 'Receive Payment')

@section('content')

<div class="mx-auto max-w-2xl">

    {{-- Back Button --}}
    <a href="{{ route('credits.customers.show', $customer) }}"
       class="text-sm font-medium text-blue-700 hover:text-blue-900">
        ← Back to {{ $customer->name }}
    </a>

    <div class="mt-4 overflow-hidden rounded-xl bg-white shadow-sm">

        {{-- Header --}}
        <div class="border-b border-slate-200 p-6">
            <h1 class="text-xl font-bold text-slate-800">
                Receive Customer Payment
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Record a payment received from this customer.
            </p>
        </div>

        <div class="p-6">

            {{-- Customer Information --}}
            <div class="mb-6 rounded-lg bg-slate-50 p-4">

                <div class="mb-3">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                        Customer
                    </p>

                    <p class="mt-1 text-lg font-bold text-slate-800">
                        {{ $customer->name }}
                    </p>

                    @if($customer->phone)
                        <p class="text-sm text-slate-500">
                            {{ $customer->phone }}
                        </p>
                    @endif
                </div>

                <div class="flex justify-between border-t border-slate-200 pt-3 text-sm">
                    <span class="text-slate-600">
                        Total  Transaction Amount
                    </span>

                    <strong class="text-slate-800">
                        Rs. {{ number_format($customer->total_credit, 2) }}
                    </strong>
                </div>

                <div class="mt-2 flex justify-between text-sm">
                    <span class="text-slate-600">
                        Total payments so far
                    </span>

                    <strong class="text-green-700">
                        Rs. {{ number_format($customer->total_paid, 2) }}
                    </strong>
                </div>

                <div class="mt-3 flex justify-between border-t border-slate-200 pt-3">
                    <span class="font-semibold text-slate-700">
                        Current Outstanding Due Balance
                    </span>

                    <strong class="text-lg {{ $customer->balance > 0 ? 'text-red-600' : 'text-green-700' }}">
                        Rs. {{ number_format($customer->balance, 2) }}
                    </strong>
                </div>

            </div>

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4">
                    <p class="mb-2 font-semibold text-red-700">
                        Please correct the following errors:
                    </p>

                    <ul class="list-inside list-disc space-y-1 text-sm text-red-600">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Payment Form --}}
            <form method="POST"
                  action="{{ route('credits.payments.store') }}"
                  class="space-y-5">

                @csrf

                <input type="hidden"
                       name="customer_id"
                       value="{{ $customer->id }}">

                {{-- Payment Date --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Payment Date *
                    </label>

                    <input
                        type="date"
                        name="payment_date"
                        value="{{ old('payment_date', now()->toDateString()) }}"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >

                    @error('payment_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Amount --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Payment Amount (Rs.) *
                    </label>

                    <input
                        type="number"
                        name="amount"
                        min="0.01"
                        max="999999999"
                        step="0.01"
                        value="{{ old('amount') }}"
                        placeholder="Enter payment amount"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-3 text-lg font-semibold focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >

                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <p class="mt-1 text-xs text-slate-500">
                        You can receive a partial payment or a full payment.
                        Extra payment will be kept as customer advance.
                    </p>
                </div>

                {{-- Payment Method --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Payment Method *
                    </label>

                    <select
                        name="payment_method"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                        <option value="cash"
                            {{ old('payment_method', 'cash') == 'cash' ? 'selected' : '' }}>
                            Cash
                        </option>

                        <option value="esewa"
                            {{ old('payment_method') == 'esewa' ? 'selected' : '' }}>
                            eSewa
                        </option>

                        <option value="khalti"
                            {{ old('payment_method') == 'khalti' ? 'selected' : '' }}>
                            Khalti
                        </option>

                        <option value="bank"
                            {{ old('payment_method') == 'bank' ? 'selected' : '' }}>
                            Bank Transfer
                        </option>

                        <option value="other"
                            {{ old('payment_method') == 'other' ? 'selected' : '' }}>
                            Other
                        </option>
                    </select>

                    @error('payment_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Reference --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Reference Number (Optional)
                    </label>

                    <input
                        type="text"
                        name="reference"
                        value="{{ old('reference') }}"
                        placeholder="Transaction ID or reference number"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >

                    @error('reference')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        Notes (Optional)
                    </label>

                    <textarea
                        name="notes"
                        rows="3"
                        placeholder="Enter additional payment details..."
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >{{ old('notes') }}</textarea>

                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Allocation Information --}}
                <div class="rounded-lg border border-blue-100 bg-blue-50 p-4">

                    <div class="flex gap-2">
                        <span class="text-blue-700">ℹ</span>

                        <div>
                            <p class="text-sm font-semibold text-blue-900">
                                Automatic Payment Allocation
                            </p>

                            <p class="mt-1 text-xs leading-5 text-blue-800">
                                Payment will automatically be applied to the
                                customer's oldest unpaid invoices first.
                                If the payment exceeds the outstanding balance,
                                the remaining amount will be saved as customer
                                advance and can be used for future credit sales.
                            </p>
                        </div>
                    </div>

                </div>

                {{-- Buttons --}}
                <div class="flex flex-col-reverse gap-2 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">

                    <a
                        href="{{ route('credits.customers.show', $customer) }}"
                        class="rounded-lg border border-slate-300 px-5 py-2.5 text-center text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700"
                    >
                        Save Payment
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endsection
