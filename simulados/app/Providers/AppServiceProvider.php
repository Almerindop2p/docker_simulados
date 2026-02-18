<?php

namespace App\Providers;

use App\Models\AdPost;
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
                    $horizontalAd = AdPost::query()
                        ->where('format', AdPost::FORMAT_HORIZONTAL)
                        ->where('is_active', true)
                        ->whereNotNull('embed_code')
                        ->latest('id')
                        ->first();
                    $verticalAd = AdPost::query()
                        ->where('format', AdPost::FORMAT_VERTICAL)
                        ->where('is_active', true)
                        ->whereNotNull('embed_code')
                        ->latest('id')
                        ->first();

                    $adsenseHorizontalCode = $horizontalAd?->embed_code;
                    $adsenseVerticalCode = $verticalAd?->embed_code;
                }
            }
        } catch (Throwable) {
            $adsenseHeadScript = null;
            $adsenseEnabled = false;
            $adsenseHorizontalCode = null;
            $adsenseVerticalCode = null;
        }

        View::share('adsenseHeadScript', $adsenseHeadScript);
        View::share('adsenseEnabled', $adsenseEnabled);
        View::share('adsenseHorizontalCode', $adsenseHorizontalCode);
        View::share('adsenseVerticalCode', $adsenseVerticalCode);

        View::composer(['welcome', 'area_aluno', 'perfil', 'layouts.admin-panel'], function ($view) {
            $user = auth()->user();

            if (!$user) {
                return;
            }

            $view->with('headerNotifications', HeaderNotifications::buildFor($user));
        });
    }
}
