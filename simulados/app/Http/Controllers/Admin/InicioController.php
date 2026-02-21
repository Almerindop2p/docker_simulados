<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageVisitCounter;
use App\Models\RouteMetric;
use App\Models\User;
use App\Models\UserMetricConsent;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class InicioController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.inicio.index');
    }

    public function metrics(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        return response()->json($this->buildStats());
    }

    public function details(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        return response()->json($this->buildDetails());
    }

    private function buildStats(): array
    {
        $totalVisualizacoes = (int) PageVisitCounter::query()->sum('visits_count');

        if ($totalVisualizacoes <= 0) {
            $totalVisualizacoes = RouteMetric::query()->count();
        }

        $visualizacoesHoje = RouteMetric::query()
            ->where('captured_at', '>=', now()->startOfDay())
            ->count();

        $visitantesUnicos = (int) (RouteMetric::query()
            ->where('captured_at', '>=', now()->subDay())
            ->selectRaw(
                "COUNT(DISTINCT COALESCE(
                    NULLIF(visitor_key, ''),
                    CASE WHEN user_id IS NOT NULL THEN CONCAT('user:', user_id) END,
                    CASE WHEN anonymous_id IS NOT NULL AND anonymous_id <> '' THEN CONCAT('anon:', anonymous_id) END,
                    CASE WHEN ip_address IS NOT NULL AND ip_address <> '' THEN CONCAT('ip:', ip_address) END
                )) as aggregate"
            )
            ->value('aggregate') ?? 0);

        $paginasMapeadas = PageVisitCounter::query()
            ->whereNotNull('page_path')
            ->where('page_path', '<>', '')
            ->distinct('page_path')
            ->count('page_path');

        if ($paginasMapeadas <= 0) {
            $paginasMapeadas = RouteMetric::query()
                ->whereNotNull('path')
                ->where('path', '<>', '')
                ->distinct('path')
                ->count('path');
        }

        $ultimaCaptura = RouteMetric::query()
            ->latest('captured_at')
            ->first(['captured_at'])
            ?->captured_at;

        $topPagina = PageVisitCounter::query()
            ->selectRaw('page_path, SUM(visits_count) as total_visits')
            ->whereNotNull('page_path')
            ->where('page_path', '<>', '')
            ->groupBy('page_path')
            ->orderByDesc('total_visits')
            ->first();

        $topPais = PageVisitCounter::query()
            ->selectRaw('country, country_code, SUM(visits_count) as total_visits')
            ->whereNotNull('country')
            ->where('country', '<>', '')
            ->groupBy('country', 'country_code')
            ->orderByDesc('total_visits')
            ->first();

        $consentimentosAtivos = 0;
        if (Schema::hasTable('user_metric_consents')) {
            $consentimentosAtivos = UserMetricConsent::query()
                ->where('is_granted', true)
                ->where('granted_at', '>=', now()->subDays(7))
                ->count();
        }

        return [
            'total_visualizacoes' => $totalVisualizacoes,
            'visualizacoes_hoje' => $visualizacoesHoje,
            'visitantes_unicos_24h' => $visitantesUnicos,
            'paginas_mapeadas' => $paginasMapeadas,
            'consentimentos_ativos' => $consentimentosAtivos,
            'ultima_captura' => $ultimaCaptura?->toIso8601String(),
            'top_pagina_path' => $topPagina?->page_path,
            'top_pagina_visitas' => (int) ($topPagina?->total_visits ?? 0),
            'top_pais_nome' => $topPais?->country,
            'top_pais_codigo' => $topPais?->country_code,
            'top_pais_visitas' => (int) ($topPais?->total_visits ?? 0),
            'atualizado_em' => now()->toIso8601String(),
        ];
    }

    private function buildDetails(): array
    {
        $browserDistribution = $this->buildBrowserDistribution();
        $topStatesPie = $this->buildTopStatesViewsDistribution();
        $topCountriesPie = $this->buildTopCountriesViewsDistribution();
        $geoDistribution = $this->buildGeoDistribution();
        $recentAccesses = $this->buildRecentAccesses();

        return [
            'browsers' => $browserDistribution,
            'top_states_pie' => $topStatesPie,
            'top_countries_pie' => $topCountriesPie,
            'countries' => $geoDistribution['countries'],
            'regions' => $geoDistribution['regions'],
            'map_points' => $geoDistribution['map_points'],
            'recent_accesses' => $recentAccesses,
            'atualizado_em' => now()->toIso8601String(),
        ];
    }

    private function buildBrowserDistribution(): array
    {
        $rows = RouteMetric::query()
            ->selectRaw('browser, COUNT(*) as total')
            ->groupBy('browser')
            ->get();

        $buckets = [
            'Chrome' => 0,
            'Firefox' => 0,
            'Opera' => 0,
            'Edge' => 0,
            'Demais' => 0,
        ];

        foreach ($rows as $row) {
            $browser = mb_strtolower(trim((string) $row->browser));
            $total = (int) $row->total;

            if ($browser === '') {
                $buckets['Demais'] += $total;
                continue;
            }

            if (str_contains($browser, 'edge') || str_contains($browser, 'edg')) {
                $buckets['Edge'] += $total;
                continue;
            }

            if (str_contains($browser, 'chrome')) {
                $buckets['Chrome'] += $total;
                continue;
            }

            if (str_contains($browser, 'firefox')) {
                $buckets['Firefox'] += $total;
                continue;
            }

            if (str_contains($browser, 'opera') || str_contains($browser, 'opr')) {
                $buckets['Opera'] += $total;
                continue;
            }

            $buckets['Demais'] += $total;
        }

        $totalAll = array_sum($buckets);
        $colors = [
            'Chrome' => '#1f5fe0',
            'Firefox' => '#e15f2d',
            'Opera' => '#d7416a',
            'Edge' => '#0c85d0',
            'Demais' => '#8aa1bf',
        ];

        $result = [];
        foreach ($buckets as $label => $count) {
            $result[] = [
                'label' => $label,
                'count' => $count,
                'percent' => $totalAll > 0 ? round(($count / $totalAll) * 100, 1) : 0,
                'color' => $colors[$label] ?? '#8aa1bf',
            ];
        }

        return $result;
    }

    private function buildTopStatesViewsDistribution(int $limit = 6): array
    {
        $rows = PageVisitCounter::query()
            ->selectRaw('state as label, SUM(visits_count) as total_visits')
            ->whereNotNull('state')
            ->where('state', '<>', '')
            ->groupBy('state')
            ->orderByDesc('total_visits')
            ->get();

        if ($rows->isEmpty()) {
            $rows = RouteMetric::query()
                ->selectRaw('state as label, COUNT(*) as total_visits')
                ->whereNotNull('state')
                ->where('state', '<>', '')
                ->groupBy('state')
                ->orderByDesc('total_visits')
                ->get();
        }

        return $this->buildTopPieDistribution($rows, $limit);
    }

    private function buildTopCountriesViewsDistribution(int $limit = 6): array
    {
        $rows = PageVisitCounter::query()
            ->selectRaw('country as label, SUM(visits_count) as total_visits')
            ->whereNotNull('country')
            ->where('country', '<>', '')
            ->groupBy('country')
            ->orderByDesc('total_visits')
            ->get();

        if ($rows->isEmpty()) {
            $rows = RouteMetric::query()
                ->selectRaw('country as label, COUNT(*) as total_visits')
                ->whereNotNull('country')
                ->where('country', '<>', '')
                ->groupBy('country')
                ->orderByDesc('total_visits')
                ->get();
        }

        return $this->buildTopPieDistribution($rows, $limit);
    }

    private function buildTopPieDistribution(Collection $rows, int $limit): array
    {
        if ($rows->isEmpty()) {
            return [];
        }

        $totalAll = (int) $rows->sum('total_visits');
        if ($totalAll <= 0) {
            return [];
        }

        $palette = ['#1f5fe0', '#0c85d0', '#17a673', '#f0a202', '#e15f2d', '#4e5d6c'];
        $result = [];
        $topRows = $rows->take($limit)->values();
        $othersCount = (int) $rows->slice($limit)->sum('total_visits');

        foreach ($topRows as $index => $row) {
            $count = (int) ($row->total_visits ?? 0);
            $label = trim((string) ($row->label ?? ''));

            if ($count <= 0 || $label === '') {
                continue;
            }

            $result[] = [
                'label' => $label,
                'count' => $count,
                'percent' => round(($count / $totalAll) * 100, 1),
                'color' => $palette[$index] ?? '#8aa1bf',
            ];
        }

        if ($othersCount > 0) {
            $result[] = [
                'label' => 'Demais',
                'count' => $othersCount,
                'percent' => round(($othersCount / $totalAll) * 100, 1),
                'color' => '#8aa1bf',
            ];
        }

        return $result;
    }

    private function buildGeoDistribution(): array
    {
        $countries = PageVisitCounter::query()
            ->selectRaw('country, country_code, SUM(visits_count) as total_visits')
            ->whereNotNull('country')
            ->where('country', '<>', '')
            ->groupBy('country', 'country_code')
            ->orderByDesc('total_visits')
            ->limit(12)
            ->get();

        if ($countries->isEmpty()) {
            $countries = RouteMetric::query()
                ->selectRaw('country, country_code, COUNT(*) as total_visits')
                ->whereNotNull('country')
                ->where('country', '<>', '')
                ->groupBy('country', 'country_code')
                ->orderByDesc('total_visits')
                ->limit(12)
                ->get();
        }

        $countryTotal = (int) $countries->sum('total_visits');

        $coordRows = RouteMetric::query()
            ->selectRaw('country_code, AVG(latitude) as latitude, AVG(longitude) as longitude')
            ->whereNotNull('country_code')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->groupBy('country_code')
            ->get()
            ->keyBy(fn ($row) => strtoupper((string) $row->country_code));

        $countriesPayload = [];
        $regionsAgg = [];
        $mapPoints = [];

        foreach ($countries as $countryRow) {
            $countryName = trim((string) $countryRow->country);
            if ($countryName === '') {
                continue;
            }

            $countryCode = strtoupper(trim((string) $countryRow->country_code));
            $visits = (int) $countryRow->total_visits;
            $percent = $countryTotal > 0 ? round(($visits / $countryTotal) * 100, 1) : 0;
            $region = $this->resolveRegionByCountryCode($countryCode);
            $coords = $this->resolveCountryCoordinates($countryCode, $coordRows);

            $countriesPayload[] = [
                'country' => $countryName,
                'country_code' => $countryCode !== '' ? $countryCode : null,
                'visits' => $visits,
                'percent' => $percent,
                'region' => $region,
            ];

            if (!isset($regionsAgg[$region])) {
                $regionsAgg[$region] = 0;
            }
            $regionsAgg[$region] += $visits;

            if ($coords !== null) {
                $mapPoints[] = [
                    'country' => $countryName,
                    'country_code' => $countryCode !== '' ? $countryCode : null,
                    'visits' => $visits,
                    'percent' => $percent,
                    'region' => $region,
                    'lat' => $coords['lat'],
                    'lon' => $coords['lon'],
                ];
            }
        }

        $regionTotal = array_sum($regionsAgg);
        $regionsPayload = [];
        foreach ($regionsAgg as $regionName => $visits) {
            $regionsPayload[] = [
                'region' => $regionName,
                'visits' => $visits,
                'percent' => $regionTotal > 0 ? round(($visits / $regionTotal) * 100, 1) : 0,
            ];
        }

        usort($regionsPayload, fn ($a, $b) => $b['visits'] <=> $a['visits']);

        return [
            'countries' => $countriesPayload,
            'regions' => $regionsPayload,
            'map_points' => $mapPoints,
        ];
    }

    private function buildRecentAccesses(): array
    {
        $rows = RouteMetric::query()
            ->with(['user:id,name,email,user_type'])
            ->latest('captured_at')
            ->limit(50)
            ->get([
                'id',
                'user_id',
                'consent_mode',
                'route_name',
                'path',
                'page_url',
                'ip_address',
                'browser',
                'device_type',
                'operating_system',
                'country',
                'state',
                'city',
                'captured_at',
            ]);

        return $rows->map(function (RouteMetric $metric): array {
            return [
                'id' => $metric->id,
                'captured_at' => $metric->captured_at?->toIso8601String(),
                'ip' => $metric->ip_address ?: '-',
                'user_name' => $metric->user?->name ?: 'Anonimo',
                'user_email' => $metric->user?->email ?: '-',
                'user_type' => $metric->user?->user_type ?: 'anonimo',
                'route_name' => $metric->route_name ?: '-',
                'path' => $metric->path ?: '-',
                'page_url' => $metric->page_url ?: '-',
                'country' => $metric->country ?: '-',
                'state' => $metric->state ?: '-',
                'city' => $metric->city ?: '-',
                'browser' => $metric->browser ?: '-',
                'device_type' => $metric->device_type ?: '-',
                'operating_system' => $metric->operating_system ?: '-',
                'consent_mode' => $metric->consent_mode ?: '-',
            ];
        })->all();
    }

    private function resolveRegionByCountryCode(string $countryCode): string
    {
        $code = strtoupper($countryCode);

        $southAmerica = ['AR', 'BO', 'BR', 'CL', 'CO', 'EC', 'GY', 'PE', 'PY', 'SR', 'UY', 'VE'];
        $northAmerica = ['CA', 'US', 'MX', 'GT', 'CU', 'PA', 'DO', 'HN', 'NI', 'SV', 'CR'];
        $europe = ['DE', 'ES', 'FR', 'GB', 'IT', 'NL', 'PT', 'BE', 'CH', 'SE', 'NO', 'PL', 'IE'];
        $asia = ['CN', 'JP', 'IN', 'KR', 'SG', 'AE', 'TR', 'ID', 'TH', 'VN'];
        $africa = ['ZA', 'NG', 'EG', 'KE', 'MA', 'AO', 'MZ', 'DZ', 'TN', 'GH'];
        $oceania = ['AU', 'NZ', 'FJ', 'PG'];

        if (in_array($code, $southAmerica, true)) {
            return 'America do Sul';
        }

        if (in_array($code, $northAmerica, true)) {
            return 'America do Norte';
        }

        if (in_array($code, $europe, true)) {
            return 'Europa';
        }

        if (in_array($code, $asia, true)) {
            return 'Asia';
        }

        if (in_array($code, $africa, true)) {
            return 'Africa';
        }

        if (in_array($code, $oceania, true)) {
            return 'Oceania';
        }

        return 'Demais';
    }

    private function resolveCountryCoordinates(string $countryCode, Collection $coordRows): ?array
    {
        $code = strtoupper($countryCode);
        $row = $coordRows->get($code);

        if ($row && $row->latitude !== null && $row->longitude !== null) {
            return [
                'lat' => round((float) $row->latitude, 4),
                'lon' => round((float) $row->longitude, 4),
            ];
        }

        $fallback = $this->fallbackCountryCoordinates($code);
        if ($fallback) {
            return $fallback;
        }

        return null;
    }

    private function fallbackCountryCoordinates(string $countryCode): ?array
    {
        $coords = [
            'BR' => ['lat' => -14.2350, 'lon' => -51.9253],
            'US' => ['lat' => 37.0902, 'lon' => -95.7129],
            'CA' => ['lat' => 56.1304, 'lon' => -106.3468],
            'MX' => ['lat' => 23.6345, 'lon' => -102.5528],
            'AR' => ['lat' => -38.4161, 'lon' => -63.6167],
            'CL' => ['lat' => -35.6751, 'lon' => -71.5430],
            'CO' => ['lat' => 4.5709, 'lon' => -74.2973],
            'PE' => ['lat' => -9.1900, 'lon' => -75.0152],
            'GB' => ['lat' => 55.3781, 'lon' => -3.4360],
            'PT' => ['lat' => 39.3999, 'lon' => -8.2245],
            'ES' => ['lat' => 40.4637, 'lon' => -3.7492],
            'FR' => ['lat' => 46.2276, 'lon' => 2.2137],
            'DE' => ['lat' => 51.1657, 'lon' => 10.4515],
            'IT' => ['lat' => 41.8719, 'lon' => 12.5674],
            'IN' => ['lat' => 20.5937, 'lon' => 78.9629],
            'JP' => ['lat' => 36.2048, 'lon' => 138.2529],
            'CN' => ['lat' => 35.8617, 'lon' => 104.1954],
            'AU' => ['lat' => -25.2744, 'lon' => 133.7751],
        ];

        return $coords[$countryCode] ?? null;
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->user_type === User::TYPE_ADM, 403);
    }
}
