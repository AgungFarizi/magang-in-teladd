@extends('layouts.app')
@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas Harian')

@section('sidebar-nav')
@include('mahasiswa._sidebar')
@endsection

@section('content')
<div class="space-y-5">

    @if($kehadiranTanpaLog->count() > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 flex-shrink-0"></i>
            <div class="flex-1">
                <p class="font-semibold text-yellow-800 text-sm">Log Aktivitas Belum Diisi</p>
                <p class="text-xs text-yellow-700 mt-0.5 mb-3">Kamu memiliki {{ $kehadiranTanpaLog->count() }} hari kehadiran yang belum diisi log aktivitasnya.</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($kehadiranTanpaLog->take(5) as $k)
                    <a href="{{ route('mahasiswa.log.create', $k) }}"
                        class="text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-3 py-1.5 rounded-lg font-medium transition-colors flex items-center gap-1">
                        <i class="fas fa-plus"></i>
                        {{ $k->tanggal->format('d/m/Y') }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- List Log --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Riwayat Log Aktivitas</h3>
            <span class="text-xs text-gray-500">Total: {{ $logs->total() }} entri</span>
        </div>

        @if($logs->count() > 0)
        <div class="divide-y divide-gray-50">
            @foreach($logs as $log)
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        {{-- Ikon Kategori --}}
                        @php
                        $catIcons = ['pembelajaran'=>['bg-blue-100','fas fa-book','text-blue-600'],
                            'proyek'=>['bg-green-100','fas fa-code','text-green-600'],
                            'administrasi'=>['bg-yellow-100','fas fa-folder','text-yellow-600'],
                            'presentasi'=>['bg-purple-100','fas fa-presentation-screen','text-purple-600'],
                            'diskusi'=>['bg-indigo-100','fas fa-comments','text-indigo-600'],
                            'laporan'=>['bg-orange-100','fas fa-file-alt','text-orange-600'],
                            'lainnya'=>['bg-gray-100','fas fa-ellipsis-h','text-gray-600']];
                        $ci = $catIcons[$log->kategori] ?? $catIcons['lainnya'];
                        @endphp
                        <div class="w-10 h-10 {{ $ci[0] }} rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="{{ $ci[1] }} {{ $ci[2] }} text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <p class="font-semibold text-gray-800 text-sm">{{ $log->judul_aktivitas }}</p>
                                <x-status-badge :status="$log->status_verifikasi" />
                            </div>
                            <p class="text-xs text-gray-500 line-clamp-1 mb-1.5">{{ $log->deskripsi_aktivitas }}</p>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-400">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-calendar"></i>
                                    {{ $log->tanggal->format('d F Y') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-tag"></i>
                                    {{ $log->kategori_label }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-clock"></i>
                                    {{ $log->durasi_format }}
                                </span>
                                @if($log->nilai_pembimbing)
                                <span class="flex items-center gap-1 text-yellow-600 font-medium">
                                    <i class="fas fa-star"></i>
                                    Nilai: {{ $log->nilai_pembimbing }}/100
                                </span>
                                @endif
                            </div>
                            @if($log->feedback_pembimbing)
                            <div class="mt-2 bg-blue-50 border border-blue-100 rounded-lg p-2 text-xs text-blue-700">
                                <span class="font-medium">Feedback Pembimbing:</span> {{ $log->feedback_pembimbing }}
                            </div>
                            @endif
                            @if($log->status_verifikasi === 'revisi')
                            <div class="mt-2 bg-orange-50 border border-orange-100 rounded-lg p-2 text-xs text-orange-700">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <span class="font-medium">Perlu direvisi.</span>
                                {{ $log->feedback_pembimbing }}
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        @if($log->file_dokumentasi && count($log->file_dokumentasi) > 0)
                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-lg">
                            <i class="fas fa-paperclip mr-1"></i>{{ count($log->file_dokumentasi) }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $logs->links() }}</div>
        @endif

        @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-book-open text-2xl text-gray-300"></i>
            </div>
            <p class="text-gray-500 font-medium">Belum ada log aktivitas</p>
            <p class="text-gray-400 text-sm mt-1">Log akan muncul setelah Anda mengisi aktivitas harian</p>
        </div>
        @endif
    </div>

</div>
@endsection
