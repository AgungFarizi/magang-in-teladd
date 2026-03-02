@extends('layouts.app')
@section('title', 'Review Proposal')
@section('page-title', 'Review Proposal')
@section('breadcrumb')
    <a href="{{ route('operator.proposal.index') }}" class="hover:text-gray-700">Proposal</a>
    <span class="mx-1">/</span><span>{{ $proposal->nomor_proposal }}</span>
@endsection
@section('sidebar-nav')
@include('operator._sidebar')
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="font-mono text-sm bg-gray-100 text-gray-600 px-3 py-1 rounded-lg">{{ $proposal->nomor_proposal }}</span>
                    <x-status-badge :status="$proposal->status" />
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ $proposal->judul_proposal }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Oleh <span class="font-medium text-gray-700">{{ $proposal->pengaju->nama_lengkap }}</span> •
                    {{ $proposal->created_at->format('d F Y') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($proposal->status === 'diajukan')
                    <form action="{{ route('operator.proposal.review', $proposal) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 text-sm font-semibold rounded-xl transition-colors">
                            <i class="fas fa-search mr-1.5"></i> Mulai Review
                        </button>
                    </form>
                @endif
                @if($proposal->status === 'review_operator')
                    <button onclick="openModal('teruskModal')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                        <i class="fas fa-share mr-1.5"></i> Teruskan ke Manajer Dep.
                    </button>
                    <button onclick="openModal('tolakModal')" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 text-sm font-semibold rounded-xl transition-colors">
                        <i class="fas fa-times mr-1.5"></i> Tolak
                    </button>
                @endif
                @if(in_array($proposal->status, ['diterima','ditolak','ditolak_manager_dep']) && !$proposal->suratBalasan)
                    <a href="{{ route('operator.surat.create', $proposal) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                        <i class="fas fa-file-signature mr-1.5"></i> Buat Surat Balasan
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-5">
            {{-- Data Mahasiswa --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-user text-blue-500"></i> Data Pengaju</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    @foreach([['Nama Lengkap','nama_lengkap'],['NIM','nim'],['Email','email'],['Institusi','institusi'],['Jurusan','jurusan'],['No. Telepon','no_telepon']] as [$label,$field])
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-500 mb-0.5">{{ $label }}</p>
                        <p class="font-medium text-gray-800">{{ $proposal->pengaju->$field ?? '—' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Isi Proposal --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-file-alt text-blue-500"></i> Isi Proposal</h3>
                <div class="space-y-4 text-sm">
                    <div><p class="text-xs text-gray-500 mb-1">Divisi Tujuan</p><p class="font-semibold text-blue-600 text-base">{{ $proposal->divisi_tujuan }}</p></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><p class="text-xs text-gray-500 mb-1">Tanggal Mulai</p><p class="font-medium text-gray-800">{{ $proposal->tanggal_mulai_diinginkan->format('d/m/Y') }}</p></div>
                        <div><p class="text-xs text-gray-500 mb-1">Tanggal Selesai</p><p class="font-medium text-gray-800">{{ $proposal->tanggal_selesai_diinginkan->format('d/m/Y') }}</p></div>
                    </div>
                    <div><p class="text-xs text-gray-500 mb-1">Latar Belakang</p><p class="text-gray-700 leading-relaxed bg-gray-50 rounded-xl p-3">{{ $proposal->latar_belakang }}</p></div>
                    <div><p class="text-xs text-gray-500 mb-1">Tujuan</p><p class="text-gray-700 leading-relaxed bg-gray-50 rounded-xl p-3">{{ $proposal->tujuan }}</p></div>
                </div>
            </div>

            {{-- Anggota --}}
            @if($proposal->anggota->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-users text-blue-500"></i> Anggota ({{ $proposal->anggota->count() }})</h3>
                <div class="space-y-2">
                    @foreach($proposal->anggota as $a)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl text-sm">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-blue-600">{{ strtoupper(substr($a->nama_lengkap,0,1)) }}</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">{{ $a->nama_lengkap }}</p>
                            <p class="text-xs text-gray-500">{{ $a->nim }} • {{ $a->email }}</p>
                        </div>
                        <span class="text-xs capitalize {{ $a->peran==='ketua' ? 'text-yellow-600 bg-yellow-50' : 'text-gray-500 bg-gray-100' }} px-2 py-1 rounded-lg">{{ $a->peran }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            {{-- Dokumen --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-paperclip text-blue-500"></i> Dokumen</h3>
                <div class="space-y-2">
                    @foreach([['file_proposal','Proposal'],['file_surat_pengantar','Surat Pengantar'],['file_transkrip','Transkrip'],['file_cv','CV']] as [$f,$l])
                    @if($proposal->$f)
                    <a href="{{ asset('storage/'.$proposal->$f) }}" target="_blank" class="flex items-center gap-2 p-3 bg-red-50 hover:bg-red-100 rounded-xl transition-colors group text-sm">
                        <i class="fas fa-file-pdf text-red-500"></i>
                        <span class="text-gray-700 flex-1 group-hover:text-red-700">{{ $l }}</span>
                        <i class="fas fa-external-link-alt text-xs text-gray-400"></i>
                    </a>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- Surat Balasan --}}
            @if($proposal->suratBalasan)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-envelope text-blue-500"></i> Surat Balasan</h3>
                <div class="bg-{{ $proposal->suratBalasan->jenis === 'penerimaan' ? 'green' : 'red' }}-50 rounded-xl p-3 text-sm">
                    <p class="font-medium text-gray-800">{{ $proposal->suratBalasan->nomor_surat }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ ucfirst($proposal->suratBalasan->jenis) }} • {{ $proposal->suratBalasan->tanggal_surat->format('d/m/Y') }}</p>
                    @if($proposal->suratBalasan->file_surat)
                    <a href="{{ asset('storage/'.$proposal->suratBalasan->file_surat) }}" target="_blank" class="text-xs text-blue-600 hover:underline mt-1 inline-block">Lihat PDF →</a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Teruskan --}}
<div id="teruskModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-share text-green-500"></i> Teruskan ke Manajer Departemen</h3>
        <form action="{{ route('operator.proposal.teruskan', $proposal) }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan (opsional)</label>
                <textarea name="catatan" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500" placeholder="Catatan untuk Manajer Departemen..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-xl text-sm">Teruskan</button>
                <button type="button" onclick="closeModal('teruskModal')" class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-xl text-sm">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tolak --}}
<div id="tolakModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-times-circle text-red-500"></i> Tolak Proposal</h3>
        <form action="{{ route('operator.proposal.tolak', $proposal) }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="catatan" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500" placeholder="Jelaskan alasan penolakan..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl text-sm">Tolak</button>
                <button type="button" onclick="closeModal('tolakModal')" class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-xl text-sm">Batal</button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
</script>
@endsection
@endsection
