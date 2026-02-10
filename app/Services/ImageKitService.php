<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageKitService
{
    /**
     * Upload Base64 Image ke ImageKit via HTTP Request
     * Fix: Bypass SSL Verification untuk mengatasi cURL Error 60
     */
    public static function uploadBase64($base64Data, $fileName, $folder = '/content')
    {
        // 1. Validasi: Jika data kosong atau bukan base64 (misal URL lama), kembalikan aslinya
        if (empty($base64Data) || !str_contains($base64Data, 'base64')) {
            return $base64Data;
        }

        // 2. Ambil Private Key
        $privateKey = config('services.imagekit.private_key');
        if (empty($privateKey)) {
            Log::error('ImageKit Error: Private Key belum diatur di .env');
            return null;
        }

        try {
            // 3. Tembak API ImageKit (Bypass SSL dengan withoutVerifying)
            $response = Http::withoutVerifying() // <--- INI KUNCI FIX ERROR 60 NYA
                ->withBasicAuth($privateKey, '') // Auth Basic (Username=Key, Password=Kosong)
                ->asForm() // Kirim sebagai Form Data
                ->post('https://upload.imagekit.io/api/v1/files/upload', [
                    'file' => $base64Data,       // String Base64
                    'fileName' => $fileName,     // Nama file
                    'folder' => $folder,         // Folder tujuan
                    'useUniqueFileName' => 'true'
                ]);

            // 4. Cek Hasil
            if ($response->successful()) {
                $result = $response->json();
                return $result['url'] ?? null;
            }

            // Log Error Detail dari ImageKit
            Log::error('ImageKit API Failed: ' . $response->body() . ' | Status: ' . $response->status());
            return null;

        } catch (\Exception $e) {
            Log::error('ImageKit Connection Error: ' . $e->getMessage());
            return null;
        }
    }
}