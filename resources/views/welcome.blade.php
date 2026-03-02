<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TELLINTER — Sistem Manajemen Magang</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --green-dark:   #0a4a2e;
            --green-mid:    #156f44;
            --green-main:   #1a9055;
            --green-light:  #22c26e;
            --green-pale:   #e8f8ef;
            --orange-main:  #f47c20;
            --orange-light: #fb9a45;
            --orange-pale:  #fff4ea;
            --white:        #ffffff;
            --gray-100:     #f5f7f4;
            --gray-200:     #e8ede6;
            --gray-400:     #8fa48a;
            --gray-700:     #3a4a35;
            --gray-900:     #111a0e;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--white);
            color: var(--gray-700);
            overflow-x: hidden;
        }

        /* ── NAVBAR ── */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            padding: 0 2rem;
            background: rgba(255,255,255,0.96);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--gray-200);
            height: 68px;
            display: flex; align-items: center;
        }
        .navbar-inner {
            max-width: 1200px; width: 100%; margin: auto;
            display: flex; align-items: center; justify-content: space-between;
        }
        .logo {
            display: flex; align-items: center; gap: 10px; text-decoration: none;
        }
        .logo-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--green-main), var(--green-dark));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(26,144,85,0.35);
        }
        .logo-icon i { color: #fff; font-size: 16px; }
        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 22px; font-weight: 900;
            color: var(--gray-900);
            letter-spacing: 0.02em;
        }
        .logo-text span { color: var(--orange-main); }
        .nav-links { display: flex; align-items: center; gap: 8px; }
        .btn-ghost {
            font-size: 14px; font-weight: 500; color: var(--gray-700);
            padding: 8px 18px; border-radius: 8px;
            text-decoration: none; transition: background 0.2s;
            border: none; background: none; cursor: pointer;
        }
        .btn-ghost:hover { background: var(--gray-100); }
        .btn-primary {
            font-size: 14px; font-weight: 700;
            color: var(--white); background: var(--green-main);
            padding: 9px 22px; border-radius: 10px;
            text-decoration: none; transition: all 0.2s;
            border: none; cursor: pointer;
            box-shadow: 0 3px 10px rgba(26,144,85,0.3);
        }
        .btn-primary:hover { background: var(--green-mid); transform: translateY(-1px); box-shadow: 0 5px 16px rgba(26,144,85,0.4); }

        /* ── HERO ── */
        .hero {
            padding-top: 120px; padding-bottom: 100px;
            padding-left: 2rem; padding-right: 2rem;
            background: var(--white);
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute; top: -80px; right: -120px;
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(26,144,85,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero::after {
            content: '';
            position: absolute; bottom: -60px; left: -80px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(244,124,32,0.07) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-inner {
            max-width: 1200px; margin: auto;
            display: grid; grid-template-columns: 1fr 1fr; align-items: center; gap: 80px;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--green-pale); border: 1px solid rgba(26,144,85,0.2);
            color: var(--green-mid); font-size: 12px; font-weight: 600;
            padding: 6px 14px; border-radius: 100px; margin-bottom: 24px;
        }
        .hero-badge .dot {
            width: 6px; height: 6px; background: var(--green-light);
            border-radius: 50%; animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%,100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.4); }
        }
        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 58px; font-weight: 900;
            line-height: 1.1; color: var(--gray-900);
            margin-bottom: 20px;
        }
        .hero h1 .accent { color: var(--orange-main); }
        .hero h1 .accent-green { color: var(--green-main); }
        .hero-desc {
            font-size: 17px; color: var(--gray-400); line-height: 1.7;
            margin-bottom: 36px; max-width: 480px;
        }
        .hero-cta { display: flex; gap: 14px; flex-wrap: wrap; }
        .btn-hero-main {
            display: inline-flex; align-items: center; gap: 10px;
            background: linear-gradient(135deg, var(--orange-main), #e06000);
            color: #fff; font-weight: 700; font-size: 15px;
            padding: 14px 30px; border-radius: 12px;
            text-decoration: none; transition: all 0.25s;
            box-shadow: 0 8px 24px rgba(244,124,32,0.35);
        }
        .btn-hero-main:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(244,124,32,0.45); }
        .btn-hero-sec {
            display: inline-flex; align-items: center; gap: 10px;
            background: var(--white); color: var(--gray-700); font-weight: 600; font-size: 15px;
            padding: 14px 30px; border-radius: 12px;
            text-decoration: none; transition: all 0.25s;
            border: 1.5px solid var(--gray-200);
        }
        .btn-hero-sec:hover { border-color: var(--green-light); color: var(--green-main); background: var(--green-pale); }

        /* hero visual */
        .hero-visual {
            position: relative;
            display: flex; justify-content: center; align-items: center;
        }
        .hero-card-main {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 20px;
            padding: 28px;
            width: 320px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.08);
            position: relative; z-index: 2;
        }
        .hero-card-main .card-header {
            display: flex; align-items: center; gap: 10px; margin-bottom: 20px;
        }
        .card-icon-green {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--green-main), var(--green-dark));
            border-radius: 12px; display: flex; align-items: center; justify-content: center;
        }
        .card-icon-green i { color: #fff; font-size: 18px; }
        .card-title { font-weight: 700; font-size: 15px; color: var(--gray-900); }
        .card-sub { font-size: 12px; color: var(--gray-400); }
        .progress-list { display: flex; flex-direction: column; gap: 12px; }
        .progress-item { display: flex; flex-direction: column; gap: 5px; }
        .progress-label { display: flex; justify-content: space-between; font-size: 12px; color: var(--gray-700); font-weight: 500; }
        .progress-bar { height: 6px; background: var(--gray-200); border-radius: 99px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 99px; transition: width 1s ease; }
        .fill-green { background: linear-gradient(90deg, var(--green-main), var(--green-light)); }
        .fill-orange { background: linear-gradient(90deg, var(--orange-main), var(--orange-light)); }
        .fill-teal { background: linear-gradient(90deg, #0e9ea8, #22d3ee); }

        .hero-card-float {
            position: absolute;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 14px;
            padding: 14px 18px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.07);
            display: flex; align-items: center; gap: 10px;
        }
        .float-1 { top: -20px; right: -30px; animation: float1 3s ease-in-out infinite; }
        .float-2 { bottom: 20px; left: -40px; animation: float2 3.5s ease-in-out infinite; }
        @keyframes float1 { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        @keyframes float2 { 0%,100%{transform:translateY(0)} 50%{transform:translateY(8px)} }
        .float-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .float-icon.orange { background: var(--orange-pale); }
        .float-icon.orange i { color: var(--orange-main); }
        .float-icon.green { background: var(--green-pale); }
        .float-icon.green i { color: var(--green-main); }
        .float-text-main { font-size: 14px; font-weight: 700; color: var(--gray-900); }
        .float-text-sub { font-size: 11px; color: var(--gray-400); }

        /* ── STATS BAR ── */
        .stats-bar {
            background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-mid) 50%, #0a4422 100%);
            padding: 36px 2rem;
        }
        .stats-inner {
            max-width: 1200px; margin: auto;
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;
            text-align: center;
        }
        .stat-item {}
        .stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 40px; font-weight: 900; color: var(--white);
            line-height: 1;
        }
        .stat-num span { color: var(--orange-light); }
        .stat-label { font-size: 13px; color: rgba(255,255,255,0.65); margin-top: 6px; font-weight: 500; }

        /* ── SECTION COMMON ── */
        .section { padding: 96px 2rem; }
        .section-alt { background: var(--gray-100); }
        .section-header { text-align: center; margin-bottom: 60px; }
        .section-eyebrow {
            display: inline-block;
            font-size: 12px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase;
            color: var(--orange-main); margin-bottom: 12px;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 40px; font-weight: 900; color: var(--gray-900); line-height: 1.2;
        }
        .section-sub { font-size: 17px; color: var(--gray-400); margin-top: 12px; max-width: 520px; margin-left: auto; margin-right: auto; }

        /* ── FEATURES ── */
        .features-grid {
            max-width: 1200px; margin: auto;
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;
        }
        .feat-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 20px; padding: 32px;
            transition: all 0.3s;
            position: relative; overflow: hidden;
        }
        .feat-card::before {
            content: ''; position: absolute; inset: 0;
            opacity: 0; transition: opacity 0.3s;
            background: linear-gradient(135deg, var(--green-pale) 0%, transparent 60%);
        }
        .feat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 50px rgba(0,0,0,0.08); border-color: rgba(26,144,85,0.2); }
        .feat-card:hover::before { opacity: 1; }
        .feat-icon-wrap {
            width: 52px; height: 52px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px; font-size: 22px;
            position: relative; z-index: 1;
        }
        .feat-icon-wrap.green { background: var(--green-pale); color: var(--green-main); }
        .feat-icon-wrap.orange { background: var(--orange-pale); color: var(--orange-main); }
        .feat-title { font-weight: 700; font-size: 17px; color: var(--gray-900); margin-bottom: 10px; position: relative; z-index: 1; }
        .feat-desc { font-size: 14px; color: var(--gray-400); line-height: 1.7; position: relative; z-index: 1; }

        /* ── PROCESS ── */
        .process-wrap { max-width: 800px; margin: auto; position: relative; }
        .process-line {
            position: absolute; left: 32px; top: 0; bottom: 0;
            width: 2px; background: linear-gradient(to bottom, var(--green-main), var(--orange-main));
        }
        .process-list { display: flex; flex-direction: column; gap: 0; }
        .process-item {
            display: flex; gap: 28px; align-items: flex-start;
            padding: 0 0 36px 0;
            opacity: 0; transform: translateX(-20px);
            transition: all 0.5s ease;
        }
        .process-item.visible { opacity: 1; transform: translateX(0); }
        .process-num {
            width: 64px; height: 64px; flex-shrink: 0;
            background: var(--white); border: 2px solid var(--green-main);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 900;
            color: var(--green-main); z-index: 1;
            box-shadow: 0 0 0 6px var(--white);
        }
        .process-body {
            background: var(--white); border: 1px solid var(--gray-200);
            border-radius: 16px; padding: 20px 24px; flex: 1;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
        }
        .process-body h4 { font-weight: 700; font-size: 15px; color: var(--gray-900); margin-bottom: 6px; }
        .process-body p { font-size: 14px; color: var(--gray-400); line-height: 1.6; }
        .process-tag {
            display: inline-block; font-size: 11px; font-weight: 700; letter-spacing: 0.08em;
            text-transform: uppercase; padding: 3px 10px; border-radius: 99px; margin-bottom: 8px;
        }
        .tag-green { background: var(--green-pale); color: var(--green-mid); }
        .tag-orange { background: var(--orange-pale); color: var(--orange-main); }

        /* ── CTA ── */
        .cta-section {
            padding: 100px 2rem;
            background: linear-gradient(135deg, var(--green-dark) 0%, #0d5c37 40%, #083d22 100%);
            position: relative; overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute; top: -100px; right: -100px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(244,124,32,0.15) 0%, transparent 70%);
        }
        .cta-inner { max-width: 680px; margin: auto; text-align: center; position: relative; z-index: 1; }
        .cta-eyebrow {
            display: inline-block; font-size: 12px; font-weight: 700; letter-spacing: 0.12em;
            text-transform: uppercase; color: var(--orange-light); margin-bottom: 16px;
        }
        .cta-inner h2 {
            font-family: 'Playfair Display', serif;
            font-size: 46px; font-weight: 900; color: var(--white); line-height: 1.15; margin-bottom: 18px;
        }
        .cta-inner p { font-size: 17px; color: rgba(255,255,255,0.6); margin-bottom: 40px; line-height: 1.7; }
        .cta-buttons { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
        .btn-cta-main {
            display: inline-flex; align-items: center; gap: 10px;
            background: var(--orange-main); color: #fff; font-weight: 700; font-size: 15px;
            padding: 15px 32px; border-radius: 12px; text-decoration: none; transition: all 0.25s;
            box-shadow: 0 8px 24px rgba(244,124,32,0.4);
        }
        .btn-cta-main:hover { background: var(--orange-light); transform: translateY(-2px); }
        .btn-cta-sec {
            display: inline-flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,0.1); color: #fff; font-weight: 600; font-size: 15px;
            padding: 15px 32px; border-radius: 12px; text-decoration: none; transition: all 0.25s;
            border: 1.5px solid rgba(255,255,255,0.25);
        }
        .btn-cta-sec:hover { background: rgba(255,255,255,0.18); }

        /* ── FOOTER ── */
        .footer {
            background: var(--gray-900); padding: 28px 2rem;
        }
        .footer-inner {
            max-width: 1200px; margin: auto;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            flex-wrap: wrap;
        }
        .footer-logo { display: flex; align-items: center; gap: 8px; }
        .footer-logo-icon {
            width: 30px; height: 30px; background: var(--green-main);
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
        }
        .footer-logo-icon i { color: #fff; font-size: 13px; }
        .footer-brand {
            font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #fff;
        }
        .footer-copy { font-size: 12px; color: rgba(255,255,255,0.35); }
        .footer-links { display: flex; gap: 20px; }
        .footer-links a { font-size: 12px; color: rgba(255,255,255,0.35); text-decoration: none; transition: color 0.2s; }
        .footer-links a:hover { color: var(--orange-light); }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .hero-inner { grid-template-columns: 1fr; gap: 50px; }
            .hero h1 { font-size: 42px; }
            .hero-visual { display: none; }
            .features-grid { grid-template-columns: 1fr 1fr; }
            .stats-inner { grid-template-columns: repeat(2, 1fr); gap: 32px; }
        }
        @media (max-width: 600px) {
            .features-grid { grid-template-columns: 1fr; }
            .stats-inner { grid-template-columns: 1fr 1fr; }
            .cta-inner h2 { font-size: 34px; }
            .section-title { font-size: 30px; }
            .hero h1 { font-size: 34px; }
        }

        /* scroll reveal */
        .reveal { opacity: 0; transform: translateY(24px); transition: opacity 0.6s ease, transform 0.6s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-inner">
        <a href="#" class="logo">
            <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
            <span class="logo-text">TELL<span>INTER</span></span>
        </a>
        <div class="nav-links">
            <a href="{{ route('login') }}" class="btn-ghost">Masuk</a>
            <a href="{{ route('register') }}" class="btn-primary">Daftar Sekarang</a>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="dot"></span>
                Platform Manajemen Magang Terpadu
            </div>
            <h1>
                Satu Platform untuk<br>
                <span class="accent-green">Semua</span> Kebutuhan<br>
                <span class="accent">Magang</span> Anda
            </h1>
            <p class="hero-desc">
                TELLINTER menyederhanakan seluruh proses magang — dari pendaftaran, approval proposal, absensi harian, hingga laporan akhir — dalam satu sistem terintegrasi.
            </p>
            <div class="hero-cta">
                <a href="{{ route('register.mahasiswa') }}" class="btn-hero-main">
                    <i class="fas fa-user-graduate"></i>
                    Daftar Sebagai Mahasiswa
                </a>
                <a href="{{ route('login') }}" class="btn-hero-sec">
                    <i class="fas fa-sign-in-alt"></i>
                    Login ke Sistem
                </a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-card-float float-1">
                <div class="float-icon orange"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="float-text-main">Proposal Disetujui</div>
                    <div class="float-text-sub">2 menit yang lalu</div>
                </div>
            </div>
            <div class="hero-card-main">
                <div class="card-header">
                    <div class="card-icon-green"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <div class="card-title">Progress Magang</div>
                        <div class="card-sub">Periode 2024/2025</div>
                    </div>
                </div>
                <div class="progress-list">
                    <div class="progress-item">
                        <div class="progress-label"><span>Absensi Hadir</span><span style="color:var(--green-main)">92%</span></div>
                        <div class="progress-bar"><div class="progress-fill fill-green" style="width:92%"></div></div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-label"><span>Log Aktivitas</span><span style="color:var(--orange-main)">78%</span></div>
                        <div class="progress-bar"><div class="progress-fill fill-orange" style="width:78%"></div></div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-label"><span>Laporan Selesai</span><span style="color:#0e9ea8">65%</span></div>
                        <div class="progress-bar"><div class="progress-fill fill-teal" style="width:65%"></div></div>
                    </div>
                </div>
            </div>
            <div class="hero-card-float float-2">
                <div class="float-icon green"><i class="fas fa-user-check"></i></div>
                <div>
                    <div class="float-text-main">42 Mahasiswa Aktif</div>
                    <div class="float-text-sub">Semester Genap</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATS BAR -->
<div class="stats-bar">
    <div class="stats-inner">
        <div class="stat-item reveal">
            <div class="stat-num">500<span>+</span></div>
            <div class="stat-label">Mahasiswa Terdaftar</div>
        </div>
        <div class="stat-item reveal" style="transition-delay:0.1s">
            <div class="stat-num">98<span>%</span></div>
            <div class="stat-label">Tingkat Persetujuan</div>
        </div>
        <div class="stat-item reveal" style="transition-delay:0.2s">
            <div class="stat-num">50<span>+</span></div>
            <div class="stat-label">Perusahaan Mitra</div>
        </div>
        <div class="stat-item reveal" style="transition-delay:0.3s">
            <div class="stat-num">5</div>
            <div class="stat-label">Level Akses Peran</div>
        </div>
    </div>
</div>

<!-- FEATURES -->
<section class="section">
    <div class="section-header">
        <span class="section-eyebrow">Fitur Lengkap</span>
        <h2 class="section-title">Dirancang untuk Semua Peran</h2>
        <p class="section-sub">Setiap stakeholder mendapatkan antarmuka dan fitur yang sesuai kebutuhannya</p>
    </div>
    <div class="features-grid">
        <div class="feat-card reveal">
            <div class="feat-icon-wrap green"><i class="fas fa-user-graduate"></i></div>
            <div class="feat-title">Mahasiswa</div>
            <div class="feat-desc">Ajukan proposal, absensi harian dengan GPS, isi log aktivitas, dan pantau status magang secara real-time dari mana saja.</div>
        </div>
        <div class="feat-card reveal" style="transition-delay:0.1s">
            <div class="feat-icon-wrap orange"><i class="fas fa-clipboard-check"></i></div>
            <div class="feat-title">Operator</div>
            <div class="feat-desc">Review dan teruskan proposal, kelola periode pendaftaran, terbitkan surat penerimaan atau penolakan berbasis PDF secara otomatis.</div>
        </div>
        <div class="feat-card reveal" style="transition-delay:0.2s">
            <div class="feat-icon-wrap green"><i class="fas fa-sitemap"></i></div>
            <div class="feat-title">Manajer Departemen</div>
            <div class="feat-desc">Setujui proposal sesuai divisi, kelola pembimbing lapang, dan pantau progress mahasiswa di seluruh divisinya secara terpusat.</div>
        </div>
        <div class="feat-card reveal" style="transition-delay:0.05s">
            <div class="feat-icon-wrap orange"><i class="fas fa-crown"></i></div>
            <div class="feat-title">Manager</div>
            <div class="feat-desc">Persetujuan final proposal, generate laporan komprehensif, kelola operator, dan kontrol seluruh token akses sistem.</div>
        </div>
        <div class="feat-card reveal" style="transition-delay:0.15s">
            <div class="feat-icon-wrap green"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="feat-title">Pembimbing Lapang</div>
            <div class="feat-desc">Verifikasi kehadiran dan log harian, berikan feedback berkala dan penilaian akhir, serta akses laporan progress mahasiswa bimbingan.</div>
        </div>
        <div class="feat-card reveal" style="transition-delay:0.25s">
            <div class="feat-icon-wrap orange"><i class="fas fa-shield-alt"></i></div>
            <div class="feat-title">Keamanan Sistem</div>
            <div class="feat-desc">RBAC 5 level akses, verifikasi email terproteksi, token akses khusus admin, dan proteksi data berlapis untuk keamanan penuh.</div>
        </div>
    </div>
</section>

<!-- PROCESS -->
<section class="section section-alt">
    <div class="section-header">
        <span class="section-eyebrow">Alur Proses</span>
        <h2 class="section-title">Transparansi Penuh dari Awal</h2>
        <p class="section-sub">Setiap langkah proses magang tercatat dan dapat dipantau secara real-time</p>
    </div>
    <div class="process-wrap">
        <div class="process-line"></div>
        <div class="process-list">
            <div class="process-item">
                <div class="process-num">1</div>
                <div class="process-body">
                    <span class="process-tag tag-green">Mahasiswa</span>
                    <h4>Pendaftaran & Pengajuan Proposal</h4>
                    <p>Mahasiswa mendaftar dan mengajukan proposal magang lengkap beserta dokumen pendukung dalam format PDF.</p>
                </div>
            </div>
            <div class="process-item">
                <div class="process-num">2</div>
                <div class="process-body">
                    <span class="process-tag tag-orange">Operator</span>
                    <h4>Verifikasi Kelengkapan Dokumen</h4>
                    <p>Operator melakukan review kelengkapan dokumen dan meneruskan ke Manajer Departemen yang bersangkutan.</p>
                </div>
            </div>
            <div class="process-item">
                <div class="process-num">3</div>
                <div class="process-body">
                    <span class="process-tag tag-green">Manajer Departemen</span>
                    <h4>Penilaian Kesesuaian Divisi</h4>
                    <p>Manajer Departemen menilai kesesuaian proposal dengan kebutuhan divisi dan memberikan rekomendasi.</p>
                </div>
            </div>
            <div class="process-item">
                <div class="process-num">4</div>
                <div class="process-body">
                    <span class="process-tag tag-orange">Manager</span>
                    <h4>Persetujuan Final & Penugasan</h4>
                    <p>Manager memberikan persetujuan final atas proposal dan menugaskan pembimbing lapang yang tepat.</p>
                </div>
            </div>
            <div class="process-item">
                <div class="process-num">5</div>
                <div class="process-body">
                    <span class="process-tag tag-green">Operator</span>
                    <h4>Penerbitan Surat Resmi</h4>
                    <p>Operator menerbitkan surat penerimaan resmi secara otomatis dan periode magang resmi dimulai.</p>
                </div>
            </div>
            <div class="process-item" style="padding-bottom:0">
                <div class="process-num" style="border-color:var(--orange-main);color:var(--orange-main)">6</div>
                <div class="process-body">
                    <span class="process-tag tag-orange">Aktif Magang</span>
                    <h4>Absensi & Log Harian</h4>
                    <p>Mahasiswa melakukan absensi harian berbasis GPS dan mengisi log aktivitas yang diverifikasi oleh pembimbing lapang setiap harinya.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="cta-inner">
        <span class="cta-eyebrow">Bergabung Sekarang</span>
        <h2>Mulai Gunakan TELLINTER Hari Ini</h2>
        <p>Nikmati kemudahan manajemen magang yang terintegrasi, transparan, dan efisien dalam satu platform.</p>
        <div class="cta-buttons">
            <a href="{{ route('register.mahasiswa') }}" class="btn-cta-main">
                <i class="fas fa-user-graduate"></i> Daftar Mahasiswa
            </a>
            <a href="{{ route('register.admin') }}" class="btn-cta-sec">
                <i class="fas fa-key"></i> Registrasi Admin (Token)
            </a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-logo">
            <div class="footer-logo-icon"><i class="fas fa-graduation-cap"></i></div>
            <span class="footer-brand">TELLINTER</span>
        </div>
        <p class="footer-copy">© {{ date('Y') }} TELLINTER. Sistem Pendaftaran dan Manajemen Magang Mahasiswa.</p>
        <div class="footer-links">
            <a href="#">Kebijakan Privasi</a>
            <a href="#">Bantuan</a>
            <a href="#">Kontak</a>
        </div>
    </div>
</footer>

<script>
    // Scroll reveal
    const revealEls = document.querySelectorAll('.reveal, .process-item');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.12 });
    revealEls.forEach(el => observer.observe(el));
</script>
</body>
</html>