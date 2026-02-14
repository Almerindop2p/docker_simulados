<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Sistema de Simulados</title>
    <style>
        :root {
            color-scheme: light;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #111827;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 460px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 24px;
        }

        h1 {
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 24px;
        }

        p {
            margin-top: 0;
            margin-bottom: 20px;
            color: #4b5563;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            height: 42px;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        input:focus {
            outline: 2px solid #2563eb;
            outline-offset: 1px;
            border-color: #2563eb;
        }

        .button {
            width: 100%;
            height: 42px;
            border: 0;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            background: #1d4ed8;
            color: #ffffff;
        }

        .error {
            margin-top: -10px;
            margin-bottom: 12px;
            color: #b91c1c;
            font-size: 13px;
        }

        .status {
            margin-bottom: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #86efac;
            color: #166534;
            background: #f0fdf4;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Criar cadastro</h1>
        <p>Registre sua conta para acessar os simulados e questoes.</p>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('cadastro.store') }}">
            @csrf

            <label for="name">Nome completo</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name">
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password">Senha</label>
            <input id="password" name="password" type="password" required autocomplete="new-password">
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password_confirmation">Confirmar senha</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">

            <button class="button" type="submit">Cadastrar</button>
        </form>
    </main>
</body>
</html>
