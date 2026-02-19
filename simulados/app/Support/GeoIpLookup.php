<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeoIpLookup
{
    /**
     * @return array{country:?string,country_code:?string,state:?string,city:?string,neighborhood:?string,latitude:?float,longitude:?float}
     */
    public function lookup(?string $ip): array
    {
        $normalizedIp = trim((string) $ip);

        if (!$this->isPublicIp($normalizedIp)) {
            return $this->emptyResult();
        }

        return Cache::remember("geoip:{$normalizedIp}", now()->addHours(12), function () use ($normalizedIp): array {
            try {
                $response = Http::timeout(2)
                    ->acceptJson()
                    ->get("https://ipwho.is/{$normalizedIp}");

                if (!$response->ok()) {
                    return $this->emptyResult();
                }

                $data = $response->json();
                if (!is_array($data) || !($data['success'] ?? false)) {
                    return $this->emptyResult();
                }

                return [
                    'country' => $this->nullableString($data['country'] ?? null),
                    'country_code' => $this->nullableCountryCode($data['country_code'] ?? null),
                    'state' => $this->nullableString($data['region'] ?? null),
                    'city' => $this->nullableString($data['city'] ?? null),
                    'neighborhood' => $this->nullableString($data['district'] ?? null),
                    'latitude' => $this->nullableFloat($data['latitude'] ?? null),
                    'longitude' => $this->nullableFloat($data['longitude'] ?? null),
                ];
            } catch (\Throwable) {
                return $this->emptyResult();
            }
        });
    }

    private function isPublicIp(string $ip): bool
    {
        if ($ip === '') {
            return false;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    private function nullableString(mixed $value): ?string
    {
        $text = trim((string) $value);
        return $text !== '' ? $text : null;
    }

    private function nullableFloat(mixed $value): ?float
    {
        if (!is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    /**
     * @return array{country:?string,country_code:?string,state:?string,city:?string,neighborhood:?string,latitude:?float,longitude:?float}
     */
    private function emptyResult(): array
    {
        return [
            'country' => null,
            'country_code' => null,
            'state' => null,
            'city' => null,
            'neighborhood' => null,
            'latitude' => null,
            'longitude' => null,
        ];
    }

    private function nullableCountryCode(mixed $value): ?string
    {
        $code = strtoupper(preg_replace('/[^a-z]/i', '', (string) $value));
        if (strlen($code) !== 2) {
            return null;
        }

        return $code;
    }
}
