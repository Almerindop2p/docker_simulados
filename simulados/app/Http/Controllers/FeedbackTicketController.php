<?php

namespace App\Http\Controllers;

use App\Http\Requests\Feedback\StoreFeedbackTicketRequest;
use App\Models\AdminNotification;
use App\Models\FeedbackTicket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
            'status' => FeedbackTicket::STATUS_ABERTO,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->notifyAdmins($ticket);

        return response()->json([
            'ok' => true,
            'message' => 'Feedback enviado com sucesso.',
            'ticket_id' => $ticket->id,
        ]);
    }

    private function notifyAdmins(FeedbackTicket $ticket): void
    {
        if (!Schema::hasTable('admin_notifications')) {
            return;
        }

        $admins = User::query()
            ->where('user_type', User::TYPE_ADM)
            ->get(['id']);

        foreach ($admins as $admin) {
            AdminNotification::query()->firstOrCreate(
                [
                    'user_id' => $admin->id,
                    'reference_key' => 'ticket-' . $ticket->id,
                ],
                [
                    'category' => AdminNotification::CATEGORY_TICKET,
                    'title' => 'Novo ticket de feedback #' . $ticket->id,
                    'message' => $this->buildTicketPreview($ticket),
                    'data' => [
                        'ticket_id' => $ticket->id,
                        'nome' => $ticket->nome,
                        'email' => $ticket->email,
                        'origem_rota' => $ticket->origem_rota,
                    ],
                    'read_at' => null,
                ]
            );
        }
    }

    private function buildTicketPreview(FeedbackTicket $ticket): string
    {
        $autor = $ticket->nome ?: $ticket->email ?: 'Usuario do site';
        $mensagem = Str::of((string) $ticket->mensagem)
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->limit(120, '...')
            ->toString();

        return "{$autor}: {$mensagem}";
    }
}
