@extends('layouts.app')
@section('title', 'Ajukan Proposal Magang')
@section('page-title', 'Ajukan Proposal Magang')
@section('breadcrumb')
    <a href="{{ route('mahasiswa.proposal.index') }}" class="hover:text-gray-700">Proposal</a>
    <span class="mx-1">/</span><span>Ajukan Baru</span>
@endsection
@section('sidebar-nav')
@include('mahasiswa._sidebar')
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('mahasiswa.proposal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Periode & Divisi --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-5 flex items-center gap-2"><i class="fas fa-calendar text-blue-500"></i> Informasi Magang</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Periode Magang <span class="text-red-500">*</span></label>
                    <select name="periode_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 @error('periode_id') border-red-400 @enderror">
                        <option value="">-- Pilih Periode --</option>
                        @foreach($periodeAktif as $p)
                        <option value="{{ $p->id }}" {{ old('periode_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama_periode }} ({{ $p->tanggal_buka_pendaftaran->format('d/m/Y') }} – {{ $p->tanggal_tutup_pendaftaran->format('d/m/Y') }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Divisi yang Dituju <span class="text-red-500">*</span></label>
                    <input type="text" name="divisi_tujuan" value="{{ old('divisi_tujuan') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 @error('divisi_tujuan') border-red-400 @enderror"
                        placeholder="Contoh: IT, Keuangan, Marketing">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_mulai_diinginkan" value="{{ old('tanggal_mulai_diinginkan') }}" required
                        min="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_selesai_diinginkan" value="{{ old('tanggal_selesai_diinginkan') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- Konten Proposal --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-5 flex items-center gap-2"><i class="fas fa-edit text-blue-500"></i> Konten Proposal</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Proposal <span class="text-red-500">*</span></label>
                    <input type="text" name="judul_proposal" value="{{ old('judul_proposal') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="Judul proposal magang Anda">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Latar Belakang <span class="text-red-500">*</span></label>
                    <textarea name="latar_belakang" rows="5" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                        placeholder="Jelaskan latar belakang mengapa Anda ingin magang di divisi ini (min. 100 karakter)...">{{ old('latar_belakang') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Minimal 100 karakter</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tujuan <span class="text-red-500">*</span></label>
                    <textarea name="tujuan" rows="3" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                        placeholder="Tujuan yang ingin dicapai selama magang (min. 50 karakter)...">{{ old('tujuan') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Upload Dokumen --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-5 flex items-center gap-2"><i class="fas fa-paperclip text-blue-500"></i> Upload Dokumen</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach([
                    ['file_proposal','Dokumen Proposal (PDF)','wajib',true],
                    ['file_surat_pengantar','Surat Pengantar Kampus (PDF)','opsional',false],
                    ['file_transkrip','Transkrip Nilai (PDF)','opsional',false],
                    ['file_cv','Curriculum Vitae (PDF)','opsional',false],
                ] as [$name,$label,$req,$required])
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ $label }}
                        @if($required)<span class="text-red-500">*</span>@else<span class="text-gray-400 text-xs">(opsional)</span>@endif
                    </label>
                    <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors group">
                        <div class="flex flex-col items-center justify-center text-center pointer-events-none">
                            <i class="fas fa-file-pdf text-2xl text-gray-300 group-hover:text-blue-400 mb-1 transition-colors"></i>
                            <p class="text-xs text-gray-500">Klik untuk upload PDF</p>
                            <p class="text-xs text-gray-400">Maks. 5 MB</p>
                        </div>
                        <input type="file" name="{{ $name }}" class="hidden" accept=".pdf" {{ $required ? 'required' : '' }}
                            onchange="showFileName(this)">
                    </label>
                    <p class="text-xs text-gray-400 mt-1 truncate" id="{{ $name }}_name"></p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Anggota Kelompok --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2"><i class="fas fa-users text-blue-500"></i> Anggota Kelompok</h3>
                <button type="button" id="addAnggota"
                    class="text-xs text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                    <i class="fas fa-plus"></i> Tambah Anggota
                </button>
            </div>

            {{-- Ketua (auto) --}}
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl mb-3">
                <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center"><span class="text-sm font-bold text-blue-600">{{ strtoupper(substr(auth()->user()->nama_lengkap,0,1)) }}</span></div>
                <div>
                    <p class="font-medium text-sm text-gray-800">{{ auth()->user()->nama_lengkap }} <span class="text-xs bg-yellow-50 text-yellow-600 px-1.5 py-0.5 rounded-md ml-1">Ketua</span></p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->nim }} • {{ auth()->user()->email }}</p>
                </div>
            </div>

            <div id="anggotaContainer" class="space-y-3">
                {{-- Anggota dinamis --}}
            </div>
            <p class="text-xs text-gray-400 mt-3">Maksimal 4 anggota tambahan (tidak termasuk Anda sebagai ketua)</p>
        </div>

        <div class="flex gap-3 pb-6">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-lg shadow-blue-500/30">
                <i class="fas fa-paper-plane mr-2"></i> Ajukan Proposal
            </button>
            <a href="{{ route('mahasiswa.proposal.index') }}" class="px-6 py-3 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-xl text-sm transition-colors font-medium">
                Batal
            </a>
        </div>
    </form>
</div>

@section('scripts')
<script>
let anggotaCount = 0;
const maxAnggota = 4;

document.getElementById('addAnggota').addEventListener('click', function() {
    if (anggotaCount >= maxAnggota) {
        alert('Maksimal ' + maxAnggota + ' anggota tambahan.');
        return;
    }
    anggotaCount++;
    const i = anggotaCount - 1;
    const div = document.createElement('div');
    div.className = 'bg-gray-50 rounded-xl p-4 border border-gray-200';
    div.id = 'anggota_' + i;
    div.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-gray-700">Anggota ${anggotaCount}</p>
            <button type="button" onclick="removeAnggota(${i})" class="text-xs text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i> Hapus
            </button>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-gray-600 mb-1 block">Nama Lengkap *</label>
                <input type="text" name="anggota[${i}][nama_lengkap]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Nama lengkap">
            </div>
            <div>
                <label class="text-xs text-gray-600 mb-1 block">NIM *</label>
                <input type="text" name="anggota[${i}][nim]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Nomor Induk">
            </div>
            <div>
                <label class="text-xs text-gray-600 mb-1 block">Email *</label>
                <input type="email" name="anggota[${i}][email]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Email">
            </div>
            <div>
                <label class="text-xs text-gray-600 mb-1 block">Jurusan</label>
                <input type="text" name="anggota[${i}][jurusan]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Jurusan">
            </div>
        </div>
    `;
    document.getElementById('anggotaContainer').appendChild(div);
});

function removeAnggota(i) {
    document.getElementById('anggota_' + i).remove();
    anggotaCount--;
}

function showFileName(input) {
    const name = input.name + '_name';
    const el = document.getElementById(name);
    if (el && input.files[0]) {
        el.textContent = '✓ ' + input.files[0].name;
        el.className = 'text-xs text-green-600 mt-1 truncate';
    }
}
</script>
@endsection
@endsection
