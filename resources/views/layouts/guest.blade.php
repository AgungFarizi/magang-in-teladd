<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TELLINTER') — Sistem Magang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full bg-gradient-to-br from-primary-900 via-primary-800 to-indigo-900 min-h-screen flex items-center justify-center p-4"
    style="background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #3730a3 100%);">

    {{-- Background Pattern --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/4 w-64 h-64 bg-blue-400/10 rounded-full blur-2xl"></div>
    </div>

    <div class="relative w-full max-w-md">
        {{-- Logo & Branding --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-xl mb-4">
                <i class="fas fa-graduation-cap text-blue-700 text-3xl"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">TELLINTER</h1>
            <p class="text-blue-200 text-sm mt-1">Sistem Pendaftaran & Manajemen Magang</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            @yield('content')
        </div>

        {{-- Footer --}}
        <p class="text-center text-blue-300 text-xs mt-6">
            © {{ date('Y') }} TELLINTER. Semua hak dilindungi.
        </p>
    </div>

    @yield('scripts')
</body>
</html>
