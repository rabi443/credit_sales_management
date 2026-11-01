<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Rabin Credit Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="grid min-h-screen place-items-center bg-slate-100 p-4">

    <main class="w-full max-w-md rounded-2xl bg-white p-8 shadow">
        <h1 class="text-2xl font-bold">💳 Rabin Credit Manager</h1>

        <p class="mb-6 mt-1 text-sm text-slate-500">
            Administrator login
        </p>

        @if ($errors->any())
            <p class="mb-3 rounded bg-red-50 p-3 text-red-700">
                {{ $errors->first() }}
            </p>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf

            <label class="block text-sm">
                Email
                <input name="email" type="email" required
                    class="mt-1 w-full rounded-lg border border-slate-300 p-3">
            </label>

            <label class="block text-sm">
                Password
                <input name="password" type="password" required
                    class="mt-1 w-full rounded-lg border border-slate-300 p-3">
            </label>

            <label class="flex gap-2 text-sm">
                <input type="checkbox" name="remember">
                Remember me
            </label>

            <button type="submit" class="w-full rounded-lg bg-blue-600 p-3 font-semibold text-white hover:bg-blue-700">
                Sign in
            </button>
        </form>

        @if (!\App\Models\User::exists())
            <a class="mt-5 block text-center text-sm text-blue-700" href="{{ route('register') }}">
                Create first administrator account
            </a>
        @endif
    </main>

</body>

</html>
