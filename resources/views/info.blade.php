<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Informasi Simple POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-slate-100 px-4">
    <div class="w-full max-w-lg bg-white rounded-md shadow p-6">
        <h1 class="text-xl font-semibold mb-3">Informasi Simple POS</h1>

        <p class="text-sm text-slate-600 mb-4">
            Selamat datang di Simple POS.
            Silakan login untuk menggunakan sistem kasir.
        </p>

        <a href="{{ route('login') }}"
           class="inline-block bg-slate-900 text-white rounded-md px-4 py-2 text-sm">
            Login
        </a>
    </div>
</body>
</html>