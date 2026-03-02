@extends('layouts.app')
@section('title', 'Absensi Harian')
@section('page-title', 'Absensi Harian')
@section('sidebar-nav')
@include('mahasiswa._sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Form Absen Hari Ini --}}
    @if(!$sudahAbsenHariIni)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-5 flex items-center gap-2">
            <i class="fas fa-clock text-blue-500"></i>
            Absensi Hari Ini — {{ now()->isoFormat('dddd, D MMMM Y') }}
        </h3>
        <form action="{{ route('mahasiswa.kehadiran.absen') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status Kehadiran <span class="text-red-500">*</span></label>
                    <select name="status_kehadiran" required class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="hadir">Hadir</option>
                        <option value="sakit">Sakit</option>
                        <option value="izin">Izin</option>
                        <option value="libur">Libur</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jam Masuk <span class="text-red-500">*</span></label>
                    <input type="time" name="jam_masuk" value="{{ now()->format('H:i') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Lokasi</label>
                    <input type="text" name="lokasi_masuk" id="lokasi"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="Alamat / koordinat GPS">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto Absen (opsional)</label>
                    <input type="file" name="foto_masuk" accept="image/*" capture="environment"
                        class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan</label>
                    <input type="text" name="keterangan"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500"
                        placeholder="Keterangan tambahan (opsional)">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm">
                    <i class="fas fa-sign-in-alt mr-2"></i> Absen Masuk
                </button>
                <button type="button" onclick="getLocation()" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition-colors">
                    <i class="fas fa-map-marker-alt mr-1.5"></i> Ambil Lokasi
                </button>
            </div>
        </form>
    </div>
    @else
    {{-- Cek apakah perlu absen keluar --}}
    @php $todayAbsen = $kehadiran->where(fn($k) => $k->tanggal->isToday())->first(); @endphp
    @if($todayAbsen && !$todayAbsen->jam_keluar)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-sign-out-alt text-orange-500"></i> Absen Keluar
        </h3>
        <p class="text-sm text-gray-600 mb-4">Anda sudah absen masuk pukul <strong>{{ $todayAbsen->jam_masuk }}</strong>. Jangan lupa absen keluar!</p>
        <form action="{{ route('mahasiswa.kehadiran.keluar', $todayAbsen) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap gap-4 items-end">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jam Keluar</label>
                <input type="time" name="jam_keluar" value="{{ now()->format('H:i') }}" required
                    class="px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-orange-500">
            </div>
            <button type="submit" class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl text-sm transition-colors">
                <i class="fas fa-sign-out-alt mr-1.5"></i> Absen Keluar
            </button>
        </form>
    </div>
    @else
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500 text-xl"></i>
        <p class="text-sm font-medium text-green-800">Absensi hari ini sudah lengkap. Jangan lupa mengisi log aktivitas!</p>
        <a href="{{ route('mahasiswa.log.index') }}" class="ml-auto text-xs font-semibold text-green-700 hover:text-green-900 bg-green-100 hover:bg-green-200 px-3 py-1.5 rounded-lg transition-colors flex-shrink-0">
            Isi Log →
        </a>
    </div>
    @endif
    @endif

    {{-- Rekap Kehadiran --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Riwayat Kehadiran</h3>
            <span class="text-xs text-gray-500">Total: {{ $kehadiran->total() }} hari</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Jam Masuk</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Jam Keluar</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Verifikasi</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Log</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($kehadiran as $k)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $k->tanggal->isoFormat('dddd') }}</p>
                            <p class="text-xs text-gray-500">{{ $k->tanggal->format('d/m/Y') }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $k->jam_masuk ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $k->jam_keluar ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @php $colors = ['hadir'=>'green','sakit'=>'yellow','izin'=>'blue','alpha'=>'red','libur'=>'gray']; @endphp
                            <x-status-badge :status="$k->status_kehadiran" />
                        </td>
                        <td class="px-6 py-4"><x-status-badge :status="$k->status_verifikasi" /></td>
                        <td class="px-6 py-4">
                            @if($k->logHarian)
                                <span class="text-xs text-green-600 font-medium"><i class="fas fa-check mr-1"></i>Sudah</span>
                            @elseif($k->status_kehadiran === 'hadir')
                                <a href="{{ route('mahasiswa.log.create', $k) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                    <i class="fas fa-plus mr-1"></i>Isi Log
                                </a>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">Belum ada data kehadiran</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kehadiran->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $kehadiran->links() }}</div>
        @endif
    </div>
</div>

@section('scripts')
<script>
function getLocation() {
    if (!navigator.geolocation) { alert('Browser tidak mendukung GPS'); return; }
    navigator.geolocation.getCurrentPosition(
        pos => {
            document.getElementById('lokasi').value = pos.coords.latitude + ',' + pos.coords.longitude;
        },
        () => alert('Gagal mendapatkan lokasi. Pastikan izin lokasi diberikan.')
    );
}
</script>
@endsection
@endsection
