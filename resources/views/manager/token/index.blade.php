@extends('layouts.app')
@section('title', 'Manajemen Token Akses')
@section('page-title', 'Manajemen Token Akses')
@section('sidebar-nav')
@include('manager._sidebar')
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Generate Token --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-5 flex items-center gap-2">
            <i class="fas fa-plus-circle text-blue-500"></i> Generate Token Baru
        </h3>
        <form action="{{ route('manager.token.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Role <span class="text-red-500">*</span></label>
                    <select name="untuk_role" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500" onchange="toggleDivisi(this.value)">
                        <option value="">-- Pilih Role --</option>
                        <option value="manager_departemen">Manager Departemen</option>
                        <option value="operator">Operator</option>
                        <option value="pembimbing_lapang">Pembimbing Lapang</option>
                    </select>
                </div>
                <div id="divisiField">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Divisi</label>
                    <input type="text" name="divisi" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500" placeholder="Nama divisi">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan</label>
                    <input type="text" name="keterangan" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500" placeholder="Keterangan token (opsional)">
                </div>
            </div>
            <button type="submit" class="mt-4 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm">
                <i class="fas fa-key mr-2"></i> Generate Token
            </button>
        </form>

        @if(session('success'))
        <div class="mt-4 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500"></i>
            <div>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                <p class="text-xs text-green-600 mt-0.5">Salin token ini sebelum meninggalkan halaman.</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Token List --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Daftar Token</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Token</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Divisi</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Digunakan Oleh</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tokens as $token)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <code class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-lg font-mono">{{ $token->token }}</code>
                                <button onclick="copyToken('{{ $token->token }}')" class="text-gray-400 hover:text-gray-600 text-xs">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">{{ $token->keterangan }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-1 rounded-lg font-medium">
                                {{ ucwords(str_replace('_',' ',$token->untuk_role)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $token->divisi ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($token->is_used)
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-lg">Sudah Dipakai</span>
                            @elseif(!$token->is_active)
                                <span class="text-xs bg-red-50 text-red-500 px-2 py-1 rounded-lg">Dinonaktifkan</span>
                            @else
                                <span class="text-xs bg-green-50 text-green-600 px-2 py-1 rounded-lg">Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $token->penggunaToken?->nama_lengkap ?? '-' }}
                            @if($token->digunakan_pada)
                            <p class="text-xs text-gray-400">{{ $token->digunakan_pada->format('d/m/Y') }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($token->is_active && !$token->is_used)
                            <form action="{{ route('manager.token.destroy', $token) }}" method="POST" onsubmit="return confirm('Nonaktifkan token ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">
                                    <i class="fas fa-ban mr-1"></i> Nonaktifkan
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-sm">Belum ada token yang dibuat</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tokens->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $tokens->links() }}</div>
        @endif
    </div>
</div>

@section('scripts')
<script>
function copyToken(token) {
    navigator.clipboard.writeText(token).then(() => {
        alert('Token disalin: ' + token);
    });
}
function toggleDivisi(role) {
    const field = document.getElementById('divisiField');
    field.style.display = ['operator','pembimbing_lapang','manager_departemen'].includes(role) ? 'block' : 'none';
}
</script>
@endsection
@endsection
