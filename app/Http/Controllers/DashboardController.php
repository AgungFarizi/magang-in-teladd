<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use App\Models\LogHarian;
use App\Models\PeriodeMagang;
use App\Models\Pengguna;
use App\Models\Proposal;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return match($user->role) {
            'manager'           => $this->dashboardManager(),
            'manager_departemen' => $this->dashboardManagerDept(),
            'operator'          => $this->dashboardOperator(),
            'pembimbing'        => $this->dashboardPembimbing(),
            'mahasiswa'         => $this->dashboardMahasiswa(),
            default             => abort(403),
        };
    }

    private function dashboardManager()
    {
        $stats = [
            'total_mahasiswa'  => Pengguna::where('role', 'mahasiswa')->count(),
            'total_proposal'   => Proposal::count(),
            'proposal_pending' => Proposal::whereIn('status', ['diteruskan_manager', 'disetujui_manager_dept'])->count(),
            'proposal_diterima'=> Proposal::where('status', 'diterima')->count(),
            'total_operator'   => Pengguna::where('role', 'operator')->count(),
            'total_pembimbing' => Pengguna::where('role', 'pembimbing')->count(),
            'periode_aktif'    => PeriodeMagang::where('status', 'aktif')->count(),
        ];

        $proposalTerbaru = Proposal::with(['mahasiswa', 'periode'])
            ->whereIn('status', ['diteruskan_manager', 'disetujui_manager_dept'])
            ->latest()->take(5)->get();

        $periodeList = PeriodeMagang::latest()->take(3)->get();

        return view('dashboard.manager', compact('stats', 'proposalTerbaru', 'periodeList'));
    }

    private function dashboardManagerDept()
    {
        $user = auth()->user();
        $stats = [
            'proposal_divisi'  => Proposal::where('divisi_tujuan', $user->divisi)
                ->whereIn('status', ['menunggu_manager_dept', 'disetujui_manager_dept', 'ditolak_manager_dept'])->count(),
            'proposal_pending' => Proposal::where('divisi_tujuan', $user->divisi)
                ->where('status', 'menunggu_manager_dept')->count(),
            'mahasiswa_aktif'  => Proposal::where('divisi_tujuan', $user->divisi)
                ->where('status', 'diterima')->count(),
            'total_pembimbing' => Pengguna::where('role', 'pembimbing')->where('divisi', $user->divisi)->count(),
        ];

        $proposalPending = Proposal::with(['mahasiswa'])
            ->where('divisi_tujuan', $user->divisi)
            ->where('status', 'menunggu_manager_dept')
            ->latest()->take(5)->get();

        return view('dashboard.manager-departemen', compact('stats', 'proposalPending', 'user'));
    }

    private function dashboardOperator()
    {
        $stats = [
            'proposal_baru'     => Proposal::where('status', 'menunggu_review')->count(),
            'sedang_direview'   => Proposal::where('status', 'direview_operator')->count(),
            'diteruskan'        => Proposal::where('status', 'diteruskan_manager')->count(),
            'periode_aktif'     => PeriodeMagang::where('status', 'aktif')->count(),
            'total_proposal'    => Proposal::count(),
        ];

        $proposalBaru = Proposal::with(['mahasiswa', 'periode'])
            ->whereIn('status', ['menunggu_review', 'direview_operator'])
            ->latest()->take(8)->get();

        return view('dashboard.operator', compact('stats', 'proposalBaru'));
    }

    private function dashboardPembimbing()
    {
        $user = auth()->user();
        $mahasiswaBimbingan = Proposal::with(['mahasiswa', 'kehadiran', 'logHarian'])
            ->where('pembimbing_id', $user->id)
            ->where('status', 'diterima')
            ->get();

        $stats = [
            'total_mahasiswa'       => $mahasiswaBimbingan->count(),
            'kehadiran_pending'     => Kehadiran::whereHas('proposal', fn($q) => $q->where('pembimbing_id', $user->id))
                ->where('status_verifikasi', 'menunggu')->count(),
            'log_pending'           => LogHarian::whereHas('proposal', fn($q) => $q->where('pembimbing_id', $user->id))
                ->where('status_verifikasi', 'menunggu')->count(),
        ];

        return view('dashboard.pembimbing', compact('stats', 'mahasiswaBimbingan', 'user'));
    }

    private function dashboardMahasiswa()
    {
        $user = auth()->user();
        $proposal = Proposal::with(['periode', 'pembimbing'])
            ->where('mahasiswa_id', $user->id)->latest()->first();

        $periodeAktif = PeriodeMagang::where('status', 'aktif')
            ->whereDate('tanggal_mulai_daftar', '<=', today())
            ->whereDate('tanggal_selesai_daftar', '>=', today())
            ->get();

        $stats = [];
        if ($proposal && $proposal->isActive()) {
            $stats = [
                'total_kehadiran'  => Kehadiran::where('mahasiswa_id', $user->id)->where('status', 'hadir')->count(),
                'total_log'        => LogHarian::where('mahasiswa_id', $user->id)->count(),
                'kehadiran_pending'=> Kehadiran::where('mahasiswa_id', $user->id)->where('status_verifikasi', 'menunggu')->count(),
                'log_pending'      => LogHarian::where('mahasiswa_id', $user->id)->where('status_verifikasi', 'menunggu')->count(),
            ];
        }

        return view('dashboard.mahasiswa', compact('proposal', 'periodeAktif', 'stats', 'user'));
    }
}
