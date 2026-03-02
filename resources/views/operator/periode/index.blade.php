@extends('layouts.app')
@section('title', 'Periode Magang')
@section('page-title', 'Kelola Periode Magang')
@section('sidebar-nav')
@include('operator._sidebar')
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-800">Daftar Periode Magang</h2>
        <a href="{{ route('operator.periode.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm flex items-center gap-2">
            <i class="fas fa-plus"></i> Buat Periode Baru
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse($periode as $p)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="font-bold text-gray-900 text-lg">{{ $p->nama_periode }}</h3>
                        <x-status-badge :status="$p->status" />
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm mt-3">
                        <div>
                            <p class="text-xs text-gray-500">Buka Pendaftaran</p>
                            <p class="font-medium text-gray-800">{{ $p->tanggal_buka_pendaftaran->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tutup Pendaftaran</p>
                            <p class="font-medium text-gray-800">{{ $p->tanggal_tutup_pendaftaran->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Mulai Magang</p>
                            <p class="font-medium text-gray-800">{{ $p->tanggal_mulai_magang->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Selesai Magang</p>
                            <p class="font-medium text-gray-800">{{ $p->tanggal_selesai_magang->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @if($p->deskripsi)
                    <p class="text-sm text-gray-500 mt-3">{{ $p->deskripsi }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <div class="text-center px-4 py-2 bg-blue-50 rounded-xl">
                        <p class="text-xl font-bold text-blue-600">{{ $p->total_daftar }}</p>
                        <p class="text-xs text-gray-500">Pendaftar</p>
                    </div>
                    <a href="{{ route('operator.periode.edit', $p) }}" class="p-2.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-colors">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-600 font-medium">Belum ada periode magang</p>
            <a href="{{ route('operator.periode.create') }}" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold">
                <i class="fas fa-plus"></i> Buat Periode Pertama
            </a>
        </div>
        @endforelse
    </div>

    @if($periode->hasPages())
    <div class="bg-white rounded-xl p-4">{{ $periode->links() }}</div>
    @endif
</div>
@endsection
