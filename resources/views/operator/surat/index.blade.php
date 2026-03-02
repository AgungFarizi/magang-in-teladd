@extends('layouts.app')
@section('title', 'Surat Balasan')
@section('page-title', 'Surat Balasan')

@section('sidebar-nav')
@include('operator._sidebar')
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800">Daftar Surat Balasan</h3>
        <span class="text-xs text-gray-500">Total: {{ $surat->total() }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nomor Surat</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Mahasiswa</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Jenis</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Perihal</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Dibaca</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">PDF</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($surat as $s)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">{{ $s->nomor_surat }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800">{{ $s->proposal->pengaju->nama_lengkap }}</p>
                        <p class="text-xs text-gray-500">{{ $s->proposal->nomor_proposal }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs {{ $s->jenis === 'penerimaan' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }} px-2 py-1 rounded-lg font-medium capitalize">
                            {{ $s->jenis }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-700">{{ Str::limit($s->perihal, 40) }}</td>
                    <td class="px-6 py-4 text-xs text-gray-500">{{ $s->tanggal_surat->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        @if($s->sudah_dibaca)
                        <span class="text-xs text-green-600"><i class="fas fa-check-circle"></i> Ya</span>
                        @else
                        <span class="text-xs text-gray-400"><i class="far fa-circle"></i> Belum</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($s->file_surat)
                        <a href="{{ asset('storage/'.$s->file_surat) }}" target="_blank"
                            class="text-xs text-red-500 hover:text-red-700 font-medium">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        @else
                        <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">Belum ada surat balasan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($surat->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $surat->links() }}</div>
    @endif
</div>
@endsection
