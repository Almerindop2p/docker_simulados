<?php

namespace App\Http\Controllers;

use App\Models\QuestaoResposta;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProgressController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user, 403);

        $now = now();

        $periodStats = [
            'hoje' => $this->buildPeriodStats($user->id, $now->copy()->startOfDay(), $now),
            'ultimos_7' => $this->buildPeriodStats($user->id, $now->copy()->subDays(6)->startOfDay(), $now),
            'ultimos_30' => $this->buildPeriodStats($user->id, $now->copy()->subDays(29)->startOfDay(), $now),
            'ultimos_90' => $this->buildPeriodStats($user->id, $now->copy()->subDays(89)->startOfDay(), $now),
        ];

        $erros = QuestaoResposta::query()
            ->where('user_id', $user->id)
            ->where('acertou', false)
            ->with([
                'questao:id,enunciado',
            ])
            ->orderByDesc('respondida_em')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'erros_page')
            ->appends($request->except('erros_page'));

        $acertos = QuestaoResposta::query()
            ->where('user_id', $user->id)
            ->where('acertou', true)
            ->with([
                'questao:id,enunciado',
            ])
            ->orderByDesc('respondida_em')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'acertos_page')
            ->appends($request->except('acertos_page'));

        return view('progresso.index', [
            'periodStats' => $periodStats,
            'erros' => $erros,
            'acertos' => $acertos,
        ]);
    }

    public function show(Request $request, QuestaoResposta $questaoResposta): View
    {
        $user = $request->user();
        abort_unless($user && $questaoResposta->user_id === $user->id, 403);

        $questaoResposta->load([
            'questao:id,enunciado,alternativa_a,alternativa_b,alternativa_c,alternativa_d,alternativa_e,gabarito,explicacao',
            'banca:id,name',
            'materia:id,name',
        ]);

        $questao = $questaoResposta->questao;
        $alternativas = [];

        if ($questao) {
            $alternativas = $questao->alternativasDisponiveis();
        }

        return view('progresso.show', [
            'resposta' => $questaoResposta,
            'questao' => $questao,
            'alternativas' => $alternativas,
        ]);
    }

    private function buildPeriodStats(int $userId, Carbon $startAt, Carbon $endAt): array
    {
        $query = QuestaoResposta::query()
            ->where('user_id', $userId)
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
}
