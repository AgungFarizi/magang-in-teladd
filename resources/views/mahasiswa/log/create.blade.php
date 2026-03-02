@extends('layouts.app')
@section('title', 'Isi Log Aktivitas')
@section('page-title', 'Log Aktivitas Harian')
@section('breadcrumb')
    <a href="{{ route('mahasiswa.log.index') }}" class="hover:text-gray-700">Log Aktivitas</a>
    <span class="mx-1">/</span><span>Isi Log</span>
@endsection
@section('sidebar-nav')
@include('mahasiswa._sidebar')
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5 flex items-center gap-3">
        <i class="fas fa-calendar-day text-blue-500 text-lg"></i>
        <div>
            <p class="font-semibold text-blue-800">{{ $kehadiran->tanggal->isoFormat('dddd, D MMMM Y') }}</p>
            <p class="text-xs text-blue-600 mt-0.5">Jam masuk: {{ $kehadiran->jam_masuk }} {{ $kehadiran->jam_keluar ? '— Keluar: '.$kehadiran->jam_keluar : '' }}</p>
        </div>
    </div>

    <form action="{{ route('mahasiswa.log.store', $kehadiran) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2"><i class="fas fa-book-open text-blue-500"></i> Detail Aktivitas</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Aktivitas <span class="text-red-500">*</span></label>
                <input type="text" name="judul_aktivitas" value="{{ old('judul_aktivitas') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                    placeholder="Ringkasan aktivitas hari ini">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        @foreach(['pembelajaran'=>'Pembelajaran','proyek'=>'Proyek','administrasi'=>'Administrasi','presentasi'=>'Presentasi','diskusi'=>'Diskusi','laporan'=>'Laporan','lainnya'=>'Lainnya'] as $val => $label)
                        <option value="{{ $val }}" {{ old('kategori') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Durasi (menit) <span class="text-red-500">*</span></label>
                    <input type="number" name="durasi_menit" value="{{ old('durasi_menit', 480) }}" required min="30" max="600"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="480">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Aktivitas <span class="text-red-500">*</span></label>
                <textarea name="deskripsi_aktivitas" rows="5" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                    placeholder="Jelaskan secara rinci apa yang Anda kerjakan hari ini (min. 50 karakter)...">{{ old('deskripsi_aktivitas') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kendala yang Dihadapi</label>
                <textarea name="kendala" rows="2"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                    placeholder="Kendala yang dihadapi hari ini (opsional)...">{{ old('kendala') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Rencana Besok</label>
                <textarea name="rencana_besok" rows="2"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                    placeholder="Aktivitas yang direncanakan untuk besok (opsional)...">{{ old('rencana_besok') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Dokumentasi (opsional)</label>
                <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                    <i class="fas fa-cloud-upload-alt text-2xl text-gray-300 mb-1"></i>
                    <p class="text-xs text-gray-500">Upload foto/dokumen pendukung (maks. 5 file, 2MB/file)</p>
                    <input type="file" name="file_dokumentasi[]" class="hidden" multiple accept="image/*,.pdf">
                </label>
            </div>
        </div>

        <div class="flex gap-3 pb-4">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-lg shadow-blue-500/30">
                <i class="fas fa-save mr-2"></i> Simpan Log Aktivitas
            </button>
            <a href="{{ route('mahasiswa.log.index') }}" class="px-6 py-3 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-xl text-sm transition-colors font-medium">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
