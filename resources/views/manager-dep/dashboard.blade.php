@extends('layouts.app')
@section('title', 'Dashboard Manager Departemen')
@section('page-title', 'Dashboard')

@section('sidebar-nav')
@include('manager-dep._sidebar')
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Dashboard Manager Departemen</h2>
        <p class="text-gray-500 text-sm mt-0.5">Divisi: <span class="font-semibold text-blue-600">{{ $divisi }}</span></p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <x-stat-card icon="fas fa-clock" label="Menunggu Review" value="{{ $stats['proposal_pending'] }}" color="yellow" href="{{ route('manager-dep.proposal.index') }}" />
        <x-stat-card icon="fas fa-check-circle" label="Diterima" value="{{ $stats['proposal_diterima'] }}" color="green" />
        <x-stat-card icon="fas fa-times-circle" label="Ditolak" value="{{ $stats['proposal_ditolak'] }}" color="red" />
        <x-stat-card icon="fas fa-user-check" label="Sedang Magang" value="{{ $stats['magang_aktif'] }}" color="blue" />
        <x-stat-card icon="fas fa-chalkboard-teacher" label="Pembimbing" value="{{ $stats['total_pembimbing'] }}" color="indigo" href="{{ route('manager-dep.pembimbing.index') }}" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Proposal Pending --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-clock text-yellow-500"></i> Proposal Menunggu Review
                </h3>
                <a href="{{ route('manager-dep.proposal.index') }}" class="text-xs text-blue-600 font-medium">Semua →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($proposalPending as $p)
                <div class="px-6 py-4 flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-sm text-gray-800 truncate">{{ $p->judul_proposal }}</p>
                        <p class="text-xs text-gray-500">{{ $p->pengaju->nama_lengkap }} • {{ $p->created_at->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('manager-dep.proposal.show', $p) }}"
                        class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-medium flex-shrink-0">
                        Review
                    </a>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-sm text-gray-400">
                    <i class="fas fa-check-circle text-2xl text-green-300 mb-2"></i><p>Semua proposal telah diproses</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Mahasiswa Aktif --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-user-check text-green-500"></i> Mahasiswa Sedang Magang
                </h3>
                <a href="{{ route('manager-dep.mahasiswa') }}" class="text-xs text-blue-600 font-medium">Semua →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($mahasiswaAktif as $p)
                <div class="px-6 py-4 flex items-center gap-3">
                    <div class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-green-600">{{ strtoupper(substr($p->pengaju->nama_lengkap,0,1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-sm text-gray-800">{{ $p->pengaju->nama_lengkap }}</p>
                        <p class="text-xs text-gray-500">{{ $p->pengaju->institusi }}</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $p->pembimbing?->nama_lengkap ?? 'Belum ditugaskan' }}</span>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-sm text-gray-400">Belum ada mahasiswa aktif</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
