<?php

namespace App\Providers;

use App\Models\AdPost;
use App\Models\UserMetricConsent;
use App\Models\SiteConfiguration;
use App\Support\HeaderNotifications;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
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
        $adsenseHorizontalCode = null;
        $adsenseVerticalCode = null;
        $adsenseFormatCodes = [];
        $metricsConsentGranted = false;
        $metricsConsentCookie = 'lgpd_metrics_consent';

        try {
            if (Schema::hasTable('site_configurations')) {
                $config = SiteConfiguration::query()
                    ->select(['adsense_enabled', 'adsense_head_script'])
                    ->find(SiteConfiguration::SINGLETON_ID);

                $adsenseEnabled = (bool) ($config?->adsense_enabled ?? false);

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
            $adsenseHorizontalCode = null;
            $adsenseVerticalCode = null;
            $adsenseFormatCodes = [];
        }

        try {
            $user = auth()->user();

            if ($user && Schema::hasTable('user_metric_consents')) {
                $metricsConsentGranted = UserMetricConsent::query()
                    ->where('user_id', $user->id)
                    ->where('is_granted', true)
                    ->exists();
            } else {
                $metricsConsentGranted = request()->cookie($metricsConsentCookie) === 'granted';
            }
        } catch (Throwable) {
            $metricsConsentGranted = false;
        }

        View::share('adsenseHeadScript', $adsenseHeadScript);
        View::share('adsenseEnabled', $adsenseEnabled);
        View::share('adsenseHorizontalCode', $adsenseHorizontalCode);
        View::share('adsenseVerticalCode', $adsenseVerticalCode);
        View::share('adsenseFormatCodes', $adsenseFormatCodes);
        View::share('metricsConsentGranted', $metricsConsentGranted);
        View::share('metricsCurrentRouteName', request()->route()?->getName());

        View::composer(['welcome', 'area_aluno', 'perfil', 'layouts.admin-panel'], function ($view) {
            $user = auth()->user();

            if (!$user) {
                return;
            }

            $view->with('headerNotifications', HeaderNotifications::buildFor($user));
        });
    }
}
