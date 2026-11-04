<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Credit Manager') - Rabin Credit Management</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100 text-slate-800 flex flex-col">

    {{-- NAVBAR --}}
    <header class="bg-slate-900 text-white shadow">

        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4">

            {{-- LOGO --}}
            <a href="{{ route('credits.index') }}" class="text-lg font-bold">
                ðŸ’³ Rabin Kirana Store
            </a>

            {{-- DESKTOP NAVIGATION --}}
            <nav class="hidden items-center gap-4 text-sm sm:flex">

                {{-- <a class="hover:text-blue-300"
                   href="{{ route('credits.index') }}">
                    Customers
                </a> --}}

                @auth
                    <span class="text-slate-300">
                        {{ auth()->user()->name }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="rounded-lg border border-slate-600 px-3 py-2 hover:bg-slate-800">
                            Logout
                        </button>
                    </form>
                @endauth

            </nav>

            {{-- MOBILE MENU BUTTON --}}
            <button
                type="button"
                id="mobileMenuButton"
                class="rounded-lg border border-slate-600 px-3 py-2 text-sm sm:hidden"
                aria-expanded="false"
                aria-controls="mobileMenu">
                â˜° Menu
            </button>

        </div>

        {{-- MOBILE NAVIGATION --}}
        <div id="mobileMenu"
             class="hidden border-t border-slate-700 px-4 py-4 sm:hidden">

            <nav class="flex flex-col gap-3 text-sm">

                <a href="{{ route('credits.index') }}"
                   class="rounded-lg px-3 py-2 hover:bg-slate-800">
                    Customers
                </a>

                <a href="{{ route('credits.sales.create') }}"
                   class="rounded-lg bg-blue-600 px-3 py-2 font-semibold hover:bg-blue-500">
                    + New Sale
                </a>

                @auth
                    <div class="border-t border-slate-700 pt-3 text-slate-300">
                        {{ auth()->user()->name }}
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full rounded-lg border border-slate-600 px-3 py-2 text-left hover:bg-slate-800">
                            Logout
                        </button>
                    </form>
                @endauth

            </nav>

        </div>

    </header>

    {{-- MAIN CONTENT --}}
    <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:px-8">

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- ERROR MESSAGE --}}
        @if(session('error'))
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- VALIDATION ERRORS --}}
        @if($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">

                <p class="font-semibold">
                    Please correct the following:
                </p>

                <ul class="mt-1 list-inside list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif

        {{-- PAGE CONTENT --}}
        @yield('content')

    </main>

    {{-- FOOTER --}}
    <footer class="mt-auto bg-slate-900 text-slate-300">

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">

                {{-- ABOUT --}}
                <div>
                    <h3 class="mb-3 text-lg font-bold text-white">
                        ðŸ’³ Rabin Credit Manager
                    </h3>

                    <p class="text-sm leading-6 text-slate-400">
                        A simple and reliable credit management system
                        to manage customers, credit sales, payments,
                        and outstanding balances.
                    </p>
                </div>

                {{-- QUICK LINKS --}}
                <div>
                    <h3 class="mb-3 text-base font-semibold text-white">
                        Quick Links
                    </h3>

                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="{{ route('credits.index') }}"
                               class="hover:text-blue-400">
                                Customers
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('credits.sales.create') }}"
                               class="hover:text-blue-400">
                                Create Credit Sale
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- CONTACT INFORMATION --}}
                <div>
                    <h3 class="mb-3 text-base font-semibold text-white">
                        Contact Information
                    </h3>

                    <ul class="space-y-3 text-sm text-slate-400">

                        <li class="flex items-start gap-2">
                            <span></span>
                            <span>Nepal</span>
                        </li>

                        <li class="flex items-start gap-2">
                            <span></span>
                            <a href="tel:+9779800000000"
                               class="hover:text-blue-400">
                                +977-9811349989 , 9842021259
                            </a>
                        </li>

                        <li class="flex items-start gap-2">
                            <span></span>
                            <a href="mailto:your-email@example.com"
                               class="hover:text-blue-400">
                                rc2583463@gmail.com
                            </a>
                        </li>

                    </ul>
                </div>

            </div>

            {{-- COPYRIGHT --}}
            <div class="mt-8 border-t border-slate-700 pt-5">

                <div class="flex flex-col items-center justify-between gap-3 text-center text-xs text-slate-400 sm:flex-row sm:text-left">

                    <p>
                        &copy; {{ date('Y') }}Credit Sales Management.
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

        </div>

    </footer>

    {{-- MOBILE MENU JAVASCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const menuButton = document.getElementById('mobileMenuButton');
            const mobileMenu = document.getElementById('mobileMenu');

            if (menuButton && mobileMenu) {

                menuButton.addEventListener('click', function () {

                    const isHidden = mobileMenu.classList.toggle('hidden');

                    menuButton.setAttribute('aria-expanded', String(!isHidden));

                    menuButton.textContent = isHidden ? 'â˜° Menu' : 'âœ• Close';

                });

            }

        });
    </script>

    {{-- PAGE-SPECIFIC JAVASCRIPT --}}
    @stack('scripts')

</body>
</html>