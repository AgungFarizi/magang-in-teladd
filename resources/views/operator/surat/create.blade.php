@extends('layouts.app')
@section('title', 'Buat Surat Balasan')
@section('page-title', 'Buat Surat Balasan')
@section('sidebar-nav')
@include('operator._sidebar')
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-1">Surat {{ ucfirst($jenis) }} Magang</h3>
        <p class="text-sm text-gray-500 mb-6">Proposal: <strong>{{ $proposal->nomor_proposal }}</strong> — {{ $proposal->pengaju->nama_lengkap }}</p>

        <form action="{{ route('operator.surat.store', $proposal) }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="jenis" value="{{ $jenis }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Surat</label>
                    <div class="px-4 py-3 bg-{{ $jenis === 'penerimaan' ? 'green' : 'red' }}-50 border border-{{ $jenis === 'penerimaan' ? 'green' : 'red' }}-200 rounded-xl text-sm font-semibold text-{{ $jenis === 'penerimaan' ? 'green' : 'red' }}-700">
                        <i class="fas fa-{{ $jenis === 'penerimaan' ? 'check' : 'times' }}-circle mr-2"></i>
                        {{ ucfirst($jenis) }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Penerima</label>
                    <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700">
                        {{ $proposal->pengaju->nama_lengkap }}
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Perihal <span class="text-red-500">*</span></label>
                <input type="text" name="perihal" required
                    value="{{ old('perihal', $jenis === 'penerimaan' ? 'Penerimaan Peserta Magang' : 'Penolakan Permohonan Magang') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Isi Surat <span class="text-red-500">*</span></label>
                <textarea name="isi_surat" rows="10" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 font-mono resize-none">{{ old('isi_surat', $jenis === 'penerimaan' ?
"Dengan hormat,

Sehubungan dengan permohonan magang yang diajukan oleh:
Nama : {$proposal->pengaju->nama_lengkap}
NIM  : {$proposal->pengaju->nim}
Institusi: {$proposal->pengaju->institusi}
Jurusan: {$proposal->pengaju->jurusan}

Dengan ini kami menyampaikan bahwa permohonan magang Saudara/i pada divisi {$proposal->divisi_tujuan} DITERIMA.

Pelaksanaan magang direncanakan pada tanggal {$proposal->tanggal_mulai_diinginkan->format('d F Y')} sampai dengan {$proposal->tanggal_selesai_diinginkan->format('d F Y')}.

Demikian surat ini kami sampaikan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.

Hormat kami,
[Nama & Jabatan]" :
"Dengan hormat,

Sehubungan dengan permohonan magang yang diajukan oleh:
Nama : {$proposal->pengaju->nama_lengkap}
NIM  : {$proposal->pengaju->nim}
Institusi: {$proposal->pengaju->institusi}

Dengan sangat menyesal kami sampaikan bahwa permohonan magang Saudara/i pada divisi {$proposal->divisi_tujuan} belum dapat kami terima saat ini karena keterbatasan kapasitas/tidak memenuhi persyaratan.

Kami harap Saudara/i tidak berkecil hati dan dapat mencoba kembali pada periode berikutnya.

Hormat kami,
[Nama & Jabatan]"
) }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl text-sm transition-colors shadow-sm">
                    <i class="fas fa-paper-plane mr-2"></i> Buat & Kirim Surat
                </button>
                <a href="{{ route('operator.proposal.show', $proposal) }}" class="px-6 py-3 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-xl text-sm font-medium transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
