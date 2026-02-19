<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MetricsConsentController;
use App\Models\PageVisitCounter;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\RouteMetric;
use App\Models\User;
use App\Models\UserMetricConsent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the login screen.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     *
     * @throws ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse|JsonResponse
    {
        $credentials = $request->safe()->only('email', 'password');
        $remember = (bool) $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Existem campos invalidos.',
                    'errors' => [
                        'email' => ['E-mail ou senha invalidos.'],
                    ],
                ]);
            }

            throw ValidationException::withMessages([
                'email' => 'E-mail ou senha invalidos.',
            ]);
        }

        $request->session()->regenerate();

        // Keep remember cookie only when explicitly requested.
        $guard = Auth::guard();
        if (!$remember && method_exists($guard, 'getRecallerName')) {
            Cookie::queue(Cookie::forget($guard->getRecallerName()));
        }

        $this->linkAnonymousConsentToUser($request);

        $redirectRoute = $this->resolveRedirectRoute();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Login realizado com sucesso.',
                'redirect' => route($redirectRoute),
            ]);
        }

        return redirect()->intended(route($redirectRoute));
    }

    /**
     * Log the user out of the application.
     */
    public function destroy(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Voce saiu da sua conta com sucesso.');
    }

    private function resolveRedirectRoute(): string
    {
        return match (Auth::user()?->user_type) {
            User::TYPE_ADM => 'adm.bancas.index',
            User::TYPE_USER_ASSINANTE => 'area_assinante',
            default => 'area_aluno',
        };
    }

    private function linkAnonymousConsentToUser(LoginRequest $request): void
    {
        $user = Auth::user();

        if (!$user || $user->user_type === User::TYPE_ADM) {
            return;
        }

        if (!$request->hasCookie(MetricsConsentController::CONSENT_COOKIE_NAME)) {
            return;
        }

        if (!Schema::hasTable('user_metric_consents')) {
            return;
        }

        $ipAddress = trim((string) $request->ip());
        $userAgent = trim((string) $request->userAgent());
        $anonymousVisitorId = trim((string) $request->cookie(MetricsConsentController::VISITOR_COOKIE_NAME));
        $hasIdentitySignals = $ipAddress !== '' || $userAgent !== '';
        $recentWindowStart = now()->subHours(48);
        $latestAnonymousMetric = null;

        if (Schema::hasTable('route_metrics')) {
            $baseQuery = RouteMetric::query()
                ->whereNull('user_id')
                ->where('consent_mode', 'cookie')
                ->where('captured_at', '>=', $recentWindowStart);

            if ($anonymousVisitorId !== '') {
                $baseQuery->where('anonymous_id', $anonymousVisitorId);
            } elseif ($hasIdentitySignals) {
                if ($ipAddress !== '') {
                    $baseQuery->where('ip_address', $ipAddress);
                }

                if ($userAgent !== '') {
                    $baseQuery->where('user_agent', $userAgent);
                }
            } else {
                $baseQuery = null;
            }

            if ($baseQuery) {
                $latestAnonymousMetric = (clone $baseQuery)
                    ->orderByDesc('id')
                    ->first([
                        'ip_address',
                        'user_agent',
                        'country',
                        'country_code',
                        'state',
                        'city',
                        'neighborhood',
                        'latitude',
                        'longitude',
                    ]);

                $baseQuery->update([
                    'user_id' => $user->id,
                    'visitor_key' => 'user:' . $user->id,
                    'consent_mode' => 'user',
                ]);
            }
        }

        if (Schema::hasTable('page_visit_counters') && $anonymousVisitorId !== '') {
            PageVisitCounter::query()
                ->whereNull('user_id')
                ->where('anonymous_id', $anonymousVisitorId)
                ->update([
                    'user_id' => $user->id,
                    'visitor_key' => 'user:' . $user->id,
                ]);
        }

        UserMetricConsent::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'is_granted' => true,
                'granted_at' => now(),
                'ip_address' => $ipAddress !== '' ? $ipAddress : ($latestAnonymousMetric?->ip_address),
                'user_agent' => $userAgent !== '' ? $userAgent : ($latestAnonymousMetric?->user_agent),
                'country' => $latestAnonymousMetric?->country,
                'country_code' => $latestAnonymousMetric?->country_code,
                'state' => $latestAnonymousMetric?->state,
                'city' => $latestAnonymousMetric?->city,
                'neighborhood' => $latestAnonymousMetric?->neighborhood,
                'latitude' => $latestAnonymousMetric?->latitude,
                'longitude' => $latestAnonymousMetric?->longitude,
            ]
        );
    }
}
