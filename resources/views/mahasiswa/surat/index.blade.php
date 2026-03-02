@extends('layouts.app')
@section('title', 'Surat Balasan')
@section('page-title', 'Surat Balasan')

@section('sidebar-nav')
@include('mahasiswa._sidebar')
@endsection

@section('content')
<div class="space-y-4">
    <div>
        <h2 class="text-lg font-bold text-gray-800">Surat Balasan</h2>
        <p class="text-sm text-gray-500">Surat resmi penerimaan atau penolakan proposal magang Anda</p>
    </div>

    @if($surat->count() > 0)
    <div class="space-y-4">
        @foreach($surat as $s)
        <div class="bg-white rounded-2xl shadow-sm border {{ $s->jenis === 'penerimaan' ? 'border-green-100' : 'border-red-100' }} p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 {{ $s->jenis === 'penerimaan' ? 'bg-green-100' : 'bg-red-100' }} rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-{{ $s->jenis === 'penerimaan' ? 'check-circle text-green-600' : 'times-circle text-red-500' }} text-xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-lg">{{ $s->nomor_surat }}</span>
                            <span class="text-xs {{ $s->jenis === 'penerimaan' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }} px-2 py-0.5 rounded-lg font-medium capitalize">
                                {{ $s->jenis }}
                            </span>
                            @if(!$s->sudah_dibaca)
                            <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-lg font-medium">Baru</span>
                            @endif
                        </div>
                        <p class="font-semibold text-gray-800">{{ $s->perihal }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $s->tanggal_surat->format('d F Y') }} •
                            Proposal: {{ $s->proposal->nomor_proposal }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if($s->file_surat)
                    <a href="{{ asset('storage/'.$s->file_surat) }}" target="_blank"
                        class="text-xs bg-gray-50 hover:bg-gray-100 text-gray-600 px-3 py-2 rounded-xl font-medium flex items-center gap-1.5 transition-colors border border-gray-200">
                        <i class="fas fa-file-pdf text-red-500"></i> PDF
                    </a>
                    @endif
                    <a href="{{ route('mahasiswa.surat.show', $s) }}"
                        class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-2 rounded-xl font-medium flex items-center gap-1.5 transition-colors">
                        <i class="fas fa-eye"></i> Baca
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-16 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-envelope-open text-2xl text-gray-300"></i>
        </div>
        <p class="text-gray-500 font-medium">Belum ada surat balasan</p>
        <p class="text-gray-400 text-sm mt-1">Surat akan muncul setelah proposal Anda diproses</p>
    </div>
    @endif
</div>
@endsection
