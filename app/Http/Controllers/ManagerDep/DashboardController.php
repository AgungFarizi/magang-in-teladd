<?php

namespace App\Http\Controllers\ManagerDep;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Pengguna;
use App\Models\Proposal;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected function divisi(): string
    {
        return auth()->user()->divisi;
    }

    public function dashboard()
    {
        $divisi = $this->divisi();

        $stats = [
            'proposal_pending' => Proposal::byDivisi($divisi)->where('status', 'review_manager_dep')->count(),
            'proposal_diterima' => Proposal::byDivisi($divisi)->whereIn('status', ['diterima', 'aktif', 'selesai'])->count(),
            'proposal_ditolak' => Proposal::byDivisi($divisi)->whereIn('status', ['ditolak', 'ditolak_manager_dep'])->count(),
            'magang_aktif' => Proposal::byDivisi($divisi)->where('status', 'aktif')->count(),
            'total_pembimbing' => Pengguna::where('role', 'pembimbing_lapang')->where('divisi', $divisi)->count(),
        ];

        $proposalPending = Proposal::with(['pengaju', 'periode'])
            ->byDivisi($divisi)
            ->where('status', 'review_manager_dep')
            ->latest()
            ->take(10)
            ->get();

        $mahasiswaAktif = Proposal::with(['pengaju', 'pembimbing'])
            ->byDivisi($divisi)
            ->where('status', 'aktif')
            ->latest()
            ->take(10)
            ->get();

        return view('manager-dep.dashboard', compact('stats', 'proposalPending', 'mahasiswaAktif', 'divisi'));
    }

    public function indexProposal(Request $request)
    {
        $divisi = $this->divisi();

        $query = Proposal::with(['pengaju', 'periode'])
            ->byDivisi($divisi)
            ->whereIn('status', ['review_manager_dep', 'disetujui_manager_dep', 'ditolak_manager_dep']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_proposal', 'like', '%' . $request->search . '%')
                  ->orWhere('judul_proposal', 'like', '%' . $request->search . '%')
                  ->orWhereHas('pengaju', fn($q) => $q->where('nama_lengkap', 'like', '%' . $request->search . '%'));
            });
        }

        $proposals = $query->latest()->paginate(15)->withQueryString();

        return view('manager-dep.proposal.index', compact('proposals', 'divisi'));
    }

    public function showProposal(Proposal $proposal)
    {
        abort_if($proposal->divisi_tujuan !== $this->divisi(), 403, 'Proposal bukan untuk divisi Anda.');
        $proposal->load(['pengaju', 'periode', 'anggota', 'operator']);
        return view('manager-dep.proposal.show', compact('proposal'));
    }

    public function approveProposal(Request $request, Proposal $proposal)
    {
        abort_if($proposal->divisi_tujuan !== $this->divisi(), 403);
        $request->validate(['catatan' => 'nullable|string|max:1000']);

        $proposal->update([
            'status' => 'review_manager',
            'manager_dep_id' => auth()->id(),
            'tgl_review_manager_dep' => now(),
            'catatan_manager_dep' => $request->catatan,
        ]);

        \App\Models\Notifikasi::kirim(
            $proposal->pengaju_id,
            'Proposal Disetujui Manager Departemen',
            "Proposal Anda ({$proposal->nomor_proposal}) telah disetujui oleh Manager Departemen {$this->divisi()} dan diteruskan ke Manager.",
            'proposal_diteruskan'
        );

        return redirect()->route('manager-dep.proposal.index')
            ->with('success', 'Proposal disetujui dan diteruskan ke Manager.');
    }

    public function rejectProposal(Request $request, Proposal $proposal)
    {
        abort_if($proposal->divisi_tujuan !== $this->divisi(), 403);
        $request->validate(['catatan' => 'required|string|max:1000']);

        $proposal->update([
            'status' => 'ditolak_manager_dep',
            'manager_dep_id' => auth()->id(),
            'tgl_review_manager_dep' => now(),
            'catatan_manager_dep' => $request->catatan,
        ]);

        \App\Models\Notifikasi::kirim(
            $proposal->pengaju_id,
            'Proposal Ditolak Manager Departemen',
            "Proposal Anda ({$proposal->nomor_proposal}) ditolak oleh Manager Departemen. Catatan: {$request->catatan}",
            'proposal_ditolak'
        );

        return redirect()->route('manager-dep.proposal.index')
            ->with('success', 'Proposal berhasil ditolak.');
    }

    // ─── Kelola Pembimbing ──────────────────────────────────────────
    public function indexPembimbing()
    {
        $pembimbing = Pengguna::where('role', 'pembimbing_lapang')
            ->where('divisi', $this->divisi())
            ->withCount(['mahasiswaBimbingan as total_bimbingan'])
            ->latest()
            ->paginate(15);

        return view('manager-dep.pembimbing.index', compact('pembimbing'));
    }

    public function storePembimbing(Request $request)
    {
        $request->validate([
            'keterangan' => 'nullable|string|max:255',
        ]);

        $token = \App\Models\AksesTokenAdmin::generate(
            'pembimbing_lapang',
            $this->divisi(),
            $request->keterangan ?? "Token pembimbing divisi {$this->divisi()}",
            auth()->id()
        );

        return back()->with('success', "Token registrasi pembimbing berhasil dibuat: {$token->token}");
    }

    public function destroyPembimbing(Pengguna $pengguna)
    {
        abort_if($pengguna->role !== 'pembimbing_lapang' || $pengguna->divisi !== $this->divisi(), 403);
        $pengguna->update(['is_active' => false]);
        return back()->with('success', 'Pembimbing berhasil dinonaktifkan.');
    }

    public function mahasiswaDivisi(Request $request)
    {
        $divisi = $this->divisi();

        $proposals = Proposal::with(['pengaju', 'pembimbing', 'kehadiran'])
            ->byDivisi($divisi)
            ->whereIn('status', ['aktif', 'selesai'])
            ->paginate(15);

        return view('manager-dep.mahasiswa', compact('proposals', 'divisi'));
    }
}
