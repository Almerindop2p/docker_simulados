<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CadastroRequest;
use App\Models\SiteConfiguration;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;
use Illuminate\View\View;

class CadastroController extends Controller
{
    /**
     * Show the registration screen.
     */
    public function create(): View
    {
        $recaptcha = $this->resolveRecaptchaConfig();

        return view('auth.cadastro', [
            'recaptchaEnabled' => $recaptcha['enabled'],
            'recaptchaSiteKey' => $recaptcha['site_key'],
        ]);
    }

    /**
     * Handle a registration request.
     */
    public function store(CadastroRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
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

        $user = DB::transaction(function () use ($validated): User {
            return User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'user_type' => User::TYPE_USER,
            ]);
        });

        event(new Registered($user));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cadastro realizado com sucesso.',
                'redirect' => route('login'),
            ], 201);
        }

        return redirect()->route('login')->with('status', 'Cadastro realizado com sucesso. Agora faca seu login.');
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

    private function buildRecaptchaErrorResponse(CadastroRequest $request): RedirectResponse|JsonResponse
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
            ->withInput($request->except(['password', 'password_confirmation']))
            ->withErrors($errors);
    }
}
