@extends('layouts.app')
@section('title', 'Dashboard Manager')
@section('page-title', 'Dashboard Manager')

@section('sidebar-nav')
@php $route = request()->routeIs('manager.*') ? request()->route()->getName() : ''; @endphp

<p class="text-primary-300 text-xs font-semibold uppercase tracking-widest px-4 mb-2">Menu Utama</p>
<a href="{{ route('manager.dashboard') }}" class="sidebar-link {{ $route === 'manager.dashboard' ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-chart-pie"></i><span>Dashboard</span>
</a>
<a href="{{ route('manager.proposal.index') }}" class="sidebar-link {{ str_contains($route,'proposal') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-file-alt"></i><span>Review Proposal</span>
    @if($stats['proposal_pending'] > 0)<span class="ml-auto bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $stats['proposal_pending'] }}</span>@endif
</a>
<a href="{{ route('manager.laporan') }}" class="sidebar-link {{ str_contains($route,'laporan') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-chart-bar"></i><span>Laporan</span>
</a>

<p class="text-primary-300 text-xs font-semibold uppercase tracking-widest px-4 mb-2 mt-5">Manajemen</p>
<a href="{{ route('manager.operator.index') }}" class="sidebar-link {{ str_contains($route,'operator') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-users-cog"></i><span>Kelola Operator</span>
</a>
<a href="{{ route('manager.token.index') }}" class="sidebar-link {{ str_contains($route,'token') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-key"></i><span>Token Akses</span>
</a>
@endsection

@section('content')
{{-- Greeting --}}
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900">Selamat datang, {{ auth()->user()->nama_lengkap }}! 👋</h2>
    <p class="text-gray-500 text-sm mt-0.5">Berikut ringkasan aktivitas sistem magang hari ini.</p>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <x-stat-card icon="fas fa-users" label="Total Mahasiswa" value="{{ $stats['total_mahasiswa'] }}" color="blue" href="{{ route('manager.laporan') }}" />
    <x-stat-card icon="fas fa-file-alt" label="Total Proposal" value="{{ $stats['total_proposal'] }}" color="indigo" />
    <x-stat-card icon="fas fa-clock" label="Menunggu Review" value="{{ $stats['proposal_pending'] }}" color="yellow" href="{{ route('manager.proposal.index') }}" />
    <x-stat-card icon="fas fa-user-check" label="Sedang Magang" value="{{ $stats['magang_aktif'] }}" color="green" />
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <x-stat-card icon="fas fa-check-circle" label="Diterima" value="{{ $stats['proposal_diterima'] }}" color="green" />
    <x-stat-card icon="fas fa-times-circle" label="Ditolak" value="{{ $stats['proposal_ditolak'] }}" color="red" />
    <x-stat-card icon="fas fa-user-tie" label="Operator Aktif" value="{{ $stats['total_operator'] }}" color="purple" />
    <x-stat-card icon="fas fa-chalkboard-teacher" label="Pembimbing Aktif" value="{{ $stats['total_pembimbing'] }}" color="indigo" />
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Proposal Menunggu Review --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-clock text-yellow-500"></i> Proposal Menunggu Persetujuan
            </h3>
            <a href="{{ route('manager.proposal.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Lihat Semua →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($proposalPending as $p)
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-medium text-sm text-gray-800 truncate">{{ $p->judul_proposal }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            <span class="font-medium">{{ $p->pengaju->nama_lengkap }}</span> •
                            {{ $p->nomor_proposal }} •
                            <span class="text-blue-600">{{ $p->divisi_tujuan }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <x-status-badge :status="$p->status" />
                        <a href="{{ route('manager.proposal.show', $p) }}"
                            class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-medium transition-colors">
                            Review
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-10 text-center">
                <i class="fas fa-check-circle text-3xl text-green-300 mb-2"></i>
                <p class="text-sm text-gray-500">Tidak ada proposal yang perlu direview</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Distribusi per Divisi --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-sitemap text-blue-500"></i> Proposal per Divisi
            </h3>
        </div>
        <div class="p-6 space-y-4">
            @forelse($proposalPerDivisi as $item)
            @php $pct = $stats['total_proposal'] > 0 ? round($item->total / $stats['total_proposal'] * 100) : 0; @endphp
            <div>
                <div class="flex justify-between text-sm mb-1.5">
                    <span class="font-medium text-gray-700">{{ $item->divisi_tujuan }}</span>
                    <span class="text-gray-500">{{ $item->total }} ({{ $pct }}%)</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
