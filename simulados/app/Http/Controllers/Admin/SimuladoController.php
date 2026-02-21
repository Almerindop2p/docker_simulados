<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSimuladoRequest;
use App\Models\Simulado;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SimuladoController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.simulados.index', [
            'simulados' => Simulado::query()->latest()->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.simulados.create');
    }

    public function edit(Request $request, Simulado $simulado): View
    {
        $this->ensureAdmin($request);

        return view('adm.simulados.edit', [
            'simulado' => $simulado,
        ]);
    }

    public function store(StoreSimuladoRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['imagem_destaque']);
        $data['descricao'] = trim((string) ($data['descricao'] ?? '')) ?: null;

        if ($request->hasFile('imagem_destaque')) {
            $data['imagem_destaque_path'] = $this->storeImagemDestaque($request->file('imagem_destaque'));
        }

        Simulado::query()->create($data);

        return redirect()
            ->route('adm.simulados.index')
            ->with('status', 'Simulado cadastrado com sucesso.');
    }

    public function update(StoreSimuladoRequest $request, Simulado $simulado): RedirectResponse
    {
        $data = $request->safe()->except(['imagem_destaque']);
        $data['descricao'] = trim((string) ($data['descricao'] ?? '')) ?: null;

        if ($request->hasFile('imagem_destaque')) {
            $newPath = $this->storeImagemDestaque($request->file('imagem_destaque'));

            if ($simulado->imagem_destaque_path) {
                Storage::disk('public')->delete($simulado->imagem_destaque_path);
            }

            $data['imagem_destaque_path'] = $newPath;
        }

        $simulado->update($data);

        return redirect()
            ->route('adm.simulados.index')
            ->with('status', 'Simulado atualizado com sucesso.');
    }

    public function destroy(Request $request, Simulado $simulado): RedirectResponse
    {
        $this->ensureAdmin($request);
        $imagemPath = $simulado->imagem_destaque_path;

        try {
            $simulado->delete();
        } catch (QueryException $exception) {
            return redirect()
                ->route('adm.simulados.index')
                ->with('status', 'Nao foi possivel excluir o simulado porque ele possui questoes vinculadas.');
        }

        if ($imagemPath) {
            Storage::disk('public')->delete($imagemPath);
        }

        return redirect()
            ->route('adm.simulados.index')
            ->with('status', 'Simulado removido com sucesso.');
    }

    public function checkName(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $name = trim((string) $request->query('name', ''));
        $ignoreId = (int) $request->query('ignore_id', 0);

        if ($name === '') {
            return response()->json([
                'exists' => false,
                'message' => 'Informe o nome do simulado para validar.',
            ]);
        }

        $query = Simulado::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)]);

        if ($ignoreId > 0) {
            $query->where('id', '!=', $ignoreId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists
                ? 'Esse simulado ja existe. Escolha outro nome.'
                : 'Nome de simulado disponivel.',
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

        $query = Simulado::query()->where($field, $value);

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

    private function storeImagemDestaque(UploadedFile $arquivo): string
    {
        return $arquivo->store('simulados', 'public');
    }
}
