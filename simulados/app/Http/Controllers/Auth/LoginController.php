<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MetricsConsentController;
use App\Models\PageVisitCounter;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\RouteMetric;
use App\Models\SiteConfiguration;
use App\Models\User;
use App\Models\UserMetricConsent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the login screen.
     */
    public function create(): View
    {
        $recaptcha = $this->resolveRecaptchaConfig();

        return view('auth.login', [
            'recaptchaEnabled' => $recaptcha['enabled'],
            'recaptchaSiteKey' => $recaptcha['site_key'],
        ]);
    }

    /**
     * Handle a login request.
     *
     * @throws ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse|JsonResponse
    {
        $recaptcha = $this->resolveRecaptchaConfig();
        if ($recaptcha['enabled']) {
            $token = trim((string) $request->input('g-recaptcha-response', ''));

            if (
                $token === ''
                || !is_string($recaptcha['secret_key'])
                || $recaptcha['secret_key'] === ''
                || !$this->verifyRecaptchaToken($token, $recaptcha['secret_key'], (string) $request->ip())
            ) {
                return $this->buildRecaptchaErrorResponse($request);
            }
        }

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
            User::TYPE_ADM => 'adm.inicio',
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

    private function resolveRecaptchaConfig(): array
    {
        try {
            if (
                !Schema::hasTable('site_configurations')
                || !Schema::hasColumn('site_configurations', 'recaptcha_enabled')
                || !Schema::hasColumn('site_configurations', 'recaptcha_site_key')
                || !Schema::hasColumn('site_configurations', 'recaptcha_secret_key')
            ) {
                return ['enabled' => false, 'site_key' => null, 'secret_key' => null];
            }

            $config = SiteConfiguration::query()
                ->select(['recaptcha_enabled', 'recaptcha_site_key', 'recaptcha_secret_key'])
                ->find(SiteConfiguration::SINGLETON_ID);

            $enabled = (bool) ($config?->recaptcha_enabled ?? false);
            $siteKey = trim((string) ($config?->recaptcha_site_key ?? ''));
            $secretKey = trim((string) ($config?->recaptcha_secret_key ?? ''));

            if ($siteKey === '' || $secretKey === '') {
                return ['enabled' => false, 'site_key' => null, 'secret_key' => null];
            }

            return ['enabled' => $enabled, 'site_key' => $siteKey, 'secret_key' => $secretKey];
        } catch (Throwable) {
            return ['enabled' => false, 'site_key' => null, 'secret_key' => null];
        }
    }

    private function verifyRecaptchaToken(string $token, string $secretKey, string $ip): bool
    {
        try {
            $response = Http::asForm()
                ->timeout(12)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secretKey,
                    'response' => $token,
                    'remoteip' => $ip,
                ]);

            if (!$response->ok()) {
                return false;
            }

            return (bool) $response->json('success', false);
        } catch (Throwable) {
            return false;
        }
    }

    private function buildRecaptchaErrorResponse(LoginRequest $request): RedirectResponse|JsonResponse
    {
        $message = 'Falha na verificacao de seguranca. Confirme o reCAPTCHA e tente novamente.';
        $errors = ['g-recaptcha-response' => [$message]];

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Existem campos invalidos.',
                'errors' => $errors,
            ], 422);
        }

        return back()
            ->withInput($request->except(['password']))
            ->withErrors($errors);
    }
}
