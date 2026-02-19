<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdPostRequest;
use App\Models\AdPost;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdPostController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.anuncios.index', [
            'anuncios' => AdPost::query()->latest()->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.anuncios.create');
    }

    public function edit(Request $request, AdPost $anuncio): View
    {
        $this->ensureAdmin($request);

        return view('adm.anuncios.edit', [
            'anuncio' => $anuncio,
        ]);
    }

    public function store(StoreAdPostRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['embed_code'] = $data['embed_code'] === '' ? null : $data['embed_code'];

        AdPost::query()->create($data);

        return redirect()
            ->route('adm.anuncios.index')
            ->with('status', 'Anuncio cadastrado com sucesso.');
    }

    public function update(StoreAdPostRequest $request, AdPost $anuncio): RedirectResponse
    {
        $data = $request->validated();
        $data['embed_code'] = $data['embed_code'] === '' ? null : $data['embed_code'];

        $anuncio->update($data);

        return redirect()
            ->route('adm.anuncios.index')
            ->with('status', 'Anuncio atualizado com sucesso.');
    }

    public function destroy(Request $request, AdPost $anuncio): RedirectResponse
    {
        $this->ensureAdmin($request);

        $anuncio->delete();

        return redirect()
            ->route('adm.anuncios.index')
            ->with('status', 'Anuncio removido com sucesso.');
    }

    public function checkField(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $field = (string) $request->query('field', 'title');
        $value = trim((string) $request->query('value', ''));
        $ignoreId = (int) $request->query('ignore_id', 0);

        if (!in_array($field, ['title', 'slug'], true)) {
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

        $query = AdPost::query()->where($field, $value);

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
