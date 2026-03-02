<?php

namespace App\Http\Controllers;

use App\Models\LogHarian;
use App\Models\Proposal;
use Illuminate\Http\Request;

class LogHarianController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isMahasiswa()) {
            $proposal = Proposal::where('mahasiswa_id', $user->id)->where('status', 'diterima')->first();
            if (!$proposal) return redirect()->route('dashboard')->with('warning', 'Proposal Anda belum diterima.');
            $logs = LogHarian::where('mahasiswa_id', $user->id)->latest('tanggal')->paginate(15);
            return view('log-harian.mahasiswa.index', compact('logs', 'proposal'));
        }

        if ($user->isPembimbing()) {
            $logs = LogHarian::with(['mahasiswa', 'proposal'])
                ->whereHas('proposal', fn($q) => $q->where('pembimbing_id', $user->id))
                ->where('status_verifikasi', 'menunggu')
                ->latest('tanggal')->paginate(15);
            return view('log-harian.pembimbing.index', compact('logs'));
        }

        abort(403);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $proposal = Proposal::where('mahasiswa_id', $user->id)->where('status', 'diterima')->firstOrFail();

        $validated = $request->validate([
            'tanggal'       => 'required|date|before_or_equal:today',
            'aktivitas'     => 'required|string|min:20',
            'hasil'         => 'required|string|min:10',
            'kendala'       => 'nullable|string',
            'rencana_besok' => 'nullable|string',
        ]);

        $existing = LogHarian::where('mahasiswa_id', $user->id)
            ->where('tanggal', $validated['tanggal'])->first();

        if ($existing) {
            return back()->withErrors(['tanggal' => 'Log harian untuk tanggal ini sudah dibuat.']);
        }

        LogHarian::create([
            ...$validated,
            'proposal_id'  => $proposal->id,
            'mahasiswa_id' => $user->id,
        ]);

        return back()->with('success', 'Log harian berhasil disimpan!');
    }

    public function verifikasi(Request $request, LogHarian $logHarian)
    {
        $request->validate([
            'action'             => 'required|in:verifikasi,revisi',
            'feedback_pembimbing'=> 'nullable|string|max:1000',
        ]);

        $logHarian->update([
            'status_verifikasi'   => $request->action === 'verifikasi' ? 'diverifikasi' : 'direvisi',
            'diverifikasi_oleh'   => auth()->id(),
            'diverifikasi_at'     => now(),
            'feedback_pembimbing' => $request->feedback_pembimbing,
        ]);

        return back()->with('success', 'Log harian berhasil diproses.');
    }
}
