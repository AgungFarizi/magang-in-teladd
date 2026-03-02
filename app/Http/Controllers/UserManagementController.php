<?php

namespace App\Http\Controllers;

use App\Models\AksesTokenAdmin;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function indexOperator()
    {
        $this->authorize_manager();
        $operators = Pengguna::where('role', 'operator')->latest()->paginate(15);
        return view('admin.operator.index', compact('operators'));
    }

    public function indexPembimbing()
    {
        $user = auth()->user();
        if ($user->isManager()) {
            $pembimbing = Pengguna::where('role', 'pembimbing')->latest()->paginate(15);
        } elseif ($user->isManagerDepartemen()) {
            $pembimbing = Pengguna::where('role', 'pembimbing')->where('divisi', $user->divisi)->latest()->paginate(15);
        } else {
            abort(403);
        }
        return view('admin.pembimbing.index', compact('pembimbing', 'user'));
    }

    public function generateToken(Request $request)
    {
        $user = auth()->user();

        if ($user->isManager()) {
            $allowedRoles = ['manager_departemen', 'operator', 'pembimbing'];
        } elseif ($user->isManagerDepartemen()) {
            $allowedRoles = ['pembimbing'];
        } else {
            abort(403);
        }

        $validated = $request->validate([
            'role'       => 'required|in:' . implode(',', $allowedRoles),
            'divisi'     => 'nullable|string|max:100',
            'deskripsi'  => 'nullable|string|max:255',
            'expired_days'=> 'nullable|integer|min:1|max:30',
        ]);

        $token = AksesTokenAdmin::generate(
            $validated['role'],
            $validated['divisi'] ?? null,
            $validated['expired_days'] ?? 7,
            $user->id,
        );

        return back()->with('token_generated', $token->token)
            ->with('success', 'Token berhasil dibuat!');
    }

    public function tokenList()
    {
        $user = auth()->user();
        if (!$user->isManager() && !$user->isManagerDepartemen()) abort(403);

        $tokens = AksesTokenAdmin::with(['pembuat', 'pengguna'])
            ->where('dibuat_oleh', $user->id)
            ->latest()->paginate(15);

        return view('admin.token.index', compact('tokens', 'user'));
    }

    public function toggleAktif(Pengguna $pengguna)
    {
        $user = auth()->user();
        if (!$user->isManager() && !($user->isManagerDepartemen() && $pengguna->role === 'pembimbing' && $pengguna->divisi === $user->divisi)) {
            abort(403);
        }

        $pengguna->update(['is_active' => !$pengguna->is_active]);
        $status = $pengguna->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Pengguna berhasil {$status}.");
    }

    public function hapus(Pengguna $pengguna)
    {
        $this->authorize_manager();
        $pengguna->delete();
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    public function indexMahasiswa()
    {
        $user = auth()->user();
        if (!$user->isManager() && !$user->isOperator()) abort(403);

        $mahasiswa = Pengguna::where('role', 'mahasiswa')
            ->with(['proposal' => fn($q) => $q->latest()->limit(1)])
            ->latest()->paginate(20);

        return view('admin.mahasiswa.index', compact('mahasiswa'));
    }

    private function authorize_manager(): void
    {
        if (!auth()->user()->isManager()) abort(403);
    }
}
