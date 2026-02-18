<?php

namespace App\Http\Controllers;

use App\Models\Simulado;
use App\Models\User;
use Illuminate\Http\Request;
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

        $simulados = $query->paginate(12)->withQueryString();

        return view('simulados.index', [
            'simulados' => $simulados,
            'searchTerm' => $term,
        ]);
    }
}

