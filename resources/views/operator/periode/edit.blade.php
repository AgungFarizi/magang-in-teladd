@extends('layouts.app')
@section('title', 'Edit Periode Magang')
@section('page-title', 'Edit Periode Magang')

@section('sidebar-nav')
@include('operator._sidebar')
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('operator.periode.update', $periode) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Periode <span class="text-red-500">*</span></label>
                <input type="text" name="nama_periode" value="{{ old('nama_periode', $periode->nama_periode) }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Buka Pendaftaran <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_buka_pendaftaran" value="{{ old('tanggal_buka_pendaftaran', $periode->tanggal_buka_pendaftaran->format('Y-m-d')) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tutup Pendaftaran <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_tutup_pendaftaran" value="{{ old('tanggal_tutup_pendaftaran', $periode->tanggal_tutup_pendaftaran->format('Y-m-d')) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mulai Magang <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_mulai_magang" value="{{ old('tanggal_mulai_magang', $periode->tanggal_mulai_magang->format('Y-m-d')) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Selesai Magang <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_selesai_magang" value="{{ old('tanggal_selesai_magang', $periode->tanggal_selesai_magang->format('Y-m-d')) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    @foreach(['draft'=>'Draft','aktif'=>'Aktif','ditutup'=>'Ditutup','selesai'=>'Selesai'] as $v => $l)
                    <option value="{{ $v }}" {{ old('status', $periode->status) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="deskripsi" rows="2"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 resize-none">{{ old('deskripsi', $periode->deskripsi) }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl text-sm transition-colors">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
                <a href="{{ route('operator.periode.index') }}" class="px-6 py-3 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-xl text-sm font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
