<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBancaRequest;
use App\Models\Banca;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BancaController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.bancas.index', [
            'bancas' => Banca::query()->latest()->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.bancas.create');
    }

    public function edit(Request $request, Banca $banca): View
    {
        $this->ensureAdmin($request);

        return view('adm.bancas.edit', [
            'banca' => $banca,
        ]);
    }

    public function store(StoreBancaRequest $request): RedirectResponse
    {
        Banca::create($request->validated());

        return redirect()
            ->route('adm.bancas.index')
            ->with('status', 'Banca cadastrada com sucesso.');
    }

    public function update(StoreBancaRequest $request, Banca $banca): RedirectResponse
    {
        $banca->update($request->validated());

        return redirect()
            ->route('adm.bancas.index')
            ->with('status', 'Banca atualizada com sucesso.');
    }

    public function destroy(Request $request, Banca $banca): RedirectResponse
    {
        $this->ensureAdmin($request);

        try {
            $banca->delete();
        } catch (QueryException $exception) {
            return redirect()
                ->route('adm.bancas.index')
                ->with('status', 'Nao foi possivel excluir a banca porque ela possui questoes vinculadas.');
        }

        return redirect()
            ->route('adm.bancas.index')
            ->with('status', 'Banca removida com sucesso.');
    }

    public function checkName(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $name = trim((string) $request->query('name', ''));
        $ignoreId = (int) $request->query('ignore_id', 0);

        if ($name === '') {
            return response()->json([
                'exists' => false,
                'message' => 'Informe o nome da banca para validar.',
            ]);
        }

        $query = Banca::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)]);

        if ($ignoreId > 0) {
            $query->where('id', '!=', $ignoreId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists
                ? 'Essa banca ja existe. Escolha outro nome.'
                : 'Nome de banca disponivel.',
        ]);
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

        $query = Banca::query()->where($field, $value);

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
