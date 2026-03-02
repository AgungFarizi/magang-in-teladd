@php $route = request()->route()->getName(); @endphp

<p class="text-primary-300 text-xs font-semibold uppercase tracking-widest px-4 mb-2">Menu</p>
<a href="{{ route('manager-dep.dashboard') }}" class="sidebar-link {{ $route === 'manager-dep.dashboard' ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-chart-pie"></i><span>Dashboard</span>
</a>
<a href="{{ route('manager-dep.proposal.index') }}" class="sidebar-link {{ str_contains($route,'manager-dep.proposal') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-file-alt"></i><span>Review Proposal</span>
</a>
<a href="{{ route('manager-dep.mahasiswa') }}" class="sidebar-link {{ str_contains($route,'mahasiswa') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-users"></i><span>Mahasiswa Divisi</span>
</a>

<p class="text-primary-300 text-xs font-semibold uppercase tracking-widest px-4 mb-2 mt-5">Manajemen</p>
<a href="{{ route('manager-dep.pembimbing.index') }}" class="sidebar-link {{ str_contains($route,'pembimbing') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-chalkboard-teacher"></i><span>Pembimbing Lapang</span>
</a>
