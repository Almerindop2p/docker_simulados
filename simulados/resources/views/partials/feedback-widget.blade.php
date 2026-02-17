@php
    $feedbackUser = auth()->user();
    $feedbackIsLogged = (bool) $feedbackUser;
    $feedbackCurrentUrl = url()->current() . (request()->getQueryString() ? '?' . request()->getQueryString() : '');
@endphp

<div class="feedback-widget-root" id="feedbackWidgetRoot">
    <button
        id="feedbackFab"
        class="feedback-fab"
        type="button"
        aria-label="Abrir feedback beta"
        aria-controls="feedbackPanel"
        aria-expanded="false"
    >
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 12a8 8 0 0 1 13.5-5.7A8 8 0 0 1 12 20a8.2 8.2 0 0 1-3.5-.8L4 20l.9-4A8 8 0 0 1 4 12Z" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9 11.2c.3 1 1.2 2 2.2 2.6.4.2.8.2 1.1-.1l.7-.7c.2-.2.5-.2.8-.1l1.2.5c.4.2.6.6.5 1-.3 1-1.3 1.6-2.3 1.5-3.1-.4-5.7-3-6.1-6.1-.1-1 .5-2 1.5-2.3.4-.1.9.1 1 .5l.5 1.2c.1.3.1.6-.1.8l-.7.7c-.3.3-.3.7-.1 1.1Z" fill="currentColor" opacity=".15"/>
        </svg>
    </button>

    <section id="feedbackPanel" class="feedback-panel" hidden aria-label="Enviar feedback beta">
        <header class="feedback-header">
            <h3>Feedback beta</h3>
            <button id="feedbackClose" class="feedback-close" type="button" aria-label="Fechar painel">X</button>
        </header>

        <p class="feedback-copy">
            Encontrou um problema ou quer sugerir algo? Envie aqui sem sair da pagina.
        </p>

        <form id="feedbackForm" class="feedback-form" method="POST" action="{{ route('feedback.tickets.store') }}">
            @csrf
            <input type="hidden" name="origem_rota" value="{{ request()->route()?->getName() }}">
            <input type="hidden" name="pagina_url" value="{{ $feedbackCurrentUrl }}">

            @if (!$feedbackIsLogged)
                <label class="feedback-label" for="feedback_nome">Nome</label>
                <input id="feedback_nome" class="feedback-input" type="text" name="nome" maxlength="120" required>

                <label class="feedback-label" for="feedback_email">E-mail</label>
                <input id="feedback_email" class="feedback-input" type="email" name="email" maxlength="255" required>
            @else
                <p class="feedback-user">
                    Envio autenticado como <strong>{{ $feedbackUser->name }}</strong> ({{ $feedbackUser->email }}).
                </p>
            @endif

            <label class="feedback-label" for="feedback_mensagem">Mensagem</label>
            <textarea id="feedback_mensagem" class="feedback-textarea" name="mensagem" rows="4" maxlength="5000" required></textarea>

            <p id="feedbackMessage" class="feedback-message" hidden></p>

            <button id="feedbackSubmit" class="feedback-submit" type="submit">
                <span class="feedback-submit-label">Enviar feedback</span>
                <span class="feedback-submit-spinner" aria-hidden="true"></span>
            </button>
        </form>
    </section>
</div>

