<?php

namespace App\Http\Controllers;

use App\Models\PageVisitCounter;
use App\Models\RouteMetric;
use App\Models\User;
use App\Models\UserMetricConsent;
use App\Support\GeoIpLookup;
use App\Support\UserAgentDetails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MetricsConsentController extends Controller
{
    public const CONSENT_COOKIE_NAME = 'lgpd_metrics_consent';
    public const VISITOR_COOKIE_NAME = 'lgpd_metrics_visitor';
    public const CONSENT_COOKIE_VALUE = '1';
    private const CONSENT_COOKIE_MINUTES = 2880; // 48 horas
    private const USER_CONSENT_VALID_DAYS = 7;

    public function grant(Request $request, GeoIpLookup $geoIpLookup): JsonResponse
    {
        if ($this->isAdminUser($request)) {
            return response()->json([
                'ok' => false,
                'message' => 'Coleta de metricas desabilitada para administrador.',
            ], 403);
        }

        $user = $request->user();
        $ipAddress = $request->ip();
        $geoDetails = $geoIpLookup->lookup($ipAddress);

        if ($user && Schema::hasTable('user_metric_consents')) {
            UserMetricConsent::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'is_granted' => true,
                    'granted_at' => now(),
                    'ip_address' => $ipAddress,
                    'user_agent' => $request->userAgent(),
                    'country' => $geoDetails['country'],
                    'country_code' => $geoDetails['country_code'],
                    'state' => $geoDetails['state'],
                    'city' => $geoDetails['city'],
                    'neighborhood' => $geoDetails['neighborhood'],
                    'latitude' => $geoDetails['latitude'],
                    'longitude' => $geoDetails['longitude'],
                ]
            );
        }

        $response = response()->json([
            'ok' => true,
            'consent_granted' => true,
        ]);

        if (!$user) {
            $anonymousVisitorId = $this->resolveAnonymousVisitorId($request);

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

            $response->cookie(
                self::VISITOR_COOKIE_NAME,
                $anonymousVisitorId,
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
        if ($this->isAdminUser($request)) {
            return response()->json([
                'ok' => false,
                'message' => 'Coleta de metricas desabilitada para administrador.',
            ], 403);
        }

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
            'device_model' => ['nullable', 'string', 'max:120'],
        ]);

        $user = $request->user();
        $userAgent = (string) $request->userAgent();
        $ipAddress = (string) $request->ip();
        $anonymousId = $user ? null : $this->resolveAnonymousVisitorId($request);
        $visitorKey = $user ? ('user:' . $user->id) : ('anon:' . $anonymousId);
        $uaDetails = UserAgentDetails::parse($userAgent);
        $geoDetails = $geoIpLookup->lookup($ipAddress);
        $deviceModel = trim((string) ($payload['device_model'] ?? '')) ?: $uaDetails['device_model'];
        $capturedAt = now();
        $pagePath = $this->normalizePagePath((string) ($payload['path'] ?? ''), (string) $payload['page_url']);

        RouteMetric::query()->create([
            'user_id' => $user?->id,
            'anonymous_id' => $anonymousId,
            'visitor_key' => $visitorKey,
            'consent_mode' => $user ? 'user' : 'cookie',
            'route_name' => $payload['route_name'] ?? null,
            'page_url' => $payload['page_url'],
            'path' => $pagePath,
            'referrer' => $payload['referrer'] ?? null,
            'ip_address' => $ipAddress !== '' ? $ipAddress : null,
            'user_agent' => $userAgent !== '' ? $userAgent : null,
            'browser' => $uaDetails['browser'],
            'browser_version' => $uaDetails['browser_version'],
            'device_type' => $uaDetails['device_type'],
            'device_model' => $deviceModel,
            'operating_system' => $uaDetails['operating_system'],
            'country' => $geoDetails['country'],
            'country_code' => $geoDetails['country_code'],
            'state' => $geoDetails['state'],
            'city' => $geoDetails['city'],
            'neighborhood' => $geoDetails['neighborhood'],
            'latitude' => $geoDetails['latitude'],
            'longitude' => $geoDetails['longitude'],
            'timezone' => $payload['timezone'] ?? null,
            'language' => $payload['language'] ?? null,
            'viewport_width' => $payload['viewport_width'] ?? null,
            'viewport_height' => $payload['viewport_height'] ?? null,
            'captured_at' => $capturedAt,
        ]);

        $this->upsertPageVisitCounter(
            userId: $user?->id,
            anonymousId: $anonymousId,
            visitorKey: $visitorKey,
            routeName: $payload['route_name'] ?? null,
            pagePath: $pagePath,
            country: $geoDetails['country'],
            countryCode: $geoDetails['country_code'],
            state: $geoDetails['state'],
            city: $geoDetails['city'],
            capturedAt: $capturedAt
        );

        $response = response()->json([
            'ok' => true,
        ]);

        if (!$user && !$request->hasCookie(self::VISITOR_COOKIE_NAME)) {
            $response->cookie(
                self::VISITOR_COOKIE_NAME,
                $anonymousId,
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
                ->where('granted_at', '>=', now()->subDays(self::USER_CONSENT_VALID_DAYS))
                ->exists();
        }

        return $request->hasCookie(self::CONSENT_COOKIE_NAME);
    }

    private function isAdminUser(Request $request): bool
    {
        return $request->user()?->user_type === User::TYPE_ADM;
    }

    private function resolveAnonymousVisitorId(Request $request): string
    {
        $existing = trim((string) $request->cookie(self::VISITOR_COOKIE_NAME));
        if ($existing !== '') {
            return mb_substr($existing, 0, 64);
        }

        return Str::uuid()->toString();
    }

    private function normalizePagePath(string $path, string $pageUrl): string
    {
        $normalizedPath = trim($path);
        if ($normalizedPath !== '') {
            return mb_substr($normalizedPath, 0, 2048);
        }

        $parsed = parse_url($pageUrl);
        $pathPart = (string) ($parsed['path'] ?? '/');
        $queryPart = (string) ($parsed['query'] ?? '');
        $joined = $queryPart !== '' ? "{$pathPart}?{$queryPart}" : $pathPart;

        return mb_substr($joined !== '' ? $joined : '/', 0, 2048);
    }

    private function normalizeCounterPath(string $pagePath): string
    {
        $basePath = (string) preg_replace('/\?.*$/', '', trim($pagePath));
        $basePath = (string) preg_replace('/\#.*$/', '', $basePath);
        $basePath = trim($basePath);

        if ($basePath === '') {
            $basePath = '/';
        }

        return mb_substr($basePath, 0, 2048);
    }

    private function upsertPageVisitCounter(
        ?int $userId,
        ?string $anonymousId,
        string $visitorKey,
        ?string $routeName,
        string $pagePath,
        ?string $country,
        ?string $countryCode,
        ?string $state,
        ?string $city,
        \DateTimeInterface $capturedAt
    ): void {
        if (!Schema::hasTable('page_visit_counters')) {
            return;
        }

        $countryNorm = $this->normalizeNullableText($country);
        $countryCodeNorm = $this->normalizeCountryCode($countryCode);
        $stateNorm = $this->normalizeNullableText($state);
        $cityNorm = $this->normalizeNullableText($city);
        $routeNameNorm = $this->normalizeNullableText($routeName);
        $counterPath = $this->normalizeCounterPath($pagePath);
        $pageHash = sha1(mb_strtolower(($routeNameNorm ?? '') . '|' . $counterPath));
        $locationHash = sha1(mb_strtolower(($countryNorm ?? '') . '|' . ($stateNorm ?? '') . '|' . ($cityNorm ?? '')));
        $where = [
            'visitor_key' => $visitorKey,
            'page_hash' => $pageHash,
            'location_hash' => $locationHash,
        ];

        $updated = PageVisitCounter::query()
            ->where($where)
            ->update([
                'user_id' => $userId,
                'anonymous_id' => $anonymousId,
                'route_name' => $routeNameNorm,
                'page_path' => $counterPath,
                'country' => $countryNorm,
                'country_code' => $countryCodeNorm,
                'state' => $stateNorm,
                'city' => $cityNorm,
                'visits_count' => DB::raw('visits_count + 1'),
                'last_visited_at' => $capturedAt,
                'updated_at' => now(),
            ]);

        if ($updated > 0) {
            return;
        }

        try {
            PageVisitCounter::query()->create([
                'user_id' => $userId,
                'anonymous_id' => $anonymousId,
                'visitor_key' => $visitorKey,
                'route_name' => $routeNameNorm,
                'page_path' => $counterPath,
                'page_hash' => $pageHash,
                'country' => $countryNorm,
                'country_code' => $countryCodeNorm,
                'state' => $stateNorm,
                'city' => $cityNorm,
                'location_hash' => $locationHash,
                'visits_count' => 1,
                'first_visited_at' => $capturedAt,
                'last_visited_at' => $capturedAt,
            ]);
        } catch (QueryException) {
            PageVisitCounter::query()
                ->where($where)
                ->update([
                    'user_id' => $userId,
                    'anonymous_id' => $anonymousId,
                    'route_name' => $routeNameNorm,
                    'page_path' => $counterPath,
                    'country' => $countryNorm,
                    'country_code' => $countryCodeNorm,
                    'state' => $stateNorm,
                    'city' => $cityNorm,
                    'visits_count' => DB::raw('visits_count + 1'),
                    'last_visited_at' => $capturedAt,
                    'updated_at' => now(),
                ]);
        }
    }

    private function normalizeNullableText(?string $value): ?string
    {
        $text = trim((string) $value);
        return $text !== '' ? mb_substr($text, 0, 120) : null;
    }

    private function normalizeCountryCode(?string $value): ?string
    {
        $code = strtoupper(preg_replace('/[^a-z]/i', '', (string) $value));
        if (strlen($code) !== 2) {
            return null;
        }

        return $code;
    }
}
