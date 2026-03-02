@extends('layouts.app')
@section('title', 'Detail Proposal')
@section('page-title', 'Detail Proposal')
@section('breadcrumb')
    <a href="{{ route('mahasiswa.proposal.index') }}" class="hover:text-gray-700">Proposal Saya</a>
    <span class="mx-1">/</span><span>{{ $proposal->nomor_proposal }}</span>
@endsection

@section('sidebar-nav')
@include('mahasiswa._sidebar')
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Status Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-2">
                    <span class="font-mono text-sm bg-gray-100 text-gray-600 px-2.5 py-1 rounded-lg">{{ $proposal->nomor_proposal }}</span>
                    <x-status-badge :status="$proposal->status" />
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ $proposal->judul_proposal }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Diajukan {{ $proposal->created_at->diffForHumans() }} •
                    Divisi: <span class="text-blue-600 font-medium">{{ $proposal->divisi_tujuan }}</span>
                </p>
            </div>
            @if($proposal->suratBalasan)
            <a href="{{ route('mahasiswa.surat.show', $proposal->suratBalasan) }}"
                class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl text-sm font-semibold transition-colors border border-indigo-200">
                <i class="fas fa-envelope-open-text"></i> Lihat Surat Balasan
            </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Rencana --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-blue-500"></i> Rencana Magang
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-500 mb-0.5">Periode</p>
                        <p class="font-medium text-gray-800">{{ $proposal->periode->nama_periode }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-500 mb-0.5">Divisi Tujuan</p>
                        <p class="font-semibold text-blue-600">{{ $proposal->divisi_tujuan }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-500 mb-0.5">Tanggal Mulai</p>
                        <p class="font-medium text-gray-800">{{ $proposal->tanggal_mulai_diinginkan->format('d F Y') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-500 mb-0.5">Tanggal Selesai</p>
                        <p class="font-medium text-gray-800">{{ $proposal->tanggal_selesai_diinginkan->format('d F Y') }}</p>
                    </div>
                </div>
                <div class="mb-3">
                    <p class="text-xs text-gray-500 mb-1.5">Latar Belakang</p>
                    <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 rounded-xl p-3">{{ $proposal->latar_belakang }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1.5">Tujuan</p>
                    <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 rounded-xl p-3">{{ $proposal->tujuan }}</p>
                </div>
            </div>

            {{-- Anggota --}}
            @if($proposal->anggota->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-users text-blue-500"></i> Anggota Kelompok
                </h3>
                <div class="space-y-2">
                    @foreach($proposal->anggota as $a)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-blue-600">{{ strtoupper(substr($a->nama_lengkap,0,1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-gray-800">{{ $a->nama_lengkap }}</p>
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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-paperclip text-blue-500"></i> Dokumen
                </h3>
                <div class="space-y-2">
                    @foreach([['file_proposal','Proposal'],['file_surat_pengantar','Surat Pengantar'],['file_transkrip','Transkrip'],['file_cv','CV']] as [$f,$l])
                    @if($proposal->$f)
                    <a href="{{ asset('storage/'.$proposal->$f) }}" target="_blank"
                        class="flex items-center gap-2.5 p-3 bg-red-50 hover:bg-red-100 rounded-xl transition-colors group text-sm">
                        <i class="fas fa-file-pdf text-red-500"></i>
                        <span class="flex-1 text-gray-700 group-hover:text-red-700">{{ $l }}</span>
                        <i class="fas fa-external-link-alt text-xs text-gray-400"></i>
                    </a>
                    @else
                    <div class="flex items-center gap-2.5 p-3 bg-gray-50 rounded-xl text-sm">
                        <i class="fas fa-file text-gray-300"></i>
                        <span class="text-gray-400">{{ $l }} — tidak ada</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- Riwayat Review --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-history text-blue-500"></i> Riwayat Review
                </h3>
                <div class="space-y-3 text-sm">
                    @if($proposal->operator)
                    <div class="border-l-2 border-blue-300 pl-3">
                        <p class="font-medium text-gray-700 text-xs uppercase tracking-wide text-blue-600 mb-0.5">Operator</p>
                        <p class="text-gray-800">{{ $proposal->operator->nama_lengkap }}</p>
                        <p class="text-xs text-gray-400">{{ $proposal->tgl_review_operator?->format('d/m/Y H:i') }}</p>
                        @if($proposal->catatan_operator)
                        <p class="text-xs text-gray-600 mt-1 bg-gray-50 rounded-lg p-2">{{ $proposal->catatan_operator }}</p>
                        @endif
                    </div>
                    @endif
                    @if($proposal->managerDep)
                    <div class="border-l-2 border-indigo-300 pl-3">
                        <p class="font-medium text-xs uppercase tracking-wide text-indigo-600 mb-0.5">Manager Departemen</p>
                        <p class="text-gray-800">{{ $proposal->managerDep->nama_lengkap }}</p>
                        <p class="text-xs text-gray-400">{{ $proposal->tgl_review_manager_dep?->format('d/m/Y H:i') }}</p>
                        @if($proposal->catatan_manager_dep)
                        <p class="text-xs text-gray-600 mt-1 bg-gray-50 rounded-lg p-2">{{ $proposal->catatan_manager_dep }}</p>
                        @endif
                    </div>
                    @endif
                    @if($proposal->manager)
                    <div class="border-l-2 border-purple-300 pl-3">
                        <p class="font-medium text-xs uppercase tracking-wide text-purple-600 mb-0.5">Manager</p>
                        <p class="text-gray-800">{{ $proposal->manager->nama_lengkap }}</p>
                        <p class="text-xs text-gray-400">{{ $proposal->tgl_review_manager?->format('d/m/Y H:i') }}</p>
                        @if($proposal->catatan_manager)
                        <p class="text-xs text-gray-600 mt-1 bg-gray-50 rounded-lg p-2">{{ $proposal->catatan_manager }}</p>
                        @endif
                    </div>
                    @endif
                    @if($proposal->pembimbing)
                    <div class="border-l-2 border-green-300 pl-3">
                        <p class="font-medium text-xs uppercase tracking-wide text-green-600 mb-0.5">Pembimbing Ditugaskan</p>
                        <p class="text-gray-800">{{ $proposal->pembimbing->nama_lengkap }}</p>
                        <p class="text-xs text-gray-400">Divisi {{ $proposal->pembimbing->divisi }}</p>
                    </div>
                    @endif
                    @if(!$proposal->operator && !$proposal->managerDep && !$proposal->manager)
                    <p class="text-xs text-gray-400 text-center py-3">Belum ada review</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="pb-4">
        <a href="{{ route('mahasiswa.proposal.index') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 font-medium">
            <i class="fas fa-arrow-left"></i> Kembali ke daftar proposal
        </a>
    </div>
</div>
@endsection
