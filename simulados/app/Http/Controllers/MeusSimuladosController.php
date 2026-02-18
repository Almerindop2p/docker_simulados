<?php

namespace App\Http\Controllers;

use App\Models\SimuladoTentativa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MeusSimuladosController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user, 403);

        $tentativas = SimuladoTentativa::query()
            ->where('user_id', $user->id)
            ->with(['simulado:id,name'])
            ->orderByRaw("CASE WHEN status = 'aberto' THEN 0 ELSE 1 END")
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('simulados.my-index', [
            'tentativas' => $tentativas,
        ]);
    }
}

