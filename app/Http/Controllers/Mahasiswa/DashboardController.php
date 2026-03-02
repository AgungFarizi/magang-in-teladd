<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\AnggotaProposal;
use App\Models\Kehadiran;
use App\Models\LogHarian;
use App\Models\Notifikasi;
use App\Models\PeriodeMagang;
use App\Models\Proposal;
use App\Models\SuratBalasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $proposalAktif = Proposal::where('pengaju_id', $user->id)
            ->whereNotIn('status', ['draft'])
            ->latest()
            ->first();

        $periodeAktif = PeriodeMagang::aktif()->first();

        $stats = [];
        if ($proposalAktif && $proposalAktif->isAktif()) {
            $stats = [
                'total_kehadiran' => Kehadiran::where('mahasiswa_id', $user->id)
                    ->where('proposal_id', $proposalAktif->id)
                    ->where('status_kehadiran', 'hadir')->count(),
                'log_pending' => LogHarian::where('mahasiswa_id', $user->id)
                    ->where('proposal_id', $proposalAktif->id)
                    ->where('status_verifikasi', 'pending')->count(),
                'kehadiran_bulan_ini' => Kehadiran::where('mahasiswa_id', $user->id)
                    ->where('proposal_id', $proposalAktif->id)
                    ->whereMonth('tanggal', now()->month)
                    ->where('status_kehadiran', 'hadir')->count(),
            ];
        }

        $notifikasi = Notifikasi::where('pengguna_id', $user->id)
            ->belumDibaca()
            ->latest()
            ->take(5)
            ->get();

        return view('mahasiswa.dashboard', compact('proposalAktif', 'periodeAktif', 'stats', 'notifikasi'));
    }

    // ─── Proposal ───────────────────────────────────────────────────
    public function indexProposal()
    {
        $proposals = Proposal::with(['periode'])
            ->where('pengaju_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('mahasiswa.proposal.index', compact('proposals'));
    }

    public function createProposal()
    {
        $periodeAktif = PeriodeMagang::aktif()->get();

        if ($periodeAktif->isEmpty()) {
            return redirect()->route('mahasiswa.proposal.index')
                ->with('warning', 'Tidak ada periode pendaftaran yang aktif saat ini.');
        }

        // Cek apakah sudah ada proposal aktif
        $proposalAktif = Proposal::where('pengaju_id', auth()->id())
            ->whereNotIn('status', ['draft', 'ditolak', 'dibatalkan', 'ditolak_manager_dep'])
            ->exists();

        if ($proposalAktif) {
            return redirect()->route('mahasiswa.proposal.index')
                ->with('warning', 'Anda sudah memiliki proposal yang sedang diproses.');
        }

        return view('mahasiswa.proposal.create', compact('periodeAktif'));
    }

    public function storeProposal(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_magang,id',
            'divisi_tujuan' => 'required|string|max:100',
            'judul_proposal' => 'required|string|max:255',
            'latar_belakang' => 'required|string|min:100',
            'tujuan' => 'required|string|min:50',
            'tanggal_mulai_diinginkan' => 'required|date|after_or_equal:today',
            'tanggal_selesai_diinginkan' => 'required|date|after:tanggal_mulai_diinginkan',
            'file_proposal' => 'required|file|mimes:pdf|max:5120',
            'file_surat_pengantar' => 'nullable|file|mimes:pdf|max:5120',
            'file_transkrip' => 'nullable|file|mimes:pdf|max:5120',
            'file_cv' => 'nullable|file|mimes:pdf|max:5120',

            // Anggota
            'anggota' => 'nullable|array|max:4',
            'anggota.*.nama_lengkap' => 'required_with:anggota|string|max:255',
            'anggota.*.nim' => 'required_with:anggota|string|max:20',
            'anggota.*.email' => 'required_with:anggota|email',
        ]);

        // Upload files
        $fileProposal = $request->file('file_proposal')->store('proposals', 'public');
        $fileSurat = $request->file('file_surat_pengantar')?->store('proposals/surat', 'public');
        $fileTranskrip = $request->file('file_transkrip')?->store('proposals/transkrip', 'public');
        $fileCv = $request->file('file_cv')?->store('proposals/cv', 'public');

        $proposal = Proposal::create([
            'periode_id' => $request->periode_id,
            'pengaju_id' => auth()->id(),
            'divisi_tujuan' => $request->divisi_tujuan,
            'judul_proposal' => $request->judul_proposal,
            'latar_belakang' => $request->latar_belakang,
            'tujuan' => $request->tujuan,
            'tanggal_mulai_diinginkan' => $request->tanggal_mulai_diinginkan,
            'tanggal_selesai_diinginkan' => $request->tanggal_selesai_diinginkan,
            'file_proposal' => $fileProposal,
            'file_surat_pengantar' => $fileSurat,
            'file_transkrip' => $fileTranskrip,
            'file_cv' => $fileCv,
            'status' => 'diajukan',
        ]);

        // Tambah ketua sebagai anggota pertama
        AnggotaProposal::create([
            'proposal_id' => $proposal->id,
            'pengguna_id' => auth()->id(),
            'nama_lengkap' => auth()->user()->nama_lengkap,
            'nim' => auth()->user()->nim,
            'email' => auth()->user()->email,
            'no_telepon' => auth()->user()->no_telepon,
            'jurusan' => auth()->user()->jurusan,
            'institusi' => auth()->user()->institusi,
            'peran' => 'ketua',
            'is_confirmed' => true,
            'confirmed_at' => now(),
        ]);

        // Tambah anggota lain
        foreach ($request->anggota ?? [] as $anggota) {
            AnggotaProposal::create([
                'proposal_id' => $proposal->id,
                'nama_lengkap' => $anggota['nama_lengkap'],
                'nim' => $anggota['nim'],
                'email' => $anggota['email'],
                'no_telepon' => $anggota['no_telepon'] ?? null,
                'jurusan' => $anggota['jurusan'] ?? null,
                'institusi' => $anggota['institusi'] ?? auth()->user()->institusi,
                'peran' => 'anggota',
            ]);
        }

        // Notifikasi ke operator
        $operators = \App\Models\Pengguna::where('role', 'operator')->where('is_active', true)->get();
        foreach ($operators as $operator) {
            Notifikasi::kirim(
                $operator->id,
                'Proposal Baru Masuk',
                "Proposal {$proposal->nomor_proposal} dari {$proposal->pengaju->nama_lengkap} menunggu review.",
                'proposal_diajukan',
                route('operator.proposal.show', $proposal)
            );
        }

        return redirect()->route('mahasiswa.proposal.show', $proposal)
            ->with('success', 'Proposal berhasil diajukan! Nomor proposal: ' . $proposal->nomor_proposal);
    }

    public function showProposal(Proposal $proposal)
    {
        abort_if($proposal->pengaju_id !== auth()->id(), 403);
        $proposal->load(['periode', 'anggota', 'suratBalasan', 'pembimbing']);
        return view('mahasiswa.proposal.show', compact('proposal'));
    }

    // ─── Kehadiran ──────────────────────────────────────────────────
    public function indexKehadiran()
    {
        $proposalAktif = Proposal::where('pengaju_id', auth()->id())
            ->where('status', 'aktif')
            ->first();

        if (!$proposalAktif) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('warning', 'Anda belum memiliki magang yang aktif.');
        }

        $kehadiran = Kehadiran::where('mahasiswa_id', auth()->id())
            ->where('proposal_id', $proposalAktif->id)
            ->latest('tanggal')
            ->paginate(20);

        $sudahAbsenHariIni = Kehadiran::where('mahasiswa_id', auth()->id())
            ->where('proposal_id', $proposalAktif->id)
            ->whereDate('tanggal', today())
            ->exists();

        return view('mahasiswa.kehadiran.index', compact('kehadiran', 'proposalAktif', 'sudahAbsenHariIni'));
    }

    public function absen(Request $request)
    {
        $proposalAktif = Proposal::where('pengaju_id', auth()->id())
            ->where('status', 'aktif')
            ->firstOrFail();

        $sudahAbsen = Kehadiran::where('mahasiswa_id', auth()->id())
            ->where('proposal_id', $proposalAktif->id)
            ->whereDate('tanggal', today())
            ->exists();

        if ($sudahAbsen) {
            return back()->with('warning', 'Anda sudah melakukan absensi hari ini.');
        }

        $request->validate([
            'jam_masuk' => 'required|date_format:H:i',
            'lokasi_masuk' => 'nullable|string|max:255',
            'foto_masuk' => 'nullable|image|max:2048',
            'status_kehadiran' => 'required|in:hadir,sakit,izin,libur',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $fotoMasuk = null;
        if ($request->hasFile('foto_masuk')) {
            $fotoMasuk = $request->file('foto_masuk')->store('kehadiran', 'public');
        }

        Kehadiran::create([
            'proposal_id' => $proposalAktif->id,
            'mahasiswa_id' => auth()->id(),
            'tanggal' => today(),
            'jam_masuk' => $request->jam_masuk,
            'lokasi_masuk' => $request->lokasi_masuk,
            'foto_masuk' => $fotoMasuk,
            'status_kehadiran' => $request->status_kehadiran,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('mahasiswa.kehadiran.index')
            ->with('success', 'Absensi masuk berhasil dicatat.');
    }

    public function absenKeluar(Request $request, Kehadiran $kehadiran)
    {
        abort_if($kehadiran->mahasiswa_id !== auth()->id(), 403);
        abort_if($kehadiran->jam_keluar !== null, 400, 'Sudah absen keluar.');

        $request->validate([
            'jam_keluar' => 'required|date_format:H:i|after:' . $kehadiran->jam_masuk,
            'lokasi_keluar' => 'nullable|string|max:255',
            'foto_keluar' => 'nullable|image|max:2048',
        ]);

        $fotoKeluar = null;
        if ($request->hasFile('foto_keluar')) {
            $fotoKeluar = $request->file('foto_keluar')->store('kehadiran', 'public');
        }

        $kehadiran->update([
            'jam_keluar' => $request->jam_keluar,
            'lokasi_keluar' => $request->lokasi_keluar,
            'foto_keluar' => $fotoKeluar,
        ]);

        return back()->with('success', 'Absensi keluar berhasil dicatat.');
    }

    // ─── Log Harian ─────────────────────────────────────────────────
    public function indexLog()
    {
        $proposalAktif = Proposal::where('pengaju_id', auth()->id())
            ->where('status', 'aktif')
            ->first();

        if (!$proposalAktif) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('warning', 'Anda belum memiliki magang yang aktif.');
        }

        $logs = LogHarian::where('mahasiswa_id', auth()->id())
            ->where('proposal_id', $proposalAktif->id)
            ->with('kehadiran')
            ->latest('tanggal')
            ->paginate(20);

        $kehadiranTanpaLog = Kehadiran::where('mahasiswa_id', auth()->id())
            ->where('proposal_id', $proposalAktif->id)
            ->where('status_kehadiran', 'hadir')
            ->whereDoesntHave('logHarian')
            ->latest('tanggal')
            ->get();

        return view('mahasiswa.log.index', compact('logs', 'proposalAktif', 'kehadiranTanpaLog'));
    }

    public function createLog(Kehadiran $kehadiran)
    {
        abort_if($kehadiran->mahasiswa_id !== auth()->id(), 403);
        abort_if($kehadiran->logHarian()->exists(), 400, 'Log harian sudah ada untuk tanggal ini.');
        return view('mahasiswa.log.create', compact('kehadiran'));
    }

    public function storeLog(Request $request, Kehadiran $kehadiran)
    {
        abort_if($kehadiran->mahasiswa_id !== auth()->id(), 403);

        $request->validate([
            'judul_aktivitas' => 'required|string|max:255',
            'deskripsi_aktivitas' => 'required|string|min:50',
            'kategori' => 'required|in:pembelajaran,proyek,administrasi,presentasi,diskusi,laporan,lainnya',
            'durasi_menit' => 'required|integer|min:30|max:600',
            'kendala' => 'nullable|string|max:500',
            'rencana_besok' => 'nullable|string|max:500',
            'file_dokumentasi' => 'nullable|array|max:5',
            'file_dokumentasi.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $fileDokumentasi = [];
        if ($request->hasFile('file_dokumentasi')) {
            foreach ($request->file('file_dokumentasi') as $file) {
                $fileDokumentasi[] = $file->store('log-dokumentasi', 'public');
            }
        }

        LogHarian::create([
            'kehadiran_id' => $kehadiran->id,
            'mahasiswa_id' => auth()->id(),
            'proposal_id' => $kehadiran->proposal_id,
            'tanggal' => $kehadiran->tanggal,
            'judul_aktivitas' => $request->judul_aktivitas,
            'deskripsi_aktivitas' => $request->deskripsi_aktivitas,
            'kategori' => $request->kategori,
            'durasi_menit' => $request->durasi_menit,
            'file_dokumentasi' => $fileDokumentasi ?: null,
            'kendala' => $request->kendala,
            'rencana_besok' => $request->rencana_besok,
        ]);

        return redirect()->route('mahasiswa.log.index')
            ->with('success', 'Log harian berhasil disimpan.');
    }

    // ─── Surat Balasan ──────────────────────────────────────────────
    public function showSurat(SuratBalasan $suratBalasan)
    {
        abort_if($suratBalasan->proposal->pengaju_id !== auth()->id(), 403);

        if (!$suratBalasan->sudah_dibaca) {
            $suratBalasan->update(['sudah_dibaca' => true, 'dibaca_pada' => now()]);
        }

        $suratBalasan->load(['proposal.pengaju', 'pembuat']);
        return view('mahasiswa.surat.show', compact('suratBalasan'));
    }

    public function indexSurat()
    {
        $surat = SuratBalasan::whereHas('proposal', fn($q) => $q->where('pengaju_id', auth()->id()))
            ->with(['proposal', 'pembuat'])
            ->latest()
            ->get();

        return view('mahasiswa.surat.index', compact('surat'));
    }
}
