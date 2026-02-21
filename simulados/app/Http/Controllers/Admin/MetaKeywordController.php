<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMetaKeywordRequest;
use App\Models\MetaKeyword;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetaKeywordController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.meta-keywords.index', [
            'metaKeywords' => MetaKeyword::query()->latest()->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.meta-keywords.create');
    }

    public function edit(Request $request, MetaKeyword $metaKeyword): View
    {
        $this->ensureAdmin($request);

        return view('adm.meta-keywords.edit', [
            'metaKeyword' => $metaKeyword,
        ]);
    }

    public function store(StoreMetaKeywordRequest $request): RedirectResponse
    {
        MetaKeyword::query()->create($request->validated());

        return redirect()
            ->route('adm.meta-keywords.index')
            ->with('status', 'Palavra-chave cadastrada com sucesso.');
    }

    public function update(StoreMetaKeywordRequest $request, MetaKeyword $metaKeyword): RedirectResponse
    {
        $metaKeyword->update($request->validated());

        return redirect()
            ->route('adm.meta-keywords.index')
            ->with('status', 'Palavra-chave atualizada com sucesso.');
    }

    public function destroy(Request $request, MetaKeyword $metaKeyword): RedirectResponse
    {
        $this->ensureAdmin($request);

        $metaKeyword->delete();

        return redirect()
            ->route('adm.meta-keywords.index')
            ->with('status', 'Palavra-chave removida com sucesso.');
    }

    public function checkField(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $value = trim((string) $request->query('value', ''));
        $ignoreId = (int) $request->query('ignore_id', 0);

        if ($value === '') {
            return response()->json([
                'exists' => false,
                'message' => 'Informe um valor para validar.',
            ]);
        }

        $query = MetaKeyword::query()
            ->whereRaw('LOWER(keyword) = ?', [mb_strtolower($value)]);

        if ($ignoreId > 0) {
            $query->where('id', '!=', $ignoreId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists
                ? 'Essa palavra-chave ja esta em uso.'
                : 'Palavra-chave disponivel.',
        ]);
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->user_type === User::TYPE_ADM, 403);
    }
}

