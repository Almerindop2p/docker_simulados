<?php

namespace App\Providers;

use App\Support\HeaderNotifications;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer(['welcome', 'area_aluno', 'perfil', 'layouts.admin-panel'], function ($view) {
            $user = auth()->user();

            if (!$user) {
                return;
            }

            $view->with('headerNotifications', HeaderNotifications::buildFor($user));
        });
    }
}
