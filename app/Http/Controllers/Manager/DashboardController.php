<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\AksesTokenAdmin;
use App\Models\Pengguna;
use App\Models\PeriodeMagang;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_mahasiswa' => Pengguna::where('role', 'mahasiswa')->count(),
            'total_proposal' => Proposal::count(),
            'proposal_pending' => Proposal::where('status', 'review_manager')->count(),
            'proposal_diterima' => Proposal::whereIn('status', ['diterima', 'aktif', 'selesai'])->count(),
            'proposal_ditolak' => Proposal::where('status', 'ditolak')->count(),
            'magang_aktif' => Proposal::where('status', 'aktif')->count(),
            'total_operator' => Pengguna::where('role', 'operator')->count(),
            'total_pembimbing' => Pengguna::where('role', 'pembimbing_lapang')->count(),
            'total_manager_dep' => Pengguna::where('role', 'manager_departemen')->count(),
        ];

        $proposalPending = Proposal::with(['pengaju', 'periode'])
            ->where('status', 'review_manager')
            ->latest()
            ->take(10)
            ->get();

        $periodeAktif = PeriodeMagang::aktif()->first();

        $proposalPerDivisi = Proposal::select('divisi_tujuan', DB::raw('count(*) as total'))
            ->whereNotIn('status', ['draft', 'dibatalkan'])
            ->groupBy('divisi_tujuan')
            ->get();

        $proposalPerBulan = Proposal::select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('count(*) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')->orderBy('bulan')
            ->get();

        return view('manager.dashboard', compact(
            'stats', 'proposalPending', 'periodeAktif', 'proposalPerDivisi', 'proposalPerBulan'
        ));
    }

    public function indexProposal(Request $request)
    {
        $query = Proposal::with(['pengaju', 'periode', 'managerDep', 'pembimbing'])
            ->where('status', 'review_manager');

        if ($request->filled('divisi')) {
            $query->where('divisi_tujuan', $request->divisi);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_proposal', 'like', '%' . $request->search . '%')
                  ->orWhere('judul_proposal', 'like', '%' . $request->search . '%')
                  ->orWhereHas('pengaju', fn($q) => $q->where('nama_lengkap', 'like', '%' . $request->search . '%'));
            });
        }

        $proposals = $query->paginate(15)->withQueryString();

        $divisiList = Proposal::select('divisi_tujuan')->distinct()->pluck('divisi_tujuan');

        return view('manager.proposal.index', compact('proposals', 'divisiList'));
    }

    public function showProposal(Proposal $proposal)
    {
        $proposal->load(['pengaju', 'periode', 'anggota', 'operator', 'managerDep', 'pembimbing']);
        return view('manager.proposal.show', compact('proposal'));
    }

    public function approveProposal(Request $request, Proposal $proposal)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:1000',
            'pembimbing_id' => 'nullable|exists:pengguna,id',
        ]);

        $proposal->update([
            'status' => 'diterima',
            'manager_id' => auth()->id(),
            'tgl_review_manager' => now(),
            'catatan_manager' => $request->catatan,
            'pembimbing_id' => $request->pembimbing_id,
        ]);

        // Kirim notifikasi ke mahasiswa
        \App\Models\Notifikasi::kirim(
            $proposal->pengaju_id,
            'Proposal Diterima!',
            "Selamat! Proposal magang Anda ({$proposal->nomor_proposal}) telah disetujui oleh Manager.",
            'proposal_diterima',
            route('mahasiswa.proposal.show', $proposal)
        );

        // Notifikasi ke operator untuk buat surat balasan
        if ($proposal->operator_id) {
            \App\Models\Notifikasi::kirim(
                $proposal->operator_id,
                'Proposal Disetujui - Buat Surat Balasan',
                "Proposal {$proposal->nomor_proposal} telah disetujui Manager. Segera buat surat penerimaan.",
                'surat_balasan',
                route('operator.surat.create', $proposal)
            );
        }

        return redirect()->route('manager.proposal.index')
            ->with('success', 'Proposal berhasil disetujui.');
    }

    public function rejectProposal(Request $request, Proposal $proposal)
    {
        $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        $proposal->update([
            'status' => 'ditolak',
            'manager_id' => auth()->id(),
            'tgl_review_manager' => now(),
            'catatan_manager' => $request->catatan,
        ]);

        \App\Models\Notifikasi::kirim(
            $proposal->pengaju_id,
            'Proposal Ditolak',
            "Mohon maaf, proposal magang Anda ({$proposal->nomor_proposal}) ditolak. Catatan: {$request->catatan}",
            'proposal_ditolak',
            route('mahasiswa.proposal.show', $proposal)
        );

        if ($proposal->operator_id) {
            \App\Models\Notifikasi::kirim(
                $proposal->operator_id,
                'Proposal Ditolak Manager',
                "Proposal {$proposal->nomor_proposal} ditolak. Segera buat surat penolakan.",
                'surat_balasan',
                route('operator.surat.create', $proposal)
            );
        }

        return redirect()->route('manager.proposal.index')
            ->with('success', 'Proposal berhasil ditolak.');
    }

    // ─── Kelola Operator ────────────────────────────────────────────
    public function indexOperator()
    {
        $operators = Pengguna::where('role', 'operator')
            ->withCount(['proposalDiajukan as proposal_direview' => function ($q) {
                // Tidak ada relasi proposal_direview, kita skip count
            }])
            ->latest()->paginate(15);

        return view('manager.operator.index', compact('operators'));
    }

    public function storeOperator(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email',
            'divisi' => 'required|string',
            'no_telepon' => 'nullable|string|max:20',
        ]);

        // Generate token untuk operator
        $token = AksesTokenAdmin::generate(
            'operator',
            $request->divisi,
            "Token untuk operator: {$request->nama_lengkap}",
            auth()->id()
        );

        return redirect()->route('manager.operator.index')
            ->with('success', "Token registrasi operator berhasil dibuat: {$token->token}");
    }

    public function destroyOperator(Pengguna $pengguna)
    {
        abort_if($pengguna->role !== 'operator', 403);
        $pengguna->update(['is_active' => false]);
        return back()->with('success', 'Operator berhasil dinonaktifkan.');
    }

    // ─── Laporan ────────────────────────────────────────────────────
    public function laporan(Request $request)
    {
        $periode = $request->get('periode_id');
        $divisi = $request->get('divisi');

        $query = Proposal::with(['pengaju', 'periode', 'pembimbing'])
            ->whereNotIn('status', ['draft', 'dibatalkan']);

        if ($periode) {
            $query->where('periode_id', $periode);
        }
        if ($divisi) {
            $query->where('divisi_tujuan', $divisi);
        }

        $proposals = $query->get();
        $periode_list = PeriodeMagang::orderBy('created_at', 'desc')->get();
        $divisiList = Proposal::select('divisi_tujuan')->distinct()->pluck('divisi_tujuan');

        return view('manager.laporan', compact('proposals', 'periode_list', 'divisiList'));
    }

    // ─── Token Management ─────────────────────────────────────────
    public function indexToken()
    {
        $tokens = AksesTokenAdmin::with(['pembuat', 'penggunaToken'])->latest()->paginate(20);
        return view('manager.token.index', compact('tokens'));
    }

    public function storeToken(Request $request)
    {
        $request->validate([
            'untuk_role' => 'required|in:manager_departemen,operator,pembimbing_lapang',
            'divisi' => 'nullable|string',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $token = AksesTokenAdmin::generate(
            $request->untuk_role,
            $request->divisi,
            $request->keterangan ?? 'Token registrasi admin',
            auth()->id()
        );

        return back()->with('success', "Token berhasil dibuat: {$token->token}");
    }

    public function destroyToken(AksesTokenAdmin $aksesTokenAdmin)
    {
        $aksesTokenAdmin->update(['is_active' => false]);
        return back()->with('success', 'Token berhasil dinonaktifkan.');
    }
}
