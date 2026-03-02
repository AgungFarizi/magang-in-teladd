@extends('layouts.app')
@section('title', 'Proposal Saya')
@section('page-title', 'Proposal Magang')

@section('sidebar-nav')
@include('mahasiswa._sidebar')
@endsection

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Proposal Magang</h2>
            <p class="text-sm text-gray-500">Daftar proposal yang telah kamu ajukan</p>
        </div>
        <a href="{{ route('mahasiswa.proposal.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
            <i class="fas fa-plus"></i> Ajukan Proposal
        </a>
    </div>

    {{-- List Proposal --}}
    @if($proposals->count() > 0)
    <div class="space-y-4">
        @foreach($proposals as $p)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">
                            {{ $p->nomor_proposal }}
                        </span>
                        <x-status-badge :status="$p->status" />
                    </div>
                    <h3 class="font-semibold text-gray-900 text-base mb-1">{{ $p->judul_proposal }}</h3>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-building text-blue-400"></i>
                            {{ $p->divisi_tujuan }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-calendar text-green-400"></i>
                            {{ $p->tanggal_mulai_diinginkan->format('d/m/Y') }} –
                            {{ $p->tanggal_selesai_diinginkan->format('d/m/Y') }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-clock text-gray-400"></i>
                            {{ $p->created_at->diffForHumans() }}
                        </span>
                    </div>

                    {{-- Progress Steps --}}
                    @php
                    $steps = [
                        'diajukan'           => 1,
                        'review_operator'    => 2,
                        'diteruskan_manager' => 2,
                        'review_manager_dep' => 3,
                        'disetujui_manager_dep' => 3,
                        'ditolak_manager_dep'   => 3,
                        'review_manager'     => 4,
                        'diterima'           => 5,
                        'ditolak'            => 5,
                        'aktif'              => 5,
                        'selesai'            => 5,
                        'dibatalkan'         => 5,
                    ];
                    $step = $steps[$p->status] ?? 1;
                    $isDitolak = in_array($p->status, ['ditolak','ditolak_manager_dep','dibatalkan']);
                    @endphp
                    <div class="mt-3 flex items-center gap-1.5">
                        @foreach(['Diajukan','Review Operator','Review Div.','Review Manajer','Keputusan'] as $i => $label)
                        @php $n = $i + 1; @endphp
                        <div class="flex items-center gap-1.5">
                            <div class="flex flex-col items-center">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                    {{ $isDitolak && $n === 5 ? 'bg-red-500 text-white' :
                                       ($n < $step ? 'bg-green-500 text-white' :
                                       ($n === $step ? 'bg-blue-600 text-white' :
                                        'bg-gray-200 text-gray-500')) }}">
                                    @if($isDitolak && $n === 5)
                                        <i class="fas fa-times" style="font-size:8px"></i>
                                    @elseif($n < $step)
                                        <i class="fas fa-check" style="font-size:8px"></i>
                                    @else
                                        {{ $n }}
                                    @endif
                                </div>
                                <span class="text-xs text-gray-400 mt-0.5 hidden sm:block" style="font-size:9px; white-space:nowrap">{{ $label }}</span>
                            </div>
                            @if($i < 4)
                            <div class="w-8 sm:w-12 h-0.5 {{ $n < $step ? 'bg-green-400' : 'bg-gray-200' }} mb-3 sm:mb-0" style="margin-bottom:12px"></div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0 sm:mt-1">
                    @if($p->suratBalasan)
                    <a href="{{ route('mahasiswa.surat.show', $p->suratBalasan) }}"
                        class="text-xs bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-3 py-2 rounded-xl font-medium flex items-center gap-1.5 transition-colors">
                        <i class="fas fa-envelope"></i> Surat
                    </a>
                    @endif
                    <a href="{{ route('mahasiswa.proposal.show', $p) }}"
                        class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-2 rounded-xl font-medium flex items-center gap-1.5 transition-colors">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                </div>
            </div>

            {{-- Catatan reviewer jika ada --}}
            @if(in_array($p->status, ['ditolak','ditolak_manager_dep']) && ($p->catatan_manager ?? $p->catatan_manager_dep ?? $p->catatan_operator))
            @php $catatan = $p->catatan_manager ?? $p->catatan_manager_dep ?? $p->catatan_operator; @endphp
            <div class="mt-3 bg-red-50 border border-red-100 rounded-xl px-4 py-2.5 flex items-start gap-2">
                <i class="fas fa-exclamation-circle text-red-400 mt-0.5 flex-shrink-0 text-sm"></i>
                <div>
                    <p class="text-xs font-medium text-red-700">Alasan Penolakan:</p>
                    <p class="text-xs text-red-600 mt-0.5">{{ $catatan }}</p>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($proposals->hasPages())
    <div class="bg-white rounded-xl p-4">{{ $proposals->links() }}</div>
    @endif

    @else
    {{-- Empty State --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-16 text-center">
        <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-file-alt text-3xl text-blue-300"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-700 mb-1">Belum Ada Proposal</h3>
        <p class="text-sm text-gray-500 mb-6">Kamu belum mengajukan proposal magang. Mulai sekarang!</p>
        <a href="{{ route('mahasiswa.proposal.create') }}"
            class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-lg shadow-blue-500/30">
            <i class="fas fa-plus"></i> Ajukan Proposal Sekarang
        </a>
    </div>
    @endif

</div>
@endsection
