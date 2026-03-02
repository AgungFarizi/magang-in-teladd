<?php

namespace App\Http\Controllers;

use App\Models\PeriodeMagang;
use Illuminate\Http\Request;

class PeriodeMagangController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $periode = PeriodeMagang::with('pembuat')->latest()->paginate(10);
        return view('periode.index', compact('periode', 'user'));
    }

    public function create()
    {
        $this->authorize_operator_or_manager();
        return view('periode.create');
    }

    public function store(Request $request)
    {
        $this->authorize_operator_or_manager();
        $validated = $request->validate([
            'nama'                    => 'required|string|max:255',
            'deskripsi'               => 'nullable|string',
            'tanggal_mulai_daftar'    => 'required|date',
            'tanggal_selesai_daftar'  => 'required|date|after:tanggal_mulai_daftar',
            'tanggal_mulai_magang'    => 'required|date|after_or_equal:tanggal_selesai_daftar',
            'tanggal_selesai_magang'  => 'required|date|after:tanggal_mulai_magang',
            'kuota'                   => 'required|integer|min:1',
        ]);

        PeriodeMagang::create([...$validated, 'status' => 'draft', 'dibuat_oleh' => auth()->id()]);

        return redirect()->route('periode.index')->with('success', 'Periode magang berhasil dibuat!');
    }

    public function edit(PeriodeMagang $periode)
    {
        $this->authorize_operator_or_manager();
        return view('periode.edit', compact('periode'));
    }

    public function update(Request $request, PeriodeMagang $periode)
    {
        $this->authorize_operator_or_manager();
        $validated = $request->validate([
            'nama'                    => 'required|string|max:255',
            'deskripsi'               => 'nullable|string',
            'tanggal_mulai_daftar'    => 'required|date',
            'tanggal_selesai_daftar'  => 'required|date|after:tanggal_mulai_daftar',
            'tanggal_mulai_magang'    => 'required|date|after_or_equal:tanggal_selesai_daftar',
            'tanggal_selesai_magang'  => 'required|date|after:tanggal_mulai_magang',
            'kuota'                   => 'required|integer|min:1',
            'status'                  => 'required|in:draft,aktif,ditutup,selesai',
        ]);

        $periode->update($validated);
        return redirect()->route('periode.index')->with('success', 'Periode magang berhasil diperbarui!');
    }

    public function toggleStatus(PeriodeMagang $periode)
    {
        $this->authorize_operator_or_manager();
        $newStatus = $periode->status === 'aktif' ? 'ditutup' : 'aktif';
        $periode->update(['status' => $newStatus]);
        return back()->with('success', "Periode magang berhasil " . ($newStatus === 'aktif' ? 'dibuka' : 'ditutup') . ".");
    }

    private function authorize_operator_or_manager(): void
    {
        $user = auth()->user();
        if (!$user->isOperator() && !$user->isManager()) abort(403);
    }
}
