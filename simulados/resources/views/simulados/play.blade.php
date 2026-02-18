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
    </style>
</head>
<body>
    @php
        $canGoBack = $currentIndex > 0;
        $canGoNext = $currentIndex < ($totalQuestoes - 1);
        $questionNumber = $currentIndex + 1;
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

    @include('partials.feedback-widget')

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
        })();
    </script>
</body>
</html>

