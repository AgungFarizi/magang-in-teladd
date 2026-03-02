<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\LogHarian;
use App\Models\Notifikasi;
use App\Models\Proposal;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $pembimbingId = auth()->id();

        $mahasiswaBimbingan = Proposal::with(['pengaju', 'kehadiran'])
            ->where('pembimbing_id', $pembimbingId)
            ->whereIn('status', ['aktif', 'selesai'])
            ->get();

        $stats = [
            'total_mahasiswa' => $mahasiswaBimbingan->count(),
            'kehadiran_pending' => Kehadiran::whereHas('proposal', fn($q) => $q->where('pembimbing_id', $pembimbingId))
                ->where('status_verifikasi', 'pending')->count(),
            'log_pending' => LogHarian::whereHas('proposal', fn($q) => $q->where('pembimbing_id', $pembimbingId))
                ->where('status_verifikasi', 'pending')->count(),
            'kehadiran_hari_ini' => Kehadiran::whereHas('proposal', fn($q) => $q->where('pembimbing_id', $pembimbingId))
                ->whereDate('tanggal', today())->count(),
        ];

        $kehadiranPending = Kehadiran::with(['mahasiswa', 'proposal.pengaju'])
            ->whereHas('proposal', fn($q) => $q->where('pembimbing_id', $pembimbingId))
            ->where('status_verifikasi', 'pending')
            ->latest()
            ->take(10)
            ->get();

        $logPending = LogHarian::with(['mahasiswa', 'proposal'])
            ->whereHas('proposal', fn($q) => $q->where('pembimbing_id', $pembimbingId))
            ->where('status_verifikasi', 'pending')
            ->latest()
            ->take(10)
            ->get();

        return view('pembimbing.dashboard', compact('stats', 'mahasiswaBimbingan', 'kehadiranPending', 'logPending'));
    }

    // ─── Kehadiran ──────────────────────────────────────────────────
    public function indexKehadiran(Request $request)
    {
        $query = Kehadiran::with(['mahasiswa', 'proposal'])
            ->whereHas('proposal', fn($q) => $q->where('pembimbing_id', auth()->id()));

        if ($request->filled('status')) {
            $query->where('status_verifikasi', $request->status);
        }

        if ($request->filled('mahasiswa_id')) {
            $query->where('mahasiswa_id', $request->mahasiswa_id);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $kehadiran = $query->latest('tanggal')->paginate(20)->withQueryString();

        $mahasiswaList = Proposal::with('pengaju')
            ->where('pembimbing_id', auth()->id())
            ->whereIn('status', ['aktif', 'selesai'])
            ->get()
            ->pluck('pengaju');

        return view('pembimbing.kehadiran.index', compact('kehadiran', 'mahasiswaList'));
    }

    public function verifikasiKehadiran(Request $request, Kehadiran $kehadiran)
    {
        $this->authorizeKehadiran($kehadiran);

        $request->validate([
            'action' => 'required|in:diverifikasi,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $kehadiran->update([
            'status_verifikasi' => $request->action,
            'diverifikasi_oleh' => auth()->id(),
            'tgl_verifikasi' => now(),
            'catatan_pembimbing' => $request->catatan,
        ]);

        Notifikasi::kirim(
            $kehadiran->mahasiswa_id,
            'Kehadiran ' . ($request->action === 'diverifikasi' ? 'Diverifikasi' : 'Ditolak'),
            "Kehadiran tanggal {$kehadiran->tanggal->format('d/m/Y')} " .
            ($request->action === 'diverifikasi' ? 'telah diverifikasi' : 'ditolak') .
            ($request->catatan ? ". Catatan: {$request->catatan}" : '.'),
            'kehadiran_diverifikasi'
        );

        return back()->with('success', 'Status kehadiran berhasil diupdate.');
    }

    public function bulkVerifikasiKehadiran(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:kehadiran,id',
            'action' => 'required|in:diverifikasi,ditolak',
        ]);

        $kehadiranList = Kehadiran::whereIn('id', $request->ids)
            ->whereHas('proposal', fn($q) => $q->where('pembimbing_id', auth()->id()))
            ->get();

        foreach ($kehadiranList as $kehadiran) {
            $kehadiran->update([
                'status_verifikasi' => $request->action,
                'diverifikasi_oleh' => auth()->id(),
                'tgl_verifikasi' => now(),
            ]);
        }

        return back()->with('success', count($kehadiranList) . ' kehadiran berhasil diverifikasi.');
    }

    // ─── Log Harian ─────────────────────────────────────────────────
    public function indexLog(Request $request)
    {
        $query = LogHarian::with(['mahasiswa', 'proposal'])
            ->whereHas('proposal', fn($q) => $q->where('pembimbing_id', auth()->id()));

        if ($request->filled('status')) {
            $query->where('status_verifikasi', $request->status);
        }

        if ($request->filled('mahasiswa_id')) {
            $query->where('mahasiswa_id', $request->mahasiswa_id);
        }

        $logs = $query->latest('tanggal')->paginate(20)->withQueryString();

        $mahasiswaList = Proposal::with('pengaju')
            ->where('pembimbing_id', auth()->id())
            ->whereIn('status', ['aktif', 'selesai'])
            ->get()
            ->pluck('pengaju');

        return view('pembimbing.log.index', compact('logs', 'mahasiswaList'));
    }

    public function showLog(LogHarian $logHarian)
    {
        $this->authorizeLog($logHarian);
        $logHarian->load(['mahasiswa', 'proposal', 'kehadiran']);
        return view('pembimbing.log.show', compact('logHarian'));
    }

    public function verifikasiLog(Request $request, LogHarian $logHarian)
    {
        $this->authorizeLog($logHarian);

        $request->validate([
            'action' => 'required|in:diverifikasi,revisi,ditolak',
            'feedback' => 'nullable|string|max:1000',
            'nilai' => 'nullable|integer|min:1|max:100',
        ]);

        $logHarian->update([
            'status_verifikasi' => $request->action,
            'diverifikasi_oleh' => auth()->id(),
            'tgl_verifikasi' => now(),
            'feedback_pembimbing' => $request->feedback,
            'nilai_pembimbing' => $request->nilai,
        ]);

        $statusLabel = match($request->action) {
            'diverifikasi' => 'diverifikasi',
            'revisi' => 'perlu direvisi',
            'ditolak' => 'ditolak',
        };

        Notifikasi::kirim(
            $logHarian->mahasiswa_id,
            'Log Harian ' . ucfirst($request->action),
            "Log aktivitas tanggal {$logHarian->tanggal->format('d/m/Y')} telah {$statusLabel}." .
            ($request->feedback ? " Feedback: {$request->feedback}" : ''),
            'log_' . ($request->action === 'revisi' ? 'revisi' : 'diverifikasi')
        );

        return back()->with('success', 'Log harian berhasil diverifikasi.');
    }

    public function laporanMahasiswa(Proposal $proposal)
    {
        abort_if($proposal->pembimbing_id !== auth()->id(), 403);

        $proposal->load(['pengaju', 'kehadiran.logHarian', 'periode']);

        $rekapKehadiran = $proposal->kehadiran()
            ->selectRaw('status_kehadiran, count(*) as total')
            ->groupBy('status_kehadiran')
            ->get()
            ->keyBy('status_kehadiran');

        $rataRataNilai = $proposal->logHarian()
            ->where('status_verifikasi', 'diverifikasi')
            ->avg('nilai_pembimbing');

        return view('pembimbing.laporan.mahasiswa', compact('proposal', 'rekapKehadiran', 'rataRataNilai'));
    }

    // ─── Helpers ────────────────────────────────────────────────────
    protected function authorizeKehadiran(Kehadiran $kehadiran): void
    {
        abort_if($kehadiran->proposal->pembimbing_id !== auth()->id(), 403);
    }

    protected function authorizeLog(LogHarian $log): void
    {
        abort_if($log->proposal->pembimbing_id !== auth()->id(), 403);
    }
}
