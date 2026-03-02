@extends('layouts.app')
@section('title', 'Detail Surat Balasan')
@section('page-title', 'Surat Balasan')
@section('breadcrumb')
    <a href="{{ route('mahasiswa.surat.index') }}" class="hover:text-gray-700">Surat Balasan</a>
    <span class="mx-1">/</span><span>{{ $suratBalasan->nomor_surat }}</span>
@endsection

@section('sidebar-nav')
@include('mahasiswa._sidebar')
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-2">
                    <span class="font-mono text-sm bg-gray-100 text-gray-600 px-2.5 py-1 rounded-lg">{{ $suratBalasan->nomor_surat }}</span>
                    <span class="text-sm {{ $suratBalasan->jenis === 'penerimaan' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} px-3 py-1 rounded-lg font-semibold capitalize">
                        <i class="fas fa-{{ $suratBalasan->jenis === 'penerimaan' ? 'check' : 'times' }}-circle mr-1"></i>
                        {{ $suratBalasan->jenis }}
                    </span>
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ $suratBalasan->perihal }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $suratBalasan->tanggal_surat->format('d F Y') }}</p>
            </div>
            @if($suratBalasan->file_surat)
            <a href="{{ asset('storage/'.$suratBalasan->file_surat) }}" target="_blank"
                class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl text-sm font-semibold transition-colors border border-red-200">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            @endif
        </div>

        {{-- Isi Surat --}}
        <div class="bg-gray-50 rounded-xl p-6 font-serif text-sm text-gray-700 leading-relaxed whitespace-pre-line border border-gray-200">
            {{ $suratBalasan->isi_surat }}
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
            <span>Dikirim oleh: {{ $suratBalasan->pembuat->nama_lengkap }}</span>
            <span>{{ $suratBalasan->dikirim_pada?->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <a href="{{ route('mahasiswa.surat.index') }}"
        class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 font-medium">
        <i class="fas fa-arrow-left"></i> Kembali ke daftar surat
    </a>
</div>
@endsection
