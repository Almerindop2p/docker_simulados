<?php

namespace App\Providers;

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

        try {
            if (Schema::hasTable('site_configurations')) {
                $config = SiteConfiguration::query()
                    ->select(['adsense_enabled', 'adsense_head_script'])
                    ->find(SiteConfiguration::SINGLETON_ID);

                if ($config?->adsense_enabled && filled($config->adsense_head_script)) {
                    $adsenseHeadScript = (string) $config->adsense_head_script;
                }
            }
        } catch (Throwable) {
            $adsenseHeadScript = null;
        }

        View::share('adsenseHeadScript', $adsenseHeadScript);

        View::composer(['welcome', 'area_aluno', 'perfil', 'layouts.admin-panel'], function ($view) {
            $user = auth()->user();

            if (!$user) {
                return;
            }

            $view->with('headerNotifications', HeaderNotifications::buildFor($user));
        });
    }
}
