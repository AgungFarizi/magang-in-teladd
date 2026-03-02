@extends('layouts.guest')
@section('title', 'Pilih Tipe Registrasi')

@section('content')
<div class="p-8">
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Buat Akun Baru</h2>
        <p class="text-gray-500 text-sm mt-1">Pilih tipe akun yang sesuai dengan peran Anda</p>
    </div>

    <div class="space-y-4">
        {{-- Mahasiswa --}}
        <a href="{{ route('register.mahasiswa') }}"
            class="flex items-center gap-4 p-5 border-2 border-gray-200 rounded-2xl hover:border-blue-500 hover:bg-blue-50 transition-all duration-200 group cursor-pointer">
            <div class="w-14 h-14 bg-blue-100 group-hover:bg-blue-200 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                <i class="fas fa-user-graduate text-blue-600 text-2xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-900 group-hover:text-blue-700">Mahasiswa</p>
                <p class="text-sm text-gray-500 mt-0.5">Daftar sebagai peserta magang, ajukan proposal, dan kelola aktivitas magang Anda.</p>
            </div>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-500 ml-auto transition-colors"></i>
        </a>

        {{-- Admin (pakai token) --}}
        <a href="{{ route('register.admin') }}"
            class="flex items-center gap-4 p-5 border-2 border-gray-200 rounded-2xl hover:border-indigo-500 hover:bg-indigo-50 transition-all duration-200 group cursor-pointer">
            <div class="w-14 h-14 bg-indigo-100 group-hover:bg-indigo-200 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors">
                <i class="fas fa-shield-alt text-indigo-600 text-2xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-900 group-hover:text-indigo-700">Staf / Admin</p>
                <p class="text-sm text-gray-500 mt-0.5">Operator, Pembimbing Lapang, Manager Departemen — memerlukan token akses khusus.</p>
            </div>
            <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-500 ml-auto transition-colors"></i>
        </a>
    </div>

    <div class="mt-8 pt-6 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-600">Sudah punya akun?
            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection
