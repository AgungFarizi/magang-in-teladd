@extends('layouts.app')
@section('title', 'Verifikasi Log Harian')
@section('page-title', 'Detail Log Harian')
@section('sidebar-nav')
@include('pembimbing._sidebar')
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Info Log --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-sm text-gray-500 mb-1">{{ $logHarian->tanggal->isoFormat('dddd, D MMMM Y') }}</p>
                <h2 class="text-xl font-bold text-gray-900">{{ $logHarian->judul_aktivitas }}</h2>
            </div>
            <x-status-badge :status="$logHarian->status_verifikasi" />
        </div>

        <div class="grid grid-cols-3 gap-4 mb-5">
            <div class="bg-gray-50 rounded-xl p-3 text-sm">
                <p class="text-xs text-gray-500 mb-0.5">Mahasiswa</p>
                <p class="font-semibold text-gray-800">{{ $logHarian->mahasiswa->nama_lengkap }}</p>
                <p class="text-xs text-gray-500">{{ $logHarian->mahasiswa->nim }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 text-sm">
                <p class="text-xs text-gray-500 mb-0.5">Kategori</p>
                <p class="font-semibold text-gray-800">{{ $logHarian->kategori_label }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 text-sm">
                <p class="text-xs text-gray-500 mb-0.5">Durasi</p>
                <p class="font-semibold text-gray-800">{{ $logHarian->durasi_format }}</p>
            </div>
        </div>

        <div class="space-y-4 text-sm">
            <div>
                <p class="text-xs text-gray-500 mb-1">Deskripsi Aktivitas</p>
                <p class="text-gray-700 leading-relaxed bg-gray-50 rounded-xl p-4">{{ $logHarian->deskripsi_aktivitas }}</p>
            </div>
            @if($logHarian->kendala)
            <div>
                <p class="text-xs text-gray-500 mb-1">Kendala</p>
                <p class="text-gray-700 bg-yellow-50 rounded-xl p-3">{{ $logHarian->kendala }}</p>
            </div>
            @endif
            @if($logHarian->rencana_besok)
            <div>
                <p class="text-xs text-gray-500 mb-1">Rencana Besok</p>
                <p class="text-gray-700 bg-blue-50 rounded-xl p-3">{{ $logHarian->rencana_besok }}</p>
            </div>
            @endif

            @if($logHarian->file_dokumentasi && count($logHarian->file_dokumentasi) > 0)
            <div>
                <p class="text-xs text-gray-500 mb-2">Dokumentasi</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($logHarian->file_dokumentasi as $f)
                    <a href="{{ asset('storage/'.$f) }}" target="_blank" class="text-xs text-blue-600 hover:underline bg-blue-50 px-2 py-1 rounded-lg">
                        <i class="fas fa-paperclip mr-1"></i>File {{ $loop->iteration }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Form Verifikasi --}}
    @if($logHarian->status_verifikasi === 'pending')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-5 flex items-center gap-2">
            <i class="fas fa-check-circle text-blue-500"></i> Berikan Penilaian
        </h3>
        <form action="{{ route('pembimbing.log.verifikasi', $logHarian) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="action" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="diverifikasi">✅ Terverifikasi</option>
                        <option value="revisi">🔄 Perlu Revisi</option>
                        <option value="ditolak">❌ Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nilai (1–100)</label>
                    <input type="number" name="nilai" min="1" max="100"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="Berikan nilai (opsional)">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Feedback untuk Mahasiswa</label>
                <textarea name="feedback" rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                    placeholder="Berikan masukan yang konstruktif untuk mahasiswa..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl text-sm transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Penilaian
                </button>
                <a href="{{ route('pembimbing.log.index') }}" class="px-5 py-3 border border-gray-300 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </form>
    </div>
    @elseif($logHarian->feedback_pembimbing)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-comment-alt text-blue-500"></i> Feedback Anda</h3>
        <div class="bg-blue-50 rounded-xl p-4 text-sm text-gray-700">
            <p>{{ $logHarian->feedback_pembimbing }}</p>
            @if($logHarian->nilai_pembimbing)
            <p class="mt-2 font-semibold text-blue-700">Nilai: {{ $logHarian->nilai_pembimbing }}/100</p>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
