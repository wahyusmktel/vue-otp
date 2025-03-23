<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OtpVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->is_verified) {
            return redirect()->route('verification.show', [
                'phone' => $request->user()->phone,
            ])->withErrors([
                'otp' => 'Silakan verifikasi OTP terlebih dahulu.',
            ]);
        }

        return $next($request);
    }
}
