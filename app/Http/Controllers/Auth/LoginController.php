<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
            ]);
        }

        // if (!$user->hasVerifiedEmail()) {
        //     Auth::logout();
        //     return redirect()->route('verification.notice')
        //         ->with('warning', 'Silakan verifikasi email Anda terlebih dahulu.')
        //         ->with('email', $credentials['email']);
        // }

        $request->session()->regenerate();

        return $this->redirectToDashboard($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }

    protected function redirectToDashboard($user)
    {
        $route = match($user->role) {
            'manager' => 'manager.dashboard',
            'manager_departemen' => 'manager-dep.dashboard',
            'operator' => 'operator.dashboard',
            'pembimbing_lapang' => 'pembimbing.dashboard',
            'mahasiswa' => 'mahasiswa.dashboard',
            default => 'login',
        };

        return redirect()->route($route)->with('success', "Selamat datang, {$user->nama_lengkap}!");
    }
}
