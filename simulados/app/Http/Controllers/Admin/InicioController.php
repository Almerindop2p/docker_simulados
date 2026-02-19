<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageVisitCounter;
use App\Models\RouteMetric;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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

        return [
            'total_visualizacoes' => $totalVisualizacoes,
            'visualizacoes_hoje' => $visualizacoesHoje,
            'visitantes_unicos_24h' => $visitantesUnicos,
            'paginas_mapeadas' => $paginasMapeadas,
            'ultima_captura' => $ultimaCaptura?->toIso8601String(),
            'top_pagina_path' => $topPagina?->page_path,
            'top_pagina_visitas' => (int) ($topPagina?->total_visits ?? 0),
            'top_pais_nome' => $topPais?->country,
            'top_pais_codigo' => $topPais?->country_code,
            'top_pais_visitas' => (int) ($topPais?->total_visits ?? 0),
            'atualizado_em' => now()->toIso8601String(),
        ];
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->user_type === User::TYPE_ADM, 403);
    }
}
