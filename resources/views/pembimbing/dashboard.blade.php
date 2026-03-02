@extends('layouts.app')
@section('title', 'Dashboard Pembimbing')
@section('page-title', 'Dashboard Pembimbing Lapang')
@section('sidebar-nav')
@include('pembimbing._sidebar')
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Pembimbing Lapang</h2>
        <p class="text-gray-500 text-sm mt-0.5">Divisi: {{ auth()->user()->divisi ?? 'Semua Divisi' }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card icon="fas fa-users" label="Mahasiswa Bimbingan" value="{{ $stats['total_mahasiswa'] }}" color="blue" />
        <x-stat-card icon="fas fa-clock" label="Kehadiran Pending" value="{{ $stats['kehadiran_pending'] }}" color="yellow" href="{{ route('pembimbing.kehadiran.index', ['status'=>'pending']) }}" />
        <x-stat-card icon="fas fa-book" label="Log Pending" value="{{ $stats['log_pending'] }}" color="orange" href="{{ route('pembimbing.log.index', ['status'=>'pending']) }}" />
        <x-stat-card icon="fas fa-calendar-day" label="Kehadiran Hari Ini" value="{{ $stats['kehadiran_hari_ini'] }}" color="green" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Kehadiran Pending --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-clock text-yellow-500"></i> Kehadiran Menunggu Verifikasi
                </h3>
                <a href="{{ route('pembimbing.kehadiran.index') }}" class="text-xs text-blue-600 font-medium">Semua →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($kehadiranPending as $k)
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="font-medium text-sm text-gray-800">{{ $k->mahasiswa->nama_lengkap }}</p>
                            <p class="text-xs text-gray-500">{{ $k->tanggal->format('d/m/Y') }} • Masuk: {{ $k->jam_masuk }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-status-badge :status="$k->status_kehadiran" />
                            <form action="{{ route('pembimbing.kehadiran.verifikasi', $k) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="action" value="diverifikasi">
                                <button type="submit" class="text-xs bg-green-50 text-green-600 hover:bg-green-100 px-2 py-1.5 rounded-lg font-medium">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('pembimbing.kehadiran.verifikasi', $k) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="action" value="ditolak">
                                <button type="submit" class="text-xs bg-red-50 text-red-500 hover:bg-red-100 px-2 py-1.5 rounded-lg font-medium">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center">
                    <i class="fas fa-check-circle text-3xl text-green-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Semua kehadiran sudah terverifikasi</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Log Pending --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-book-open text-blue-500"></i> Log Aktivitas Menunggu Review
                </h3>
                <a href="{{ route('pembimbing.log.index') }}" class="text-xs text-blue-600 font-medium">Semua →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($logPending as $log)
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-medium text-sm text-gray-800">{{ $log->mahasiswa->nama_lengkap }}</p>
                            <p class="text-xs text-gray-600 truncate mt-0.5">{{ $log->judul_aktivitas }}</p>
                            <p class="text-xs text-gray-400">{{ $log->tanggal->format('d/m/Y') }}</p>
                        </div>
                        <a href="{{ route('pembimbing.log.show', $log) }}" class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-medium flex-shrink-0">
                            Review
                        </a>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center">
                    <i class="fas fa-check-circle text-3xl text-green-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Semua log sudah direview</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Daftar Mahasiswa Bimbingan --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-users text-blue-500"></i> Mahasiswa Bimbingan
            </h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($mahasiswaBimbingan as $p)
            <div class="px-6 py-4 flex items-center gap-4">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="font-bold text-blue-600">{{ strtoupper(substr($p->pengaju->nama_lengkap,0,1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-sm text-gray-800">{{ $p->pengaju->nama_lengkap }}</p>
                    <p class="text-xs text-gray-500">{{ $p->pengaju->nim }} • {{ $p->pengaju->institusi }}</p>
                </div>
                <div class="text-right">
                    <x-status-badge :status="$p->status" />
                    <p class="text-xs text-gray-400 mt-1">{{ $p->kehadiran->where('status_kehadiran','hadir')->count() }} hari hadir</p>
                </div>
                <a href="{{ route('pembimbing.laporan.mahasiswa', $p) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                    Laporan
                </a>
            </div>
            @empty
            <div class="px-6 py-10 text-center">
                <i class="fas fa-user-graduate text-3xl text-gray-300 mb-2"></i>
                <p class="text-sm text-gray-500">Belum ada mahasiswa bimbingan</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
