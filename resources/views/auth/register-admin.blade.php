@extends('layouts.guest')
@section('title', 'Registrasi Staf/Admin')

@section('content')
<div class="p-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('register') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Registrasi Staf / Admin</h2>
            <p class="text-gray-500 text-sm mt-0.5">Memerlukan token akses yang diberikan oleh Manager</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
        <p class="font-medium mb-1 flex items-center gap-1.5"><i class="fas fa-exclamation-circle"></i> Perbaiki kesalahan berikut:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('register.admin.post') }}" method="POST" class="space-y-4">
        @csrf

        {{-- Token Admin --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Token Akses Admin <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fas fa-key text-indigo-400 text-sm"></i>
                </div>
                <input type="text" name="token_admin" value="{{ old('token_admin') }}"
                    class="w-full pl-10 pr-4 py-3 border-2 border-indigo-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono uppercase tracking-widest @error('token_admin') border-red-400 bg-red-50 @enderror"
                    placeholder="XXXXXXXX-XXXXXXXX-XXXXXXXX" required
                    oninput="this.value = this.value.toUpperCase()">
            </div>
            @error('token_admin')
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
            @enderror
            <p class="mt-1.5 text-xs text-gray-500">Token diberikan oleh Manager saat Anda ditambahkan sebagai staf.</p>
        </div>

        {{-- Nama --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nama_lengkap') border-red-400 bg-red-50 @enderror"
                placeholder="Nama lengkap sesuai identitas" required>
        </div>

        {{-- Email & Telepon --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-400 bg-red-50 @enderror"
                    placeholder="email@instansi.com" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon</label>
                <input type="text" name="no_telepon" value="{{ old('no_telepon') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="08xxxxxxxxxx">
            </div>
        </div>

        {{-- Password --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10"
                        placeholder="Min. 8 karakter" required>
                    <button type="button" onclick="togglePwd('password','eye1')" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                        <i id="eye1" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_conf"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10"
                        placeholder="Ulangi password" required>
                    <button type="button" onclick="togglePwd('password_conf','eye2')" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                        <i id="eye2" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-3 flex items-start gap-2 text-xs text-indigo-700">
            <i class="fas fa-shield-alt mt-0.5 flex-shrink-0"></i>
            <span>Role dan divisi Anda akan ditentukan otomatis berdasarkan token akses yang diberikan oleh Manager.</span>
        </div>

        <button type="submit"
            class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold py-3 rounded-xl text-sm transition-all duration-200 shadow-lg shadow-indigo-500/30 mt-2">
            <i class="fas fa-user-shield mr-2"></i> Buat Akun Staf
        </button>
    </form>

    <div class="mt-6 pt-5 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-600">Sudah punya akun?
            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Masuk di sini</a>
        </p>
    </div>
</div>

@section('scripts')
<script>
function togglePwd(id, iconId) {
    const el = document.getElementById(id);
    const icon = document.getElementById(iconId);
    el.type = el.type === 'password' ? 'text' : 'password';
    icon.className = el.type === 'password' ? 'fas fa-eye text-sm' : 'fas fa-eye-slash text-sm';
}
</script>
@endsection
@endsection
