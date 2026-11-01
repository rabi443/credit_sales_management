<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'My Account') - Rabin Credit Manager</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex min-h-screen flex-col bg-slate-100 text-slate-800">

    {{-- =========================
        HEADER
    ========================= --}}
    <header class="bg-slate-900 text-white shadow">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4">

            <a href="{{ route('customer.dashboard') }}"
               class="text-lg font-bold">
                💳 My Credit Account
            </a>

            <div class="flex items-center gap-3 text-sm">

                <span class="hidden sm:inline">
                    {{ auth('customer')->user()->name }}
                </span>

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


    {{-- =========================
        MAIN CONTENT
    ========================= --}}
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


    {{-- =========================
        FOOTER
    ========================= --}}
    <footer class="mt-10 bg-slate-900 text-slate-300">

        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-5 py-10 sm:grid-cols-2 lg:grid-cols-4">

            {{-- SHOP INFORMATION --}}
            <div>
                <h2 class="mb-4 text-lg font-bold text-white">
                    💳 Credit Sales Management
                </h2>

                <p class="text-sm leading-6 text-slate-400">
                    Welcome to your customer credit account.
                    View your purchases, payment history and
                    outstanding balance anytime.
                </p>

                <p class="mt-4 text-sm text-slate-400">
                    Thank you for being our valued customer.
                </p>
            </div>


            {{-- CONTACT INFORMATION --}}
            <div>
                <h2 class="mb-4 text-lg font-bold text-white">
                    Contact Us
                </h2>

                <div class="space-y-3 text-sm">

                    {{-- PHONE --}}
                    <p>
                        <span class="mr-2">📞</span>
                        <a href="tel:+9779811349989"
                           class="transition hover:text-white">
                            +977 9811349989
                        </a>
                    </p>

                    {{-- WHATSAPP --}}
                    <p>
                        <span class="mr-2">💬</span>
                        <a href="https://wa.me/9779816321861"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="transition hover:text-green-400">
                            Chat on WhatsApp
                        </a>
                    </p>

                    {{-- EMAIL --}}
                    <p>
                        <span class="mr-2">✉️</span>
                        <a href="mailto:your-email@example.com"
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

                    {{-- Put your actual eSewa QR image here --}}
                    <img
                        src="{{ asset('images/esewa-qr.png') }}"
                        alt="Rabin Kirana Store eSewa QR Code"
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

                <p class="mt-2 text-xs text-yellow-400">
                    Please verify the receiver name before paying and take the screenshot of the payment confirmation for your records along with the reference number and send it to us for confirmation on WhatsApp. Otherwise your payment will not be recorded.
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
