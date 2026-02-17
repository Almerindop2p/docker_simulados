<?php

namespace App\Support;

use App\Models\AdminNotification;
use App\Models\FeedbackTicket;
use App\Models\QuestaoResposta;
use App\Models\User;
use App\Models\UserNotificationRead;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class HeaderNotifications
{
    public static function buildFor(User $user): array
    {
        return match ($user->user_type) {
            User::TYPE_ADM => self::buildForAdmin($user),
            User::TYPE_USER_ASSINANTE => self::buildForAssinante($user),
            User::TYPE_USER => self::buildForAluno($user),
            User::TYPE_COLABORADOR => self::buildForColaborador($user),
            default => self::buildForGenerico($user),
        };
    }

    private static function buildForAdmin(User $user): array
    {
        if (!self::tableExists('admin_notifications')) {
            return self::fallbackAdminSummary();
        }

        self::ensureDailySystemNotification($user);

        $query = AdminNotification::query()->where('user_id', $user->id);
        $unreadCount = (clone $query)->whereNull('read_at')->count();

        $items = $query
            ->latest()
            ->limit(12)
            ->get()
            ->map(fn (AdminNotification $notification) => self::mapAdminNotification($notification))
            ->all();

        return [
            'title' => 'Notificacoes ADM',
            'items' => $items,
            'count' => $unreadCount,
        ];
    }

    private static function mapAdminNotification(AdminNotification $notification): array
    {
        $isTicket = $notification->category === AdminNotification::CATEGORY_TICKET;
        $isRead = $notification->read_at !== null;
        $baseType = $isTicket && !$isRead ? 'warning' : 'info';

        return [
            'id' => $notification->id,
            'type' => $baseType,
            'title' => $notification->title,
            'message' => $notification->message,
            'read' => $isRead,
            'action' => $isTicket ? 'link' : 'modal',
            'url' => $isTicket ? route('adm.notifications.open', $notification) : null,
            'mark_read_url' => route('adm.notifications.read', $notification),
            'modal_title' => $notification->title,
            'modal_message' => $notification->message,
            'category' => $notification->category,
            'created_at' => self::formatRelativeTime($notification->created_at),
        ];
    }

    private static function ensureDailySystemNotification(User $user): void
    {
        $today = now()->toDateString();
        $referenceKey = 'daily-system-' . $today;

        $alreadyExists = AdminNotification::query()
            ->where('user_id', $user->id)
            ->where('reference_key', $referenceKey)
            ->exists();

        if ($alreadyExists) {
            return;
        }

        $ticketsAbertos = self::tableExists('feedback_tickets')
            ? FeedbackTicket::query()->where('status', 'aberto')->count()
            : 0;

        $respostasHoje = self::tableExists('questao_respostas')
            ? QuestaoResposta::query()->whereDate('respondida_em', now()->toDateString())->count()
            : 0;

        $assinantes = self::tableExists('users')
            ? User::query()->where('user_type', User::TYPE_USER_ASSINANTE)->count()
            : 0;

        $message = 'Resumo do dia: '
            . $ticketsAbertos . ' ticket(s) aberto(s), '
            . $respostasHoje . ' resposta(s) registradas hoje, '
            . $assinantes . ' assinante(s) ativos.';

        AdminNotification::query()->create([
            'user_id' => $user->id,
            'category' => AdminNotification::CATEGORY_SYSTEM,
            'title' => 'Mensagem padrao do sistema',
            'message' => $message,
            'data' => [
                'source' => 'daily-summary',
                'date' => $today,
            ],
            'reference_key' => $referenceKey,
            'read_at' => null,
        ]);
    }

    private static function fallbackAdminSummary(): array
    {
        $items = [];

        if (self::tableExists('feedback_tickets')) {
            $feedbackAbertos = FeedbackTicket::query()->where('status', 'aberto')->count();
            if ($feedbackAbertos > 0) {
                $items[] = [
                    'type' => 'warning',
                    'title' => 'Tickets pendentes',
                    'message' => $feedbackAbertos . ' feedback(s) aguardando analise.',
                    'url' => null,
                ];
            }
        }

        return self::finalize($items, 'Notificacoes ADM');
    }

    private static function buildForAssinante(User $user): array
    {
        $readKeyMap = self::readKeyMapForUser($user);
        $items = self::buildStudentCore($user);
        $items[] = [
            'key' => self::notificationKey('assinante-plano', [$user->id, now()->toDateString()]),
            'type' => 'success',
            'title' => 'Plano assinante',
            'message' => 'Voce esta no modo assinante. Continue aproveitando os recursos premium.',
            'url' => route('area_assinante'),
        ];

        return self::finalize($items, 'Notificacoes do Assinante', $readKeyMap);
    }

    private static function buildForAluno(User $user): array
    {
        $readKeyMap = self::readKeyMapForUser($user);
        $items = self::buildStudentCore($user);

        if (self::tableExists('users')) {
            $items[] = [
                'key' => self::notificationKey('aluno-dica-beta', [$user->id, now()->toDateString()]),
                'type' => 'info',
                'title' => 'Dica beta',
                'message' => 'Envie feedback pelo icone verde para ajudar na evolucao da plataforma.',
                'url' => null,
            ];
        }

        return self::finalize($items, 'Notificacoes do Aluno', $readKeyMap);
    }

    private static function buildForColaborador(User $user): array
    {
        $readKeyMap = self::readKeyMapForUser($user);
        $items = [];

        if (self::tableExists('feedback_tickets')) {
            $feedbackAbertos = FeedbackTicket::query()->where('status', 'aberto')->count();
            $items[] = [
                'key' => self::notificationKey('colaborador-fila-tickets', [$user->id, now()->toDateString()]),
                'type' => $feedbackAbertos > 0 ? 'warning' : 'info',
                'title' => 'Fila de tickets',
                'message' => $feedbackAbertos > 0
                    ? 'Ha ticket(s) aberto(s) aguardando triagem.'
                    : 'Nenhum ticket pendente de triagem no momento.',
                'url' => null,
            ];
        }

        if (self::tableExists('questao_respostas')) {
            $respostasHoje = QuestaoResposta::query()->whereDate('respondida_em', now()->toDateString())->count();
            $items[] = [
                'key' => self::notificationKey('colaborador-movimento-plataforma', [$user->id, now()->toDateString(), $respostasHoje]),
                'type' => 'info',
                'title' => 'Movimento da plataforma',
                'message' => $respostasHoje . ' resposta(s) registradas hoje.',
                'url' => null,
            ];
        }

        return self::finalize($items, 'Notificacoes do Colaborador', $readKeyMap);
    }

    private static function buildForGenerico(User $user): array
    {
        $readKeyMap = self::readKeyMapForUser($user);
        $items = self::buildStudentCore($user);
        $tipo = str_replace('_', ' ', (string) $user->user_type);

        $items[] = [
            'key' => self::notificationKey('perfil-identificado', [$user->id, now()->toDateString(), $user->user_type]),
            'type' => 'info',
            'title' => 'Perfil identificado',
            'message' => "Voce esta logado como {$tipo}.",
            'url' => null,
        ];

        return self::finalize($items, 'Notificacoes Gerais', $readKeyMap);
    }

    private static function buildStudentCore(User $user): array
    {
        $items = [];

        if (self::tableExists('questao_respostas')) {
            $respostasHoje = QuestaoResposta::query()
                ->where('user_id', $user->id)
                ->whereDate('respondida_em', now()->toDateString())
                ->count();

            $totalRespostas = QuestaoResposta::query()
                ->where('user_id', $user->id)
                ->count();

            $items[] = [
                'key' => self::notificationKey('aluno-ritmo-hoje', [$user->id, now()->toDateString(), $respostasHoje]),
                'type' => $respostasHoje > 0 ? 'success' : 'info',
                'title' => 'Seu ritmo hoje',
                'message' => $respostasHoje > 0
                    ? "Voce respondeu {$respostasHoje} questao(oes) hoje."
                    : 'Nenhuma resposta registrada hoje. Bora praticar?',
                'url' => route('home'),
            ];

            $items[] = [
                'key' => self::notificationKey('aluno-historico-pratica', [$user->id, now()->toDateString(), $totalRespostas]),
                'type' => 'info',
                'title' => 'Historico de pratica',
                'message' => "Total acumulado: {$totalRespostas} resposta(s).",
                'url' => route('home'),
            ];
        }

        if (self::tableExists('feedback_tickets')) {
            $feedbackAbertos = FeedbackTicket::query()
                ->where('user_id', $user->id)
                ->where('status', 'aberto')
                ->count();

            if ($feedbackAbertos > 0) {
                $items[] = [
                    'key' => self::notificationKey('aluno-feedback-andamento', [$user->id, now()->toDateString()]),
                    'type' => 'warning',
                    'title' => 'Feedback em andamento',
                    'message' => 'Voce possui ticket(s) com status aberto.',
                    'url' => null,
                ];
            }
        }

        return $items;
    }

    private static function finalize(array $items, string $title, array $readKeyMap = []): array
    {
        $normalizedItems = collect($items)
            ->map(fn (array $item) => self::normalizeItem($item, $readKeyMap))
            ->values()
            ->all();

        $unreadCount = collect($normalizedItems)->where('read', false)->count();

        return [
            'title' => $title,
            'items' => $normalizedItems,
            'count' => $unreadCount,
        ];
    }

    private static function normalizeItem(array $item, array $readKeyMap): array
    {
        $notificationKey = isset($item['key']) ? (string) $item['key'] : null;
        $isRead = $notificationKey
            ? isset($readKeyMap[$notificationKey])
            : (bool) ($item['read'] ?? false);

        $action = $item['action'] ?? (!empty($item['url']) ? 'link' : 'modal');

        $item['id'] = $item['id'] ?? $notificationKey;
        $item['read'] = $isRead;
        $item['action'] = $action;
        $item['modal_title'] = $item['modal_title'] ?? ($item['title'] ?? 'Notificacao');
        $item['modal_message'] = $item['modal_message'] ?? ($item['message'] ?? '');

        if ($notificationKey && !isset($item['mark_read_url'])) {
            $item['mark_read_url'] = route('notifications.read', ['notificationKey' => $notificationKey]);
        }

        $item['created_at'] = self::formatRelativeTime(
            $item['created_at'] ?? self::inferFallbackCreatedAt($notificationKey)
        ) ?? '1s';

        return $item;
    }

    private static function inferFallbackCreatedAt(?string $notificationKey): \DateTimeInterface
    {
        if ($notificationKey && preg_match('/\b(\d{4}-\d{2}-\d{2})\b/', $notificationKey, $matches)) {
            try {
                return Carbon::createFromFormat('Y-m-d', $matches[1])->startOfDay();
            } catch (\Throwable) {
                return now();
            }
        }

        return now();
    }

    private static function formatRelativeTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            $date = $value instanceof \DateTimeInterface
                ? Carbon::instance($value)
                : Carbon::parse((string) $value);
        } catch (\Throwable) {
            return null;
        }

        $elapsed = max(1, now()->timestamp - $date->timestamp);

        if ($elapsed < 60) {
            return $elapsed . 's';
        }

        if ($elapsed < 3600) {
            return intdiv($elapsed, 60) . 'min';
        }

        if ($elapsed < 86400) {
            return intdiv($elapsed, 3600) . 'h';
        }

        if ($elapsed < 2592000) {
            return intdiv($elapsed, 86400) . 'd';
        }

        if ($elapsed < 31536000) {
            return intdiv($elapsed, 2592000) . 'mes';
        }

        return intdiv($elapsed, 31536000) . 'a';
    }

    private static function readKeyMapForUser(User $user): array
    {
        if (!self::tableExists('user_notification_reads')) {
            return [];
        }

        return UserNotificationRead::query()
            ->where('user_id', $user->id)
            ->pluck('notification_key')
            ->mapWithKeys(fn ($key) => [(string) $key => true])
            ->all();
    }

    private static function notificationKey(string $prefix, array $parts = []): string
    {
        $segments = array_merge([$prefix], $parts);

        $normalized = collect($segments)
            ->map(function ($segment) {
                $value = strtolower((string) $segment);
                $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
                $value = trim($value, '-');

                return $value !== '' ? $value : 'item';
            })
            ->filter()
            ->values()
            ->all();

        $key = implode('-', $normalized);

        return substr($key, 0, 180);
    }

    private static function tableExists(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }
}
