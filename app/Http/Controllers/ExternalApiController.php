<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalApiController extends Controller
{
    private const BASE_URL = 'https://api.teknokrat.ac.id';

    /**
     * Ambil semua data prodi dari API eksternal
     */
    public static function getProdi()
    {
        return self::request('/api/prodi');
    }

    /**
     * Ambil data mahasiswa dengan parameter query dinamis.
     * Contoh: ['angkatan' => '2021'].
     */
    public static function getMahasiswa(array $query = [])
    {
        return self::request('/api/mahasiswa', $query);
    }

    /**
     * Backward compatibility: ambil data mahasiswa berdasarkan angkatan.
     */
    public static function getMahasiswaByAngkatan($angkatan)
    {
        return self::getMahasiswa([
            'angkatan' => $angkatan,
        ]);
    }

    /**
     * Request helper untuk endpoint API Teknokrat.
     */
    private static function request(string $path, array $query = [])
    {
        $url = self::BASE_URL . $path;

        try {
            $httpClient = Http::acceptJson()
                ->timeout(60)
                ->retry(2, 500);

            try {
                $response = $httpClient->get($url, $query);
            } catch (\Throwable $e) {
                $message = strtolower($e->getMessage());
                $hasTlsIssue = str_contains($message, 'curl error 77')
                    || str_contains($message, 'ssl')
                    || str_contains($message, 'certificate');

                if (!$hasTlsIssue) {
                    throw $e;
                }

                // Fallback untuk environment lokal yang bermasalah di CA bundle.
                $response = $httpClient
                    ->withoutVerifying()
                    ->get($url, $query);
            }

            if ($response->failed()) {
                Log::warning('External API request failed', [
                    'url' => $url,
                    'query' => $query,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $json = $response->object();
            $data = $json->data ?? [];

            return is_array($data) ? $data : [];
        } catch (\Throwable $e) {
            Log::error('External API request error', [
                'path' => $path,
                'query' => $query,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
