<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | Simulados e Questoes</title>
    @include('partials.edu-theme-head')
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Plus Jakarta Sans", "Manrope", "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 12% 12%, #ffffff 0%, rgba(255, 255, 255, 0) 45%),
                radial-gradient(circle at 88% 88%, #e9f1ff 0%, rgba(233, 241, 255, 0) 42%),
                linear-gradient(180deg, var(--bg-main), var(--bg-soft));
            color: var(--text-main);
            min-height: 100vh;
            padding: 20px 16px 28px;
        }

        .container {
            max-width: 1080px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            color: var(--text-main);
            text-decoration: none;
        }

        .logo-badge {
            width: 34px;
            height: 34px;
            border-radius: 11px;
            display: grid;
            place-items: center;
            font-size: 14px;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, #1f5fe0, #5a8cff);
            box-shadow: 0 6px 16px rgba(31, 95, 224, 0.35);
        }

        .header-link {
            color: var(--brand-dark);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            border-radius: 10px;
            padding: 8px 10px;
        }

        .header-link:hover {
            background: rgba(31, 95, 224, 0.08);
        }

        .layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .panel,
        .form-panel {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
        }

        .panel {
            padding: 24px;
            background: linear-gradient(165deg, #ffffff 0%, #f8fbff 48%, #eef4ff 100%);
        }

        .form-panel {
            padding: 22px;
        }

        .kicker {
            margin: 0 0 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #d4e4ff;
            border-radius: 999px;
            padding: 6px 12px;
            color: #1e4caa;
            background: #f2f7ff;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .panel h1 {
            margin: 0 0 12px;
            font-size: clamp(1.5rem, 2.2vw, 2.1rem);
            line-height: 1.2;
        }

        .panel p {
            margin: 0;
            color: var(--text-soft);
            line-height: 1.6;
        }

        .form-title {
            margin: 0 0 6px;
            font-size: 1.5rem;
        }

        .form-subtitle {
            margin: 0 0 16px;
            color: var(--text-soft);
            line-height: 1.6;
            font-size: 14px;
        }

        .status,
        .error-summary {
            border-radius: var(--radius-sm);
            border: 1px solid;
            padding: 12px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .status {
            border-color: var(--ok-line);
            background: var(--ok-bg);
            color: var(--ok-text);
        }

        .error-summary {
            border-color: var(--error-line);
            background: var(--error-bg);
            color: var(--error-text);
        }

        .error-summary ul {
            margin: 0;
            padding-left: 18px;
        }

        .error-summary[hidden] {
            display: none;
        }

        .field { margin-bottom: 14px; }

        .field label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .field input[type="email"],
        .field input[type="password"] {
            width: 100%;
            height: 48px;
            border: 1px solid var(--line);
            background: #fff;
            border-radius: var(--radius-md);
            font-size: 14px;
            color: var(--text-main);
            padding: 10px 12px;
        }

        .field input:focus-visible {
            outline: none;
            border-color: #7ea6ef;
            box-shadow: 0 0 0 4px rgba(35, 102, 217, 0.15);
        }

        .field.has-error input {
            border-color: #ec9898;
            background: #fffafa;
        }

        .helper-error {
            margin-top: 6px;
            color: #b12626;
            font-size: 12px;
            font-weight: 600;
        }

        .helper-error:empty {
            display: none;
        }

        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 6px 0 16px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-soft);
        }

        .remember input {
            width: 16px;
            height: 16px;
            accent-color: var(--brand);
        }

        .btn {
            width: 100%;
            border: 0;
            border-radius: var(--radius-md);
            height: 46px;
            padding: 0 16px;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), #4d83f0);
            box-shadow: 0 10px 22px rgba(31, 95, 224, .28);
        }

        .btn[disabled] {
            opacity: .75;
            cursor: not-allowed;
            box-shadow: none;
        }

        .btn-loader {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,.45);
            border-top-color: #fff;
            border-radius: 999px;
            display: none;
            animation: spin .85s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        .btn.is-loading .btn-loader {
            display: inline-block;
        }

        .note {
            margin-top: 10px;
            font-size: 12px;
            color: #6b7d96;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (min-width: 1024px) {
            .layout {
                grid-template-columns: 1fr 1fr;
                gap: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <a class="logo" href="{{ route('home') }}">
                <span class="logo-badge">EN</span>
                <span>Simulados e Questoes</span>
            </a>
            <a class="header-link" href="{{ route('cadastro.create') }}">Nao tenho conta -> Cadastrar</a>
        </header>

        <main class="layout">
            <aside class="panel">
                <p class="kicker">Acesso rapido</p>
                <h1>Entre e continue sua preparacao para ENEM e concursos publicos.</h1>
                <p>Acesse simulados e questoes gratuitas, acompanhe desempenho e evolua no seu ritmo.</p>
            </aside>

            <section class="form-panel">
                <h2 class="form-title">Entrar na plataforma</h2>
                <p class="form-subtitle">Acesse sua conta e continue seus simulados personalizados.</p>

                @if (session('status'))
                    <div class="status" role="status">{{ session('status') }}</div>
                @endif

                <div id="login-error-summary" class="error-summary" role="alert" aria-live="polite" {{ $errors->any() ? '' : 'hidden' }}>
                    <ul id="login-error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

                <form id="loginForm" method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <div class="field {{ $errors->has('email') ? 'has-error' : '' }}">
                        <label for="email">E-mail</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        <div id="error-email" class="helper-error">@error('email'){{ $message }}@enderror</div>
                    </div>

                    <div class="field {{ $errors->has('password') ? 'has-error' : '' }}">
                        <label for="password">Senha</label>
                        <input id="password" name="password" type="password" required autocomplete="current-password">
                        <div id="error-password" class="helper-error">@error('password'){{ $message }}@enderror</div>
                    </div>

                    <div class="row">
                        <label class="remember" for="remember">
                            <input id="remember" name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }}>
                            <span>Lembrar de mim</span>
                        </label>
                    </div>

                    <button id="submitBtn" class="btn" type="submit">
                        <span class="btn-loader" aria-hidden="true"></span>
                        <span class="btn-text">Entrar e continuar</span>
                    </button>

                    <p class="note">Leva menos de 1 minuto para retomar seu plano de estudos.</p>
                </form>
            </section>
        </main>
    </div>

    <script>
        (function () {
            var form = document.getElementById('loginForm');
            var submitBtn = document.getElementById('submitBtn');
            var summary = document.getElementById('login-error-summary');
            var summaryList = document.getElementById('login-error-list');
            if (!form || !submitBtn || !summary || !summaryList) return;

            function setLoading(isLoading) {
                var text = submitBtn.querySelector('.btn-text');
                if (isLoading) {
                    submitBtn.classList.add('is-loading');
                    submitBtn.setAttribute('disabled', 'disabled');
                    if (text) text.textContent = 'Entrando...';
                    return;
                }

                submitBtn.classList.remove('is-loading');
                submitBtn.removeAttribute('disabled');
                if (text) text.textContent = 'Entrar e continuar';
            }

            function clearErrors() {
                summaryList.innerHTML = '';
                summary.hidden = true;
                ['email', 'password'].forEach(function (field) {
                    var errorEl = document.getElementById('error-' + field);
                    var fieldWrap = form.querySelector('[name="' + field + '"]');
                    if (errorEl) errorEl.textContent = '';
                    if (fieldWrap && fieldWrap.parentElement) {
                        fieldWrap.parentElement.classList.remove('has-error');
                    }
                });
            }

            function addSummaryMessage(message) {
                var li = document.createElement('li');
                li.textContent = message;
                summaryList.appendChild(li);
                summary.hidden = false;
            }

            function showFieldErrors(errors) {
                var firstField = null;
                Object.keys(errors).forEach(function (field) {
                    var input = form.querySelector('[name="' + field + '"]');
                    var errorEl = document.getElementById('error-' + field);
                    var message = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                    if (errorEl) errorEl.textContent = message;
                    if (input && input.parentElement) {
                        input.parentElement.classList.add('has-error');
                    }
                    addSummaryMessage(message);
                    if (!firstField && input) firstField = input;
                });

                if (firstField) firstField.focus();
            }

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                clearErrors();
                setLoading(true);

                try {
                    var response = await fetch(form.action, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new FormData(form)
                    });

                    var data = {};
                    try {
                        data = await response.json();
                    } catch (e) {
                        data = {};
                    }

                    if (response.ok && data.errors) {
                        showFieldErrors(data.errors);
                        return;
                    }

                    if (response.ok) {
                        window.location.href = data.redirect || "{{ route('area_aluno') }}";
                        return;
                    }

                    if (response.status === 422 && data.errors) {
                        showFieldErrors(data.errors);
                        return;
                    }

                    if (response.status === 429) {
                        addSummaryMessage('Muitas tentativas em pouco tempo. Aguarde e tente novamente.');
                        return;
                    }

                    addSummaryMessage(data.message || 'Nao foi possivel concluir o login. Tente novamente.');
                } catch (error) {
                    addSummaryMessage('Falha de conexao. Verifique sua internet e tente novamente.');
                } finally {
                    setLoading(false);
                }
            });

        })();
    </script>
</body>
</html>
