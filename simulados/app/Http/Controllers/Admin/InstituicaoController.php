<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInstituicaoRequest;
use App\Models\Instituicao;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstituicaoController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.instituicoes.index', [
            'instituicoes' => Instituicao::query()->latest()->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.instituicoes.create');
    }

    public function edit(Request $request, Instituicao $instituicao): View
    {
        $this->ensureAdmin($request);

        return view('adm.instituicoes.edit', [
            'instituicao' => $instituicao,
        ]);
    }

    public function store(StoreInstituicaoRequest $request): RedirectResponse
    {
        Instituicao::create($request->validated());

        return redirect()
            ->route('adm.instituicoes.index')
            ->with('status', 'Instituicao cadastrada com sucesso.');
    }

    public function update(StoreInstituicaoRequest $request, Instituicao $instituicao): RedirectResponse
    {
        $instituicao->update($request->validated());

        return redirect()
            ->route('adm.instituicoes.index')
            ->with('status', 'Instituicao atualizada com sucesso.');
    }

    public function destroy(Request $request, Instituicao $instituicao): RedirectResponse
    {
        $this->ensureAdmin($request);

        try {
            $instituicao->delete();
        } catch (QueryException $exception) {
            return redirect()
                ->route('adm.instituicoes.index')
                ->with('status', 'Nao foi possivel excluir a instituicao.');
        }

        return redirect()
            ->route('adm.instituicoes.index')
            ->with('status', 'Instituicao removida com sucesso.');
    }

    public function checkName(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $name = trim((string) $request->query('name', ''));
        $ignoreId = (int) $request->query('ignore_id', 0);

        if ($name === '') {
            return response()->json([
                'exists' => false,
                'message' => 'Informe o nome da instituicao para validar.',
            ]);
        }

        $query = Instituicao::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)]);

        if ($ignoreId > 0) {
            $query->where('id', '!=', $ignoreId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists
                ? 'Essa instituicao ja existe. Escolha outro nome.'
                : 'Nome de instituicao disponivel.',
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

        $query = Instituicao::query()->where($field, $value);

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
