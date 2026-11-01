@extends('layouts.customer')

@section('title', 'My Profile')

@section('content')

<div class="mx-auto max-w-4xl">

    {{-- PAGE HEADER --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">
            My Profile
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            View your personal information and manage your account security.
        </p>
    </div>


    {{-- PROFILE INFORMATION --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="bg-slate-900 px-6 py-8 text-white">

            <div class="flex flex-col items-center gap-4 sm:flex-row">

                {{-- PROFILE PHOTO --}}
                @if($customer->photo)

                    <a
                        href="{{ \Illuminate\Support\Facades\Storage::url($customer->photo) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        title="View full photo"
                    >
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::url($customer->photo) }}"
                            alt="{{ $customer->name }}"
                            class="h-24 w-24 rounded-full border-4 border-white object-cover"
                        >
                    </a>

                @else

                    <div class="flex h-24 w-24 items-center justify-center rounded-full border-4 border-white bg-blue-600 text-3xl font-bold">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>

                @endif

                {{-- CUSTOMER NAME --}}
                <div class="text-center sm:text-left">

                    <h2 class="text-2xl font-bold">
                        {{ $customer->name }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-300">
                        Customer Account
                    </p>

                </div>

            </div>

        </div>


        {{-- PERSONAL DETAILS --}}
        <div class="p-6">

            <h3 class="mb-5 text-lg font-bold text-slate-800">
                Personal Information
            </h3>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                {{-- NAME --}}
                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Full Name
                    </p>

                    <p class="mt-1 font-medium text-slate-800">
                        {{ $customer->name }}
                    </p>
                </div>


                {{-- PHONE --}}
                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Phone Number
                    </p>

                    <p class="mt-1 font-medium text-slate-800">
                        {{ $customer->phone ?: 'Not provided' }}
                    </p>
                </div>


                {{-- ADDRESS --}}
                <div class="sm:col-span-2">

                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Address
                    </p>

                    <p class="mt-1 font-medium text-slate-800">
                        {{ $customer->address ?: 'Not provided' }}
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- CHANGE PASSWORD --}}
    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <h3 class="text-lg font-bold text-slate-800">
                🔐 Change Password
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Update your password to keep your account secure.
            </p>

        </div>


        <div class="p-6">

            @if(session('success'))
                <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif


            <form
                action="{{ route('customer.password.update') }}"
                method="POST"
                class="space-y-5"
            >

                @csrf
                @method('PUT')


                {{-- CURRENT PASSWORD --}}
                <div>

                    <label for="current_password"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Current Password
                    </label>

                    <input
                        type="password"
                        name="current_password"
                        id="current_password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        placeholder="Enter current password"
                    >

                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- NEW PASSWORD --}}
                <div>

                    <label for="password"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        New Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        placeholder="Enter new password"
                    >

                    @error('password')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- CONFIRM PASSWORD --}}
                <div>

                    <label for="password_confirmation"
                           class="mb-2 block text-sm font-semibold text-slate-700">
                        Confirm New Password
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        placeholder="Confirm new password"
                    >

                </div>


                {{-- SUBMIT --}}
                <div class="border-t border-slate-100 pt-5">

                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        🔒 Update Password
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- BACK TO DASHBOARD --}}
    <div class="mt-6">

        <a
            href="{{ route('customer.dashboard') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-800"
        >
            ← Back to Dashboard
        </a>

    </div>

</div>

@endsection