<style>
    .feedback-widget-root {
        position: fixed;
        right: 16px;
        bottom: 16px;
        z-index: 75;
    }

    .feedback-fab {
        width: 58px;
        height: 58px;
        border: 0;
        border-radius: 999px;
        background: linear-gradient(140deg, #25d366, #1fa855);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 14px 28px rgba(24, 132, 64, 0.35);
        cursor: pointer;
    }

    .feedback-fab svg {
        width: 26px;
        height: 26px;
    }

    .feedback-panel {
        position: absolute;
        right: 0;
        bottom: 72px;
        width: min(360px, calc(100vw - 20px));
        border: 1px solid #d5e0ef;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 18px 40px rgba(18, 39, 67, 0.22);
        padding: 12px;
        display: grid;
        gap: 10px;
        opacity: 0;
        transform: translateY(12px) scale(0.84);
        transform-origin: calc(100% - 18px) calc(100% + 64px);
        pointer-events: none;
        transition: opacity .2s ease, transform .2s ease;
        will-change: opacity, transform;
    }

    .feedback-panel.is-open {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    .feedback-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .feedback-header h3 {
        margin: 0;
        font-size: 1rem;
        color: #17365a;
    }

    .feedback-close {
        width: 28px;
        height: 28px;
        border: 1px solid #d8e3f1;
        border-radius: 8px;
        background: #fff;
        cursor: pointer;
        color: #355679;
        font-weight: 700;
    }

    .feedback-copy {
        margin: 0;
        font-size: 13px;
        color: #486482;
        line-height: 1.5;
    }

    .feedback-form {
        display: grid;
        gap: 8px;
    }

    .feedback-label {
        font-size: 12px;
        color: #334d6e;
        font-weight: 700;
    }

    .feedback-input,
    .feedback-textarea {
        width: 100%;
        border: 1px solid #ccdaec;
        border-radius: 10px;
        background: #fff;
        color: #1f3f66;
        padding: 10px;
        font-family: inherit;
        font-size: 14px;
    }

    .feedback-textarea {
        resize: vertical;
        min-height: 95px;
    }

    .feedback-user {
        margin: 0;
        font-size: 12px;
        color: #49657f;
        border: 1px solid #dce7f5;
        border-radius: 10px;
        background: #f7faff;
        padding: 8px 9px;
    }

    .feedback-submit {
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

    .feedback-submit[disabled] {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .feedback-submit-spinner {
        display: none;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-top-color: #fff;
        border-radius: 999px;
        animation: feedbackSpin 0.7s linear infinite;
    }

    .feedback-submit.is-loading .feedback-submit-spinner {
        display: inline-block;
    }

    @keyframes feedbackSpin {
        to {
            transform: rotate(360deg);
        }
    }

    .feedback-message {
        margin: 0;
        font-size: 13px;
        border-radius: 10px;
        padding: 8px 10px;
        line-height: 1.45;
    }

    .feedback-message.error {
        border: 1px solid #f3c9d1;
        background: #fff4f6;
        color: #8b1f34;
    }

    .feedback-message.success {
        border: 1px solid #b9e4c9;
        background: #edfaf2;
        color: #1d5c38;
    }

    @media (max-width: 480px) {
        .feedback-widget-root {
            right: 10px;
            bottom: 10px;
        }

        .feedback-panel {
            width: min(360px, calc(100vw - 14px));
            bottom: 68px;
        }
    }
</style>

<script>
    (function () {
        var root = document.getElementById('feedbackWidgetRoot');
        var fab = document.getElementById('feedbackFab');
        var panel = document.getElementById('feedbackPanel');
        var closeBtn = document.getElementById('feedbackClose');
        var form = document.getElementById('feedbackForm');
        var submit = document.getElementById('feedbackSubmit');
        var messageBox = document.getElementById('feedbackMessage');

        if (!root || !fab || !panel || !closeBtn || !form || !submit || !messageBox) {
            return;
        }

        var isAnimating = false;

        function openPanel() {
            if (isAnimating || !panel.hidden) {
                return;
            }

            isAnimating = true;
            panel.hidden = false;
            fab.setAttribute('aria-expanded', 'true');
            requestAnimationFrame(function () {
                panel.classList.add('is-open');
                isAnimating = false;
            });
        }

        function closePanel() {
            if (isAnimating || panel.hidden) {
                return;
            }

            isAnimating = true;
            fab.setAttribute('aria-expanded', 'false');
            panel.classList.remove('is-open');

            window.setTimeout(function () {
                panel.hidden = true;
                isAnimating = false;
            }, 220);
        }

        function showMessage(text, type) {
            messageBox.textContent = text;
            messageBox.className = 'feedback-message ' + type;
            messageBox.hidden = false;
        }

        function clearMessage() {
            messageBox.hidden = true;
            messageBox.textContent = '';
            messageBox.className = 'feedback-message';
        }

        function setLoadingState(loading) {
            var label = submit.querySelector('.feedback-submit-label');
            if (loading) {
                submit.setAttribute('disabled', 'disabled');
                submit.classList.add('is-loading');
                if (label) {
                    label.textContent = 'Enviando...';
                }
                return;
            }

            submit.removeAttribute('disabled');
            submit.classList.remove('is-loading');
            if (label) {
                label.textContent = 'Enviar feedback';
            }
        }

        fab.addEventListener('click', function () {
            if (panel.hidden) {
                openPanel();
            } else {
                closePanel();
            }
        });

        closeBtn.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            closePanel();
        });

        document.addEventListener('click', function (event) {
            if (panel.hidden) {
                return;
            }
            if (!panel.contains(event.target) && !fab.contains(event.target)) {
                closePanel();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closePanel();
            }
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            clearMessage();
            setLoadingState(true);

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

                if (response.ok && data.ok) {
                    showMessage(data.message || 'Feedback enviado com sucesso.', 'success');
                    form.reset();
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
                    showMessage(firstMessage || 'Verifique os campos e tente novamente.', 'error');
                    return;
                }

                if (response.status === 429) {
                    showMessage('Muitas tentativas em pouco tempo. Aguarde e tente novamente.', 'error');
                    return;
                }

                showMessage(data.message || 'Nao foi possivel enviar agora. Tente novamente.', 'error');
            } catch (error) {
                showMessage('Falha de conexao. Tente novamente.', 'error');
            } finally {
                setLoadingState(false);
            }
        });
    })();
</script>
