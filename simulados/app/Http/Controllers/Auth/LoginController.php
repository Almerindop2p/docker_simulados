<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
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
}