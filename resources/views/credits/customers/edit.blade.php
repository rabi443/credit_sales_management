@extends('layouts.credit')

@section('title', 'Edit Customer')

@section('content')

<div class="mx-auto max-w-3xl px-4 py-8">

    {{-- PAGE HEADER --}}
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


    {{-- VALIDATION ERRORS --}}
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


            {{-- ========================================= --}}
            {{-- CUSTOMER PHOTO --}}
            {{-- ========================================= --}}

            <div class="mb-7">

                <label class="mb-3 block text-sm font-semibold text-slate-700">
                    Customer Photo
                </label>


                {{-- CLICKABLE PHOTO PREVIEW --}}
                <div class="mb-4">

                    @if($customer->photo)

                        <img
                            id="photoPreview"
                            src="{{ Storage::url($customer->photo) }}"
                            onclick="openPhotoModal(this.src)"
                            class="h-28 w-28 cursor-zoom-in rounded-full border-2 border-slate-200 object-cover transition hover:opacity-80 hover:ring-4 hover:ring-blue-200"
                            alt="Customer photo"
                        >

                    @else

                        <div id="photoPlaceholder"
                             class="flex h-28 w-28 items-center justify-center rounded-full bg-blue-100 text-3xl font-bold text-blue-700">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>

                        <img
                            id="photoPreview"
                            onclick="openPhotoModal(this.src)"
                            class="hidden h-28 w-28 cursor-zoom-in rounded-full border-2 border-slate-200 object-cover transition hover:opacity-80"
                            alt="Customer photo preview"
                        >

                    @endif

                    <p class="mt-2 text-xs text-blue-600">
                        Click photo to view full size.
                    </p>

                </div>


                {{-- HIDDEN FILE INPUT --}}
                <input
                    type="file"
                    name="photo"
                    id="photoInput"
                    accept="image/jpeg,image/png,image/webp"
                    class="hidden"
                >


                {{-- PHOTO BUTTONS --}}
                <div class="flex flex-wrap gap-3">

                    {{-- GALLERY BUTTON --}}
                    <button
                        type="button"
                        id="galleryButton"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m21 15-5-5L5 21"/>

                        </svg>

                        Choose from Gallery

                    </button>


                    {{-- CAMERA BUTTON --}}
                    <button
                        type="button"
                        id="cameraButton"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-5 w-5"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 7h3l2-3h8l2 3h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z"/>

                            <circle cx="12" cy="13" r="4"/>

                        </svg>

                        Take Photo

                    </button>

                </div>


                {{-- PHOTO INFORMATION --}}
                <p class="mt-3 text-xs text-slate-500">
                    Choose an existing photo from your gallery or take
                    a new photo using your mobile camera.
                </p>

                <p id="selectedPhotoName"
                   class="mt-2 text-xs font-semibold text-green-600">
                </p>

                <p class="mt-2 text-xs text-slate-500">
                    Supported formats: JPG, JPEG, PNG and WEBP.
                    Maximum 5 MB.
                </p>

            </div>


            {{-- ========================================= --}}
            {{-- CUSTOMER NAME --}}
            {{-- ========================================= --}}

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


            {{-- ========================================= --}}
            {{-- PHONE NUMBER --}}
            {{-- ========================================= --}}

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


            {{-- ========================================= --}}
            {{-- ADDRESS --}}
            {{-- ========================================= --}}

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


            {{-- ========================================= --}}
            {{-- FORM BUTTONS --}}
            {{-- ========================================= --}}

            <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">

                <a
                    href="{{ route('credits.customers.show', $customer) }}"
                    class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    Update Customer
                </button>

            </div>

        </form>

    </div>

</div>


{{-- ========================================= --}}
{{-- FULL SIZE PHOTO MODAL --}}
{{-- ========================================= --}}

