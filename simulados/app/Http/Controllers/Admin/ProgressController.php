<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestaoResposta;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProgressController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        $now = now();

        $periodStats = [
            'hoje' => $this->buildPeriodStats($now->copy()->startOfDay(), $now),
            'ultimos_7' => $this->buildPeriodStats($now->copy()->subDays(6)->startOfDay(), $now),
            'ultimos_30' => $this->buildPeriodStats($now->copy()->subDays(29)->startOfDay(), $now),
            'ultimos_90' => $this->buildPeriodStats($now->copy()->subDays(89)->startOfDay(), $now),
        ];

        return view('adm.progresso.index', [
            'periodStats' => $periodStats,
        ]);
    }

    private function buildPeriodStats(Carbon $startAt, Carbon $endAt): array
    {
        $query = QuestaoResposta::query()
            ->whereBetween('respondida_em', [$startAt, $endAt]);

        $total = (clone $query)->count();
        $acertos = (clone $query)->where('acertou', true)->count();
        $erros = max(0, $total - $acertos);
        $acertosPercentual = $total > 0 ? round(($acertos / $total) * 100, 1) : 0;
        $errosPercentual = $total > 0 ? round(($erros / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'acertos' => $acertos,
            'erros' => $erros,
            'acertos_percentual' => $acertosPercentual,
            'erros_percentual' => $errosPercentual,
        ];
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->user_type === User::TYPE_ADM, 403);
    }
}
