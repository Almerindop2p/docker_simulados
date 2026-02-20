<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Simulado | {{ $simulado->name }}</title>
    @include('partials.edu-theme-head')
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Plus Jakarta Sans", "Manrope", "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 10% 8%, #ffffff 0%, rgba(255, 255, 255, 0) 45%),
                radial-gradient(circle at 88% 86%, #e9f1ff 0%, rgba(233, 241, 255, 0) 42%),
                linear-gradient(180deg, var(--bg-main), var(--bg-soft));
            color: var(--text-main);
            min-height: 100vh;
        }

        .shell {
            width: min(1100px, 92vw);
            margin: 0 auto;
            padding: 20px 0 32px;
            display: grid;
            gap: 18px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .logo {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--text-main);
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .logo-badge {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            color: #fff;
            background: linear-gradient(135deg, #1f5fe0, #5a8cff);
            box-shadow: 0 8px 18px rgba(31, 95, 224, 0.3);
        }

        .top-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .link-btn {
            text-decoration: none;
            min-height: 40px;
            padding: 9px 12px;
            border-radius: 10px;
            border: 1px solid #d2dff1;
            background: #fff;
            color: #1f446f;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border: 1px solid #d9e4f3;
            border-radius: var(--radius-lg);
            background: #fff;
            box-shadow: var(--shadow-soft);
            padding: 18px;
            display: grid;
            gap: 14px;
        }

        .simulado-inline-ad {
            border: 1px dashed #b6bfcb;
            border-radius: 12px;
            background: #e7eaef;
            box-shadow: 0 8px 24px rgba(16, 36, 63, 0.08);
            min-height: 90px;
            padding: 10px;
            overflow: hidden;
        }

        .simulado-inline-ad-placeholder {
            min-height: 70px;
            display: grid;
            place-items: center;
            text-align: center;
            color: #5f6e83;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: 8px;
        }

        .meta-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }

        .meta-item {
            border: 1px solid #d7e3f4;
            border-radius: 12px;
            background: #f8fbff;
            padding: 10px;
            display: grid;
            gap: 4px;
        }

        .meta-item strong {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #5f7390;
        }

        .meta-item span {
            color: #21456d;
            font-weight: 700;
        }

        .title {
            margin: 0;
            font-size: 1.2rem;
            color: #17365a;
        }

        .enunciado {
            margin: 0;
            color: #26486f;
            line-height: 1.7;
        }

        .question-image-wrap {
            display: flex;
            justify-content: center;
        }

        .question-image {
            display: block;
            width: 100%;
            max-width: 760px;
            height: auto;
            border-radius: 12px;
            border: 1px solid #d7e4f5;
            background: #fff;
        }

        .choices {
            display: grid;
            gap: 8px;
        }

        .choice {
            border: 1px solid #d2deef;
            border-radius: 12px;
            background: #fff;
            padding: 10px;
            display: grid;
            grid-template-columns: auto auto 1fr;
            align-items: start;
            gap: 10px;
        }

        .choice-letter {
            font-size: 12px;
            font-weight: 800;
            color: #20456f;
            background: #e9f1ff;
            border-radius: 8px;
            min-width: 26px;
            min-height: 26px;
            display: inline-grid;
            place-items: center;
        }

        .choice-text {
            color: #234465;
            line-height: 1.6;
            font-size: 0.92rem;
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .btn {
            min-height: 42px;
            border-radius: 11px;
            border: 1px solid #c6d8f2;
            background: #fff;
            color: #1f4a79;
            font-weight: 700;
            font-size: 13px;
            padding: 0 14px;
            cursor: pointer;
        }

        .btn.primary {
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, #2264e5, #588dff);
            box-shadow: 0 10px 18px rgba(31, 95, 224, 0.24);
        }

        .warning {
            margin: 0;
            border: 1px solid #f2d9be;
            background: #fffbf4;
            color: #7f4e1a;
            border-radius: 12px;
            padding: 11px 12px;
            font-size: 13px;
            line-height: 1.6;
        }

        .sim-feedback-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 27, 44, 0.62);
            z-index: 1200;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 12px;
        }

        .sim-feedback-backdrop.is-open {
            display: flex;
        }

        .sim-feedback-modal {
            width: min(520px, 100%);
            border-radius: 16px;
            border: 1px solid #d2dfef;
            background: #fff;
            box-shadow: 0 24px 60px rgba(16, 36, 63, 0.3);
            padding: 16px;
            display: grid;
            gap: 10px;
        }

        .sim-feedback-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .sim-feedback-title {
            margin: 0;
            font-size: 1.05rem;
            color: #163b63;
            letter-spacing: -0.01em;
        }

        .sim-feedback-close {
            width: 32px;
            height: 32px;
            border: 1px solid #d7e2f0;
            border-radius: 10px;
            background: #fff;
            color: #385a80;
            font-weight: 800;
            cursor: pointer;
        }

        .sim-feedback-copy {
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
            color: #355677;
        }

        .sim-feedback-form {
            display: grid;
            gap: 8px;
        }

        .sim-feedback-label {
            font-size: 12px;
            color: #334d6e;
            font-weight: 700;
        }

        .sim-feedback-input,
        .sim-feedback-textarea {
            width: 100%;
            border: 1px solid #ccdaec;
            border-radius: 10px;
            background: #fff;
            color: #1f3f66;
            padding: 10px;
            font-family: inherit;
            font-size: 14px;
        }

        .sim-feedback-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .sim-feedback-user {
            margin: 0;
            font-size: 12px;
            color: #49657f;
            border: 1px solid #dce7f5;
            border-radius: 10px;
            background: #f7faff;
            padding: 8px 9px;
        }

        .sim-feedback-submit {
            min-height: 42px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, #25d366, #1fa855);
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .sim-feedback-submit[disabled] {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .sim-feedback-spinner {
            display: none;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-top-color: #fff;
            border-radius: 999px;
            animation: simFeedbackSpin 0.7s linear infinite;
        }

        .sim-feedback-submit.is-loading .sim-feedback-spinner {
            display: inline-block;
        }

        @keyframes simFeedbackSpin {
            to {
                transform: rotate(360deg);
            }
        }

        .sim-feedback-message {
            margin: 0;
            font-size: 13px;
            border-radius: 10px;
            padding: 8px 10px;
            line-height: 1.45;
        }

        .sim-feedback-message.error {
            border: 1px solid #f3c9d1;
            background: #fff4f6;
            color: #8b1f34;
        }

        .sim-feedback-message.success {
            border: 1px solid #b9e4c9;
            background: #edfaf2;
            color: #1d5c38;
        }
    </style>
</head>
<body>
    @php
        $loggedUser = auth()->user();
        $isAdm = ($loggedUser->user_type ?? null) === \App\Models\User::TYPE_ADM;
        $canGoBack = $currentIndex > 0;
        $canGoNext = $currentIndex < ($totalQuestoes - 1);
        $questionNumber = $currentIndex + 1;
        $feedbackPromptEnabled = false;
        $showFeedbackPromptModal = false;
        $feedbackPromptCurrentUrl = url()->current() . (request()->getQueryString() ? '?' . request()->getQueryString() : '');
    @endphp

    <div class="shell">
        <header class="topbar">
            <a class="logo" href="{{ route('home') }}">
                <span class="logo-badge">EN</span>
                <span>{{ $simulado->name }}</span>
            </a>
            <div class="top-actions">
                <a class="link-btn" href="{{ route('simulados.public') }}">Voltar para simulados</a>
            </div>
        </header>

        @include('partials.ad-slot', [
            'format' => 'horizontal',
            'tag' => 'section',
            'slotClass' => 'simulado-inline-ad',
            'placeholderClass' => 'simulado-inline-ad-placeholder',
            'placeholder' => 'Espaco anuncio do simulado',
            'ariaLabel' => 'Publicidade do simulado',
        ])

        <section class="card">
            <div class="meta-grid">
                <article class="meta-item">
                    <strong>Questao</strong>
                    <span>{{ $questionNumber }} de {{ $totalQuestoes }}</span>
                </article>
                <article class="meta-item">
                    <strong>Tempo da questao</strong>
                    <span id="questionTimer">00:00</span>
                </article>
                <article class="meta-item">
                    <strong>Tempo total</strong>
                    <span id="totalTimer">00:00</span>
                </article>
                <article class="meta-item">
                    <strong>Modo</strong>
                    <span>{{ $isGuest ? 'Anonimo (URL)' : 'Usuario logado (Banco)' }}</span>
                </article>
            </div>

            @if (!$questao)
                <p class="warning">A questao desta posicao nao esta mais disponivel. Use os botoes para navegar para outra questao.</p>
            @else
                <h1 class="title">Questao #{{ $questao->id }}</h1>
                @if ($questao->imagem_url)
                    <div class="question-image-wrap">
                        <img
                            class="question-image"
                            src="{{ $questao->imagem_url }}"
                            alt="Imagem da questao {{ $questao->id }}"
                            loading="lazy"
                        >
                    </div>
                @endif
                <p class="enunciado">{!! nl2br(e($questao->enunciado)) !!}</p>
            @endif

            <form id="simuladoForm" method="POST" action="{{ route('simulados.submit', $simulado) }}">
                @csrf
                @if (!$isGuest)
                    <input type="hidden" name="attempt_id" value="{{ $attemptId }}">
                @else
                    <input type="hidden" name="state" value="{{ $guestState }}">
                @endif
                <input type="hidden" name="current_index" value="{{ $currentIndex }}">
                <input type="hidden" id="elapsed_delta_seconds" name="elapsed_delta_seconds" value="0">
                <input type="hidden" id="actionInput" name="action" value="next">

                @if ($questao)
                    @php
                        $alternativas = [
                            'A' => $questao->alternativa_a,
                            'B' => $questao->alternativa_b,
                            'C' => $questao->alternativa_c,
                            'D' => $questao->alternativa_d,
                        ];
                        if (!blank($questao->alternativa_e)) {
                            $alternativas['E'] = $questao->alternativa_e;
                        }
                    @endphp
                    <div class="choices">
                        @foreach ($alternativas as $letra => $alternativa)
                            <label class="choice" for="questao-{{ $questao->id }}-{{ strtolower($letra) }}">
                                <input
                                    id="questao-{{ $questao->id }}-{{ strtolower($letra) }}"
                                    type="radio"
                                    name="resposta"
                                    value="{{ $letra }}"
                                    @checked($selectedAnswer === $letra)
                                >
                                <span class="choice-letter">{{ $letra }}</span>
                                <span class="choice-text">{!! nl2br(e($alternativa)) !!}</span>
                            </label>
                        @endforeach
                    </div>
                @endif

                <div class="actions">
                    @if ($canGoBack)
                        <button class="btn" type="button" data-action="back">Voltar</button>
                    @endif

                    @if ($canGoNext)
                        <button class="btn primary" type="button" data-action="next">Proximo</button>
                    @else
                        <button class="btn primary" type="button" data-action="finish">Finalizar</button>
                    @endif
                </div>
            </form>
        </section>
    </div>

    @if ($feedbackPromptEnabled)
        <div
            id="simFeedbackBackdrop"
            class="sim-feedback-backdrop"
            data-auto-open="{{ $showFeedbackPromptModal ? '1' : '0' }}"
            aria-hidden="true"
        >
            <section class="sim-feedback-modal" role="dialog" aria-modal="true" aria-labelledby="simFeedbackTitle">
                <header class="sim-feedback-head">
                    <h2 id="simFeedbackTitle" class="sim-feedback-title">Sua opiniao melhora a plataforma</h2>
                    <button id="simFeedbackClose" class="sim-feedback-close" type="button" aria-label="Fechar modal">X</button>
                </header>

                <p class="sim-feedback-copy">
                    Seu feedback ajuda a priorizar melhorias da versao beta.
                </p>

                <form id="simFeedbackForm" class="sim-feedback-form" method="POST" action="{{ route('feedback.tickets.store') }}">
                    @csrf
                    <input type="hidden" name="origem_rota" value="simulados.play.feedback.modal">
                    <input type="hidden" name="pagina_url" value="{{ $feedbackPromptCurrentUrl }}">

                    @if (!$loggedUser)
                        <label class="sim-feedback-label" for="sim_feedback_nome">Nome</label>
                        <input id="sim_feedback_nome" class="sim-feedback-input" type="text" name="nome" maxlength="120" required>

                        <label class="sim-feedback-label" for="sim_feedback_email">E-mail</label>
                        <input id="sim_feedback_email" class="sim-feedback-input" type="email" name="email" maxlength="255" required>
                    @else
                        <p class="sim-feedback-user">
                            Envio autenticado como <strong>{{ $loggedUser->name }}</strong> ({{ $loggedUser->email }}).
                        </p>
                    @endif

                    <label class="sim-feedback-label" for="sim_feedback_mensagem">Mensagem</label>
                    <textarea
                        id="sim_feedback_mensagem"
                        class="sim-feedback-textarea"
                        name="mensagem"
                        rows="4"
                        maxlength="5000"
                        required
                    ></textarea>

                    <p id="simFeedbackMessage" class="sim-feedback-message" hidden></p>

                    <button id="simFeedbackSubmit" class="sim-feedback-submit" type="submit">
                        <span class="sim-feedback-submit-label">Enviar feedback</span>
                        <span class="sim-feedback-spinner" aria-hidden="true"></span>
                    </button>
                </form>
            </section>
        </div>
    @endif

    @include('partials.feedback-widget')
    @include('partials.adsense-placements')

    <script>
        (function () {
            var form = document.getElementById('simuladoForm');
            var actionInput = document.getElementById('actionInput');
            var elapsedInput = document.getElementById('elapsed_delta_seconds');
            var questionTimer = document.getElementById('questionTimer');
            var totalTimer = document.getElementById('totalTimer');
            var startAt = Date.now();
            var baseQuestion = {{ (int) $elapsedCurrentSeconds }};
            var baseTotal = {{ (int) $totalElapsedSeconds }};

            function formatTime(totalSeconds) {
                var seconds = Math.max(0, parseInt(totalSeconds, 10) || 0);
                var hh = Math.floor(seconds / 3600);
                var mm = Math.floor((seconds % 3600) / 60);
                var ss = seconds % 60;
                if (hh > 0) {
                    return String(hh).padStart(2, '0') + ':' + String(mm).padStart(2, '0') + ':' + String(ss).padStart(2, '0');
                }
                return String(mm).padStart(2, '0') + ':' + String(ss).padStart(2, '0');
            }

            function tick() {
                var elapsedDelta = Math.max(0, Math.floor((Date.now() - startAt) / 1000));
                if (questionTimer) {
                    questionTimer.textContent = formatTime(baseQuestion + elapsedDelta);
                }
                if (totalTimer) {
                    totalTimer.textContent = formatTime(baseTotal + elapsedDelta);
                }
            }

            tick();
            setInterval(tick, 1000);

            function submitWithAction(action) {
                if (!form || !actionInput || !elapsedInput) {
                    return;
                }
                var elapsedDelta = Math.max(0, Math.floor((Date.now() - startAt) / 1000));
                actionInput.value = action;
                elapsedInput.value = String(elapsedDelta);
                form.submit();
            }

            document.querySelectorAll('[data-action]').forEach(function (button) {
                button.addEventListener('click', function () {
                    submitWithAction(button.getAttribute('data-action') || 'next');
                });
            });

            var feedbackBackdrop = document.getElementById('simFeedbackBackdrop');
            var feedbackClose = document.getElementById('simFeedbackClose');
            var feedbackForm = document.getElementById('simFeedbackForm');
            var feedbackSubmit = document.getElementById('simFeedbackSubmit');
            var feedbackMessage = document.getElementById('simFeedbackMessage');

            function openFeedbackModal() {
                if (!feedbackBackdrop) {
                    return;
                }

                feedbackBackdrop.classList.add('is-open');
                feedbackBackdrop.setAttribute('aria-hidden', 'false');
            }

            function closeFeedbackModal() {
                if (!feedbackBackdrop) {
                    return;
                }

                feedbackBackdrop.classList.remove('is-open');
                feedbackBackdrop.setAttribute('aria-hidden', 'true');
            }

            function setFeedbackMessage(text, type) {
                if (!feedbackMessage) {
                    return;
                }

                feedbackMessage.hidden = false;
                feedbackMessage.textContent = text;
                feedbackMessage.className = 'sim-feedback-message ' + type;
            }

            function clearFeedbackMessage() {
                if (!feedbackMessage) {
                    return;
                }

                feedbackMessage.hidden = true;
                feedbackMessage.textContent = '';
                feedbackMessage.className = 'sim-feedback-message';
            }

            function setFeedbackLoading(loading) {
                if (!feedbackSubmit) {
                    return;
                }

                var label = feedbackSubmit.querySelector('.sim-feedback-submit-label');
                if (loading) {
                    feedbackSubmit.setAttribute('disabled', 'disabled');
                    feedbackSubmit.classList.add('is-loading');
                    if (label) {
                        label.textContent = 'Enviando...';
                    }
                    return;
                }

                feedbackSubmit.removeAttribute('disabled');
                feedbackSubmit.classList.remove('is-loading');
                if (label) {
                    label.textContent = 'Enviar feedback';
                }
            }

            if (feedbackBackdrop) {
                if (feedbackBackdrop.getAttribute('data-auto-open') === '1') {
                    openFeedbackModal();
                }

                if (feedbackClose) {
                    feedbackClose.addEventListener('click', function () {
                        closeFeedbackModal();
                    });
                }

                feedbackBackdrop.addEventListener('click', function (event) {
                    if (event.target === feedbackBackdrop) {
                        closeFeedbackModal();
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && feedbackBackdrop.classList.contains('is-open')) {
                        closeFeedbackModal();
                    }
                });
            }

            if (feedbackForm) {
                feedbackForm.addEventListener('submit', async function (event) {
                    event.preventDefault();
                    clearFeedbackMessage();
                    setFeedbackLoading(true);

                    try {
                        var response = await fetch(feedbackForm.action, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: new FormData(feedbackForm)
                        });

                        var data = {};
                        try {
                            data = await response.json();
                        } catch (error) {
                            data = {};
                        }

                        if (response.ok && data.ok) {
                            setFeedbackMessage(data.message || 'Feedback enviado com sucesso.', 'success');
                            feedbackForm.reset();
                            window.setTimeout(function () {
                                closeFeedbackModal();
                            }, 700);
                            return;
                        }

                        if (response.status === 422 && data.errors) {
                            var firstMessage = null;
                            Object.keys(data.errors).forEach(function (field) {
                                if (!firstMessage) {
                                    var value = data.errors[field];
                                    firstMessage = Array.isArray(value) ? value[0] : value;
                                }
                            });
                            setFeedbackMessage(firstMessage || 'Verifique os campos e tente novamente.', 'error');
                            return;
                        }

                        if (response.status === 429) {
                            setFeedbackMessage('Muitas tentativas em pouco tempo. Aguarde e tente novamente.', 'error');
                            return;
                        }

                        setFeedbackMessage(data.message || 'Nao foi possivel enviar agora. Tente novamente.', 'error');
                    } catch (error) {
                        setFeedbackMessage('Falha de conexao. Tente novamente.', 'error');
                    } finally {
                        setFeedbackLoading(false);
                    }
                });
            }
        })();
    </script>
</body>
</html>
