<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use App\Models\Proposal;
use Illuminate\Http\Request;

class KehadiranController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isMahasiswa()) {
            $proposal = Proposal::where('mahasiswa_id', $user->id)->where('status', 'diterima')->first();
            if (!$proposal) return redirect()->route('dashboard')->with('warning', 'Proposal Anda belum diterima.');
            $kehadiran = Kehadiran::where('mahasiswa_id', $user->id)->latest('tanggal')->paginate(20);
            return view('kehadiran.mahasiswa.index', compact('kehadiran', 'proposal'));
        }

        if ($user->isPembimbing()) {
            $kehadiran = Kehadiran::with(['mahasiswa', 'proposal'])
                ->whereHas('proposal', fn($q) => $q->where('pembimbing_id', $user->id))
                ->where('status_verifikasi', 'menunggu')
                ->latest('tanggal')->paginate(20);
            return view('kehadiran.pembimbing.index', compact('kehadiran'));
        }

        abort(403);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $proposal = Proposal::where('mahasiswa_id', $user->id)->where('status', 'diterima')->firstOrFail();

        $validated = $request->validate([
            'tanggal'     => 'required|date|before_or_equal:today',
            'jam_masuk'   => 'required|date_format:H:i',
            'jam_keluar'  => 'nullable|date_format:H:i|after:jam_masuk',
            'status'      => 'required|in:hadir,izin,sakit,libur',
            'keterangan'  => 'nullable|string|max:500',
            'foto_masuk'  => 'nullable|image|max:2048',
            'foto_keluar' => 'nullable|image|max:2048',
        ]);

        $existing = Kehadiran::where('mahasiswa_id', $user->id)
            ->where('tanggal', $validated['tanggal'])->first();

        if ($existing) {
            return back()->withErrors(['tanggal' => 'Kehadiran untuk tanggal ini sudah dicatat.']);
        }

        $fotoMasuk = $request->hasFile('foto_masuk')
            ? $request->file('foto_masuk')->store('kehadiran', 'public') : null;
        $fotoKeluar = $request->hasFile('foto_keluar')
            ? $request->file('foto_keluar')->store('kehadiran', 'public') : null;

        Kehadiran::create([
            ...$validated,
            'proposal_id'  => $proposal->id,
            'mahasiswa_id' => $user->id,
            'foto_masuk'   => $fotoMasuk,
            'foto_keluar'  => $fotoKeluar,
        ]);

        return back()->with('success', 'Kehadiran berhasil dicatat!');
    }

    public function verifikasi(Request $request, Kehadiran $kehadiran)
    {
        $request->validate([
            'action'           => 'required|in:verifikasi,tolak',
            'catatan_pembimbing' => 'nullable|string|max:500',
        ]);

        $kehadiran->update([
            'status_verifikasi'   => $request->action === 'verifikasi' ? 'diverifikasi' : 'ditolak',
            'diverifikasi_oleh'   => auth()->id(),
            'diverifikasi_at'     => now(),
            'catatan_pembimbing'  => $request->catatan_pembimbing,
        ]);

        return back()->with('success', 'Kehadiran berhasil diverifikasi.');
    }
}
