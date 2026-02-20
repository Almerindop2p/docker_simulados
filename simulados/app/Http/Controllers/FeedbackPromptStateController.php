<?php

namespace App\Http\Controllers;

use App\Models\SiteConfiguration;
use App\Models\User;
use App\Models\UserFeedbackPromptState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class FeedbackPromptStateController extends Controller
{
    private const FEEDBACK_PROMPT_COOLDOWN_SECONDS = 48 * 60 * 60;
    private const FEEDBACK_PROMPT_COOLDOWN_COOKIE = 'feedback_prompt_cooldown_until';

    public function dismiss(Request $request): JsonResponse
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
                'message' => 'Conta admin nao utiliza este fluxo de feedback.',
            ], 403);
        }

        $now = now();
        $cooldownUntil = $now->copy()->addSeconds(self::FEEDBACK_PROMPT_COOLDOWN_SECONDS);

        if ($user && Schema::hasTable('user_feedback_prompt_states')) {
            UserFeedbackPromptState::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'cooldown_until' => $cooldownUntil,
                    'last_prompt_at' => $now,
                    'last_dismissed_at' => $now,
                ]
            );
        }

        $response = response()->json([
            'ok' => true,
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
}
