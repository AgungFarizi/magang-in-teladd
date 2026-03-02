@extends('layouts.guest')
@section('title', 'Registrasi Mahasiswa')

@section('content')
<div class="p-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('register') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Registrasi Mahasiswa</h2>
            <p class="text-gray-500 text-sm mt-0.5">Isi data diri Anda dengan lengkap dan benar</p>
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

    <form action="{{ route('register.mahasiswa.post') }}" method="POST" class="space-y-4">
        @csrf

        {{-- Nama --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_lengkap') border-red-400 bg-red-50 @enderror"
                placeholder="Nama sesuai KTP" required>
        </div>

        {{-- NIM & Email --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">NIM <span class="text-red-500">*</span></label>
                <input type="text" name="nim" value="{{ old('nim') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nim') border-red-400 bg-red-50 @enderror"
                    placeholder="Nomor Induk Mahasiswa" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon</label>
                <input type="text" name="no_telepon" value="{{ old('no_telepon') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="08xxxxxxxxxx">
            </div>
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-400 bg-red-50 @enderror"
                placeholder="email@kampus.ac.id" required>
        </div>

        {{-- Institusi & Jurusan --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Universitas/Institusi <span class="text-red-500">*</span></label>
                <input type="text" name="institusi" value="{{ old('institusi') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('institusi') border-red-400 bg-red-50 @enderror"
                    placeholder="Nama universitas" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jurusan/Prodi <span class="text-red-500">*</span></label>
                <input type="text" name="jurusan" value="{{ old('jurusan') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jurusan') border-red-400 bg-red-50 @enderror"
                    placeholder="Program studi" required>
            </div>
        </div>

        {{-- Password --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10 @error('password') border-red-400 bg-red-50 @enderror"
                        placeholder="Min. 8 karakter" required>
                    <button type="button" onclick="togglePwd('password','eye1')" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                        <i id="eye1" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10"
                        placeholder="Ulangi password" required>
                    <button type="button" onclick="togglePwd('password_confirmation','eye2')" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                        <i id="eye2" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 flex items-start gap-2 text-xs text-blue-700">
            <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
            <span>Password minimal 8 karakter, mengandung huruf dan angka. Setelah registrasi, link verifikasi akan dikirimkan ke email Anda.</span>
        </div>

        <button type="submit"
            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 rounded-xl text-sm transition-all duration-200 shadow-lg shadow-blue-500/30 mt-2">
            <i class="fas fa-user-plus mr-2"></i> Daftar Sekarang
        </button>
    </form>

    <div class="mt-6 pt-5 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-600">Sudah punya akun?
            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Masuk di sini</a>
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
