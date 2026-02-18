<?php

namespace App\Http\Controllers;

use App\Models\Questao;
use App\Models\QuestaoResposta;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        abort_unless($user, 403);

        $queryUsuario = QuestaoResposta::query()
            ->where('user_id', $user->id);

        $respostasHoje = (clone $queryUsuario)
            ->whereDate('respondida_em', now()->toDateString())
            ->count();

        $totalRespostas = (clone $queryUsuario)->count();
        $totalAcertos = (clone $queryUsuario)->where('acertou', true)->count();
        $taxaAcerto = $totalRespostas > 0 ? round(($totalAcertos / $totalRespostas) * 100, 1) : 0.0;
        $totalQuestoes = Questao::query()->count();
        $questoesRespondidasUnicas = (clone $queryUsuario)
            ->whereNotNull('questao_id')
            ->distinct('questao_id')
            ->count('questao_id');
        $questoesRestantes = max(0, $totalQuestoes - $questoesRespondidasUnicas);
        $progressoGeralPercent = $totalQuestoes > 0
            ? round(($questoesRespondidasUnicas / $totalQuestoes) * 100, 1)
            : 0.0;

        $ultimasRespostas = (clone $queryUsuario)
            ->select(['id', 'acertou'])
            ->orderByDesc('respondida_em')
            ->orderByDesc('id')
            ->limit(60)
            ->get();

        $blocoRecente = $ultimasRespostas->take(30);
        $blocoAnterior = $ultimasRespostas->slice(30, 30);

        $recenteTotal = $blocoRecente->count();
        $anteriorTotal = $blocoAnterior->count();

        $recenteTaxa = $recenteTotal > 0
            ? (($blocoRecente->where('acertou', true)->count() / $recenteTotal) * 100)
            : null;
        $anteriorTaxa = $anteriorTotal > 0
            ? (($blocoAnterior->where('acertou', true)->count() / $anteriorTotal) * 100)
            : null;

        $variacaoTaxa = null;
        if ($recenteTaxa !== null && $anteriorTaxa !== null) {
            $variacaoTaxa = round($recenteTaxa - $anteriorTaxa, 1);
        }

        return view('area_aluno', [
            'dashboardStats' => [
                'respostas_hoje' => $respostasHoje,
                'total_respostas' => $totalRespostas,
                'taxa_acerto_percent' => $this->formatPercent($taxaAcerto),
                'desempenho_resumo' => $this->buildDesempenhoResumo(
                    $totalRespostas,
                    $recenteTotal,
                    $anteriorTotal,
                    $variacaoTaxa
                ),
                'progresso_geral_percent' => $this->formatPercent($progressoGeralPercent),
                'progresso_geral_resumo' => $this->buildProgressoGeralResumo(
                    $totalQuestoes,
                    $questoesRespondidasUnicas,
                    $questoesRestantes
                ),
            ],
        ]);
    }

    private function buildDesempenhoResumo(
        int $totalRespostas,
        int $recenteTotal,
        int $anteriorTotal,
        ?float $variacaoTaxa
    ): string {
        if ($totalRespostas === 0) {
            return 'Sem respostas registradas ainda. Comece a responder questoes para gerar desempenho real.';
        }

        if ($recenteTotal === 0 || $anteriorTotal === 0 || $variacaoTaxa === null) {
            return "Desempenho calculado com base em {$totalRespostas} resposta(s) registrada(s).";
        }

        $variacaoFormatada = $this->formatPercent(abs($variacaoTaxa));

        if ($variacaoTaxa > 0) {
            return "Voce subiu {$variacaoFormatada}% nas ultimas respostas comparadas ao bloco anterior.";
        }

        if ($variacaoTaxa < 0) {
            return "Voce caiu {$variacaoFormatada}% nas ultimas respostas comparadas ao bloco anterior.";
        }

        return 'Seu desempenho se manteve estavel nas ultimas respostas comparadas ao bloco anterior.';
    }

    private function buildProgressoGeralResumo(
        int $totalQuestoes,
        int $questoesRespondidasUnicas,
        int $questoesRestantes
    ): string {
        if ($totalQuestoes === 0) {
            return 'Sem questoes cadastradas no sistema para calcular progresso geral.';
        }

        if ($questoesRestantes === 0) {
            return "Voce concluiu {$questoesRespondidasUnicas} de {$totalQuestoes} questao(oes) disponivel(is).";
        }

        return "Faltam {$questoesRestantes} questao(oes) para concluir {$totalQuestoes} questao(oes) disponivel(is).";
    }

    private function formatPercent(float $value): string
    {
        $formatted = number_format($value, 1, '.', '');

        if (str_ends_with($formatted, '.0')) {
            return substr($formatted, 0, -2);
        }

        return $formatted;
    }
}
