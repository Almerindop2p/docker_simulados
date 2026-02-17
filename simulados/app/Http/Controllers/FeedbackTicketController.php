<?php

namespace App\Http\Controllers;

use App\Http\Requests\Feedback\StoreFeedbackTicketRequest;
use App\Models\FeedbackTicket;
use Illuminate\Http\JsonResponse;

class FeedbackTicketController extends Controller
{
    public function store(StoreFeedbackTicketRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $ticket = FeedbackTicket::query()->create([
            'user_id' => $user?->id,
            'nome' => $user?->name ?? ($data['nome'] ?? null),
            'email' => $user?->email ?? ($data['email'] ?? null),
            'mensagem' => $data['mensagem'],
            'origem_rota' => $data['origem_rota'] ?? $request->route()?->getName(),
            'pagina_url' => $data['pagina_url'] ?? $request->headers->get('referer'),
            'status' => 'aberto',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Feedback enviado com sucesso.',
            'ticket_id' => $ticket->id,
        ]);
    }
}

