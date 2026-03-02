<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerificationController extends Controller
{
    public function notice()
    {
        return view('auth.verify-email');
    }

    public function verify(Request $request, string $token)
    {
        $verification = DB::table('email_verifications')
            ->where('token', $token)
            ->first();

        if (!$verification) {
            return redirect()->route('login')->withErrors(['email' => 'Token verifikasi tidak valid.']);
        }

        if (now()->gt($verification->expired_at)) {
            DB::table('email_verifications')->where('token', $token)->delete();
            return redirect()->route('verification.notice')
                ->withErrors(['token' => 'Token verifikasi sudah kedaluwarsa. Silakan minta token baru.']);
        }

        $user = Pengguna::find($verification->user_id);
        $user->update(['email_verified_at' => now()]);
        DB::table('email_verifications')->where('token', $token)->delete();

        return redirect()->route('login')
            ->with('success', 'Email berhasil diverifikasi! Silakan login.');
    }

    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = Pengguna::where('email', $request->email)->first();

        if (!$user || $user->email_verified_at) {
            return back()->with('info', 'Email tidak ditemukan atau sudah diverifikasi.');
        }

        DB::table('email_verifications')->where('user_id', $user->id)->delete();
        app(RegisterController::class)->sendVerificationEmailPublic($user);

        return back()->with('success', 'Email verifikasi telah dikirim ulang.');
    }
}
