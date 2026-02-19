<?php

namespace App\Http\Controllers;

use App\Models\RouteMetric;
use App\Models\UserMetricConsent;
use App\Support\GeoIpLookup;
use App\Support\UserAgentDetails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MetricsConsentController extends Controller
{
    public const CONSENT_COOKIE_NAME = 'lgpd_metrics_consent';
    public const CONSENT_COOKIE_VALUE = 'granted';
    private const CONSENT_COOKIE_MINUTES = 525600; // 365 dias

    public function grant(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && Schema::hasTable('user_metric_consents')) {
            UserMetricConsent::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'is_granted' => true,
                    'granted_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );
        }

        $response = response()->json([
            'ok' => true,
            'consent_granted' => true,
        ]);

        if (!$user) {
            $response->cookie(
                self::CONSENT_COOKIE_NAME,
                self::CONSENT_COOKIE_VALUE,
                self::CONSENT_COOKIE_MINUTES,
                '/',
                null,
                $request->isSecure(),
                true,
                false,
                'Lax'
            );
        }

        return $response;
    }

    public function storeMetric(Request $request, GeoIpLookup $geoIpLookup): JsonResponse
    {
        if (!$this->hasConsent($request)) {
            return response()->json([
                'ok' => false,
                'message' => 'Consentimento LGPD ausente para coleta.',
            ], 403);
        }

        if (!Schema::hasTable('route_metrics')) {
            return response()->json([
                'ok' => false,
                'message' => 'Tabela de metricas indisponivel.',
            ], 503);
        }

        $payload = $request->validate([
            'route_name' => ['nullable', 'string', 'max:255'],
            'page_url' => ['required', 'string', 'max:5000'],
            'path' => ['nullable', 'string', 'max:5000'],
            'referrer' => ['nullable', 'string', 'max:5000'],
            'timezone' => ['nullable', 'string', 'max:80'],
            'language' => ['nullable', 'string', 'max:32'],
            'viewport_width' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'viewport_height' => ['nullable', 'integer', 'min:0', 'max:10000'],
        ]);

        $user = $request->user();
        $userAgent = (string) $request->userAgent();
        $ipAddress = (string) $request->ip();
        $uaDetails = UserAgentDetails::parse($userAgent);
        $geoDetails = $geoIpLookup->lookup($ipAddress);

        RouteMetric::query()->create([
            'user_id' => $user?->id,
            'consent_mode' => $user ? 'user' : 'cookie',
            'route_name' => $payload['route_name'] ?? null,
            'page_url' => $payload['page_url'],
            'path' => $payload['path'] ?? null,
            'referrer' => $payload['referrer'] ?? null,
            'ip_address' => $ipAddress !== '' ? $ipAddress : null,
            'user_agent' => $userAgent !== '' ? $userAgent : null,
            'browser' => $uaDetails['browser'],
            'browser_version' => $uaDetails['browser_version'],
            'device_type' => $uaDetails['device_type'],
            'operating_system' => $uaDetails['operating_system'],
            'country' => $geoDetails['country'],
            'state' => $geoDetails['state'],
            'city' => $geoDetails['city'],
            'neighborhood' => $geoDetails['neighborhood'],
            'latitude' => $geoDetails['latitude'],
            'longitude' => $geoDetails['longitude'],
            'timezone' => $payload['timezone'] ?? null,
            'language' => $payload['language'] ?? null,
            'viewport_width' => $payload['viewport_width'] ?? null,
            'viewport_height' => $payload['viewport_height'] ?? null,
            'captured_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
        ]);
    }

    private function hasConsent(Request $request): bool
    {
        $user = $request->user();
        if ($user) {
            if (!Schema::hasTable('user_metric_consents')) {
                return false;
            }

            return UserMetricConsent::query()
                ->where('user_id', $user->id)
                ->where('is_granted', true)
                ->exists();
        }

        return $request->cookie(self::CONSENT_COOKIE_NAME) === self::CONSENT_COOKIE_VALUE;
    }
}
