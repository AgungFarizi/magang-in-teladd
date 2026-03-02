@extends('layouts.app')
@section('title', 'Buat Periode Magang')
@section('page-title', 'Buat Periode Magang Baru')
@section('sidebar-nav')
@include('operator._sidebar')
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('operator.periode.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Periode <span class="text-red-500">*</span></label>
                <input type="text" name="nama_periode" value="{{ old('nama_periode') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: Magang Semester Ganjil 2025/2026">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Buka Pendaftaran <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_buka_pendaftaran" value="{{ old('tanggal_buka_pendaftaran') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tutup Pendaftaran <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_tutup_pendaftaran" value="{{ old('tanggal_tutup_pendaftaran') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mulai Magang <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_mulai_magang" value="{{ old('tanggal_mulai_magang') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Selesai Magang <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_selesai_magang" value="{{ old('tanggal_selesai_magang') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kuota Total</label>
                    <input type="number" name="kuota_total" value="{{ old('kuota_total', 0) }}" min="0"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="0 = tidak terbatas">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kuota per Divisi</label>
                    <input type="number" name="kuota_per_divisi" value="{{ old('kuota_per_divisi', 0) }}" min="0"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Syarat Dokumen</label>
                <textarea name="syarat_dokumen" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                    placeholder="Daftar dokumen yang wajib dilampirkan...">{{ old('syarat_dokumen') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="deskripsi" rows="2"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                    placeholder="Deskripsi singkat periode magang ini...">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-sm">
                    <i class="fas fa-save mr-2"></i> Simpan Periode
                </button>
                <a href="{{ route('operator.periode.index') }}" class="px-6 py-3 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-xl text-sm font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
