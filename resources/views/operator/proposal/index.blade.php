@extends('layouts.app')
@section('title', 'Kelola Proposal')
@section('page-title', 'Kelola Proposal')

@section('sidebar-nav')
@include('operator._sidebar')
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    {{-- Filter --}}
    <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <h3 class="font-semibold text-gray-800">Semua Proposal</h3>
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nomor / nama..."
                class="px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 w-48">
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                <option value="review_operator" {{ request('status') === 'review_operator' ? 'selected' : '' }}>Review Operator</option>
                <option value="diteruskan_manager" {{ request('status') === 'diteruskan_manager' ? 'selected' : '' }}>Diteruskan</option>
                <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm hover:bg-blue-700">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nomor</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Mahasiswa</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Judul</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Divisi</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($proposals as $p)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">{{ $p->nomor_proposal }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800">{{ $p->pengaju->nama_lengkap }}</p>
                        <p class="text-xs text-gray-500">{{ $p->pengaju->nim }}</p>
                    </td>
                    <td class="px-6 py-4 max-w-xs">
                        <p class="text-gray-800 truncate">{{ $p->judul_proposal }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded-lg font-medium">{{ $p->divisi_tujuan }}</span>
                    </td>
                    <td class="px-6 py-4"><x-status-badge :status="$p->status" /></td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $p->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('operator.proposal.show', $p) }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-medium">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">Tidak ada proposal ditemukan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($proposals->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $proposals->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
