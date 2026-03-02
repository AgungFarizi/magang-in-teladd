@extends('layouts.guest')
@section('title', 'Verifikasi Email')

@section('content')
<div class="p-8 text-center">
    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-5">
        <i class="fas fa-envelope-open text-blue-600 text-3xl"></i>
    </div>

    <h2 class="text-2xl font-bold text-gray-900 mb-2">Cek Email Anda</h2>
    <p class="text-gray-500 text-sm leading-relaxed mb-6">
        Kami telah mengirimkan link verifikasi ke alamat email Anda.<br>
        Klik link tersebut untuk mengaktifkan akun.
    </p>

    @if(session('success'))
    <div class="mb-5 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <div class="bg-gray-50 rounded-xl p-4 mb-6 text-left">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Yang perlu dilakukan:</p>
        <div class="space-y-2">
            @foreach(['Buka aplikasi email Anda','Cari email dari TELLINTER','Klik tombol "Verifikasi Email"','Login ke sistem'] as $i => $step)
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">{{ $i+1 }}</span>
                <span class="text-sm text-gray-600">{{ $step }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="border-t border-gray-100 pt-5">
        <p class="text-sm text-gray-500 mb-3">Tidak menerima email?</p>
        <form action="{{ route('verification.resend') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ session('email') ?? old('email') }}">
            @if(!session('email'))
            <input type="email" name="email" placeholder="Masukkan email Anda"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 mb-3" required>
            @endif
            <button type="submit"
                class="w-full bg-white border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white font-semibold py-2.5 rounded-xl text-sm transition-all duration-200">
                <i class="fas fa-redo mr-2"></i> Kirim Ulang Email Verifikasi
            </button>
        </form>
    </div>

    <div class="mt-4">
        <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-gray-600">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke halaman login
        </a>
    </div>
</div>
@endsection
