@php $route = request()->route()->getName(); @endphp

<p class="text-primary-300 text-xs font-semibold uppercase tracking-widest px-4 mb-2">Menu</p>
<a href="{{ route('mahasiswa.dashboard') }}" class="sidebar-link {{ $route === 'mahasiswa.dashboard' ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-home"></i><span>Dashboard</span>
</a>
<a href="{{ route('mahasiswa.proposal.index') }}" class="sidebar-link {{ str_contains($route,'mahasiswa.proposal') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-file-alt"></i><span>Proposal Magang</span>
</a>
<a href="{{ route('mahasiswa.kehadiran.index') }}" class="sidebar-link {{ str_contains($route,'mahasiswa.kehadiran') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-calendar-check"></i><span>Absensi Harian</span>
</a>
<a href="{{ route('mahasiswa.log.index') }}" class="sidebar-link {{ str_contains($route,'mahasiswa.log') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-book-open"></i><span>Log Aktivitas</span>
</a>
<a href="{{ route('mahasiswa.surat.index') }}" class="sidebar-link {{ str_contains($route,'mahasiswa.surat') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-envelope-open-text"></i><span>Surat Balasan</span>
</a>
