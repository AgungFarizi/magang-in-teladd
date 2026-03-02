<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — TELLINTER</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; margin: 0; background: #f3f4f6; }
        .app-wrapper { display: flex; height: 100vh; overflow: hidden; }
        #sidebar {
            width: 256px;
            min-width: 256px;
            display: flex;
            flex-direction: column;
            background: linear-gradient(to bottom, #1e40af, #1e3a8a);
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            height: 100vh;
            overflow-y: auto;
            position: relative;
            z-index: 50;
            transition: transform 0.3s ease;
            flex-shrink: 0;
        }
        @media (max-width: 1023px) {
            #sidebar { position: fixed; top:0; left:0; bottom:0; transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #hamburger-btn { display: block !important; }
        }
        .main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; border-radius: 8px;
            font-size: 14px; font-weight: 500; text-decoration: none;
            color: #bfdbfe; transition: all 0.15s; margin-bottom: 2px;
        }
        .sidebar-link:hover { background: rgba(255,255,255,0.12); color: white; }
        .sidebar-link.active { background: white; color: #1d4ed8; box-shadow: 0 1px 4px rgba(0,0,0,0.15); font-weight: 600; }
        .sidebar-link.active i { color: #1d4ed8; }
        .sidebar-link i { width: 18px; text-align: center; flex-shrink: 0; font-size: 14px; }
        #sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 40; }
        #hamburger-btn { display: none; }
    </style>
</head>
<body>
<div class="app-wrapper">

    {{-- SIDEBAR --}}
    <aside id="sidebar">
        <div style="display:flex;align-items:center;gap:12px;padding:20px 20px 16px;border-bottom:1px solid rgba(255,255,255,0.1);flex-shrink:0">
            <div style="width:40px;height:40px;background:white;border-radius:10px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.2)">
                <i class="fas fa-graduation-cap" style="color:#1d4ed8;font-size:18px"></i>
            </div>
            <div>
                <p style="color:white;font-weight:800;font-size:17px;margin:0;line-height:1.2">TELLINTER</p>
                <p style="color:#93c5fd;font-size:11px;margin:0">Sistem Magang</p>
            </div>
        </div>

        <div style="padding:14px 16px;border-bottom:1px solid rgba(255,255,255,0.1);flex-shrink:0">
            <div style="display:flex;align-items:center;gap:10px">
                <img src="{{ auth()->user()->foto_profil_url }}" alt="Avatar"
                    style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.3);flex-shrink:0">
                <div style="min-width:0">
                    <p style="color:white;font-weight:600;font-size:13px;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ auth()->user()->nama_lengkap }}</p>
                    <span style="display:inline-block;background:#1d4ed8;color:#bfdbfe;font-size:10px;font-weight:600;padding:1px 8px;border-radius:20px;margin-top:2px">{{ auth()->user()->role_label }}</span>
                </div>
            </div>
        </div>

        <nav style="flex:1;padding:12px 10px;overflow-y:auto">
            @yield('sidebar-nav')
        </nav>

        <div style="padding:10px;border-top:1px solid rgba(255,255,255,0.1);flex-shrink:0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link" style="width:100%;background:none;border:none;cursor:pointer">
                    <i class="fas fa-sign-out-alt"></i><span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <div id="sidebar-overlay" onclick="closeSidebar()"></div>

    {{-- MAIN AREA --}}
    <div class="main-area">
        <header style="background:white;border-bottom:1px solid #e5e7eb;padding:12px 20px;display:flex;align-items:center;gap:12px;z-index:30;box-shadow:0 1px 3px rgba(0,0,0,0.06);flex-shrink:0">
            <button id="hamburger-btn" onclick="openSidebar()" style="background:none;border:none;cursor:pointer;padding:4px;color:#6b7280">
                <i class="fas fa-bars" style="font-size:20px"></i>
            </button>
            <div style="flex:1;min-width:0">
                <h1 style="font-size:17px;font-weight:600;color:#111827;margin:0">@yield('page-title', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                <nav style="font-size:11px;color:#9ca3af;margin-top:1px">@yield('breadcrumb')</nav>
                @endif
            </div>
            <div style="position:relative">
                <button onclick="toggleNotif(event)" style="background:none;border:none;cursor:pointer;position:relative;padding:8px;color:#6b7280;border-radius:8px">
                    <i class="fas fa-bell" style="font-size:18px"></i>
                    @php $unread = auth()->user()->notifikasi()->where('sudah_dibaca', false)->count(); @endphp
                    @if($unread > 0)
                    <span style="position:absolute;top:4px;right:4px;width:16px;height:16px;background:#ef4444;color:white;font-size:9px;font-weight:700;border-radius:50%;display:flex;align-items:center;justify-content:center">{{ $unread > 9 ? '9+' : $unread }}</span>
                    @endif
                </button>
                <div id="notif-dropdown" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:300px;background:white;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,0.12);border:1px solid #e5e7eb;z-index:999">
                    <div style="padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between">
                        <span style="font-weight:600;font-size:14px">Notifikasi</span>
                        @if($unread > 0)<span style="font-size:12px;color:#2563eb">{{ $unread }} belum dibaca</span>@endif
                    </div>
                    <div style="max-height:280px;overflow-y:auto">
                        @forelse(auth()->user()->notifikasi()->latest()->take(8)->get() as $notif)
                        <div style="padding:12px 16px;{{ !$notif->sudah_dibaca ? 'background:#eff6ff;' : '' }}border-bottom:1px solid #f9fafb">
                            <p style="font-size:13px;font-weight:500;color:#111827;margin:0">{{ $notif->judul }}</p>
                            <p style="font-size:12px;color:#6b7280;margin:2px 0 0">{{ Str::limit($notif->pesan, 70) }}</p>
                            <p style="font-size:11px;color:#9ca3af;margin:4px 0 0">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                        @empty
                        <div style="padding:24px;text-align:center;color:#9ca3af;font-size:13px">
                            <i class="fas fa-bell-slash" style="font-size:24px;display:block;margin-bottom:8px;color:#d1d5db"></i>Tidak ada notifikasi
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <img src="{{ auth()->user()->foto_profil_url }}" alt="Profil" style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid #e5e7eb;cursor:pointer">
        </header>

        @if(session('success'))
        <div style="margin:16px 20px 0;display:flex;align-items:center;gap:10px;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:12px 16px;border-radius:12px;font-size:14px">
            <i class="fas fa-check-circle" style="color:#22c55e;flex-shrink:0"></i><span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;color:#4ade80">&times;</button>
        </div>
        @endif
        @if(session('error'))
        <div style="margin:16px 20px 0;display:flex;align-items:center;gap:10px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:12px;font-size:14px">
            <i class="fas fa-exclamation-circle" style="color:#ef4444;flex-shrink:0"></i><span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;color:#f87171">&times;</button>
        </div>
        @endif
        @if(session('warning'))
        <div style="margin:16px 20px 0;display:flex;align-items:center;gap:10px;background:#fffbeb;border:1px solid #fde68a;color:#92400e;padding:12px 16px;border-radius:12px;font-size:14px">
            <i class="fas fa-exclamation-triangle" style="color:#f59e0b;flex-shrink:0"></i><span>{{ session('warning') }}</span>
            <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;color:#fbbf24">&times;</button>
        </div>
        @endif
        @if($errors->any())
        <div style="margin:16px 20px 0;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:12px;font-size:14px">
            <p style="font-weight:600;margin:0 0 6px;display:flex;align-items:center;gap:6px"><i class="fas fa-exclamation-circle" style="color:#ef4444"></i> Terdapat kesalahan:</p>
            <ul style="margin:0;padding-left:20px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
        @endif

        <main style="flex:1;overflow-y:auto;padding:24px 20px">
            @yield('content')
        </main>
    </div>
</div>

<script>
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebar-overlay').style.display = 'block';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebar-overlay').style.display = 'none';
}
function toggleNotif(e) {
    e.stopPropagation();
    const d = document.getElementById('notif-dropdown');
    d.style.display = d.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function() {
    const d = document.getElementById('notif-dropdown');
    if (d) d.style.display = 'none';
});
</script>
@yield('scripts')
</body>
</html>
