@php $route = request()->route()->getName(); @endphp

<p class="text-primary-300 text-xs font-semibold uppercase tracking-widest px-4 mb-2">Menu</p>
<a href="{{ route('pembimbing.dashboard') }}" class="sidebar-link {{ $route === 'pembimbing.dashboard' ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
</a>
<a href="{{ route('pembimbing.kehadiran.index') }}" class="sidebar-link {{ str_contains($route,'kehadiran') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-calendar-check"></i><span>Verifikasi Kehadiran</span>
</a>
<a href="{{ route('pembimbing.log.index') }}" class="sidebar-link {{ str_contains($route,'pembimbing.log') ? 'active' : 'text-primary-100' }}">
    <i class="fas fa-book-open"></i><span>Verifikasi Log</span>
</a>
