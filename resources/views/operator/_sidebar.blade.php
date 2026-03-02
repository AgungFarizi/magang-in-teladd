@php $route = request()->route()->getName(); @endphp

<p class="text-primary-300 text-xs font-semibold uppercase tracking-widest px-4 mb-2">Menu Utama</p>
<a href="{{ route('operator.dashboard') }}" class="sidebar-link {{ $route === 'operator.dashboard' ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
</a>
<a href="{{ route('operator.proposal.index') }}" class="sidebar-link {{ str_contains($route,'operator.proposal') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-file-alt"></i><span>Kelola Proposal</span>
</a>
<a href="{{ route('operator.surat.index') }}" class="sidebar-link {{ str_contains($route,'operator.surat') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-envelope-open-text"></i><span>Surat Balasan</span>
</a>

<p class="text-primary-300 text-xs font-semibold uppercase tracking-widest px-4 mb-2 mt-5">Pengaturan</p>
<a href="{{ route('operator.periode.index') }}" class="sidebar-link {{ str_contains($route,'operator.periode') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-calendar-alt"></i><span>Periode Magang</span>
</a>
