@extends('layouts.app')
@section('title', 'Detail Proposal')
@section('page-title', 'Detail Proposal')
@section('breadcrumb')
    <a href="{{ route('manager.proposal.index') }}" class="hover:text-gray-700">Review Proposal</a>
    <span class="mx-1">/</span>
    <span>{{ $proposal->nomor_proposal }}</span>
@endsection

@section('sidebar-nav')
@include('manager._sidebar')
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="font-mono text-sm bg-gray-100 text-gray-600 px-3 py-1 rounded-lg">{{ $proposal->nomor_proposal }}</span>
                    <x-status-badge :status="$proposal->status" />
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $proposal->judul_proposal }}</h2>
                <p class="text-sm text-gray-500">
                    Diajukan oleh <span class="font-medium text-gray-700">{{ $proposal->pengaju->nama_lengkap }}</span> •
                    {{ $proposal->created_at->format('d F Y') }} •
                    Divisi: <span class="text-blue-600 font-medium">{{ $proposal->divisi_tujuan }}</span>
                </p>
            </div>
            @if($proposal->status === 'review_manager')
            <div class="flex gap-2 flex-shrink-0">
                <button onclick="openModal('approveModal')"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                    <i class="fas fa-check mr-1.5"></i> Setujui
                </button>
                <button onclick="openModal('rejectModal')"
                    class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold rounded-xl transition-colors border border-red-200">
                    <i class="fas fa-times mr-1.5"></i> Tolak
                </button>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Pengaju --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-user text-blue-500"></i> Data Pengaju</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    @foreach([['Nama','nama_lengkap'],['NIM','nim'],['Email','email'],['Institusi','institusi'],['Jurusan','jurusan'],['Telepon','no_telepon']] as [$label,$field])
                    <div>
                        <p class="text-gray-500 text-xs mb-0.5">{{ $label }}</p>
                        <p class="font-medium text-gray-800">{{ $proposal->pengaju->$field ?? '-' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Rencana Magang --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-calendar-alt text-blue-500"></i> Rencana Magang</h3>
                <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <p class="text-gray-500 text-xs mb-0.5">Tanggal Mulai</p>
                        <p class="font-medium text-gray-800">{{ $proposal->tanggal_mulai_diinginkan->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-0.5">Tanggal Selesai</p>
                        <p class="font-medium text-gray-800">{{ $proposal->tanggal_selesai_diinginkan->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-0.5">Periode</p>
                        <p class="font-medium text-gray-800">{{ $proposal->periode->nama_periode }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-0.5">Divisi Tujuan</p>
                        <p class="font-semibold text-blue-600">{{ $proposal->divisi_tujuan }}</p>
                    </div>
                </div>
                <div class="mb-4">
                    <p class="text-gray-500 text-xs mb-1">Latar Belakang</p>
                    <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 rounded-xl p-3">{{ $proposal->latar_belakang }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs mb-1">Tujuan</p>
                    <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 rounded-xl p-3">{{ $proposal->tujuan }}</p>
                </div>
            </div>

            {{-- Anggota --}}
            @if($proposal->anggota->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-users text-blue-500"></i> Anggota Kelompok ({{ $proposal->anggota->count() }})</h3>
                <div class="space-y-3">
                    @foreach($proposal->anggota as $a)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-blue-600">{{ strtoupper(substr($a->nama_lengkap,0,1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-gray-800">{{ $a->nama_lengkap }}</p>
                            <p class="text-xs text-gray-500">{{ $a->nim }} • {{ $a->email }}</p>
                        </div>
                        <span class="text-xs font-medium {{ $a->peran === 'ketua' ? 'text-yellow-600 bg-yellow-50' : 'text-gray-500 bg-gray-100' }} px-2 py-1 rounded-lg capitalize">{{ $a->peran }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar Panel --}}
        <div class="space-y-5">
            {{-- Dokumen --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-paperclip text-blue-500"></i> Dokumen</h3>
                <div class="space-y-2">
                    @foreach([['file_proposal','Proposal Utama','pdf'],['file_surat_pengantar','Surat Pengantar','file-pdf'],['file_transkrip','Transkrip Nilai','list-alt'],['file_cv','Curriculum Vitae','id-card']] as [$field,$label,$icon])
                    @if($proposal->$field)
                    <a href="{{ asset('storage/'.$proposal->$field) }}" target="_blank"
                        class="flex items-center gap-2.5 p-3 bg-red-50 hover:bg-red-100 rounded-xl transition-colors group">
                        <i class="fas fa-file-{{ $icon }} text-red-500 text-lg"></i>
                        <span class="text-sm font-medium text-gray-700 group-hover:text-red-700 flex-1">{{ $label }}</span>
                        <i class="fas fa-external-link-alt text-xs text-gray-400"></i>
                    </a>
                    @else
                    <div class="flex items-center gap-2.5 p-3 bg-gray-50 rounded-xl">
                        <i class="fas fa-file text-gray-300 text-lg"></i>
                        <span class="text-sm text-gray-400">{{ $label }} — tidak ada</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- History Review --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-history text-blue-500"></i> Riwayat Review</h3>
                <div class="space-y-3 text-sm">
                    @if($proposal->operator)
                    <div class="border-l-2 border-blue-300 pl-3">
                        <p class="font-medium text-gray-700">Operator: {{ $proposal->operator->nama_lengkap }}</p>
                        <p class="text-xs text-gray-500">{{ $proposal->tgl_review_operator?->format('d/m/Y H:i') }}</p>
                        @if($proposal->catatan_operator)<p class="text-xs text-gray-600 mt-1 bg-gray-50 rounded-lg p-2">{{ $proposal->catatan_operator }}</p>@endif
                    </div>
                    @endif
                    @if($proposal->managerDep)
                    <div class="border-l-2 border-indigo-300 pl-3">
                        <p class="font-medium text-gray-700">Manajer Dep.: {{ $proposal->managerDep->nama_lengkap }}</p>
                        <p class="text-xs text-gray-500">{{ $proposal->tgl_review_manager_dep?->format('d/m/Y H:i') }}</p>
                        @if($proposal->catatan_manager_dep)<p class="text-xs text-gray-600 mt-1 bg-gray-50 rounded-lg p-2">{{ $proposal->catatan_manager_dep }}</p>@endif
                    </div>
                    @endif
                    @if(!$proposal->operator && !$proposal->managerDep)
                    <p class="text-gray-400 text-xs">Belum ada review</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Approve --}}
<div id="approveModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i> Setujui Proposal
        </h3>
        <form action="{{ route('manager.proposal.approve', $proposal) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan (opsional)</label>
                <textarea name="catatan" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500" placeholder="Catatan persetujuan..."></textarea>
            </div>
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tugaskan Pembimbing</label>
                <select name="pembimbing_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500">
                    <option value="">-- Pilih Pembimbing (Opsional) --</option>
                    @foreach(\App\Models\Pengguna::where('role','pembimbing_lapang')->where('is_active',true)->where('divisi',$proposal->divisi_tujuan)->get() as $pb)
                    <option value="{{ $pb->id }}">{{ $pb->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors">
                    <i class="fas fa-check mr-1.5"></i> Setujui Proposal
                </button>
                <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2.5 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-xl text-sm transition-colors">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Reject --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-times-circle text-red-500"></i> Tolak Proposal
        </h3>
        <form action="{{ route('manager.proposal.reject', $proposal) }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea name="catatan" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500" placeholder="Jelaskan alasan penolakan..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors">
                    <i class="fas fa-times mr-1.5"></i> Tolak Proposal
                </button>
                <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2.5 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-xl text-sm transition-colors">Batal</button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
document.querySelectorAll('[id$="Modal"]').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.add('hidden'); });
});
</script>
@endsection
@endsection
