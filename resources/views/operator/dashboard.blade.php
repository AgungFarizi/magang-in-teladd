@extends('layouts.app')
@section('title', 'Dashboard Operator')
@section('page-title', 'Dashboard Operator')
@section('sidebar-nav')
@include('operator._sidebar')
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Dashboard Operator</h2>
        <p class="text-gray-500 text-sm mt-0.5">{{ auth()->user()->divisi ? 'Divisi: '.auth()->user()->divisi : 'Kelola proposal dan periode magang' }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card icon="fas fa-inbox" label="Proposal Baru" value="{{ $stats['proposal_baru'] }}" color="yellow" href="{{ route('operator.proposal.index', ['status'=>'diajukan']) }}" />
        <x-stat-card icon="fas fa-search" label="Sedang Direview" value="{{ $stats['sedang_direview'] }}" color="blue" />
        <x-stat-card icon="fas fa-share" label="Diteruskan" value="{{ $stats['diteruskan'] }}" color="green" />
        <x-stat-card icon="fas fa-envelope" label="Surat Pending" value="{{ $stats['surat_pending'] }}" color="red" href="{{ route('operator.surat.index') }}" />
    </div>

    {{-- Periode Aktif --}}
    @if($periodeAktif)
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-5 text-white">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                    <span class="text-sm opacity-90">Periode Aktif</span>
                </div>
                <h3 class="font-bold text-lg">{{ $periodeAktif->nama_periode }}</h3>
                <p class="text-sm opacity-80 mt-0.5">Tutup: {{ $periodeAktif->tanggal_tutup_pendaftaran->format('d F Y') }}</p>
            </div>
            <a href="{{ route('operator.periode.index') }}" class="text-xs bg-white/20 hover:bg-white/30 text-white font-semibold px-4 py-2 rounded-xl transition-colors">
                Kelola Periode
            </a>
        </div>
    </div>
    @endif

    {{-- Proposal Baru Masuk --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-inbox text-yellow-500"></i> Proposal Baru Masuk
            </h3>
            <a href="{{ route('operator.proposal.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Lihat Semua →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($proposalBaru as $p)
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="font-medium text-sm text-gray-800 truncate">{{ $p->judul_proposal }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            <span class="font-medium">{{ $p->pengaju->nama_lengkap }}</span> •
                            {{ $p->pengaju->nim }} •
                            <span class="text-blue-600">{{ $p->divisi_tujuan }}</span> •
                            {{ $p->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <x-status-badge :status="$p->status" />
                        <a href="{{ route('operator.proposal.show', $p) }}"
                            class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-medium">
                            Review
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-10 text-center">
                <i class="fas fa-check-circle text-3xl text-green-300 mb-2"></i>
                <p class="text-sm text-gray-500">Tidak ada proposal baru</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
