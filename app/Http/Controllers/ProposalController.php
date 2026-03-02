<?php

namespace App\Http\Controllers;

use App\Models\AnggotaProposal;
use App\Models\Notifikasi;
use App\Models\PeriodeMagang;
use App\Models\Pengguna;
use App\Models\Proposal;
use App\Models\SuratBalasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProposalController extends Controller
{
    // ===== MAHASISWA =====
    public function index()
    {
        $user = auth()->user();

        if ($user->isMahasiswa()) {
            $proposals = Proposal::with(['periode', 'pembimbing'])
                ->where('mahasiswa_id', $user->id)->latest()->get();
            return view('proposal.mahasiswa.index', compact('proposals'));
        }

        // Admin views
        $query = Proposal::with(['mahasiswa', 'periode', 'reviewerOperator']);

        if ($user->isOperator()) {
            $proposals = $query->latest()->paginate(15);
            return view('proposal.operator.index', compact('proposals'));
        }

        if ($user->isManagerDepartemen()) {
            $proposals = $query->where('divisi_tujuan', $user->divisi)->latest()->paginate(15);
            return view('proposal.manager-departemen.index', compact('proposals'));
        }

        if ($user->isManager()) {
            $proposals = $query->latest()->paginate(15);
            return view('proposal.manager.index', compact('proposals'));
        }

        abort(403);
    }

    public function create()
    {
        $user = auth()->user();
        $periodeAktif = PeriodeMagang::where('status', 'aktif')
            ->whereDate('tanggal_mulai_daftar', '<=', today())
            ->whereDate('tanggal_selesai_daftar', '>=', today())
            ->get();

        // Check if already has active proposal
        $existingProposal = Proposal::where('mahasiswa_id', $user->id)
            ->whereNotIn('status', ['ditolak', 'ditolak_manager', 'ditolak_manager_dept'])
            ->first();

        if ($existingProposal) {
            return redirect()->route('proposal.index')
                ->with('warning', 'Anda sudah memiliki proposal yang sedang diproses.');
        }

        return view('proposal.mahasiswa.create', compact('periodeAktif', 'user'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'periode_id'           => 'required|exists:periode_magang,id',
            'judul'                => 'required|string|max:255',
            'deskripsi'            => 'required|string|min:50',
            'bidang_minat'         => 'required|string|max:100',
            'divisi_tujuan'        => 'required|string|max:100',
            'file_proposal'        => 'required|file|mimes:pdf|max:5120',
            'file_surat_pengantar' => 'nullable|file|mimes:pdf|max:5120',
            'anggota'              => 'nullable|array',
            'anggota.*.nama'       => 'required_with:anggota|string|max:255',
            'anggota.*.nim'        => 'required_with:anggota|string|max:50',
            'anggota.*.jurusan'    => 'required_with:anggota|string|max:100',
            'anggota.*.universitas'=> 'required_with:anggota|string|max:150',
        ]);

        DB::transaction(function () use ($request, $user, $validated) {
            $fileProposal = $request->file('file_proposal')->store('proposal', 'public');
            $fileSurat = null;
            if ($request->hasFile('file_surat_pengantar')) {
                $fileSurat = $request->file('file_surat_pengantar')->store('surat_pengantar', 'public');
            }

            $proposal = Proposal::create([
                'periode_id'           => $validated['periode_id'],
                'mahasiswa_id'         => $user->id,
                'judul'                => $validated['judul'],
                'deskripsi'            => $validated['deskripsi'],
                'bidang_minat'         => $validated['bidang_minat'],
                'divisi_tujuan'        => $validated['divisi_tujuan'],
                'file_proposal'        => $fileProposal,
                'file_surat_pengantar' => $fileSurat,
                'status'               => 'menunggu_review',
            ]);

            // Ketua (mahasiswa pengaju)
            AnggotaProposal::create([
                'proposal_id'  => $proposal->id,
                'mahasiswa_id' => $user->id,
                'nama_lengkap' => $user->nama,
                'nim'          => $user->nim,
                'jurusan'      => $user->jurusan,
                'universitas'  => $user->universitas,
                'peran'        => 'ketua',
            ]);

            // Anggota tambahan
            if (!empty($validated['anggota'])) {
                foreach ($validated['anggota'] as $anggota) {
                    AnggotaProposal::create([
                        'proposal_id'  => $proposal->id,
                        'mahasiswa_id' => $user->id,
                        'nama_lengkap' => $anggota['nama'],
                        'nim'          => $anggota['nim'],
                        'jurusan'      => $anggota['jurusan'],
                        'universitas'  => $anggota['universitas'],
                        'peran'        => 'anggota',
                    ]);
                }
            }

            // Notifikasi ke operator
            Pengguna::where('role', 'operator')->each(function ($op) use ($proposal) {
                Notifikasi::create([
                    'user_id' => $op->id,
                    'judul'   => 'Proposal Baru',
                    'pesan'   => "Proposal baru dari {$proposal->mahasiswa->nama} perlu direview.",
                    'tipe'    => 'info',
                    'url'     => route('proposal.show', $proposal->id),
                ]);
            });
        });

        return redirect()->route('proposal.index')
            ->with('success', 'Proposal berhasil diajukan! Silakan tunggu konfirmasi dari operator.');
    }

    public function show(Proposal $proposal)
    {
        $user = auth()->user();
        $this->authorizeView($user, $proposal);

        $proposal->load(['mahasiswa', 'anggota', 'periode', 'pembimbing', 'suratBalasan',
            'reviewerOperator', 'approverManagerDept', 'approverManager']);

        return view('proposal.show', compact('proposal', 'user'));
    }

    // ===== OPERATOR =====
    public function review(Request $request, Proposal $proposal)
    {
        $request->validate([
            'action'   => 'required|in:teruskan,tolak',
            'catatan'  => 'nullable|string|max:1000',
        ]);

        $status = $request->action === 'teruskan' ? 'menunggu_manager_dept' : 'ditolak';
        $oldStatus = $proposal->status;

        $proposal->update([
            'status'           => $status,
            'catatan_operator' => $request->catatan,
            'direview_oleh'    => auth()->id(),
            'direview_at'      => now(),
        ]);

        if ($status === 'ditolak') {
            $this->buatSuratBalasan($proposal, 'penolakan', $request->catatan);
        }

        Notifikasi::create([
            'user_id' => $proposal->mahasiswa_id,
            'judul'   => $status === 'ditolak' ? 'Proposal Ditolak' : 'Proposal Diteruskan',
            'pesan'   => $status === 'ditolak'
                ? 'Proposal Anda ditolak oleh operator. ' . $request->catatan
                : 'Proposal Anda telah diteruskan ke Manager Departemen.',
            'tipe'    => $status === 'ditolak' ? 'danger' : 'info',
            'url'     => route('proposal.show', $proposal->id),
        ]);

        return back()->with('success', 'Proposal berhasil ' . ($status === 'ditolak' ? 'ditolak' : 'diteruskan'));
    }

    // ===== MANAGER DEPARTEMEN =====
    public function approveManagerDept(Request $request, Proposal $proposal)
    {
        $request->validate([
            'action'  => 'required|in:setujui,tolak',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $status = $request->action === 'setujui' ? 'diteruskan_manager' : 'ditolak_manager_dept';

        $proposal->update([
            'status'                       => $status,
            'catatan_manager_dept'         => $request->catatan,
            'disetujui_manager_dept_oleh'  => $user->id,
            'disetujui_manager_dept_at'    => now(),
        ]);

        Notifikasi::create([
            'user_id' => $proposal->mahasiswa_id,
            'judul'   => $status === 'ditolak_manager_dept' ? 'Proposal Ditolak' : 'Proposal Diproses',
            'pesan'   => $status === 'ditolak_manager_dept'
                ? 'Proposal Anda ditolak oleh Manager Departemen. ' . $request->catatan
                : 'Proposal Anda telah disetujui Manager Departemen, menunggu approval Manager.',
            'tipe'    => $status === 'ditolak_manager_dept' ? 'danger' : 'info',
            'url'     => route('proposal.show', $proposal->id),
        ]);

        return back()->with('success', 'Proposal berhasil diproses.');
    }

    // ===== MANAGER =====
    public function approveManager(Request $request, Proposal $proposal)
    {
        $request->validate([
            'action'       => 'required|in:setujui,tolak',
            'catatan'      => 'nullable|string|max:1000',
            'pembimbing_id'=> 'required_if:action,setujui|nullable|exists:pengguna,id',
        ]);

        $user = auth()->user();
        $status = $request->action === 'setujui' ? 'diterima' : 'ditolak_manager';

        $proposal->update([
            'status'                    => $status,
            'catatan_manager'           => $request->catatan,
            'disetujui_manager_oleh'    => $user->id,
            'disetujui_manager_at'      => now(),
            'pembimbing_id'             => $request->action === 'setujui' ? $request->pembimbing_id : null,
        ]);

        $this->buatSuratBalasan($proposal, $status === 'diterima' ? 'penerimaan' : 'penolakan', $request->catatan);

        Notifikasi::create([
            'user_id' => $proposal->mahasiswa_id,
            'judul'   => $status === 'diterima' ? '🎉 Proposal Diterima!' : 'Proposal Ditolak',
            'pesan'   => $status === 'diterima'
                ? 'Selamat! Proposal magang Anda telah diterima. Silakan lihat surat penerimaan.'
                : 'Proposal Anda ditolak oleh Manager. ' . $request->catatan,
            'tipe'    => $status === 'diterima' ? 'success' : 'danger',
            'url'     => route('proposal.show', $proposal->id),
        ]);

        return back()->with('success', 'Keputusan berhasil disimpan.');
    }

    private function buatSuratBalasan(Proposal $proposal, string $jenis, string $catatan = null): void
    {
        $isi = $jenis === 'penerimaan'
            ? "Dengan hormat,\n\nKami menyatakan bahwa proposal magang dengan judul \"{$proposal->judul}\" yang diajukan oleh {$proposal->mahasiswa->nama} ({$proposal->mahasiswa->nim}) dari {$proposal->mahasiswa->universitas} telah DITERIMA.\n\nSilakan menghubungi pembimbing lapang Anda untuk informasi lebih lanjut.\n\nDemikian surat ini dibuat.\n\nHormat kami,\nTim TELLINTER"
            : "Dengan hormat,\n\nKami menyatakan bahwa proposal magang dengan judul \"{$proposal->judul}\" yang diajukan oleh {$proposal->mahasiswa->nama} ({$proposal->mahasiswa->nim}) dari {$proposal->mahasiswa->universitas} tidak dapat kami terima.\n\nAlasan: " . ($catatan ?? 'Tidak memenuhi persyaratan.') . "\n\nDemikian surat ini dibuat.\n\nHormat kami,\nTim TELLINTER";

        SuratBalasan::updateOrCreate(
            ['proposal_id' => $proposal->id],
            [
                'jenis'      => $jenis,
                'nomor_surat'=> 'TELL/' . date('Y') . '/' . str_pad($proposal->id, 4, '0', STR_PAD_LEFT),
                'isi_surat'  => $isi,
                'dibuat_oleh'=> auth()->id(),
                'dikirim_at' => now(),
            ]
        );
    }

    private function authorizeView($user, Proposal $proposal): void
    {
        if ($user->isMahasiswa() && $proposal->mahasiswa_id !== $user->id) {
            abort(403);
        }
        if ($user->isManagerDepartemen() && $proposal->divisi_tujuan !== $user->divisi) {
            abort(403);
        }
        if ($user->isPembimbing() && $proposal->pembimbing_id !== $user->id) {
            abort(403);
        }
    }
}
