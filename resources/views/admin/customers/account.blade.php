
@extends('layouts.credit')

@section('title', 'Customer Login Account')

@section('content')

<div class="mx-auto max-w-xl">

    <div class="mb-6">
        <a href="{{ route('credits.customers.show', $customer) }}"
           class="text-sm font-medium text-blue-600 hover:underline">
            ← Back to Customer
        </a>

        <h1 class="mt-3 text-2xl font-bold text-slate-800">
            Customer Login Account
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Create or reset the customer's portal password.
        </p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="mb-5 rounded-lg bg-slate-50 p-4">
            <p class="font-semibold text-slate-800">
                {{ $customer->name }}
            </p>

            <p class="mt-1 text-sm text-slate-500">
                Phone: {{ $customer->phone }}
            </p>

            <p class="mt-2 text-xs text-slate-500">
                Login URL: {{ url('/customer/login') }}
            </p>
        </div>

        <form method="POST"
              action="{{ route('credits.customers.account.update', $customer) }}">

            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="mb-2 block text-sm font-semibold">
                    New Password
                </label>

                <input
                    type="password"
                    name="password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    placeholder="Enter at least 8 characters">
            </div>

            <div class="mb-5">
                <label class="mb-2 block text-sm font-semibold">
                    Confirm Password
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    placeholder="Re-enter password">
            </div>

            <button type="submit"
                    class="w-full rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700">
                Save Customer Password
            </button>

        </form>

    </div>

</div>

@endsection
