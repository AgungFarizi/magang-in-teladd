<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Pengguna;
use App\Models\PeriodeMagang;
use App\Models\Proposal;
use App\Models\SuratBalasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'proposal_baru' => Proposal::where('status', 'diajukan')->count(),
            'sedang_direview' => Proposal::where('status', 'review_operator')->where('operator_id', auth()->id())->count(),
            'diteruskan' => Proposal::where('status', 'diteruskan_manager')->count(),
            'surat_pending' => Proposal::whereIn('status', ['diterima', 'ditolak'])->whereDoesntHave('suratBalasan')->count(),
        ];

        $proposalBaru = Proposal::with(['pengaju', 'periode'])
            ->where('status', 'diajukan')
            ->latest()
            ->take(10)
            ->get();

        $periodeAktif = PeriodeMagang::aktif()->first();

        return view('operator.dashboard', compact('stats', 'proposalBaru', 'periodeAktif'));
    }

    // ─── Manajemen Proposal ─────────────────────────────────────────
    public function indexProposal(Request $request)
    {
        $query = Proposal::with(['pengaju', 'periode'])
            ->whereIn('status', ['diajukan', 'review_operator', 'diteruskan_manager', 'review_manager_dep', 'diterima', 'ditolak']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_proposal', 'like', '%' . $request->search . '%')
                  ->orWhereHas('pengaju', fn($q) => $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhere('nim', 'like', '%' . $request->search . '%'));
            });
        }

        $proposals = $query->latest()->paginate(15)->withQueryString();

        return view('operator.proposal.index', compact('proposals'));
    }

    public function showProposal(Proposal $proposal)
    {
        $proposal->load(['pengaju', 'periode', 'anggota', 'suratBalasan']);
        return view('operator.proposal.show', compact('proposal'));
    }

    public function mulaiReview(Proposal $proposal)
    {
        abort_if(!in_array($proposal->status, ['diajukan']), 403, 'Proposal tidak dapat direview.');

        $proposal->update([
            'status' => 'review_operator',
            'operator_id' => auth()->id(),
        ]);

        return back()->with('success', 'Proposal sedang Anda review.');
    }

    public function teruskProposal(Request $request, Proposal $proposal)
    {
        abort_if($proposal->status !== 'review_operator', 403);
        $request->validate(['catatan' => 'nullable|string|max:1000']);

        $proposal->update([
            'status' => 'review_manager_dep',
            'operator_id' => auth()->id(),
            'tgl_review_operator' => now(),
            'catatan_operator' => $request->catatan,
        ]);

        // Notifikasi ke Manager Departemen yang sesuai divisi
        $managerDep = Pengguna::where('role', 'manager_departemen')
            ->where('divisi', $proposal->divisi_tujuan)
            ->where('is_active', true)
            ->first();

        if ($managerDep) {
            Notifikasi::kirim(
                $managerDep->id,
                'Proposal Baru Perlu Review',
                "Proposal {$proposal->nomor_proposal} dari {$proposal->pengaju->nama_lengkap} menunggu review Anda.",
                'proposal_diajukan',
                route('manager-dep.proposal.show', $proposal)
            );
        }

        Notifikasi::kirim(
            $proposal->pengaju_id,
            'Proposal Diteruskan ke Manager Departemen',
            "Proposal Anda ({$proposal->nomor_proposal}) telah diteruskan ke Manager Departemen {$proposal->divisi_tujuan}.",
            'proposal_diteruskan'
        );

        return redirect()->route('operator.proposal.index')
            ->with('success', 'Proposal berhasil diteruskan ke Manager Departemen.');
    }

    public function tolakProposal(Request $request, Proposal $proposal)
    {
        abort_if($proposal->status !== 'review_operator', 403);
        $request->validate(['catatan' => 'required|string|max:1000']);

        $proposal->update([
            'status' => 'ditolak',
            'operator_id' => auth()->id(),
            'tgl_review_operator' => now(),
            'catatan_operator' => $request->catatan,
        ]);

        Notifikasi::kirim(
            $proposal->pengaju_id,
            'Proposal Ditolak',
            "Proposal Anda ({$proposal->nomor_proposal}) ditolak oleh Operator. Catatan: {$request->catatan}",
            'proposal_ditolak'
        );

        return redirect()->route('operator.proposal.index')
            ->with('success', 'Proposal berhasil ditolak.');
    }

    // ─── Surat Balasan ──────────────────────────────────────────────
    public function createSurat(Proposal $proposal)
    {
        abort_if(!in_array($proposal->status, ['diterima', 'ditolak', 'ditolak_manager_dep']), 403);
        $jenis = in_array($proposal->status, ['diterima']) ? 'penerimaan' : 'penolakan';
        $proposal->load(['pengaju', 'periode']);
        return view('operator.surat.create', compact('proposal', 'jenis'));
    }

    public function storeSurat(Request $request, Proposal $proposal)
    {
        $request->validate([
            'jenis' => 'required|in:penerimaan,penolakan',
            'perihal' => 'required|string|max:255',
            'isi_surat' => 'required|string',
        ]);

        // Generate PDF surat
        $nomorSurat = SuratBalasan::generateNomor($request->jenis);

        $pdf = Pdf::loadView('pdf.surat-balasan', [
            'proposal' => $proposal,
            'jenis' => $request->jenis,
            'nomor_surat' => $nomorSurat,
            'perihal' => $request->perihal,
            'isi_surat' => $request->isi_surat,
            'tanggal' => now()->format('d F Y'),
        ]);

        $filePath = 'surat-balasan/' . $nomorSurat . '.pdf';
        Storage::disk('public')->put($filePath, $pdf->output());

        $surat = SuratBalasan::create([
            'nomor_surat' => $nomorSurat,
            'proposal_id' => $proposal->id,
            'jenis' => $request->jenis,
            'perihal' => $request->perihal,
            'isi_surat' => $request->isi_surat,
            'file_surat' => $filePath,
            'tanggal_surat' => now(),
            'dibuat_oleh' => auth()->id(),
            'dikirim_pada' => now(),
        ]);

        // Update status proposal jika diterima, aktifkan magang
        if ($request->jenis === 'penerimaan' && $proposal->status === 'diterima') {
            $proposal->update(['status' => 'aktif']);
        }

        Notifikasi::kirim(
            $proposal->pengaju_id,
            'Surat Balasan Tersedia',
            "Surat balasan {$request->jenis} untuk proposal Anda ({$proposal->nomor_proposal}) telah diterbitkan.",
            'surat_balasan',
            route('mahasiswa.surat.show', $surat)
        );

        return redirect()->route('operator.surat.index')
            ->with('success', 'Surat balasan berhasil dibuat dan dikirim.');
    }

    public function indexSurat(Request $request)
    {
        $surat = SuratBalasan::with(['proposal.pengaju', 'pembuat'])
            ->latest()
            ->paginate(15);

        return view('operator.surat.index', compact('surat'));
    }

    // ─── Periode Magang ─────────────────────────────────────────────
    public function indexPeriode()
    {
        $periode = PeriodeMagang::with('pembuat')->latest()->paginate(10);
        return view('operator.periode.index', compact('periode'));
    }

    public function createPeriode()
    {
        return view('operator.periode.create');
    }

    public function storePeriode(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_buka_pendaftaran' => 'required|date',
            'tanggal_tutup_pendaftaran' => 'required|date|after:tanggal_buka_pendaftaran',
            'tanggal_mulai_magang' => 'required|date|after_or_equal:tanggal_tutup_pendaftaran',
            'tanggal_selesai_magang' => 'required|date|after:tanggal_mulai_magang',
            'kuota_total' => 'nullable|integer|min:0',
            'kuota_per_divisi' => 'nullable|integer|min:0',
            'syarat_dokumen' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        PeriodeMagang::create([
            ...$request->validated(),
            'status' => 'draft',
            'dibuat_oleh' => auth()->id(),
        ]);

        return redirect()->route('operator.periode.index')
            ->with('success', 'Periode magang berhasil dibuat.');
    }

    public function editPeriode(PeriodeMagang $periodeMagang)
    {
        return view('operator.periode.edit', ['periode' => $periodeMagang]);
    }

    public function updatePeriode(Request $request, PeriodeMagang $periodeMagang)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_buka_pendaftaran' => 'required|date',
            'tanggal_tutup_pendaftaran' => 'required|date|after:tanggal_buka_pendaftaran',
            'tanggal_mulai_magang' => 'required|date',
            'tanggal_selesai_magang' => 'required|date|after:tanggal_mulai_magang',
            'status' => 'required|in:draft,aktif,ditutup,selesai',
        ]);

        $periodeMagang->update($request->validated());

        return redirect()->route('operator.periode.index')
            ->with('success', 'Periode magang berhasil diupdate.');
    }
}
