<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | Simulados e Questoes Gratuitas</title>
    @include('partials.edu-theme-head')
    <style>
        * {
            box-sizing: border-box;
        }

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

        .app-shell {
            max-width: 1180px;
            margin: 0 auto;
            width: 100%;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 24px;
            animation: fadeUp .45s ease both;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            color: var(--text-main);
            text-decoration: none;
            letter-spacing: -0.01em;
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
            transition: background-color .2s ease;
        }

        .header-link:hover {
            background: rgba(31, 95, 224, 0.08);
        }

        .layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .panel-form,
        .panel-info {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
        }

        .panel-form {
            padding: 20px;
            order: 1;
            animation: fadeUp .48s ease both;
        }

        .panel-info {
            padding: 24px;
            order: 2;
            background:
                linear-gradient(165deg, #ffffff 0%, #f8fbff 48%, #eef4ff 100%);
            animation: fadeUp .56s ease both;
        }

        .hero-kicker {
            margin: 0 0 12px;
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

        .hero-title {
            margin: 0 0 12px;
            font-size: clamp(1.55rem, 2.2vw, 2.25rem);
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .hero-subtitle {
            margin: 0 0 18px;
            color: var(--text-soft);
            line-height: 1.6;
        }

        .benefits {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 12px;
        }

        .benefit-item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 10px;
            align-items: start;
            padding: 12px;
            border: 1px solid #d9e6fa;
            border-radius: var(--radius-sm);
            background: #ffffff;
            box-shadow: var(--shadow-soft);
        }

        .benefit-dot {
            width: 24px;
            height: 24px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: #eaf2ff;
            color: #1f5fe0;
            font-weight: 800;
            font-size: 13px;
            margin-top: 1px;
        }

        .benefit-title {
            display: block;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .benefit-text {
            margin: 0;
            color: var(--text-soft);
            font-size: 13px;
            line-height: 1.5;
        }

        .form-title {
            margin: 0 0 6px;
            font-size: 1.55rem;
            letter-spacing: -0.01em;
        }

        .form-subtitle {
            margin: 0 0 16px;
            color: var(--text-soft);
            line-height: 1.6;
            font-size: 14px;
        }

        .microcopy {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 0 0 16px;
        }

        .chip {
            border: 1px solid #d7e2f4;
            background: #f7faff;
            color: #274260;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 10px;
        }

        .status,
        .error-summary {
            border-radius: var(--radius-sm);
            border: 1px solid;
            padding: 12px;
            margin-bottom: 14px;
            font-size: 13px;
            line-height: 1.5;
        }

        .status {
            border-color: var(--ok-line);
            background: var(--ok-bg);
            color: var(--ok-text);
        }

        .error-summary {
            border-color: #d9e3f1;
            background: #f7f9fc;
            color: #3e526f;
        }

        .error-summary p {
            margin: 0 0 8px;
            font-weight: 700;
            color: #2f4361;
            font-size: 13px;
        }

        .error-summary ul {
            margin: 0;
            padding-left: 18px;
        }

        .error-summary[hidden] {
            display: none;
        }

        .stepper {
            margin: 0 0 16px;
        }

        .stepper-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
            font-size: 12px;
            color: var(--text-soft);
            font-weight: 700;
        }

        .progress {
            height: 8px;
            border-radius: 99px;
            background: #e7edf6;
            overflow: hidden;
        }

        .progress-bar {
            display: block;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, #1f5fe0, #5f8fff);
            border-radius: inherit;
            transition: width .25s ease;
        }

        .form-step {
            display: block;
        }

        .js-ready .form-step {
            display: none;
        }

        .js-ready .form-step.is-active {
            display: block;
        }

        .field {
            margin-bottom: 14px;
        }

        .field label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .field-wrap {
            position: relative;
        }

        .field-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            width: 16px;
            height: 16px;
            transform: translateY(-50%);
            opacity: .62;
            pointer-events: none;
        }

        .field input {
            width: 100%;
            height: 48px;
            border: 1px solid var(--line);
            background: #fff;
            border-radius: var(--radius-md);
            font-size: 14px;
            color: var(--text-main);
            padding: 10px 12px 10px 40px;
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        .field input::placeholder {
            color: #8ca0b8;
        }

        .field input:hover {
            border-color: #c8d6ea;
            background: #fbfdff;
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

        .password-strength {
            margin-top: 8px;
        }

        .password-strength-track {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            background: #e7edf6;
            overflow: hidden;
        }

        .password-strength-fill {
            display: block;
            height: 100%;
            width: 0;
            border-radius: inherit;
            transition: width .2s ease, background-color .2s ease;
            background: #9cb2d0;
        }

        .password-strength-fill.is-weak {
            background: #d44747;
        }

        .password-strength-fill.is-medium {
            background: #e49b2d;
        }

        .password-strength-fill.is-strong {
            background: #2ea665;
        }

        .password-strength-label {
            margin: 6px 0 0;
            font-size: 12px;
            color: #5a708d;
            line-height: 1.4;
        }

        .step-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 6px;
        }

        .btn {
            border: 0;
            border-radius: var(--radius-md);
            height: 46px;
            padding: 0 16px;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
            transition: transform .08s ease, box-shadow .2s ease, background-color .2s ease, opacity .2s ease;
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn-secondary {
            background: #f0f5fc;
            color: #2f4767;
            border: 1px solid #d2dfef;
        }

        .btn-secondary:hover {
            background: #e9f0fa;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand), #4d83f0);
            color: #fff;
            box-shadow: 0 10px 22px rgba(31, 95, 224, .28);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1a56ce, #3f77e4);
        }

        .btn[disabled] {
            cursor: not-allowed;
            opacity: .72;
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

        .footer-note {
            margin-top: 12px;
            color: #6b7d96;
            font-size: 12px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (min-width: 768px) {
            body {
                padding: 28px;
            }

            .panel-form {
                padding: 26px;
            }
        }

        @media (min-width: 1100px) {
            .layout {
                grid-template-columns: 1.05fr .95fr;
                align-items: stretch;
                gap: 24px;
            }

            .panel-info {
                order: 1;
            }

            .panel-form {
                order: 2;
            }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <header class="header">
            <a class="logo" href="{{ route('home') }}">
                <span class="logo-badge">EN</span>
                <span>Simulados e Questoes</span>
            </a>
            <a class="header-link" href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}">
                Ja tenho conta -> Entrar
            </a>
        </header>

        <main class="layout">
            <section class="panel-form">
                <h1 class="form-title">Crie sua conta</h1>
                <p class="form-subtitle">Comece seu plano de estudos para ENEM e concursos publicos com simulados e questoes gratuitas.</p>

                <div class="microcopy" aria-label="Dicas rapidas">
                    <span class="chip">Leva menos de 1 minuto</span>
                    <span class="chip">Ajuste seu plano de estudos depois</span>
                </div>

                @if (session('status'))
                    <div class="status" role="status">{{ session('status') }}</div>
                @endif

                <div id="cadastro-error-summary" class="error-summary" role="alert" aria-live="polite" {{ $errors->any() ? '' : 'hidden' }}>
                    <p>Ajustes rapidos para continuar:</p>
                    <ul id="cadastro-error-list">
                        @if ($errors->first('name'))
                            <li>{{ $errors->first('name') }}</li>
                        @endif
                        @if ($errors->first('email'))
                            <li>{{ $errors->first('email') }}</li>
                        @endif
                        @if ($errors->first('password'))
                            <li>{{ $errors->first('password') }}</li>
                        @endif
                        @if ($errors->first('password_confirmation'))
                            <li>{{ $errors->first('password_confirmation') }}</li>
                        @endif
                    </ul>
                </div>

                <form id="cadastroForm" method="POST" action="{{ route('cadastro.store') }}" data-initial-step="{{ $errors->has('password') || $errors->has('password_confirmation') ? 2 : 1 }}">
                    @csrf

                    <div class="stepper" aria-live="polite">
                        <div class="stepper-head">
                            <span id="stepLabel">Etapa 1 de 2</span>
                            <span id="stepHint">Dados pessoais</span>
                        </div>
                        <div class="progress" aria-hidden="true">
                            <span id="progressBar" class="progress-bar"></span>
                        </div>
                    </div>

                    <div class="form-step is-active" data-step="1">
                        <div class="field {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name">Nome completo</label>
                            <div class="field-wrap">
                                <svg class="field-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 12.5a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 2.5c-4.2 0-7.5 2.2-7.5 5v1.5h15V20c0-2.8-3.3-5-7.5-5Z" fill="#5E7590"/>
                                </svg>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name" placeholder="Ex.: Ana Beatriz Silva">
                            </div>
                            <div id="error-name" class="helper-error">@error('name'){{ $message }}@enderror</div>
                        </div>

                        <div class="field {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label for="email">E-mail</label>
                            <div class="field-wrap">
                                <svg class="field-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M3 6.5A2.5 2.5 0 0 1 5.5 4h13A2.5 2.5 0 0 1 21 6.5v11a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 17.5v-11Zm2 0v.2l7 4.6 7-4.6v-.2a.5.5 0 0 0-.5-.5h-13a.5.5 0 0 0-.5.5Zm16 2.6-6.4 4.2a4.5 4.5 0 0 1-5 0L3 9.1v8.4c0 .3.2.5.5.5h13c.3 0 .5-.2.5-.5V9.1Z" fill="#5E7590"/>
                                </svg>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="voce@email.com">
                            </div>
                            <div id="error-email" class="helper-error">@error('email'){{ $message }}@enderror</div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn btn-primary" data-next-step>Continuar</button>
                        </div>
                    </div>

                    <div class="form-step" data-step="2">
                        <div class="field {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label for="password">Senha</label>
                            <div class="field-wrap">
                                <svg class="field-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M7 10V8a5 5 0 0 1 10 0v2h1.2A2.8 2.8 0 0 1 21 12.8v6.4a2.8 2.8 0 0 1-2.8 2.8H5.8A2.8 2.8 0 0 1 3 19.2v-6.4A2.8 2.8 0 0 1 5.8 10H7Zm2 0h6V8a3 3 0 1 0-6 0v2Zm3 4a1.8 1.8 0 0 0-1 3.3V19h2v-1.7a1.8 1.8 0 0 0-1-3.3Z" fill="#5E7590"/>
                                </svg>
                                <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Minimo de 8 caracteres" aria-describedby="password-strength-label error-password">
                            </div>
                            <div id="error-password" class="helper-error">@error('password'){{ $message }}@enderror</div>
                            <div class="password-strength" aria-live="polite" aria-atomic="true">
                                <div class="password-strength-track" aria-hidden="true">
                                    <span id="password-strength-fill" class="password-strength-fill"></span>
                                </div>
                                <p id="password-strength-label" class="password-strength-label">Forca da senha: informe sua senha.</p>
                            </div>
                        </div>

                        <div class="field {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                            <label for="password_confirmation">Confirmar senha</label>
                            <div class="field-wrap">
                                <svg class="field-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M7 10V8a5 5 0 0 1 10 0v2h1.2A2.8 2.8 0 0 1 21 12.8v6.4a2.8 2.8 0 0 1-2.8 2.8H5.8A2.8 2.8 0 0 1 3 19.2v-6.4A2.8 2.8 0 0 1 5.8 10H7Zm2 0h6V8a3 3 0 1 0-6 0v2Z" fill="#5E7590"/>
                                </svg>
                                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Repita sua senha">
                            </div>
                            <div id="error-password_confirmation" class="helper-error">@error('password_confirmation'){{ $message }}@enderror</div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn btn-secondary" data-prev-step>Voltar</button>
                            <button id="submitBtn" class="btn btn-primary" type="submit">
                                <span class="btn-loader" aria-hidden="true"></span>
                                <span class="btn-text">Criar minha conta</span>
                            </button>
                        </div>
                    </div>

                    <p class="footer-note">Ao continuar, voce concorda com os termos da plataforma de simulados.</p>
                </form>
            </section>

            <aside class="panel-info">
                <p class="hero-kicker">Preparacao inteligente</p>
                <h2 class="hero-title">Seu ambiente completo para ENEM, concursos publicos, simulados e questoes gratuitas.</h2>
                <p class="hero-subtitle">Monte sua rotina com foco no que mais cai nas provas, acompanhe sua evolucao e ganhe confianca para o dia do exame.</p>

                <ul class="benefits">
                    <li class="benefit-item">
                        <span class="benefit-dot">1</span>
                        <div>
                            <span class="benefit-title">Simulados realistas</span>
                            <p class="benefit-text">Treine com tempo cronometrado para ENEM e concursos.</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <span class="benefit-dot">2</span>
                        <div>
                            <span class="benefit-title">Trilhas de estudo</span>
                            <p class="benefit-text">Organize conteudos por dificuldade e prioridade de aprendizado.</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <span class="benefit-dot">3</span>
                        <div>
                            <span class="benefit-title">Desempenho detalhado</span>
                            <p class="benefit-text">Acompanhe acertos, tempo por questao e evolucao por materia.</p>
                        </div>
                    </li>
                </ul>
            </aside>
        </main>
    </div>

    @include('partials.feedback-widget')
    @include('partials.adsense-placements')

    <script>
        (function () {
            document.body.classList.add('js-ready');
            var form = document.getElementById('cadastroForm');
            var summary = document.getElementById('cadastro-error-summary');
            var summaryList = document.getElementById('cadastro-error-list');
            if (!form) return;
            if (!summary || !summaryList) return;

            var stepLabel = document.getElementById('stepLabel');
            var stepHint = document.getElementById('stepHint');
            var progressBar = document.getElementById('progressBar');
            var submitBtn = document.getElementById('submitBtn');
            var steps = Array.prototype.slice.call(form.querySelectorAll('.form-step'));
            var nextBtn = form.querySelector('[data-next-step]');
            var prevBtn = form.querySelector('[data-prev-step]');
            var currentStep = Number(form.getAttribute('data-initial-step') || 1);
            var fields = ['name', 'email', 'password', 'password_confirmation'];
            var passwordInput = form.querySelector('#password');
            var passwordStrengthFill = document.getElementById('password-strength-fill');
            var passwordStrengthLabel = document.getElementById('password-strength-label');

            function focusFirstInput(step) {
                var active = form.querySelector('.form-step[data-step="' + step + '"] input');
                if (active) active.focus();
            }

            function setStep(step) {
                currentStep = step;
                steps.forEach(function (panel) {
                    panel.classList.toggle('is-active', Number(panel.getAttribute('data-step')) === step);
                });

                if (step === 1) {
                    stepLabel.textContent = 'Etapa 1 de 2';
                    stepHint.textContent = 'Dados pessoais';
                    progressBar.style.width = '50%';
                } else {
                    stepLabel.textContent = 'Etapa 2 de 2';
                    stepHint.textContent = 'Seguranca da conta';
                    progressBar.style.width = '100%';
                }
            }

            function evaluatePasswordStrength(value) {
                var lengthOk = value.length >= 8;
                var lowerOk = /[a-z]/.test(value);
                var upperOk = /[A-Z]/.test(value);
                var digitOk = /\d/.test(value);
                var symbolOk = /[^A-Za-z0-9]/.test(value);
                var score = [lengthOk, lowerOk, upperOk, digitOk, symbolOk].filter(Boolean).length;

                if (!value) {
                    return { level: 'none', percent: 0, label: 'Forca da senha: informe sua senha.' };
                }

                if (score === 5) {
                    return { level: 'strong', percent: 100, label: 'Forca da senha: forte.' };
                }

                if (score >= 3) {
                    return { level: 'medium', percent: 66, label: 'Forca da senha: media.' };
                }

                return { level: 'weak', percent: 33, label: 'Forca da senha: fraca.' };
            }

            function updatePasswordStrength() {
                if (!passwordInput || !passwordStrengthFill || !passwordStrengthLabel) return;

                var result = evaluatePasswordStrength(passwordInput.value || '');
                passwordStrengthFill.style.width = result.percent + '%';
                passwordStrengthFill.classList.remove('is-weak', 'is-medium', 'is-strong');
                if (result.level !== 'none') {
                    passwordStrengthFill.classList.add('is-' + result.level);
                }

                passwordStrengthLabel.textContent = result.label;
            }

            function setLoading(isLoading) {
                if (!submitBtn) return;
                var text = submitBtn.querySelector('.btn-text');
                if (isLoading) {
                    submitBtn.classList.add('is-loading');
                    submitBtn.setAttribute('disabled', 'disabled');
                    if (text) text.textContent = 'Criando conta...';
                    return;
                }

                submitBtn.classList.remove('is-loading');
                submitBtn.removeAttribute('disabled');
                if (text) text.textContent = 'Criar minha conta';
            }

            function clearErrors() {
                summaryList.innerHTML = '';
                summary.hidden = true;
                fields.forEach(function (field) {
                    var input = form.querySelector('[name="' + field + '"]');
                    var errorEl = document.getElementById('error-' + field);
                    if (errorEl) errorEl.textContent = '';
                    if (input && input.closest('.field')) {
                        input.closest('.field').classList.remove('has-error');
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
                var hasStep2Error = false;
                var firstField = null;

                fields.forEach(function (field) {
                    if (!errors[field]) return;
                    var input = form.querySelector('[name="' + field + '"]');
                    var errorEl = document.getElementById('error-' + field);
                    var message = Array.isArray(errors[field]) ? errors[field][0] : errors[field];

                    if (errorEl) errorEl.textContent = message;
                    if (input && input.closest('.field')) {
                        input.closest('.field').classList.add('has-error');
                    }

                    addSummaryMessage(message);
                    if (!firstField && input) firstField = input;
                    if (field === 'password' || field === 'password_confirmation') {
                        hasStep2Error = true;
                    }
                });

                setStep(hasStep2Error ? 2 : 1);
                if (firstField) firstField.focus();
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function () {
                    setStep(2);
                    focusFirstInput(2);
                });
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', function () {
                    setStep(1);
                    focusFirstInput(1);
                });
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
                        window.location.href = data.redirect || "{{ route('login') }}";
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

                    addSummaryMessage(data.message || 'Nao foi possivel concluir o cadastro. Tente novamente.');
                } catch (error) {
                    addSummaryMessage('Falha de conexao. Verifique sua internet e tente novamente.');
                } finally {
                    setLoading(false);
                }
            });

            if (passwordInput) {
                passwordInput.addEventListener('input', updatePasswordStrength);
                passwordInput.addEventListener('blur', updatePasswordStrength);
                updatePasswordStrength();
            }

            setStep(currentStep === 2 ? 2 : 1);
        })();
    </script>
</body>
</html>
