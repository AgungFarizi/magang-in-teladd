<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmailVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek env APP_ENV — jika local/development, skip verifikasi email
        if (app()->environment('local', 'development')) {
            return $next($request);
        }

        if ($request->user() && !$request->user()->hasVerifiedEmail()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Email belum diverifikasi.'], 403);
            }
            return redirect()->route('verification.notice')
                ->with('warning', 'Silakan verifikasi email Anda terlebih dahulu.');
        }

        return $next($request);
    }
}