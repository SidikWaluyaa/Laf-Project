<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    private string $token;
    private string $endpoint = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token', env('FONNTE_TOKEN', ''));
    }

    /**
     * Mengirim pesan WhatsApp via Fonnte
     *
     * @param string $target Nomor tujuan (contoh: 081234567890)
     * @param string $message Isi pesan
     * @return bool True jika berhasil dikirim atau masuk antrean
     */
    public function sendMessage(string $target, string $message): bool
    {
        if (empty($this->token) || empty($target)) {
            Log::warning('Fonnte sendMessage cancelled: Token or Target is empty', [
                'has_token' => !empty($this->token),
                'target' => $target
            ]);
            return false;
        }

        try {
            // Menggunakan timeout singkat (3 detik) agar jika server Fonnte down, 
            // aplikasi kita tidak ikut hank / loading terlalu lama
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->timeout(3)->post($this->endpoint, [
                'target' => $target,
                'message' => $message,
                'delay' => '2', // Delay 2 detik standar Fonnte
                'countryCode' => '62', // Default Indonesia
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['status']) && $responseData['status'] === true) {
                    return true;
                }
                
                Log::error('Fonnte API Error', ['response' => $responseData]);
                return false;
            }

            Log::error('Fonnte HTTP Error', ['status' => $response->status(), 'body' => $response->body()]);
            return false;

        } catch (\Exception $e) {
            Log::error('Fonnte Exception: ' . $e->getMessage());
            return false;
        }
    }
}
