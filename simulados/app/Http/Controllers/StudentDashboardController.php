<?php

namespace App\Http\Controllers;

use App\Models\QuestaoResposta;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        abort_unless($user, 403);

        $respostasHoje = QuestaoResposta::query()
            ->where('user_id', $user->id)
            ->whereDate('respondida_em', now()->toDateString())
            ->count();

        $totalRespostas = QuestaoResposta::query()
            ->where('user_id', $user->id)
            ->count();

        return view('area_aluno', [
            'dashboardStats' => [
                'respostas_hoje' => $respostasHoje,
                'total_respostas' => $totalRespostas,
            ],
        ]);
    }
}

