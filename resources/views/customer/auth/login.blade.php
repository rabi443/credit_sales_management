
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customer Login - Rabin Credit Manager</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex min-h-screen items-center justify-center bg-slate-100 px-4">

    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">

        <div class="mb-7 text-center">
            <div class="mb-3 text-4xl">💳</div>

            <h1 class="text-2xl font-bold text-slate-800">
                Customer Portal
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Login to view your credit account.
            </p>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('customer.login.store') }}">
            @csrf

            <div class="mb-5">
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Phone Number
                </label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    placeholder="Enter your phone number">
            </div>

            <div class="mb-6">
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    placeholder="Enter your password">
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700">
                Login to My Account
            </button>
        </form>

        <div class="mt-6 border-t pt-5 text-center text-sm text-slate-500">
            Don't have a login account?
            <p class="mt-1">
                Please contact the shop owner.
            </p>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}"
               class="text-sm text-blue-600 hover:underline">
                Admin Login
            </a>
        </div>

    </div>

</body>
</html>
