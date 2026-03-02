@extends('layouts.app')
@section('title', 'Review Proposal')
@section('page-title', 'Review Proposal')

@section('sidebar-nav')
@include('manager._sidebar')
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    {{-- Header & Filter --}}
    <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <h3 class="font-semibold text-gray-800">Proposal Menunggu Persetujuan Manager</h3>
        <form method="GET" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nomor / nama / judul..."
                class="px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 w-56">
            <select name="divisi" class="px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Divisi</option>
                @foreach($divisiList as $d)
                <option value="{{ $d }}" {{ request('divisi') === $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm hover:bg-blue-700 transition-colors">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nomor</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Mahasiswa</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Judul Proposal</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Divisi</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Manager Dep.</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($proposals as $p)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded-lg">{{ $p->nomor_proposal }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-medium text-gray-800">{{ $p->pengaju->nama_lengkap }}</p>
                            <p class="text-xs text-gray-500">{{ $p->pengaju->nim }} • {{ $p->pengaju->institusi }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-gray-800 line-clamp-2 max-w-xs">{{ $p->judul_proposal }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded-lg font-medium">{{ $p->divisi_tujuan }}</span>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-600">
                        {{ $p->managerDep?->nama_lengkap ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $p->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('manager.proposal.show', $p) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-medium transition-colors">
                            <i class="fas fa-eye"></i> Review
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <i class="fas fa-check-circle text-4xl text-green-300 mb-3"></i>
                        <p class="text-gray-500 font-medium">Tidak ada proposal yang perlu direview</p>
                        <p class="text-gray-400 text-xs mt-1">Semua proposal sudah diproses</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($proposals->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $proposals->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
