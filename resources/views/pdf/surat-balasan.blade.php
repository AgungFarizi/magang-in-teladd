<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Balasan — {{ $nomor_surat }}</title>
    <style>
        @page { margin: 2.5cm; }
        body { font-family: 'Times New Roman', serif; font-size: 12pt; color: #000; line-height: 1.6; }
        .kop { border-bottom: 3px solid #1e3a8a; padding-bottom: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 20px; }
        .kop-logo { width: 70px; height: 70px; background: #1e3a8a; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 22pt; font-weight: bold; }
        .kop-text h1 { margin: 0; font-size: 16pt; color: #1e3a8a; }
        .kop-text p { margin: 2px 0; font-size: 9pt; color: #555; }
        .nomor-surat { margin: 18px 0; font-size: 11pt; }
        .perihal-table { margin-bottom: 20px; font-size: 11pt; }
        .perihal-table td { padding: 2px 0; vertical-align: top; }
        .perihal-table .label { width: 120px; }
        .perihal-table .sep { width: 20px; }
        h3 { font-size: 12pt; margin-bottom: 6px; text-decoration: underline; }
        .isi { text-align: justify; margin-bottom: 24px; white-space: pre-line; }
        .ttd { margin-top: 40px; }
        .ttd-table { }
        .ttd-table td { vertical-align: top; }
        .ttd-nama { margin-top: 70px; font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    {{-- KOP Surat --}}
    <table width="100%" style="border-bottom: 3px solid #1e3a8a; margin-bottom: 18px; padding-bottom: 10px;">
        <tr>
            <td width="80" style="vertical-align: middle;">
                <div style="width:65px; height:65px; background:#1e3a8a; border-radius:6px; text-align:center; line-height:65px; color:white; font-size:20pt; font-weight:bold;">T</div>
            </td>
            <td style="vertical-align: middle; padding-left: 16px;">
                <div style="font-size:15pt; font-weight:bold; color:#1e3a8a; margin-bottom:2px;">TELLINTER</div>
                <div style="font-size:9pt; color:#555;">Sistem Pendaftaran dan Manajemen Magang</div>
                <div style="font-size:9pt; color:#555;">Jl. Contoh No. 123, Kota, Indonesia — info@tellinter.ac.id</div>
            </td>
        </tr>
    </table>

    {{-- Nomor & Tanggal --}}
    <table class="perihal-table" width="100%">
        <tr>
            <td class="label">Nomor</td><td class="sep">:</td>
            <td>{{ $nomor_surat }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td><td class="sep">:</td>
            <td>{{ $tanggal }}</td>
        </tr>
        <tr>
            <td class="label">Perihal</td><td class="sep">:</td>
            <td><strong>{{ $perihal }}</strong></td>
        </tr>
        <tr>
            <td class="label">Kepada Yth.</td><td class="sep">:</td>
            <td>
                {{ $proposal->pengaju->nama_lengkap }}<br>
                {{ $proposal->pengaju->institusi }}
            </td>
        </tr>
    </table>

    <p style="margin: 16px 0 8px;">Dengan hormat,</p>

    <div class="isi">{{ $isi_surat }}</div>

    <div class="ttd" style="margin-top: 48px;">
        <p style="margin-bottom: 4px;">Hormat kami,</p>
        <p><strong>TELLINTER</strong></p>
        <br><br><br>
        <p style="text-decoration: underline; font-weight: bold;">Authorized Signatory</p>
        <p style="font-size: 10pt; color: #555;">Operator / Manager</p>
    </div>
</body>
</html>
