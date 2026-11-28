<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'My Account') - Rabin Credit Manager</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex min-h-screen flex-col bg-slate-100 text-slate-800">

    {{-- HEADER --}}
    <header class="bg-slate-900 text-white shadow">

        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-4">

            {{-- LOGO --}}
            <a href="{{ route('customer.dashboard') }}"
               class="text-lg font-bold">
                💳 Rabin Kirana Store
            </a>


            {{-- CUSTOMER ACTIONS --}}
            <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-sm">

                @php
                    $loggedCustomer = auth('customer')->user();

                    $profilePhoto = $loggedCustomer && $loggedCustomer->photo
                        ? \Illuminate\Support\Facades\Storage::url($loggedCustomer->photo)
                        : null;

                    $profileInitial = $loggedCustomer
                        ? strtoupper(substr($loggedCustomer->name, 0, 1))
                        : 'C';
                @endphp


                {{-- PRODUCTS BUTTON --}}
                <a href="{{ route('customer.products.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-2 font-semibold text-white transition hover:bg-indigo-700">

                    🛍️ <span>Products</span>

                </a>


                {{-- SHOPPING CART BUTTON --}}
                <a href="{{ route('customer.cart.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 font-semibold text-white transition hover:bg-emerald-700">

                    🛒 <span>Cart</span>

                    <span class="rounded-full bg-white px-2 py-0.5 text-xs text-emerald-700">
                        {{ collect(session('customer_cart', []))->sum('quantity') }}
                    </span>

                </a>


                {{-- PROFILE PHOTO --}}
                @if($profilePhoto)

                    <a href="{{ $profilePhoto }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       title="View full profile photo"
                       class="shrink-0">

                        <img src="{{ $profilePhoto }}"
                             alt="{{ $loggedCustomer->name }}"
                             class="h-10 w-10 cursor-pointer rounded-full border-2 border-white object-cover transition hover:scale-110 hover:ring-2 hover:ring-blue-400">

                    </a>

                @else

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border-2 border-white bg-blue-600 text-lg font-bold text-white">
                        {{ $profileInitial }}
                    </div>

                @endif


                {{-- CUSTOMER PROFILE --}}
                @if($loggedCustomer)

                    <a href="{{ route('customer.profile') }}"
                       class="font-semibold text-white transition hover:text-blue-300"
                       title="View My Profile">

                        {{ $loggedCustomer->name }}

                    </a>

                @endif


                {{-- LOGOUT --}}
                <form method="POST" action="{{ route('customer.logout') }}">
                    @csrf

                    <button type="submit"
                            class="rounded-lg border border-slate-600 px-3 py-2 transition hover:bg-slate-800">

                        Logout

                    </button>
                </form>

            </div>

        </div>

    </header>


    {{-- MAIN CONTENT --}}
    <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')

    </main>


    {{-- FOOTER --}}
    <footer class="mt-10 bg-slate-900 text-slate-300">

        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-5 py-10 sm:grid-cols-2 lg:grid-cols-4">

            {{-- SHOP INFORMATION --}}
            <div>

                <h2 class="mb-4 text-lg font-bold text-white">
                    💳 Credit Sales Management
                </h2>

                <p class="text-sm leading-6 text-slate-400">
                    Welcome to our Rabin Kirana Store.
                    View your purchases, payment history and
                    outstanding balance anytime.
                </p>

                <p class="mt-4 text-sm text-slate-400">
                    Thank you for being our valued customer.
                </p>

                <a href="{{ route('customer.products.index') }}"
                   class="mt-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">

                    🛍️ Explore Products

                </a>

            </div>


            {{-- CONTACT INFORMATION --}}
            <div>

                <h2 class="mb-4 text-lg font-bold text-white">
                    Contact Us
                </h2>

                <div class="space-y-3 text-sm">

                    <p>
                        <span class="mr-2">📞</span>

                        <a href="tel:+9779811349989"
                           class="transition hover:text-white">
                            +977 9811349989
                        </a>
                    </p>

                    <p>
                        <span class="mr-2">💬</span>

                        <a href="https://wa.me/9779816321861"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="transition hover:text-green-400">
                            Chat on WhatsApp
                        </a>
                    </p>

                    <p>
                        <span class="mr-2">✉️</span>

                        <a href="mailto:rc2583463@gmail.com"
                           class="break-all transition hover:text-white">
                            rc2583463@gmail.com
                        </a>
                    </p>

                </div>

            </div>


            {{-- ADDRESS --}}
            <div>

                <h2 class="mb-4 text-lg font-bold text-white">
                    Our Location
                </h2>

                <div class="space-y-3 text-sm text-slate-400">

                    <p>
                        <span class="mr-2">📍</span>
                        Gadhi-2, Aurabani, school tole
                    </p>

                    <p>
                        <span class="mr-2">🏘️</span>
                        Gadhi Gaupalika / Sunsari
                    </p>

                    <p>
                        <span class="mr-2">🇳🇵</span>
                        Nepal
                    </p>

                </div>

            </div>


            {{-- ESEWA QR CODE --}}
            <div>

                <h2 class="mb-4 text-lg font-bold text-white">
                    Pay with eSewa
                </h2>

                <div class="inline-block rounded-xl bg-white p-3">

                    <img
                        src="{{ asset('images/esewa-qr.png') }}"
                        alt="eSewa QR Code"
                        class="h-40 w-40 object-contain"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >

                    <div class="hidden h-40 w-40 items-center justify-center bg-slate-100 text-center text-xs text-slate-500">
                        Upload your eSewa QR image
                    </div>

                </div>

                <p class="mt-3 text-sm text-slate-400">
                    Scan the QR code using your eSewa app to make a payment.
                </p>

                <p class="mt-2 text-xs leading-5 text-yellow-400">
                    Please verify the receiver name before paying and
                    take a screenshot of the payment confirmation for
                    your records along with the reference number.
                    Send it to us for confirmation on WhatsApp.
                    Otherwise your payment will not be recorded.
                </p>

            </div>

        </div>


        {{-- COPYRIGHT --}}
        <div class="border-t border-slate-700">

            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-5 py-5 text-center text-xs text-slate-400 sm:flex-row">

                <p>
                    &copy; {{ date('Y') }} Credit Sales Management.
                    All Rights Reserved.
                </p>

                <p>
                    Developed by

                    <span class="font-semibold text-white">
                        Rabin Kumar Chaudhary
                    </span>
                </p>

            </div>

        </div>

    </footer>

</body>
</html>
