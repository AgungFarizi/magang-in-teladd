<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::where('user_id', auth()->id())->latest()->paginate(20);
        return view('notifikasi.index', compact('notifikasi'));
    }

    public function baca(Notifikasi $notifikasi)
    {
        if ($notifikasi->user_id !== auth()->id()) abort(403);
        $notifikasi->update(['dibaca_at' => now()]);
        return redirect($notifikasi->url ?? route('dashboard'));
    }

    public function bacaSemua()
    {
        Notifikasi::where('user_id', auth()->id())->whereNull('dibaca_at')->update(['dibaca_at' => now()]);
        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
