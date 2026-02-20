<?php

namespace App\Http\Controllers;

use App\Models\Questao;
use App\Models\QuestaoResposta;
use App\Models\Simulado;
use App\Models\SimuladoTentativa;
use App\Models\SimuladoTentativaResposta;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SimuladoCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $term = trim((string) $request->query('q', ''));
        $user = $request->user();

        $query = Simulado::query()
            ->withCount('questoes')
            ->orderBy('name');

        if ($user?->user_type === User::TYPE_ADM) {
            // ADM pode visualizar todos os simulados cadastrados.
        } elseif (in_array($user?->user_type, [User::TYPE_USER_ASSINANTE, User::TYPE_COLABORADOR], true)) {
            $query->whereIn('visibilidade', [
                Simulado::VISIBILIDADE_PUBLICO,
                Simulado::VISIBILIDADE_ASSINANTES,
            ]);
        } else {
            $query->where('visibilidade', Simulado::VISIBILIDADE_PUBLICO);
        }

        if ($term !== '') {
            $query->where(function ($subQuery) use ($term) {
                $subQuery
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%");
            });
        }

        $simulados = $query->paginate(20)->withQueryString();

        $tentativasAbertas = [];

        if ($user) {
            $tentativasAbertas = SimuladoTentativa::query()
                ->where('user_id', $user->id)
                ->where('status', SimuladoTentativa::STATUS_ABERTO)
                ->pluck('id', 'simulado_id')
                ->all();
        }

        return view('simulados.index', [
            'simulados' => $simulados,
            'searchTerm' => $term,
            'tentativasAbertas' => $tentativasAbertas,
        ]);
    }

    public function start(Request $request, Simulado $simulado): RedirectResponse
    {
        if (!$this->canAccessSimulado($simulado, $request->user())) {
            abort(403);
        }

        $questoes = $simulado->questoes()
            ->orderBy('id')
            ->get(['id', 'gabarito']);

        if ($questoes->isEmpty()) {
            return redirect()
                ->route('simulados.public')
                ->with('status', 'Esse simulado ainda nao possui questoes cadastradas.');
        }

        $user = $request->user();

        if ($user) {
            $tentativaAberta = SimuladoTentativa::query()
                ->where('user_id', $user->id)
                ->where('simulado_id', $simulado->id)
                ->where('status', SimuladoTentativa::STATUS_ABERTO)
                ->latest('id')
                ->first();

            if ($tentativaAberta) {
                return redirect()->route('simulados.play', [
                    'simulado' => $simulado,
                    'attempt' => $tentativaAberta->id,
                ]);
            }

            $tentativa = DB::transaction(function () use ($user, $simulado, $questoes) {
                $snapshot = $questoes->pluck('id')->map(fn ($id) => (int) $id)->values()->all();

                $tentativa = SimuladoTentativa::query()->create([
                    'user_id' => $user->id,
                    'simulado_id' => $simulado->id,
                    'status' => SimuladoTentativa::STATUS_ABERTO,
                    'questoes_snapshot' => $snapshot,
                    'total_questoes' => count($snapshot),
                    'started_at' => now(),
                    'current_index' => 0,
                ]);

                $rows = $questoes->values()->map(function (Questao $questao, int $index) use ($tentativa) {
                    return [
                        'tentativa_id' => $tentativa->id,
                        'questao_id' => $questao->id,
                        'question_index' => $index,
                        'resposta_marcada' => null,
                        'gabarito' => strtoupper((string) $questao->gabarito),
                        'acertou' => false,
                        'elapsed_seconds' => 0,
                        'answered_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->all();

                SimuladoTentativaResposta::query()->insert($rows);

                return $tentativa;
            });

            return redirect()->route('simulados.play', [
                'simulado' => $simulado,
                'attempt' => $tentativa->id,
            ]);
        }

        $state = $this->encodeGuestState([
            'i' => 0,
            's' => $questoes->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
            'a' => [],
            'e' => [],
            'te' => 0,
        ]);

        return redirect()->route('simulados.play', [
            'simulado' => $simulado,
            'state' => $state,
        ]);
    }

    public function play(Request $request, Simulado $simulado): View|RedirectResponse
    {
        if (!$this->canAccessSimulado($simulado, $request->user())) {
            abort(403);
        }

        $user = $request->user();

        if ($user) {
            $attemptId = (int) $request->query('attempt', 0);

            if ($attemptId <= 0) {
                return $this->start($request, $simulado);
            }

            $tentativa = SimuladoTentativa::query()
                ->where('id', $attemptId)
                ->where('user_id', $user->id)
                ->where('simulado_id', $simulado->id)
                ->firstOrFail();

            if ($tentativa->status === SimuladoTentativa::STATUS_CONCLUIDO) {
                return redirect()->route('simulados.result', [
                    'simulado' => $simulado,
                    'attempt' => $tentativa->id,
                ]);
            }

            $snapshot = collect($tentativa->questoes_snapshot ?? [])->map(fn ($id) => (int) $id)->filter()->values()->all();

            if (empty($snapshot)) {
                return redirect()->route('simulados.public')->with('status', 'Tentativa invalida. Inicie novamente o simulado.');
            }

            $currentIndex = max(0, min((int) $request->query('i', $tentativa->current_index), count($snapshot) - 1));
            $questionId = $snapshot[$currentIndex] ?? null;
            $questao = $questionId ? Questao::query()->find($questionId) : null;

            $respostaAtual = SimuladoTentativaResposta::query()
                ->where('tentativa_id', $tentativa->id)
                ->where('question_index', $currentIndex)
                ->first();

            return view('simulados.play', [
                'simulado' => $simulado,
                'questao' => $questao,
                'currentIndex' => $currentIndex,
                'totalQuestoes' => count($snapshot),
                'selectedAnswer' => $respostaAtual?->resposta_marcada,
                'elapsedCurrentSeconds' => (int) ($respostaAtual?->elapsed_seconds ?? 0),
                'totalElapsedSeconds' => (int) $tentativa->total_elapsed_seconds,
                'attemptId' => $tentativa->id,
                'isGuest' => false,
                'guestState' => null,
            ]);
        }

        $state = $this->decodeGuestState((string) $request->query('state', ''));

        if (empty($state['s']) || !is_array($state['s'])) {
            return $this->start($request, $simulado);
        }

        $snapshot = collect($state['s'])->map(fn ($id) => (int) $id)->filter()->values()->all();

        if (empty($snapshot)) {
            return $this->start($request, $simulado);
        }

        $currentIndex = max(0, min((int) ($state['i'] ?? 0), count($snapshot) - 1));
        $questionId = $snapshot[$currentIndex] ?? null;
        $questao = $questionId ? Questao::query()->find($questionId) : null;
        $answers = is_array($state['a'] ?? null) ? $state['a'] : [];
        $elapsedMap = is_array($state['e'] ?? null) ? $state['e'] : [];

        return view('simulados.play', [
            'simulado' => $simulado,
            'questao' => $questao,
            'currentIndex' => $currentIndex,
            'totalQuestoes' => count($snapshot),
            'selectedAnswer' => strtoupper((string) ($answers[$currentIndex] ?? '')),
            'elapsedCurrentSeconds' => (int) ($elapsedMap[$currentIndex] ?? 0),
            'totalElapsedSeconds' => (int) ($state['te'] ?? 0),
            'attemptId' => null,
            'isGuest' => true,
            'guestState' => $this->encodeGuestState([
                'i' => $currentIndex,
                's' => $snapshot,
                'a' => $answers,
                'e' => $elapsedMap,
                'te' => (int) ($state['te'] ?? 0),
            ]),
        ]);
    }

    public function submit(Request $request, Simulado $simulado): RedirectResponse
    {
        if (!$this->canAccessSimulado($simulado, $request->user())) {
            abort(403);
        }

        $action = (string) $request->input('action', 'next');
        if (!in_array($action, ['back', 'next', 'finish'], true)) {
            $action = 'next';
        }

        $currentIndex = max(0, (int) $request->input('current_index', 0));
        $answer = strtoupper((string) $request->input('resposta', ''));
        if (!in_array($answer, ['A', 'B', 'C', 'D', 'E'], true)) {
            $answer = '';
        }

        $elapsedDelta = max(0, min((int) $request->input('elapsed_delta_seconds', 0), 86400));
        $user = $request->user();

        if ($user) {
            $attemptId = (int) $request->input('attempt_id', 0);

            $tentativa = SimuladoTentativa::query()
                ->where('id', $attemptId)
                ->where('user_id', $user->id)
                ->where('simulado_id', $simulado->id)
                ->firstOrFail();

            if ($tentativa->status === SimuladoTentativa::STATUS_CONCLUIDO) {
                return redirect()->route('simulados.result', [
                    'simulado' => $simulado,
                    'attempt' => $tentativa->id,
                ]);
            }

            $snapshot = collect($tentativa->questoes_snapshot ?? [])->map(fn ($id) => (int) $id)->filter()->values()->all();
            $totalQuestoes = count($snapshot);
            if ($totalQuestoes === 0) {
                return redirect()->route('simulados.public')->with('status', 'Tentativa invalida. Inicie novamente o simulado.');
            }

            $currentIndex = max(0, min($currentIndex, $totalQuestoes - 1));

            $respostaAtual = SimuladoTentativaResposta::query()
                ->where('tentativa_id', $tentativa->id)
                ->where('question_index', $currentIndex)
                ->first();

            if ($respostaAtual) {
                $gabarito = strtoupper((string) ($respostaAtual->gabarito ?? ''));
                $respostaAtual->elapsed_seconds = max(0, (int) $respostaAtual->elapsed_seconds + $elapsedDelta);
                if ($answer !== '') {
                    $respostaAtual->resposta_marcada = $answer;
                    $respostaAtual->acertou = ($gabarito !== '' && $answer === $gabarito);
                    $respostaAtual->answered_at = now();
                }
                $respostaAtual->save();
            }

            if ($action === 'finish') {
                $this->finalizeAttempt($tentativa);

                return redirect()->route('simulados.result', [
                    'simulado' => $simulado,
                    'attempt' => $tentativa->id,
                ]);
            }

            $nextIndex = $action === 'back'
                ? max(0, $currentIndex - 1)
                : min($totalQuestoes - 1, $currentIndex + 1);

            $this->refreshAttemptProgress($tentativa, $nextIndex);

            return redirect()->route('simulados.play', [
                'simulado' => $simulado,
                'attempt' => $tentativa->id,
                'i' => $nextIndex,
            ]);
        }

        $state = $this->decodeGuestState((string) $request->input('state', ''));
        $snapshot = collect($state['s'] ?? [])->map(fn ($id) => (int) $id)->filter()->values()->all();
        $totalQuestoes = count($snapshot);

        if ($totalQuestoes === 0) {
            return redirect()->route('simulados.start', ['simulado' => $simulado]);
        }

        $currentIndex = max(0, min($currentIndex, $totalQuestoes - 1));

        $answers = is_array($state['a'] ?? null) ? $state['a'] : [];
        $elapsedMap = is_array($state['e'] ?? null) ? $state['e'] : [];
        $totalElapsed = max(0, (int) ($state['te'] ?? 0));

        if ($answer !== '') {
            $answers[$currentIndex] = $answer;
        }

        $elapsedMap[$currentIndex] = max(0, (int) ($elapsedMap[$currentIndex] ?? 0) + $elapsedDelta);
        $totalElapsed += $elapsedDelta;

        if ($action === 'finish') {
            $encoded = $this->encodeGuestState([
                'i' => $currentIndex,
                's' => $snapshot,
                'a' => $answers,
                'e' => $elapsedMap,
                'te' => $totalElapsed,
            ]);

            return redirect()->route('simulados.result', [
                'simulado' => $simulado,
                'state' => $encoded,
            ]);
        }

        $nextIndex = $action === 'back'
            ? max(0, $currentIndex - 1)
            : min($totalQuestoes - 1, $currentIndex + 1);

        $encoded = $this->encodeGuestState([
            'i' => $nextIndex,
            's' => $snapshot,
            'a' => $answers,
            'e' => $elapsedMap,
            'te' => $totalElapsed,
        ]);

        return redirect()->route('simulados.play', [
            'simulado' => $simulado,
            'state' => $encoded,
        ]);
    }

    public function result(Request $request, Simulado $simulado): View|RedirectResponse
    {
        if (!$this->canAccessSimulado($simulado, $request->user())) {
            abort(403);
        }

        $user = $request->user();

        if ($user) {
            $attemptId = (int) $request->query('attempt', 0);

            $tentativa = SimuladoTentativa::query()
                ->with([
                    'respostas' => fn ($query) => $query->orderBy('question_index'),
                    'respostas.questao:id,enunciado',
                ])
                ->where('id', $attemptId)
                ->where('user_id', $user->id)
                ->where('simulado_id', $simulado->id)
                ->firstOrFail();

            if ($tentativa->status === SimuladoTentativa::STATUS_ABERTO) {
                $this->finalizeAttempt($tentativa);
                $tentativa->refresh()->load([
                    'respostas' => fn ($query) => $query->orderBy('question_index'),
                    'respostas.questao:id,enunciado',
                ]);
            }

            $respostas = $tentativa->respostas->sortBy('question_index')->values();
            $acertosRows = $respostas
                ->filter(fn (SimuladoTentativaResposta $item) => !blank($item->resposta_marcada) && (bool) $item->acertou)
                ->values();
            $errosRows = $respostas
                ->filter(fn (SimuladoTentativaResposta $item) => blank($item->resposta_marcada) || !(bool) $item->acertou)
                ->values();

            return view('simulados.result', [
                'simulado' => $simulado,
                'acertos' => (int) $tentativa->acertos,
                'erros' => (int) $tentativa->erros,
                'total' => (int) $tentativa->total_questoes,
                'totalElapsedSeconds' => (int) $tentativa->total_elapsed_seconds,
                'attemptId' => $tentativa->id,
                'isGuest' => false,
                'resumeUrl' => null,
                'acertosRows' => $acertosRows,
                'errosRows' => $errosRows,
            ]);
        }

        $state = $this->decodeGuestState((string) $request->query('state', ''));
        $snapshot = collect($state['s'] ?? [])->map(fn ($id) => (int) $id)->filter()->values()->all();

        if (empty($snapshot)) {
            return $this->start($request, $simulado);
        }

        $answers = is_array($state['a'] ?? null) ? $state['a'] : [];

        $gabaritos = Questao::query()
            ->whereIn('id', $snapshot)
            ->pluck('gabarito', 'id');

        $acertos = 0;
        foreach ($snapshot as $index => $questaoId) {
            $marcada = strtoupper((string) ($answers[$index] ?? ''));
            $gabarito = strtoupper((string) ($gabaritos[$questaoId] ?? ''));
            if ($marcada !== '' && $gabarito !== '' && $marcada === $gabarito) {
                $acertos++;
            }
        }

        $total = count($snapshot);
        $erros = max(0, $total - $acertos);
        $totalElapsed = max(0, (int) ($state['te'] ?? 0));

        return view('simulados.result', [
            'simulado' => $simulado,
            'acertos' => $acertos,
            'erros' => $erros,
            'total' => $total,
            'totalElapsedSeconds' => $totalElapsed,
            'attemptId' => null,
            'isGuest' => true,
            'resumeUrl' => route('simulados.play', [
                'simulado' => $simulado,
                'state' => $this->encodeGuestState($state),
            ]),
            'acertosRows' => collect(),
            'errosRows' => collect(),
        ]);
    }

    public function resultQuestion(
        Request $request,
        Simulado $simulado,
        SimuladoTentativa $tentativa,
        SimuladoTentativaResposta $resposta
    ): View {
        $user = $request->user();

        abort_unless($user, 403);
        abort_unless($this->canAccessSimulado($simulado, $user), 403);
        abort_unless((int) $tentativa->simulado_id === (int) $simulado->id, 404);
        abort_unless((int) $tentativa->user_id === (int) $user->id, 403);
        abort_unless((int) $resposta->tentativa_id === (int) $tentativa->id, 404);

        $resposta->load([
            'questao:id,enunciado,imagem_path,alternativa_a,alternativa_b,alternativa_c,alternativa_d,alternativa_e,gabarito,explicacao',
        ]);

        $questao = $resposta->questao;

        $alternativas = [];
        if ($questao) {
            $alternativas = [
                'A' => $questao->alternativa_a,
                'B' => $questao->alternativa_b,
                'C' => $questao->alternativa_c,
                'D' => $questao->alternativa_d,
            ];

            if (!blank($questao->alternativa_e)) {
                $alternativas['E'] = $questao->alternativa_e;
            }
        }

        return view('simulados.question-result', [
            'simulado' => $simulado,
            'tentativa' => $tentativa,
            'resposta' => $resposta,
            'questao' => $questao,
            'alternativas' => $alternativas,
        ]);
    }

    private function canAccessSimulado(Simulado $simulado, ?User $user): bool
    {
        $visibilidade = (string) $simulado->visibilidade;

        if ($user?->user_type === User::TYPE_ADM) {
            return true;
        }

        if ($visibilidade === Simulado::VISIBILIDADE_PUBLICO) {
            return true;
        }

        if ($visibilidade === Simulado::VISIBILIDADE_ASSINANTES) {
            return in_array($user?->user_type, [User::TYPE_USER_ASSINANTE, User::TYPE_COLABORADOR], true);
        }

        return false;
    }

    private function refreshAttemptProgress(SimuladoTentativa $tentativa, int $nextIndex): void
    {
        $tentativa->load('respostas');

        $respondidas = $tentativa->respostas->whereNotNull('resposta_marcada')->count();
        $acertos = $tentativa->respostas->where('acertou', true)->whereNotNull('resposta_marcada')->count();
        $total = max(0, (int) $tentativa->total_questoes);
        $erros = max(0, $total - $acertos);
        $totalElapsed = $tentativa->respostas->sum(fn (SimuladoTentativaResposta $resposta) => max(0, (int) $resposta->elapsed_seconds));

        $tentativa->update([
            'questoes_respondidas' => $respondidas,
            'acertos' => $acertos,
            'erros' => $erros,
            'current_index' => $nextIndex,
            'total_elapsed_seconds' => $totalElapsed,
        ]);
    }

    private function finalizeAttempt(SimuladoTentativa $tentativa): void
    {
        if ($tentativa->status === SimuladoTentativa::STATUS_CONCLUIDO) {
            return;
        }

        DB::transaction(function () use ($tentativa) {
            $tentativa->load('respostas');

            $respondidas = $tentativa->respostas->whereNotNull('resposta_marcada');
            $acertos = $respondidas->where('acertou', true)->count();
            $total = max(0, (int) $tentativa->total_questoes);
            $erros = max(0, $total - $acertos);
            $totalElapsed = $tentativa->respostas->sum(fn (SimuladoTentativaResposta $resposta) => max(0, (int) $resposta->elapsed_seconds));

            $tentativa->update([
                'status' => SimuladoTentativa::STATUS_CONCLUIDO,
                'questoes_respondidas' => $respondidas->count(),
                'acertos' => $acertos,
                'erros' => $erros,
                'current_index' => $total > 0 ? ($total - 1) : 0,
                'total_elapsed_seconds' => $totalElapsed,
                'finished_at' => now(),
            ]);

            foreach ($respondidas as $resposta) {
                if (!$resposta->questao_id || !$tentativa->user_id) {
                    continue;
                }

                $questao = Questao::query()->find($resposta->questao_id, ['id', 'banca_id', 'materia_id']);
                if (!$questao) {
                    continue;
                }

                QuestaoResposta::query()->create([
                    'user_id' => $tentativa->user_id,
                    'questao_id' => $resposta->questao_id,
                    'banca_id' => $questao->banca_id,
                    'materia_id' => $questao->materia_id,
                    'resposta_marcada' => strtoupper((string) $resposta->resposta_marcada),
                    'gabarito' => strtoupper((string) $resposta->gabarito),
                    'acertou' => (bool) $resposta->acertou,
                    'respondida_em' => $resposta->answered_at ?? now(),
                ]);
            }
        });
    }

    private function encodeGuestState(array $state): string
    {
        $json = json_encode($state, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return '';
        }

        return rtrim(strtr(base64_encode($json), '+/', '-_'), '=');
    }

    private function decodeGuestState(string $encoded): array
    {
        if ($encoded === '') {
            return [];
        }

        $normalized = strtr($encoded, '-_', '+/');
        $padding = strlen($normalized) % 4;
        if ($padding > 0) {
            $normalized .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($normalized, true);
        if ($decoded === false || $decoded === '') {
            return [];
        }

        $payload = json_decode($decoded, true);
        if (!is_array($payload)) {
            return [];
        }

        return [
            'i' => max(0, (int) ($payload['i'] ?? 0)),
            's' => is_array($payload['s'] ?? null) ? array_values($payload['s']) : [],
            'a' => is_array($payload['a'] ?? null) ? array_values($payload['a']) : [],
            'e' => is_array($payload['e'] ?? null) ? array_values($payload['e']) : [],
            'te' => max(0, (int) ($payload['te'] ?? 0)),
        ];
    }

}
