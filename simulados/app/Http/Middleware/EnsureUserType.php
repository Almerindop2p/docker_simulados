<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserType
{
    public function handle(Request $request, Closure $next, string ...$allowedTypes): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Sempre consulta o tipo mais recente no banco para refletir mudancas de privilegio.
        $freshUser = $user->fresh();

        if (!$freshUser) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        Auth::setUser($freshUser);

        if (in_array((string) $freshUser->user_type, $allowedTypes, true)) {
            return $next($request);
        }

        return $this->redirectToProfileRoute($freshUser);
    }

    private function redirectToProfileRoute(User $user): RedirectResponse
    {
        return match ($user->user_type) {
            User::TYPE_ADM => redirect()->route('adm.bancas.index'),
            User::TYPE_USER => redirect()->route('area_aluno'),
            User::TYPE_USER_ASSINANTE => redirect()->route('area_assinante'),
            default => redirect()->route('home'),
        };
    }
}