<div id="photoModal"
     class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/90 p-4"
     onclick="closePhotoModal()">

    {{-- CLOSE BUTTON --}}
    <button
        type="button"
        onclick="closePhotoModal()"
        class="absolute right-5 top-5 flex h-11 w-11 items-center justify-center rounded-full bg-white/20 text-3xl text-white transition hover:bg-white/40"
        aria-label="Close photo preview">

        &times;

    </button>


    {{-- FULL SIZE IMAGE --}}
    <img
        id="fullSizePhoto"
        src=""
        alt="Full size customer photo"
        class="max-h-[90vh] max-w-full rounded-lg object-contain shadow-2xl"
        onclick="event.stopPropagation()"
    >

</div>


{{-- ========================================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const photoInput = document.getElementById('photoInput');

    const galleryButton = document.getElementById('galleryButton');
    const cameraButton = document.getElementById('cameraButton');

    const photoPreview = document.getElementById('photoPreview');
    const photoPlaceholder = document.getElementById('photoPlaceholder');

    const selectedPhotoName = document.getElementById('selectedPhotoName');

    let previewUrl = null;


    /*
    |--------------------------------------------------------------------------
    | OPEN GALLERY
    |--------------------------------------------------------------------------
    */

    function openGallery() {

        photoInput.removeAttribute('capture');

        photoInput.click();

    }


    /*
    |--------------------------------------------------------------------------
    | GALLERY BUTTON
    |--------------------------------------------------------------------------
    */

    galleryButton.addEventListener('click', function () {

        openGallery();

    });


    /*
    |--------------------------------------------------------------------------
    | CAMERA BUTTON
    |--------------------------------------------------------------------------
    */

    cameraButton.addEventListener('click', function () {

        photoInput.setAttribute('capture', 'environment');

        photoInput.click();

    });


    /*
    |--------------------------------------------------------------------------
    | PHOTO PREVIEW AFTER SELECTION
    |--------------------------------------------------------------------------
    */

    photoInput.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) return;


        // Validate image format.
        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {

            alert('Please select JPG, JPEG, PNG or WEBP image.');

            this.value = '';

            return;

        }


        // Validate maximum 5 MB.
        if (file.size > 5 * 1024 * 1024) {

            alert('Photo size must not exceed 5 MB.');

            this.value = '';

            return;

        }


        // Remove previous preview URL.
        if (previewUrl) {

            URL.revokeObjectURL(previewUrl);

        }


        // Create new preview URL.
        previewUrl = URL.createObjectURL(file);

        photoPreview.src = previewUrl;

        photoPreview.classList.remove('hidden');


        // Hide placeholder.
        if (photoPlaceholder) {

            photoPlaceholder.classList.add('hidden');

        }


        // Display selected file name.
        selectedPhotoName.textContent =
            'Selected: ' + file.name;

    });


    /*
    |--------------------------------------------------------------------------
    | CLEANUP PREVIEW URL
    |--------------------------------------------------------------------------
    */

    window.addEventListener('beforeunload', function () {

        if (previewUrl) {

            URL.revokeObjectURL(previewUrl);

        }

    });

});


/*
|--------------------------------------------------------------------------
| OPEN FULL SIZE PHOTO
|--------------------------------------------------------------------------
*/

function openPhotoModal(imageSrc) {

    if (!imageSrc) return;

    const modal = document.getElementById('photoModal');

    const fullSizePhoto = document.getElementById('fullSizePhoto');

    fullSizePhoto.src = imageSrc;

    modal.classList.remove('hidden');

    modal.classList.add('flex');

    document.body.style.overflow = 'hidden';

}


/*
|--------------------------------------------------------------------------
| CLOSE FULL SIZE PHOTO
|--------------------------------------------------------------------------
*/

function closePhotoModal() {

    const modal = document.getElementById('photoModal');

    const fullSizePhoto = document.getElementById('fullSizePhoto');

    modal.classList.add('hidden');

    modal.classList.remove('flex');

    fullSizePhoto.src = '';

    document.body.style.overflow = '';

}


/*
|--------------------------------------------------------------------------
| CLOSE USING ESCAPE KEY
|--------------------------------------------------------------------------
*/

document.addEventListener('keydown', function (event) {

    if (event.key === 'Escape') {

        closePhotoModal();

    }

});

</script>

@endsection
