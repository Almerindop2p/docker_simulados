<?php

namespace App\Http\Controllers;

use App\Http\Requests\Feedback\StoreFeedbackTicketRequest;
use App\Models\AdminNotification;
use App\Models\FeedbackTicket;
use App\Models\SiteConfiguration;
use App\Models\User;
use App\Models\UserFeedbackPromptState;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FeedbackTicketController extends Controller
{
    private const FEEDBACK_PROMPT_COOLDOWN_SECONDS = 48 * 60 * 60;
    private const FEEDBACK_PROMPT_COOLDOWN_COOKIE = 'feedback_prompt_cooldown_until';

    public function store(StoreFeedbackTicketRequest $request): JsonResponse
    {
        if (!$this->isFeedbackFeedEnabled()) {
            return response()->json([
                'ok' => false,
                'message' => 'Canal de feedback esta inativo no momento.',
            ], 409);
        }

        $user = $request->user();

        if (($user?->user_type ?? null) === User::TYPE_ADM) {
            return response()->json([
                'ok' => false,
                'message' => 'Conta admin nao pode enviar feedback por este canal.',
            ], 403);
        }

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

        $now = now();
        $cooldownUntil = $now->copy()->addSeconds(self::FEEDBACK_PROMPT_COOLDOWN_SECONDS);

        if ($user && Schema::hasTable('user_feedback_prompt_states')) {
            UserFeedbackPromptState::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'cooldown_until' => $cooldownUntil,
                    'last_prompt_at' => $now,
                    'last_sent_at' => $now,
                ]
            );
        }

        $response = response()->json([
            'ok' => true,
            'message' => 'Feedback enviado com sucesso.',
            'ticket_id' => $ticket->id,
            'cooldown_until' => $cooldownUntil->timestamp,
        ]);

        return $response->cookie(
            self::FEEDBACK_PROMPT_COOLDOWN_COOKIE,
            (string) $cooldownUntil->timestamp,
            (int) ceil(self::FEEDBACK_PROMPT_COOLDOWN_SECONDS / 60),
            '/',
            null,
            $request->isSecure(),
            false,
            false,
            'lax'
        );
    }

    private function isFeedbackFeedEnabled(): bool
    {
        try {
            if (!Schema::hasTable('site_configurations') || !Schema::hasColumn('site_configurations', 'feedback_feed_enabled')) {
                return false;
            }

            return (bool) (SiteConfiguration::query()
                ->whereKey(SiteConfiguration::SINGLETON_ID)
                ->value('feedback_feed_enabled') ?? false);
        } catch (\Throwable) {
            return false;
        }
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
