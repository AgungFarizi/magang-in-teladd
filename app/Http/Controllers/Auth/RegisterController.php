<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerification;
use App\Models\Notifikasi;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function showPilihan()
    {
        return view('auth.pilihan-register');
    }

    public function showFormMahasiswa()
    {
        return view('auth.register-mahasiswa');
    }

    public function showFormAdmin()
    {
        return view('auth.register-admin');
    }

    // ─── Registrasi Mahasiswa ────────────────────────────────────────
    public function registerMahasiswa(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nim'          => 'required|string|max:20|unique:pengguna,nim',
            'email'        => 'required|email|unique:pengguna,email',
            'no_telepon'   => 'nullable|string|max:20',
            'institusi'    => 'required|string|max:255',
            'jurusan'      => 'required|string|max:255',
            'password'     => 'required|confirmed|min:8',
        ], [
            'nim.unique'         => 'NIM sudah terdaftar.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        $pengguna = Pengguna::create([
            'nama_lengkap'      => $request->nama_lengkap,
            'nim'               => $request->nim,
            'email'             => $request->email,
            'no_telepon'        => $request->no_telepon,
            'institusi'         => $request->institusi,
            'jurusan'           => $request->jurusan,
            'password'          => Hash::make($request->password),
            'role'              => 'mahasiswa',
            'is_active'         => true,
            // Di local: langsung verified. Di production: null (perlu verif email)
            'email_verified_at' => app()->environment('local', 'development') ? now() : null,
        ]);

        if (app()->environment('local', 'development')) {
            // Langsung login tanpa verif email
            Auth::login($pengguna);
            return redirect()->route('mahasiswa.dashboard')
                ->with('success', 'Registrasi berhasil! Selamat datang, ' . $pengguna->nama_lengkap . '.');
        }

        // Production: kirim email verifikasi
        $this->kirimEmailVerifikasi($pengguna);
        return redirect()->route('verification.notice')
            ->with('success', 'Registrasi berhasil! Silakan cek email untuk verifikasi.');
    }

    // ─── Registrasi Admin/Staf ───────────────────────────────────────
    public function registerAdmin(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|unique:pengguna,email',
            'no_telepon'   => 'nullable|string|max:20',
            'password'     => 'required|confirmed|min:8',
            'token_admin'  => 'required|string',
        ], [
            'email.unique'         => 'Email sudah terdaftar.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.',
            'password.min'         => 'Password minimal 8 karakter.',
            'token_admin.required' => 'Token admin wajib diisi.',
        ]);

        // Cek token langsung via DB (hindari bug Eloquent boolean casting)
        $tokenRecord = DB::table('akses_token_admin')
            ->where('token', strtoupper(trim($request->token_admin)))
            ->where('is_active', 1)
            ->where('is_used', 0)
            ->first();

        if (!$tokenRecord) {
            return back()
                ->withErrors(['token_admin' => 'Token tidak valid atau sudah pernah digunakan.'])
                ->withInput();
        }

        $pengguna = Pengguna::create([
            'nama_lengkap'      => $request->nama_lengkap,
            'email'             => $request->email,
            'no_telepon'        => $request->no_telepon,
            'password'          => Hash::make($request->password),
            'role'              => $tokenRecord->untuk_role,
            'divisi'            => $tokenRecord->divisi,
            'is_active'         => true,
            // Di local: langsung verified
            'email_verified_at' => app()->environment('local', 'development') ? now() : null,
        ]);

        // Tandai token sudah terpakai
        DB::table('akses_token_admin')->where('id', $tokenRecord->id)->update([
            'is_used'        => 1,
            'digunakan_oleh' => $pengguna->id,
            'digunakan_pada' => now(),
            'updated_at'     => now(),
        ]);

        if (app()->environment('local', 'development')) {
            // Langsung login
            Auth::login($pengguna);
            $dashboardRoute = $this->getDashboardRoute($pengguna->role);
            return redirect()->route($dashboardRoute)
                ->with('success', 'Registrasi berhasil! Selamat datang, ' . $pengguna->nama_lengkap . '.');
        }

        // Production: kirim email verifikasi
        $this->kirimEmailVerifikasi($pengguna);
        $roleLabel = ucwords(str_replace('_', ' ', $tokenRecord->untuk_role));
        return redirect()->route('verification.notice')
            ->with('success', "Registrasi sebagai {$roleLabel} berhasil! Silakan verifikasi email.");
    }

    // ─── Verifikasi Email ────────────────────────────────────────────
    public function showVerificationNotice()
    {
        return view('auth.verify-email');
    }

    public function verifyEmail(Request $request, string $token)
    {
        $verification = EmailVerification::where('token', $token)->first();

        if (!$verification || !$verification->isValid()) {
            return redirect()->route('login')
                ->with('error', 'Link verifikasi tidak valid atau sudah kadaluarsa.');
        }

        $pengguna = Pengguna::where('email', $verification->email)->first();

        if (!$pengguna) {
            return redirect()->route('login')->with('error', 'Pengguna tidak ditemukan.');
        }

        $pengguna->update(['email_verified_at' => now()]);
        $verification->update(['is_used' => true]);

        Notifikasi::kirim(
            $pengguna->id,
            'Selamat Datang di TELLINTER!',
            'Akun Anda telah aktif. Selamat menggunakan platform.',
            'sistem'
        );

        Auth::login($pengguna);

        return redirect()->route($this->getDashboardRoute($pengguna->role))
            ->with('success', 'Email berhasil diverifikasi! Selamat datang.');
    }

    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $pengguna = Pengguna::where('email', $request->email)->whereNull('email_verified_at')->first();

        if (!$pengguna) {
            return back()->with('info', 'Email tidak ditemukan atau sudah terverifikasi.');
        }

        $this->kirimEmailVerifikasi($pengguna);
        return back()->with('success', 'Email verifikasi telah dikirim ulang.');
    }

    // ─── Helper ──────────────────────────────────────────────────────
    protected function kirimEmailVerifikasi(Pengguna $pengguna): void
    {
        $verification = EmailVerification::buat($pengguna->email);
        try {
            Mail::to($pengguna->email)->send(
                new \App\Mail\EmailVerifikasiMail($pengguna, $verification->token)
            );
        } catch (\Exception $e) {
            \Log::error('Gagal kirim email verifikasi: ' . $e->getMessage());
        }
    }

    protected function getDashboardRoute(string $role): string
    {
        return match($role) {
            'manager'            => 'manager.dashboard',
            'manager_departemen' => 'manager-dep.dashboard',
            'operator'           => 'operator.dashboard',
            'pembimbing_lapang'  => 'pembimbing.dashboard',
            'mahasiswa'          => 'mahasiswa.dashboard',
            default              => 'login',
        };
    }
}