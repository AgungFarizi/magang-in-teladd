<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email TELLINTER</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 560px; margin: 40px auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #1e3a8a, #2563eb); padding: 40px 32px; text-align: center; }
        .header h1 { color: white; margin: 0 0 4px; font-size: 28px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { color: rgba(255,255,255,0.75); margin: 0; font-size: 14px; }
        .body { padding: 32px; }
        .greeting { font-size: 18px; font-weight: 700; color: #111; margin-bottom: 12px; }
        .text { color: #4b5563; font-size: 15px; line-height: 1.7; margin-bottom: 12px; }
        .btn-container { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white !important; text-decoration: none; padding: 14px 36px; border-radius: 12px; font-weight: 700; font-size: 15px; letter-spacing: 0.3px; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
        .small { font-size: 12px; color: #9ca3af; line-height: 1.6; }
        .url { color: #3b82f6; word-break: break-all; font-size: 13px; }
        .footer { background: #f9fafb; padding: 20px 32px; text-align: center; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TELLINTER</h1>
            <p>Sistem Pendaftaran & Manajemen Magang</p>
        </div>
        <div class="body">
            <p class="greeting">Halo, {{ $pengguna->nama_lengkap }}! 👋</p>
            <p class="text">
                Terima kasih telah mendaftar di <strong>TELLINTER</strong>.
                Untuk mengaktifkan akun Anda, silakan klik tombol di bawah ini:
            </p>
            <div class="btn-container">
                <a href="{{ $verificationUrl }}" class="btn">Verifikasi Email Saya</a>
            </div>
            <p class="text">
                Tombol ini akan kadaluarsa dalam <strong>24 jam</strong>.
                Jika Anda tidak mendaftar, abaikan email ini.
            </p>
            <hr class="divider">
            <p class="small">
                Atau salin dan tempel URL ini di browser:<br>
                <a href="{{ $verificationUrl }}" class="url">{{ $verificationUrl }}</a>
            </p>
        </div>
        <div class="footer">
            © {{ date('Y') }} TELLINTER. Sistem Manajemen Magang.
        </div>
    </div>
</body>
</html>
