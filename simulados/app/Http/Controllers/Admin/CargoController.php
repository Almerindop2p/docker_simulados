<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCargoRequest;
use App\Models\Cargo;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CargoController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.cargos.index', [
            'cargos' => Cargo::query()->latest()->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.cargos.create');
    }

    public function edit(Request $request, Cargo $cargo): View
    {
        $this->ensureAdmin($request);

        return view('adm.cargos.edit', [
            'cargo' => $cargo,
        ]);
    }

    public function store(StoreCargoRequest $request): RedirectResponse
    {
        Cargo::create($request->validated());

        return redirect()
            ->route('adm.cargos.index')
            ->with('status', 'Cargo cadastrado com sucesso.');
    }

    public function update(StoreCargoRequest $request, Cargo $cargo): RedirectResponse
    {
        $cargo->update($request->validated());

        return redirect()
            ->route('adm.cargos.index')
            ->with('status', 'Cargo atualizado com sucesso.');
    }

    public function destroy(Request $request, Cargo $cargo): RedirectResponse
    {
        $this->ensureAdmin($request);

        $cargo->delete();

        return redirect()
            ->route('adm.cargos.index')
            ->with('status', 'Cargo removido com sucesso.');
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

        $query = Cargo::query()->where($field, $value);

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
