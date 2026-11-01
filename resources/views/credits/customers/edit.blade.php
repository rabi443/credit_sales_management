@extends('layouts.credit')

@section('title', 'Edit Customer')

@section('content')

<div class="mx-auto max-w-3xl px-4 py-8">

    <div class="mb-6">
        <a href="{{ route('credits.customers.show', $customer) }}"
           class="text-sm font-medium text-blue-600 hover:underline">
            ← Back to Customer Ledger
        </a>

        <h1 class="mt-3 text-2xl font-bold text-slate-800">
            Edit Customer
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Update customer information and profile photo.
        </p>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

        <form action="{{ route('credits.customers.update', $customer) }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            {{-- PHOTO --}}
            <div class="mb-6">

                <label class="mb-3 block text-sm font-semibold text-slate-700">
                    Customer Photo
                </label>

                <div class="mb-4">
                    @if($customer->photo)
                        <img
                            id="photoPreview"
                            src="{{ Storage::url($customer->photo) }}"
                            class="h-24 w-24 rounded-full border object-cover"
                            alt="Customer photo"
                        >
                    @else
                        <div id="photoPlaceholder"
                             class="flex h-24 w-24 items-center justify-center rounded-full bg-blue-100 text-2xl font-bold text-blue-700">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>

                        <img
                            id="photoPreview"
                            class="hidden h-24 w-24 rounded-full border object-cover"
                            alt="New customer photo"
                        >
                    @endif
                </div>

                <input
                    type="file"
                    name="photo"
                    id="photoInput"
                    accept="image/jpeg,image/png,image/webp"
                    class="block w-full rounded-lg border border-slate-300 p-2 text-sm"
                >

                <p class="mt-1 text-xs text-slate-500">
                    JPG, JPEG, PNG or WEBP. Maximum 2 MB.
                </p>

            </div>


            {{-- NAME --}}
            <div class="mb-5">

                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Customer Name *
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $customer->name) }}"
                    required
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    placeholder="Enter customer name"
                >

            </div>


            {{-- PHONE --}}
            <div class="mb-5">

                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Phone Number
                </label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $customer->phone) }}"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    placeholder="Enter phone number"
                >

            </div>


            {{-- ADDRESS --}}
            <div class="mb-6">

                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Address
                </label>

                <textarea
                    name="address"
                    rows="4"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    placeholder="Enter customer address"
                >{{ old('address', $customer->address) }}</textarea>

            </div>


            {{-- BUTTONS --}}
            <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">

                <a
                    href="{{ route('credits.customers.show', $customer) }}"
                    class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Update Customer
                </button>

            </div>

        </form>

    </div>

</div>


{{-- PHOTO PREVIEW --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    const photoPlaceholder = document.getElementById('photoPlaceholder');

    if (!photoInput || !photoPreview) return;

    photoInput.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) return;

        photoPreview.src = URL.createObjectURL(file);
        photoPreview.classList.remove('hidden');

        if (photoPlaceholder) {
            photoPlaceholder.classList.add('hidden');
        }

    });

});
</script>

@endsection