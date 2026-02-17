<?php

namespace App\Http\Controllers;

use App\Models\Banca;
use App\Models\Cargo;
use App\Models\Materia;
use App\Models\Questao;
use App\Models\QuestaoResposta;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $bancaId = (int) $request->query('banca_id', 0);
        $cargoId = (int) $request->query('cargo_id', 0);
        $materiaId = (int) $request->query('materia_id', 0);

        $temPesquisa = $bancaId > 0 || $cargoId > 0 || $materiaId > 0;

        $bancas = Banca::query()->orderBy('name')->get(['id', 'name']);
        $cargos = Cargo::query()->orderBy('name')->get(['id', 'name']);
        $materias = Materia::query()->orderBy('name')->get(['id', 'name']);

        $questoes = null;
        $totalResultados = null;

        if ($temPesquisa) {
            $query = Questao::query()
                ->with(['banca:id,name', 'materia:id,name', 'instituicao:id,name', 'cargos:id,name'])
                ->latest();

            if ($bancaId > 0) {
                $query->filtrarPorBanca($bancaId);
            }

            if ($materiaId > 0) {
                $query->filtrarPorMateria($materiaId);
            }

            if ($cargoId > 0) {
                $query->filtrarPorCargo($cargoId);
            }

            $questoes = $query->paginate(20)->withQueryString();
            $totalResultados = $questoes->total();
        }

        return view('welcome', [
            'bancas' => $bancas,
            'cargos' => $cargos,
            'materias' => $materias,
            'questoes' => $questoes,
            'temPesquisa' => $temPesquisa,
            'totalResultados' => $totalResultados,
            'filtros' => [
                'banca_id' => $bancaId,
                'cargo_id' => $cargoId,
                'materia_id' => $materiaId,
            ],
        ]);
    }

    public function answer(Request $request, Questao $questao): RedirectResponse
    {
        $query = $this->extractSearchQuery($request);

        $validator = Validator::make(
            $request->all(),
            [
                'resposta' => ['required', 'in:A,B,C,D,E'],
            ],
            [
                'resposta.required' => 'Selecione uma alternativa para responder.',
                'resposta.in' => 'A alternativa selecionada e invalida.',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->route('home', $query + ['respondida' => $questao->id])
                ->with('resultado_resposta', [
                    'questao_id' => $questao->id,
                    'erro' => $validator->errors()->first('resposta'),
                ]);
        }

        $respostaEnviada = strtoupper((string) $request->input('resposta'));
        $gabarito = strtoupper((string) $questao->gabarito);

        $this->storeAnswerAttempt($request->user(), $questao, $respostaEnviada, $gabarito);

        return redirect()
            ->to(route('home', $query + ['respondida' => $questao->id]) . '#questao-' . $questao->id)
            ->with('resultado_resposta', [
                'questao_id' => $questao->id,
                'resposta_enviada' => $respostaEnviada,
                'gabarito' => $gabarito,
                'acertou' => $respostaEnviada === $gabarito,
                'explicacao' => $questao->explicacao,
            ]);
    }

    private function extractSearchQuery(Request $request): array
    {
        $query = [];
        $bancaId = (int) $request->input('banca_id', 0);
        $cargoId = (int) $request->input('cargo_id', 0);
        $materiaId = (int) $request->input('materia_id', 0);
        $page = (int) $request->input('page', 1);

        if ($bancaId > 0) {
            $query['banca_id'] = $bancaId;
        }

        if ($cargoId > 0) {
            $query['cargo_id'] = $cargoId;
        }

        if ($materiaId > 0) {
            $query['materia_id'] = $materiaId;
        }

        if ($page > 1) {
            $query['page'] = $page;
        }

        return $query;
    }

    private function storeAnswerAttempt(?User $user, Questao $questao, string $respostaEnviada, string $gabarito): void
    {
        if (!$user) {
            return;
        }

        QuestaoResposta::query()->create([
            'user_id' => $user->id,
            'questao_id' => $questao->id,
            'banca_id' => $questao->banca_id,
            'materia_id' => $questao->materia_id,
            'resposta_marcada' => $respostaEnviada,
            'gabarito' => $gabarito,
            'acertou' => $respostaEnviada === $gabarito,
            'respondida_em' => now(),
        ]);
    }
}
