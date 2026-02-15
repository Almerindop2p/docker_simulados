<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CadastroRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CadastroController extends Controller
{
    /**
     * Show the registration screen.
     */
    public function create(): View
    {
        return view('auth.cadastro');
    }

    /**
     * Handle a registration request.
     */
    public function store(CadastroRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

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
}
