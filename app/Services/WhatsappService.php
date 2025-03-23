<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    public static function sendOtp($phone, $otp)
    {
        // Format nomor telepon ke internasional (628xxxx)
        $formattedPhone = preg_replace('/^0/', '62', $phone);

        // Pesan OTP
        $message = urlencode("Kode OTP kamu adalah $otp. Berlaku selama 5 menit.");

        // Buat URL
        $url = env('WABLAS_ENDPOINT') .
               '?token=' . env('WABLAS_TOKEN') . '.' . env('WABLAS_SECRET') .
               '&phone=' . $formattedPhone .
               '&message=' . $message;

        // Kirim GET request
        try {
            $response = Http::get($url);
            return $response->json();
        } catch (\Exception $e) {
            logger()->error('Gagal kirim OTP ke WhatsApp: ' . $e->getMessage());
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }
}
