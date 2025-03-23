<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\WhatsappService;

class OTPVerificationController extends Controller
{
    public function show($phone)
    {
        $user = User::where('phone', $phone)->firstOrFail();

        if (!$user->otp) {
            return redirect()->route('login')->with('error', 'OTP sudah diverifikasi.');
        }

        return inertia('Auth/VerifyOtp', ['phone' => $phone]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'User tidak ditemukan.']);
        }

        // Jika OTP sudah kosong (sudah dipakai)
        if (!$user->otp) {
            return back()->withErrors(['otp' => 'OTP sudah digunakan atau tidak tersedia.']);
        }

        // Batasi 3 percobaan
        if ($user->otp_attempts >= 3) {
            return back()->withErrors(['otp' => 'Terlalu banyak percobaan. Silakan daftar ulang atau minta bantuan admin.']);
        }

        // OTP tidak cocok
        if ($user->otp !== $request->otp) {
            $user->increment('otp_attempts');
            return back()->withErrors(['otp' => 'OTP tidak cocok.']);
        }

        // OTP expired
        if ($user->otp_expires_at < now()) {
            return back()->withErrors(['otp' => 'OTP sudah kedaluwarsa.']);
        }

        // Sukses: reset OTP dan login
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
            'is_verified' => true,
        ]);

        Auth::login($user);
        return redirect()->route('dashboard');
    }

    public function resend(Request $request)
    {
        $request->validate(['phone' => 'required']);
        
        $user = User::where('phone', $request->phone)->firstOrFail();
        $otp = rand(100000, 999999);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        // logger("OTP RESEND untuk {$user->phone}: $otp");
        WhatsappService::sendOtp($user->phone, $otp);

        return response()->json(['message' => 'OTP baru telah dikirim.']);
    }
}
