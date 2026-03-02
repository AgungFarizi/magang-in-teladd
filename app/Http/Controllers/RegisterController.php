<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AksesTokenAdmin;
use App\Models\EmailVerification;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showMahasiswa()
    {
        return view('auth.register-mahasiswa');
    }

    public function showAdmin()
    {
        return view('auth.register-admin');
    }

    public function registerMahasiswa(Request $request)
    {
        $data = $request->validate([
            'nama'         => 'required|string|max:255',
            'email'        => 'required|email|unique:pengguna,email',
            'nim'          => 'required|string|max:50|unique:pengguna,nim',
            'jurusan'      => 'required|string|max:100',
            'universitas'  => 'required|string|max:150',
            'no_hp'        => 'required|string|max:20',
            'password'     => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Pengguna::create([
            ...$data,
            'role'     => 'mahasiswa',
            'password' => Hash::make($data['password']),
        ]);

        $this->sendVerificationEmail($user);

        return redirect()->route('verification.notice')
            ->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi.');
    }

    public function registerAdmin(Request $request)
    {
        $data = $request->validate([
            'nama'         => 'required|string|max:255',
            'email'        => 'required|email|unique:pengguna,email',
            'token'        => 'required|string',
            'no_hp'        => 'nullable|string|max:20',
            'password'     => ['required', 'confirmed', Password::min(8)],
        ]);

        $aksesToken = AksesTokenAdmin::where('token', $data['token'])->first();

        if (!$aksesToken || !$aksesToken->isValid()) {
            return back()->withErrors(['token' => 'Token tidak valid atau sudah digunakan.'])->withInput();
        }

        $user = Pengguna::create([
            'nama'     => $data['nama'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'no_hp'    => $data['no_hp'] ?? null,
            'role'     => $aksesToken->role,
            'divisi'   => $aksesToken->divisi,
        ]);

        $aksesToken->update([
            'is_used'  => true,
            'used_by'  => $user->id,
            'used_at'  => now(),
        ]);

        $this->sendVerificationEmail($user);

        return redirect()->route('verification.notice')
            ->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi.');
    }

    private function sendVerificationEmail(Pengguna $user): void
    {
        $token = Str::random(64);
        \DB::table('email_verifications')->insert([
            'user_id'    => $user->id,
            'token'      => $token,
            'expired_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $verifyUrl = route('verification.verify', ['token' => $token]);

        Mail::send('emails.verify', ['user' => $user, 'verifyUrl' => $verifyUrl], function ($mail) use ($user) {
            $mail->to($user->email)->subject('Verifikasi Email - TELLINTER');
        });
    }
}
