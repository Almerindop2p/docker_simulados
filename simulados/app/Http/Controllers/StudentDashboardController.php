<?php

namespace App\Http\Controllers;

use App\Models\Questao;
use App\Models\QuestaoResposta;
use App\Models\SimuladoTentativa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
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
        $atividadesPendentes = $this->buildAtividadesPendentesData(
            $totalQuestoes,
            $questoesRespondidasUnicas,
            $questoesRestantes
        );
        $continuarEstudo = $this->buildContinuarEstudoData($user->id);

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
                'atividades_pendentes_count' => (int) $atividadesPendentes['count'],
                'atividades_pendentes_titulo' => (string) $atividadesPendentes['title'],
                'atividades_pendentes_descricao' => (string) $atividadesPendentes['description'],
                'continuar_estudo_descricao' => (string) $continuarEstudo['description'],
                'continuar_estudo_label' => (string) $continuarEstudo['action_label'],
                'continuar_estudo_url' => (string) $continuarEstudo['action_url'],
            ],
        ]);
    }

    public function pendingActivities(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 403);

        $queryUsuario = QuestaoResposta::query()
            ->where('user_id', $user->id);

        $totalQuestoes = Questao::query()->count();
        $questoesRespondidasUnicas = (clone $queryUsuario)
            ->whereNotNull('questao_id')
            ->distinct('questao_id')
            ->count('questao_id');
        $questoesRestantes = max(0, $totalQuestoes - $questoesRespondidasUnicas);
        $payload = $this->buildAtividadesPendentesData(
            $totalQuestoes,
            $questoesRespondidasUnicas,
            $questoesRestantes
        );

        return response()->json($payload);
    }

    private function buildDesempenhoResumo(
        int $totalRespostas,
        int $recenteTotal,
        int $anteriorTotal,
        ?float $variacaoTaxa
    ): string {
        if ($totalRespostas === 0) {
            return 'Sem dados ainda. Responda questoes para gerar desempenho dos seus simulados.';
        }

        if ($recenteTotal === 0 || $anteriorTotal === 0 || $variacaoTaxa === null) {
            return "Desempenho dos seus simulados com base em {$totalRespostas} resposta(s).";
        }

        $variacaoFormatada = $this->formatPercent(abs($variacaoTaxa));

        if ($variacaoTaxa > 0) {
            return "Seu desempenho nos simulados subiu {$variacaoFormatada}%.";
        }

        if ($variacaoTaxa < 0) {
            return "Seu desempenho nos simulados caiu {$variacaoFormatada}%.";
        }

        return 'Seu desempenho nos simulados se manteve estavel.';
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

    private function buildAtividadesPendentesData(
        int $totalQuestoes,
        int $questoesRespondidasUnicas,
        int $questoesRestantes
    ): array {
        if ($totalQuestoes === 0) {
            return [
                'count' => 0,
                'title' => 'Nenhuma atividade pendente',
                'description' => 'Sem questoes cadastradas no sistema no momento.',
            ];
        }

        if ($questoesRestantes === 0) {
            return [
                'count' => 0,
                'title' => 'Nenhuma atividade pendente',
                'description' => "Voce ja respondeu {$questoesRespondidasUnicas} questao(oes) disponivel(is).",
            ];
        }

        return [
            'count' => $questoesRestantes,
            'title' => "{$questoesRestantes} questao(oes) para resolver",
            'description' => "Voce ja respondeu {$questoesRespondidasUnicas} de {$totalQuestoes} questao(oes).",
        ];
    }

    private function buildContinuarEstudoData(int $userId): array
    {
        $tentativaAberta = SimuladoTentativa::query()
            ->with(['simulado:id,name'])
            ->where('user_id', $userId)
            ->where('status', SimuladoTentativa::STATUS_ABERTO)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();

        if ($tentativaAberta && $tentativaAberta->simulado) {
            return [
                'description' => "Voce possui um simulado em andamento: {$tentativaAberta->simulado->name}. Retome exatamente de onde parou.",
                'action_label' => 'Retomar simulado',
                'action_url' => route('simulados.play', [
                    'simulado' => $tentativaAberta->simulado,
                    'attempt' => $tentativaAberta->id,
                    'i' => max(0, (int) $tentativaAberta->current_index),
                ]),
            ];
        }

        return [
            'description' => 'Veja o desempenho dos seus simulados e continue seus estudos.',
            'action_label' => 'Ir para simulados',
            'action_url' => route('simulados.public'),
        ];
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
