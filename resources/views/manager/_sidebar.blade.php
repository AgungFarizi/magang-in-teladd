@php $route = request()->route()->getName(); @endphp

<p class="text-primary-300 text-xs font-semibold uppercase tracking-widest px-4 mb-2">Menu Utama</p>
<a href="{{ route('manager.dashboard') }}" class="sidebar-link {{ $route === 'manager.dashboard' ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-chart-pie"></i><span>Dashboard</span>
</a>
<a href="{{ route('manager.proposal.index') }}" class="sidebar-link {{ str_contains($route,'proposal') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-file-alt"></i><span>Review Proposal</span>
</a>
<a href="{{ route('manager.laporan') }}" class="sidebar-link {{ str_contains($route,'laporan') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-chart-bar"></i><span>Laporan</span>
</a>

<p class="text-primary-300 text-xs font-semibold uppercase tracking-widest px-4 mb-2 mt-5">Manajemen</p>
<a href="{{ route('manager.operator.index') }}" class="sidebar-link {{ str_contains($route,'operator') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-users-cog"></i><span>Kelola Operator</span>
</a>
<a href="{{ route('manager.token.index') }}" class="sidebar-link {{ str_contains($route,'token') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-key"></i><span>Token Akses</span>
</a>
