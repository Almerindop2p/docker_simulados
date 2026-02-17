<?php

namespace App\Support;

use App\Models\FeedbackTicket;
use App\Models\Questao;
use App\Models\QuestaoResposta;
use App\Models\User;
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
        $items = [];

        if (self::tableExists('feedback_tickets')) {
            $feedbackAbertos = FeedbackTicket::query()->where('status', 'aberto')->count();
            if ($feedbackAbertos > 0) {
                $items[] = [
                    'type' => 'warning',
                    'title' => 'Tickets pendentes',
                    'message' => "{$feedbackAbertos} feedback(s) aguardando analise.",
                    'url' => null,
                ];
            }
        }

        if (self::tableExists('questoes')) {
            $questoesSemInstituicao = Questao::query()->whereNull('instituicao_id')->count();
            if ($questoesSemInstituicao > 0) {
                $items[] = [
                    'type' => 'warning',
                    'title' => 'Questoes sem instituicao',
                    'message' => "{$questoesSemInstituicao} questao(oes) sem vinculacao de instituicao.",
                    'url' => route('adm.questoes.index'),
                ];
            }
        }

        if (self::tableExists('questao_respostas')) {
            $respostasHoje = QuestaoResposta::query()->whereDate('respondida_em', now()->toDateString())->count();
            $items[] = [
                'type' => 'info',
                'title' => 'Atividade hoje',
                'message' => "{$respostasHoje} resposta(s) registradas na plataforma hoje.",
                'url' => null,
            ];
        }

        if (self::tableExists('users')) {
            $assinantes = User::query()->where('user_type', User::TYPE_USER_ASSINANTE)->count();
            $items[] = [
                'type' => 'info',
                'title' => 'Base de assinantes',
                'message' => "{$assinantes} usuario(s) assinante(s) ativos.",
                'url' => null,
            ];
        }

        return self::finalize($items, 'Notificacoes ADM');
    }

    private static function buildForAssinante(User $user): array
    {
        $items = self::buildStudentCore($user);
        $items[] = [
            'type' => 'success',
            'title' => 'Plano assinante',
            'message' => 'Voce esta no modo assinante. Continue aproveitando os recursos premium.',
            'url' => route('area_assinante'),
        ];

        return self::finalize($items, 'Notificacoes do Assinante');
    }

    private static function buildForAluno(User $user): array
    {
        $items = self::buildStudentCore($user);

        if (self::tableExists('users')) {
            $items[] = [
                'type' => 'info',
                'title' => 'Dica beta',
                'message' => 'Envie feedback pelo icone verde para ajudar na evolucao da plataforma.',
                'url' => null,
            ];
        }

        return self::finalize($items, 'Notificacoes do Aluno');
    }

    private static function buildForColaborador(User $user): array
    {
        $items = [];

        if (self::tableExists('feedback_tickets')) {
            $feedbackAbertos = FeedbackTicket::query()->where('status', 'aberto')->count();
            $items[] = [
                'type' => $feedbackAbertos > 0 ? 'warning' : 'info',
                'title' => 'Fila de tickets',
                'message' => $feedbackAbertos > 0
                    ? "{$feedbackAbertos} ticket(s) aberto(s) aguardando triagem."
                    : 'Nenhum ticket pendente de triagem no momento.',
                'url' => null,
            ];
        }

        if (self::tableExists('questoes')) {
            $questoesSemInstituicao = Questao::query()->whereNull('instituicao_id')->count();
            if ($questoesSemInstituicao > 0) {
                $items[] = [
                    'type' => 'warning',
                    'title' => 'Pendencia de catalogacao',
                    'message' => "{$questoesSemInstituicao} questao(oes) sem instituicao vinculada.",
                    'url' => route('adm.questoes.index'),
                ];
            }
        }

        if (self::tableExists('questao_respostas')) {
            $respostasHoje = QuestaoResposta::query()->whereDate('respondida_em', now()->toDateString())->count();
            $items[] = [
                'type' => 'info',
                'title' => 'Movimento da plataforma',
                'message' => "{$respostasHoje} resposta(s) registradas hoje.",
                'url' => null,
            ];
        }

        return self::finalize($items, 'Notificacoes do Colaborador');
    }

    private static function buildForGenerico(User $user): array
    {
        $items = self::buildStudentCore($user);
        $tipo = str_replace('_', ' ', (string) $user->user_type);

        $items[] = [
            'type' => 'info',
            'title' => 'Perfil identificado',
            'message' => "Voce esta logado como {$tipo}.",
            'url' => null,
        ];

        return self::finalize($items, 'Notificacoes Gerais');
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
                'type' => $respostasHoje > 0 ? 'success' : 'info',
                'title' => 'Seu ritmo hoje',
                'message' => $respostasHoje > 0
                    ? "Voce respondeu {$respostasHoje} questao(oes) hoje."
                    : 'Nenhuma resposta registrada hoje. Bora praticar?',
                'url' => route('home'),
            ];

            $items[] = [
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
                    'type' => 'warning',
                    'title' => 'Feedback em andamento',
                    'message' => "Voce tem {$feedbackAbertos} ticket(s) com status aberto.",
                    'url' => null,
                ];
            }
        }

        return $items;
    }

    private static function finalize(array $items, string $title): array
    {
        $badgeCount = collect($items)->whereIn('type', ['warning', 'danger'])->count();

        return [
            'title' => $title,
            'items' => $items,
            'count' => $badgeCount,
        ];
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
