@extends('layouts.credit')
@section('title', 'Add Customer')
@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('credits.index') }}" class="text-sm font-medium text-blue-700">← Back to customers</a>
    <div class="mt-4 rounded-xl bg-white p-6 shadow-sm">
        <h1 class="text-xl font-bold">Add New Customer</h1>
        <p class="mt-1 text-sm text-slate-500">Save customer details before recording credit sales.</p>
        <form method="POST" action="{{ route('credits.customers.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
            @csrf
            <div><label class="mb-1 block text-sm font-medium">Customer Name *</label><input name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"></div>
            <div><label class="mb-1 block text-sm font-medium">Phone Number</label><input name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"></div>
            <div><label class="mb-1 block text-sm font-medium">Address</label><textarea name="address" rows="3" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('address') }}</textarea></div>
            <div class="mt-4">
                <label class="mb-2 block text-sm font-medium text-slate-700">
                    Customer Photo
                </label>

                <input
                    type="file"
                    name="photo"
                    accept="image/*"
                    class="w-full rounded-lg border border-slate-300 p-2 text-sm"
                >

                @error('photo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end gap-2"><a href="{{ route('credits.index') }}" class="rounded-lg border px-4 py-2.5 text-sm">Cancel</a><button class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Save Customer</button></div>
        </form>
    </div>
</div>
@endsection
