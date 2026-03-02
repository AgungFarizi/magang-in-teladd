@extends('layouts.app')
@section('title', 'Dashboard Mahasiswa')
@section('page-title', 'Dashboard')
@section('sidebar-nav')
@include('mahasiswa._sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Greeting --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900">Halo, {{ auth()->user()->nama_lengkap }}! 👋</h2>
        <p class="text-gray-500 text-sm mt-0.5">{{ auth()->user()->institusi }} — {{ auth()->user()->jurusan }}</p>
    </div>

    {{-- Status Banner --}}
    @if($proposalAktif)
        @if($proposalAktif->isAktif())
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                        <span class="text-sm font-medium opacity-90">Magang Aktif</span>
                    </div>
                    <h3 class="text-xl font-bold">{{ $proposalAktif->divisi_tujuan }}</h3>
                    <p class="text-sm opacity-80 mt-0.5">{{ $proposalAktif->periode->nama_periode }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('mahasiswa.kehadiran.index') }}" class="px-4 py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur rounded-xl text-sm font-semibold transition-colors">
                        <i class="fas fa-calendar-check mr-1.5"></i>Absensi
                    </a>
                    <a href="{{ route('mahasiswa.log.index') }}" class="px-4 py-2.5 bg-white hover:bg-gray-100 text-emerald-700 rounded-xl text-sm font-semibold transition-colors shadow-sm">
                        <i class="fas fa-book-open mr-1.5"></i>Log Hari Ini
                    </a>
                </div>
            </div>
        </div>
        @elseif(in_array($proposalAktif->status, ['diterima']))
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center"><i class="fas fa-check-circle text-blue-600 text-2xl"></i></div>
            <div>
                <p class="font-semibold text-blue-800">Proposal Diterima!</p>
                <p class="text-sm text-blue-600 mt-0.5">Menunggu surat penerimaan resmi dari operator. Status akan segera diperbarui.</p>
            </div>
        </div>
        @elseif(in_array($proposalAktif->status, ['diajukan','review_operator','diteruskan_manager','review_manager_dep','review_manager']))
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center"><i class="fas fa-hourglass-half text-yellow-600 text-2xl"></i></div>
            <div>
                <p class="font-semibold text-yellow-800">Proposal Sedang Diproses</p>
                <p class="text-sm text-yellow-600 mt-0.5">Nomor: <strong>{{ $proposalAktif->nomor_proposal }}</strong> — Status: {{ $proposalAktif->status_label }}</p>
            </div>
            <a href="{{ route('mahasiswa.proposal.show', $proposalAktif) }}" class="ml-auto text-xs font-medium text-yellow-700 hover:text-yellow-900 flex-shrink-0">
                Lihat Detail →
            </a>
        </div>
        @elseif(in_array($proposalAktif->status, ['ditolak','ditolak_manager_dep']))
        <div class="bg-red-50 border border-red-200 rounded-2xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center"><i class="fas fa-times-circle text-red-600 text-2xl"></i></div>
            <div>
                <p class="font-semibold text-red-800">Proposal Ditolak</p>
                <p class="text-sm text-red-600 mt-0.5">Silakan buat proposal baru atau hubungi operator untuk informasi lebih lanjut.</p>
            </div>
            <a href="{{ route('mahasiswa.proposal.create') }}" class="ml-auto text-xs font-medium text-red-700 hover:text-red-900 flex-shrink-0">
                Ajukan Ulang →
            </a>
        </div>
        @endif
    @else
        @if($periodeAktif)
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white">
            <h3 class="font-bold text-lg mb-1">Periode Pendaftaran Terbuka!</h3>
            <p class="text-sm opacity-90 mb-4">{{ $periodeAktif->nama_periode }} • Tutup {{ $periodeAktif->tanggal_tutup_pendaftaran->format('d F Y') }}</p>
            <a href="{{ route('mahasiswa.proposal.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-blue-700 hover:bg-blue-50 font-semibold rounded-xl text-sm transition-colors shadow-sm">
                <i class="fas fa-file-signature"></i> Ajukan Proposal Sekarang
            </a>
        </div>
        @else
        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5 text-center">
            <i class="fas fa-calendar-times text-3xl text-gray-300 mb-2"></i>
            <p class="text-gray-600 font-medium">Tidak ada periode pendaftaran yang aktif saat ini</p>
            <p class="text-gray-400 text-sm mt-1">Pantau terus untuk informasi periode magang berikutnya.</p>
        </div>
        @endif
    @endif

    {{-- Stats (saat aktif magang) --}}
    @if($proposalAktif && $proposalAktif->isAktif() && !empty($stats))
    <div class="grid grid-cols-3 gap-4">
        <x-stat-card icon="fas fa-calendar-check" label="Total Kehadiran" value="{{ $stats['total_kehadiran'] }}" color="green" />
        <x-stat-card icon="fas fa-book-open" label="Log Bulan Ini" value="{{ $stats['kehadiran_bulan_ini'] }}" color="blue" />
        <x-stat-card icon="fas fa-clock" label="Log Pending" value="{{ $stats['log_pending'] }}" color="yellow" />
    </div>
    @endif

    {{-- Notifikasi Terbaru --}}
    @if($notifikasi->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2"><i class="fas fa-bell text-blue-500"></i> Notifikasi Terbaru</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($notifikasi as $n)
            <div class="px-6 py-4 flex items-start gap-3 {{ !$n->sudah_dibaca ? 'bg-blue-50/50' : '' }}">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fas fa-bell text-blue-600 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-sm text-gray-800">{{ $n->judul }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $n->pesan }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                </div>
                @if(!$n->sudah_dibaca)<span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></span>@endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
