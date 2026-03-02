@extends('layouts.app')
@section('title', 'Detail Proposal')
@section('page-title', 'Detail Proposal')
@section('sidebar-nav')
@include('manager-dep._sidebar')
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-2">
                    <span class="font-mono text-sm bg-gray-100 text-gray-600 px-2.5 py-1 rounded-lg">{{ $proposal->nomor_proposal }}</span>
                    <x-status-badge :status="$proposal->status" />
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ $proposal->judul_proposal }}</h2>
                <p class="text-sm text-gray-500 mt-1">Divisi: <span class="text-blue-600 font-semibold">{{ $proposal->divisi_tujuan }}</span></p>
            </div>
            @if($proposal->status === 'review_manager_dep')
            <div class="flex gap-2 flex-shrink-0">
                <button onclick="openModal('setujuiModal')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl shadow-sm">
                    <i class="fas fa-check mr-1.5"></i> Setujui
                </button>
                <button onclick="openModal('tolakModal')" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 text-sm font-semibold rounded-xl">
                    <i class="fas fa-times mr-1.5"></i> Tolak
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- Isi Proposal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-user text-blue-500"></i> Data Pengaju</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                @foreach([['Nama','nama_lengkap'],['NIM','nim'],['Email','email'],['Institusi','institusi'],['Jurusan','jurusan']] as [$l,$f])
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 mb-0.5">{{ $l }}</p>
                    <p class="font-medium text-gray-800">{{ $proposal->pengaju->$f ?? '—' }}</p>
                </div>
                @endforeach
            </div>
            <div class="mt-4 space-y-3 text-sm">
                <div><p class="text-xs text-gray-500 mb-1">Latar Belakang</p>
                    <p class="text-gray-700 bg-gray-50 rounded-xl p-3 leading-relaxed">{{ $proposal->latar_belakang }}</p></div>
                <div><p class="text-xs text-gray-500 mb-1">Tujuan</p>
                    <p class="text-gray-700 bg-gray-50 rounded-xl p-3 leading-relaxed">{{ $proposal->tujuan }}</p></div>
            </div>
        </div>
        <div class="space-y-4">
            {{-- Dokumen --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-paperclip text-blue-500"></i> Dokumen</h3>
                <div class="space-y-2">
                    @foreach([['file_proposal','Proposal'],['file_surat_pengantar','Surat Pengantar'],['file_transkrip','Transkrip']] as [$f,$l])
                    @if($proposal->$f)
                    <a href="{{ asset('storage/'.$proposal->$f) }}" target="_blank" class="flex items-center gap-2 p-2.5 bg-red-50 hover:bg-red-100 rounded-xl text-xs group">
                        <i class="fas fa-file-pdf text-red-500"></i>
                        <span class="flex-1 text-gray-700 group-hover:text-red-700">{{ $l }}</span>
                        <i class="fas fa-external-link-alt text-gray-400 text-xs"></i>
                    </a>
                    @endif
                    @endforeach
                </div>
            </div>
            {{-- Catatan Operator --}}
            @if($proposal->catatan_operator)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 text-sm flex items-center gap-2"><i class="fas fa-comment text-blue-500"></i> Catatan Operator</h3>
                <p class="text-sm text-gray-700 bg-gray-50 rounded-xl p-3">{{ $proposal->catatan_operator }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Setujui --}}
<div id="setujuiModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> Setujui & Teruskan ke Manager</h3>
        <form action="{{ route('manager-dep.proposal.approve', $proposal) }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan (opsional)</label>
                <textarea name="catatan" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500" placeholder="Catatan untuk Manager..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-xl text-sm">Setujui & Teruskan</button>
                <button type="button" onclick="closeModal('setujuiModal')" class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-xl text-sm">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tolak --}}
<div id="tolakModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-times-circle text-red-500"></i> Tolak Proposal</h3>
        <form action="{{ route('manager-dep.proposal.reject', $proposal) }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="catatan" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500" placeholder="Jelaskan alasan penolakan..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl text-sm">Tolak Proposal</button>
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
