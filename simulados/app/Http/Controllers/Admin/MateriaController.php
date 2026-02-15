<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMateriaRequest;
use App\Models\Materia;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MateriaController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.materias.index', [
            'materias' => Materia::query()->latest()->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.materias.create');
    }

    public function edit(Request $request, Materia $materia): View
    {
        $this->ensureAdmin($request);

        return view('adm.materias.edit', [
            'materia' => $materia,
        ]);
    }

    public function store(StoreMateriaRequest $request): RedirectResponse
    {
        Materia::create($request->validated());

        return redirect()
            ->route('adm.materias.index')
            ->with('status', 'Materia cadastrada com sucesso.');
    }

    public function update(StoreMateriaRequest $request, Materia $materia): RedirectResponse
    {
        $materia->update($request->validated());

        return redirect()
            ->route('adm.materias.index')
            ->with('status', 'Materia atualizada com sucesso.');
    }

    public function destroy(Request $request, Materia $materia): RedirectResponse
    {
        $this->ensureAdmin($request);

        try {
            $materia->delete();
        } catch (QueryException $exception) {
            return redirect()
                ->route('adm.materias.index')
                ->with('status', 'Nao foi possivel excluir a materia porque ela possui questoes vinculadas.');
        }

        return redirect()
            ->route('adm.materias.index')
            ->with('status', 'Materia removida com sucesso.');
    }

    public function checkField(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $field = (string) $request->query('field', 'name');
        $value = trim((string) $request->query('value', ''));
        $ignoreId = (int) $request->query('ignore_id', 0);

        if (!in_array($field, ['name', 'slug'], true)) {
            return response()->json([
                'exists' => false,
                'message' => 'Campo de validacao invalido.',
            ], 422);
        }

        if ($value === '') {
            return response()->json([
                'exists' => false,
                'message' => 'Informe um valor para validar.',
            ]);
        }

        $query = Materia::query()->where($field, $value);

        if ($ignoreId > 0) {
            $query->where('id', '!=', $ignoreId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists
                ? 'Esse valor ja esta em uso.'
                : 'Valor disponivel.',
        ]);
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->user_type === User::TYPE_ADM, 403);
    }
}
