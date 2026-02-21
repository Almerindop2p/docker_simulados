<?php

namespace App\Providers;

use App\Models\AdPost;
use App\Models\MetaKeyword;
use App\Models\User;
use App\Models\UserMetricConsent;
use App\Models\SiteConfiguration;
use App\Models\UserFeedbackPromptState;
use App\Support\HeaderNotifications;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    private const USER_CONSENT_VALID_DAYS = 7;
    private const FEEDBACK_PROMPT_INITIAL_DELAY_MS = 60 * 60 * 1000;
    private const FEEDBACK_PROMPT_COOLDOWN_SECONDS = 48 * 60 * 60;
    private const FEEDBACK_PROMPT_COOLDOWN_COOKIE = 'feedback_prompt_cooldown_until';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $adsenseHeadScript = null;
        $adsenseEnabled = false;
        $feedbackFeedEnabled = true;
        $adsenseHorizontalCode = null;
        $adsenseVerticalCode = null;
        $adsenseFormatCodes = [];
        $metricsConsentGranted = false;
        $metricsConsentEnabled = true;
        $metricsConsentCookie = 'lgpd_metrics_consent';
        $feedbackPromptInitialDelayMs = self::FEEDBACK_PROMPT_INITIAL_DELAY_MS;
        $feedbackPromptCooldownSeconds = self::FEEDBACK_PROMPT_COOLDOWN_SECONDS;
        $feedbackPromptCooldownUntilTs = 0;
        $metaKeywordsContent = '';

        try {
            if (Schema::hasTable('site_configurations')) {
                $siteConfigColumns = ['adsense_enabled', 'adsense_head_script'];
                if (Schema::hasColumn('site_configurations', 'feedback_feed_enabled')) {
                    $siteConfigColumns[] = 'feedback_feed_enabled';
                }

                $config = SiteConfiguration::query()
                    ->select($siteConfigColumns)
                    ->find(SiteConfiguration::SINGLETON_ID);

                $adsenseEnabled = (bool) ($config?->adsense_enabled ?? false);
                $feedbackFeedEnabled = (bool) ($config?->feedback_feed_enabled ?? true);

                if ($adsenseEnabled && filled($config->adsense_head_script)) {
                    $adsenseHeadScript = (string) $config->adsense_head_script;
                }

                if ($adsenseEnabled && Schema::hasTable('ad_posts')) {
                    $activeAds = AdPost::query()
                        ->where('is_active', true)
                        ->whereNotNull('embed_code')
                        ->whereNotNull('format')
                        ->orderByDesc('id')
                        ->get(['format', 'embed_code']);

                    foreach ($activeAds as $adPost) {
                        $format = strtolower(trim((string) $adPost->format));

                        if ($format !== '' && !isset($adsenseFormatCodes[$format])) {
                            $adsenseFormatCodes[$format] = $adPost->embed_code;
                        }
                    }

                    $adsenseHorizontalCode = $adsenseFormatCodes[AdPost::FORMAT_HORIZONTAL] ?? null;
                    $adsenseVerticalCode = $adsenseFormatCodes[AdPost::FORMAT_VERTICAL] ?? null;
                }
            }
        } catch (Throwable) {
            $adsenseHeadScript = null;
            $adsenseEnabled = false;
            $feedbackFeedEnabled = true;
            $adsenseHorizontalCode = null;
            $adsenseVerticalCode = null;
            $adsenseFormatCodes = [];
        }

        try {
            if (Schema::hasTable('meta_keywords')) {
                $metaKeywordsContent = MetaKeyword::query()
                    ->orderBy('keyword')
                    ->pluck('keyword')
                    ->map(function ($keyword) {
                        return trim((string) $keyword);
                    })
                    ->filter(function (string $keyword) {
                        return $keyword !== '';
                    })
                    ->unique(function (string $keyword) {
                        return mb_strtolower($keyword);
                    })
                    ->values()
                    ->implode(', ');
            }
        } catch (Throwable) {
            $metaKeywordsContent = '';
        }

        try {
            $user = auth()->user();
            $metricsConsentEnabled = !($user && $user->user_type === User::TYPE_ADM);

            if (!$metricsConsentEnabled) {
                $metricsConsentGranted = false;
            } elseif ($user && Schema::hasTable('user_metric_consents')) {
                $metricsConsentGranted = UserMetricConsent::query()
                    ->where('user_id', $user->id)
                    ->where('is_granted', true)
                    ->where('granted_at', '>=', now()->subDays(self::USER_CONSENT_VALID_DAYS))
                    ->exists();
            } else {
                $metricsConsentGranted = request()->hasCookie($metricsConsentCookie);
            }
        } catch (Throwable) {
            $metricsConsentGranted = false;
            $metricsConsentEnabled = false;
        }

        try {
            $user = auth()->user();
            $cookieCooldownUntil = (int) request()->cookie(self::FEEDBACK_PROMPT_COOLDOWN_COOKIE, 0);
            $nowTs = now()->timestamp;

            if (!$feedbackFeedEnabled) {
                $feedbackPromptCooldownUntilTs = 0;
            } elseif ($user && $user->user_type !== User::TYPE_ADM) {
                $dbCooldownUntil = 0;

                if (Schema::hasTable('user_feedback_prompt_states')) {
                    $state = UserFeedbackPromptState::query()
                        ->where('user_id', $user->id)
                        ->first();

                    $dbCooldownUntil = (int) ($state?->cooldown_until?->timestamp ?? 0);

                    if ($cookieCooldownUntil > $nowTs && $cookieCooldownUntil > $dbCooldownUntil) {
                        UserFeedbackPromptState::query()->updateOrCreate(
                            ['user_id' => $user->id],
                            ['cooldown_until' => now()->setTimestamp($cookieCooldownUntil)]
                        );
                        $dbCooldownUntil = $cookieCooldownUntil;
                    }
                }

                $feedbackPromptCooldownUntilTs = max($cookieCooldownUntil, $dbCooldownUntil);
            } else {
                $feedbackPromptCooldownUntilTs = max(0, $cookieCooldownUntil);
            }
        } catch (Throwable) {
            $feedbackPromptCooldownUntilTs = 0;
        }

        View::share('adsenseHeadScript', $adsenseHeadScript);
        View::share('adsenseEnabled', $adsenseEnabled);
        View::share('feedbackFeedEnabled', $feedbackFeedEnabled);
        View::share('adsenseHorizontalCode', $adsenseHorizontalCode);
        View::share('adsenseVerticalCode', $adsenseVerticalCode);
        View::share('adsenseFormatCodes', $adsenseFormatCodes);
        View::share('metricsConsentEnabled', $metricsConsentEnabled);
        View::share('metricsConsentGranted', $metricsConsentGranted);
        View::share('metricsCurrentRouteName', request()->route()?->getName());
        View::share('feedbackPromptInitialDelayMs', $feedbackPromptInitialDelayMs);
        View::share('feedbackPromptCooldownSeconds', $feedbackPromptCooldownSeconds);
        View::share('feedbackPromptCooldownUntilTs', $feedbackPromptCooldownUntilTs);
        View::share('metaKeywordsContent', $metaKeywordsContent);

        View::composer(['welcome', 'area_aluno', 'perfil', 'layouts.admin-panel'], function ($view) {
            $user = auth()->user();

            if (!$user) {
                return;
            }

            $view->with('headerNotifications', HeaderNotifications::buildFor($user));
        });
    }
}
