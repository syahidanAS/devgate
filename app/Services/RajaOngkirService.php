<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RajaOngkirService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.rajaongkir.key', '');
        $this->baseUrl = config('services.rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1');
    }

    // ──────────────────────────────────────────────────────────────
    // Provinces
    // ──────────────────────────────────────────────────────────────

    public function getProvinces(): array
    {
        if (empty($this->apiKey)) {
            Log::warning('RajaOngkir API Key not configured, using offline fallback.');
            return $this->fallbackProvinces();
        }

        if (Cache::has('rajaongkir_offline_mode')) {
            return $this->fallbackProvinces();
        }

        $cacheKey = 'rajaongkir_provinces';

        return Cache::remember($cacheKey, 86400, function () {
            try {
                $response = Http::withHeaders(['Key' => $this->apiKey])
                    ->timeout(4)
                    ->get($this->baseUrl . '/destination/province');

                if ($response->successful()) {
                    $data = $response->json('data') ?? [];
                    if (!empty($data)) {
                        $results = collect($data)->map(fn($item) => [
                            'province_id' => (string) $item['id'],
                            'province'    => Str::title(strtolower($item['name'])),
                        ])->sortBy('province')->values()->all();

                        return $results;
                    }
                }

                Log::error('RajaOngkir Province Fetch Failed: ' . ($response->json('meta.message') ?? 'Status ' . $response->status()));
            } catch (\Exception $e) {
                Log::error('RajaOngkir Province Fetch Error: ' . $e->getMessage());
                Cache::put('rajaongkir_offline_mode', true, 600);
            }

            return $this->fallbackProvinces();
        });
    }

    // ──────────────────────────────────────────────────────────────
    // Cities
    // ──────────────────────────────────────────────────────────────

    public function getCities(string $provinceId): array
    {
        if (empty($this->apiKey) || Cache::has('rajaongkir_offline_mode')) {
            return $this->fallbackCities($provinceId);
        }

        $cacheKey = 'rajaongkir_cities_' . $provinceId;

        return Cache::remember($cacheKey, 86400, function () use ($provinceId) {
            try {
                $response = Http::withHeaders(['Key' => $this->apiKey])
                    ->timeout(4)
                    ->get($this->baseUrl . '/destination/city/' . $provinceId);

                if ($response->successful()) {
                    $data = $response->json('data') ?? [];
                    if (!empty($data)) {
                        return collect($data)->map(fn($item) => [
                            'city_id'   => (string) $item['id'],
                            'city_name' => Str::title(strtolower($item['name'])),
                        ])->sortBy('city_name')->values()->all();
                    }
                }

                Log::error('RajaOngkir City Fetch Failed: ' . ($response->json('meta.message') ?? 'Status ' . $response->status()));
            } catch (\Exception $e) {
                Log::error('RajaOngkir City Fetch Error: ' . $e->getMessage());
                Cache::put('rajaongkir_offline_mode', true, 600);
            }

            return $this->fallbackCities($provinceId);
        });
    }

    // ──────────────────────────────────────────────────────────────
    // Offline Fallback Data
    // ──────────────────────────────────────────────────────────────

    public function fallbackProvinces(): array
    {
        return [
            ['province_id' => '9',  'province' => 'Aceh'],
            ['province_id' => '15', 'province' => 'Bali'],
            ['province_id' => '24', 'province' => 'Bangka Belitung'],
            ['province_id' => '11', 'province' => 'Banten'],
            ['province_id' => '6',  'province' => 'Bengkulu'],
            ['province_id' => '19', 'province' => 'DI Yogyakarta'],
            ['province_id' => '10', 'province' => 'DKI Jakarta'],
            ['province_id' => '17', 'province' => 'Gorontalo'],
            ['province_id' => '13', 'province' => 'Jambi'],
            ['province_id' => '5',  'province' => 'Jawa Barat'],
            ['province_id' => '12', 'province' => 'Jawa Tengah'],
            ['province_id' => '18', 'province' => 'Jawa Timur'],
            ['province_id' => '28', 'province' => 'Kalimantan Barat'],
            ['province_id' => '3',  'province' => 'Kalimantan Selatan'],
            ['province_id' => '4',  'province' => 'Kalimantan Tengah'],
            ['province_id' => '7',  'province' => 'Kalimantan Timur'],
            ['province_id' => '31', 'province' => 'Kalimantan Utara'],
            ['province_id' => '8',  'province' => 'Kepulauan Riau'],
            ['province_id' => '30', 'province' => 'Lampung'],
            ['province_id' => '2',  'province' => 'Maluku'],
            ['province_id' => '32', 'province' => 'Maluku Utara'],
            ['province_id' => '1',  'province' => 'Nusa Tenggara Barat'],
            ['province_id' => '21', 'province' => 'Nusa Tenggara Timur'],
            ['province_id' => '14', 'province' => 'Papua'],
            ['province_id' => '29', 'province' => 'Papua Barat'],
            ['province_id' => '25', 'province' => 'Riau'],
            ['province_id' => '33', 'province' => 'Sulawesi Selatan'],
            ['province_id' => '27', 'province' => 'Sulawesi Tengah'],
            ['province_id' => '20', 'province' => 'Sulawesi Tenggara'],
            ['province_id' => '34', 'province' => 'Sulawesi Barat'],
            ['province_id' => '22', 'province' => 'Sulawesi Utara'],
            ['province_id' => '23', 'province' => 'Sumatera Barat'],
            ['province_id' => '26', 'province' => 'Sumatera Selatan'],
            ['province_id' => '16', 'province' => 'Sumatera Utara'],
        ];
    }

    public function fallbackCities(string $provinceId): array
    {
        return match ($provinceId) {
            '10' => [
                ['city_id' => '135', 'city_name' => 'Jakarta Barat'],
                ['city_id' => '136', 'city_name' => 'Jakarta Selatan'],
                ['city_id' => '137', 'city_name' => 'Jakarta Pusat'],
                ['city_id' => '138', 'city_name' => 'Jakarta Utara'],
                ['city_id' => '139', 'city_name' => 'Jakarta Timur'],
            ],
            '11' => [
                ['city_id' => '143', 'city_name' => 'Cilegon'],
                ['city_id' => '148', 'city_name' => 'Serang'],
                ['city_id' => '592', 'city_name' => 'Tangerang'],
                ['city_id' => '594', 'city_name' => 'Tangerang Selatan'],
            ],
            '5'  => [
                ['city_id' => '23',  'city_name' => 'Bandung'],
                ['city_id' => '54',  'city_name' => 'Bekasi'],
                ['city_id' => '78',  'city_name' => 'Bogor'],
                ['city_id' => '115', 'city_name' => 'Depok'],
            ],
            '12' => [
                ['city_id' => '399', 'city_name' => 'Semarang'],
                ['city_id' => '445', 'city_name' => 'Surakarta'],
            ],
            '18' => [
                ['city_id' => '256', 'city_name' => 'Malang'],
                ['city_id' => '444', 'city_name' => 'Surabaya'],
            ],
            '19' => [
                ['city_id' => '39',  'city_name' => 'Bantul'],
                ['city_id' => '419', 'city_name' => 'Sleman'],
                ['city_id' => '501', 'city_name' => 'Yogyakarta'],
            ],
            '15' => [
                ['city_id' => '17',  'city_name' => 'Badung'],
                ['city_id' => '114', 'city_name' => 'Denpasar'],
                ['city_id' => '128', 'city_name' => 'Gianyar'],
            ],
            default => [
                ['city_id' => '137', 'city_name' => 'Jakarta Pusat'],
            ],
        };
    }
}
