<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestaoRequest;
use App\Models\Banca;
use App\Models\Cargo;
use App\Models\Materia;
use App\Models\Questao;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class QuestaoController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        $query = Questao::query()
            ->with(['banca:id,name', 'materia:id,name', 'cargos:id,name'])
            ->latest();

        $bancaId = (int) $request->query('banca_id', 0);
        $materiaId = (int) $request->query('materia_id', 0);
        $cargoId = (int) $request->query('cargo_id', 0);

        if ($bancaId > 0) {
            $query->filtrarPorBanca($bancaId);
        }

        if ($materiaId > 0) {
            $query->filtrarPorMateria($materiaId);
        }

        if ($cargoId > 0) {
            $query->filtrarPorCargo($cargoId);
        }

        return view('adm.questoes.index', [
            'questoes' => $query->get(),
            'bancas' => Banca::query()->orderBy('name')->get(['id', 'name']),
            'materias' => Materia::query()->orderBy('name')->get(['id', 'name']),
            'cargos' => Cargo::query()->orderBy('name')->get(['id', 'name']),
            'filtros' => [
                'banca_id' => $bancaId,
                'materia_id' => $materiaId,
                'cargo_id' => $cargoId,
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.questoes.create', [
            'bancas' => Banca::query()->orderBy('name')->get(['id', 'name']),
            'materias' => Materia::query()->orderBy('name')->get(['id', 'name']),
            'cargos' => Cargo::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function edit(Request $request, Questao $questao): View
    {
        $this->ensureAdmin($request);

        $questao->load('cargos:id');

        return view('adm.questoes.edit', [
            'questao' => $questao,
            'selectedCargoIds' => $questao->cargos->pluck('id')->all(),
            'bancas' => Banca::query()->orderBy('name')->get(['id', 'name']),
            'materias' => Materia::query()->orderBy('name')->get(['id', 'name']),
            'cargos' => Cargo::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreQuestaoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $questao = Questao::create([
            'banca_id' => $data['banca_id'],
            'materia_id' => $data['materia_id'],
            'enunciado' => $data['enunciado'],
            'alternativa_a' => $data['alternativa_a'],
            'alternativa_b' => $data['alternativa_b'],
            'alternativa_c' => $data['alternativa_c'],
            'alternativa_d' => $data['alternativa_d'],
            'alternativa_e' => $data['alternativa_e'] ?? null,
            'gabarito' => $data['gabarito'],
            'explicacao' => $data['explicacao'] ?? null,
            'keywords' => $this->buildKeywords($data),
        ]);

        $questao->cargos()->sync($data['cargo_ids']);

        return redirect()
            ->route('adm.questoes.index')
            ->with('status', 'Questao cadastrada com sucesso.');
    }

    public function update(StoreQuestaoRequest $request, Questao $questao): RedirectResponse
    {
        $data = $request->validated();

        $questao->update([
            'banca_id' => $data['banca_id'],
            'materia_id' => $data['materia_id'],
            'enunciado' => $data['enunciado'],
            'alternativa_a' => $data['alternativa_a'],
            'alternativa_b' => $data['alternativa_b'],
            'alternativa_c' => $data['alternativa_c'],
            'alternativa_d' => $data['alternativa_d'],
            'alternativa_e' => $data['alternativa_e'] ?? null,
            'gabarito' => $data['gabarito'],
            'explicacao' => $data['explicacao'] ?? null,
            'keywords' => $this->buildKeywords($data),
        ]);

        $questao->cargos()->sync($data['cargo_ids']);

        return redirect()
            ->route('adm.questoes.index')
            ->with('status', 'Questao atualizada com sucesso.');
    }

    public function destroy(Request $request, Questao $questao): RedirectResponse
    {
        $this->ensureAdmin($request);

        $questao->delete();

        return redirect()
            ->route('adm.questoes.index')
            ->with('status', 'Questao removida com sucesso.');
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->user_type === User::TYPE_ADM, 403);
    }

    private function buildKeywords(array $data): string
    {
        $banca = Banca::query()->find($data['banca_id'], ['name', 'slug']);
        $materia = Materia::query()->find($data['materia_id'], ['name', 'slug']);
        $cargos = Cargo::query()
            ->whereIn('id', $data['cargo_ids'] ?? [])
            ->orderBy('name')
            ->get(['name', 'slug']);

        $keywords = collect([
            $banca?->name,
            $banca?->slug,
            $materia?->name,
            $materia?->slug,
            'gabarito ' . ($data['gabarito'] ?? ''),
        ]);

        foreach ($cargos as $cargo) {
            $keywords->push($cargo->name);
            $keywords->push($cargo->slug);
        }

        return $keywords
            ->flatMap(fn (?string $value) => $this->normalizeKeywordTokens($value))
            ->unique()
            ->implode(',');
    }

    private function normalizeKeywordTokens(?string $value): array
    {
        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $normalized = preg_replace('/\s+/', ' ', str_replace(',', ' ', trim($value)));
        $lower = Str::lower($normalized);
        $slug = Str::slug($normalized);

        return array_values(array_filter(array_unique([$lower, $slug])));
    }
}