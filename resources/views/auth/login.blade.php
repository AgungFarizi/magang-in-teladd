@extends('layouts.guest')
@section('title', 'Login')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Selamat Datang!</h2>
        <p class="text-gray-500 text-sm mt-1">Masukkan kredensial Anda untuk melanjutkan</p>
    </div>

    @if(session('success'))
    <div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('warning'))
    <div class="mb-4 flex items-center gap-2 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
    </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400 text-sm"></i>
                </div>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                    {{ $errors->has('email') ? 'border-red-400 bg-red-50' : '' }}"
                    placeholder="email@contoh.com" required autofocus>
            </div>
            @error('email')
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <a href="#" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Lupa password?</a>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                </div>
                <input type="password" name="password" id="password"
                    class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                    {{ $errors->has('password') ? 'border-red-400 bg-red-50' : '' }}"
                    placeholder="Masukkan password" required>
                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600">
                    <i id="eye-icon" class="fas fa-eye text-sm"></i>
                </button>
            </div>
            @error('password')
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <label for="remember" class="ml-2 text-sm text-gray-600">Ingat saya</label>
        </div>

        <button type="submit"
            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800
            text-white font-semibold py-3 rounded-xl text-sm transition-all duration-200
            shadow-lg shadow-blue-500/30 hover:shadow-blue-500/40 focus:ring-4 focus:ring-blue-300">
            <i class="fas fa-sign-in-alt mr-2"></i> Masuk ke Sistem
        </button>
    </form>

    <div class="mt-6 pt-6 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-600">Belum punya akun?
            <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Daftar Sekarang</a>
        </p>
    </div>
</div>

@section('scripts')
<script>
function togglePassword() {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.className = 'fas fa-eye-slash text-sm';
    } else {
        pwd.type = 'password';
        icon.className = 'fas fa-eye text-sm';
    }
}
</script>
@endsection
@endsection
