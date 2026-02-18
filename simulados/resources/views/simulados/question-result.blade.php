<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhe da Questao | {{ $simulado->name }}</title>
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
            width: min(980px, 92vw);
            margin: 0 auto;
            padding: 20px 0 32px;
            display: grid;
            gap: 16px;
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
            background: #fff;
            border: 1px solid #d9e4f3;
            border-radius: 14px;
            box-shadow: var(--shadow-soft);
            padding: 16px;
            display: grid;
            gap: 12px;
        }

        .title { margin: 0; font-size: 1.12rem; letter-spacing: -0.01em; color: #17365a; }
        .enunciado { margin: 0; line-height: 1.7; color: #24476f; }

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

        .meta-item strong { font-size: 12px; text-transform: uppercase; letter-spacing: .08em; color: #5f7390; }
        .meta-item span { color: #21456d; font-weight: 700; }

        .question-image-wrap { display: flex; justify-content: center; }
        .question-image {
            display: block;
            width: 100%;
            max-width: 760px;
            height: auto;
            border-radius: 12px;
            border: 1px solid #d7e4f5;
            background: #fff;
        }

        .alternativas { display: grid; gap: 8px; }
        .alternativa {
            border: 1px solid #d7e3f4;
            border-radius: 12px;
            background: #fff;
            padding: 10px;
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 10px;
            align-items: start;
        }

        .alternativa .letra {
            min-width: 26px;
            min-height: 26px;
            border-radius: 8px;
            background: #e9f1ff;
            color: #1e4573;
            font-size: 12px;
            font-weight: 800;
            display: grid;
            place-items: center;
        }

        .alternativa .texto { color: #25476f; line-height: 1.6; }
        .alternativa-correta { border-color: #bde0ca; background: #f2fbf6; }
        .alternativa-selecionada-incorreta { border-color: #efc8d1; background: #fff6f8; }

        .flag {
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #d3dff1;
            background: #fff;
            color: #2a4b73;
            display: inline-flex;
        }

        .flag.ok { border-color: #b8dfc8; background: #eefaf3; color: #1e5b3c; }
        .flag.bad { border-color: #efc8d1; background: #fff5f7; color: #97253d; }

        .comentario {
            margin: 0;
            border: 1px solid #d8e8d6;
            border-radius: 12px;
            background: #f7fcf7;
            padding: 12px;
            color: #244b2f;
            line-height: 1.7;
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
        $selecionada = strtoupper((string) $resposta->resposta_marcada);
        $gabarito = strtoupper((string) $resposta->gabarito);
        $acertou = (bool) $resposta->acertou;
    @endphp

    <div class="shell">
        <header class="topbar">
            <a class="logo" href="{{ route('simulados.public') }}">
                <span class="logo-badge">EN</span>
                <span>{{ $simulado->name }}</span>
            </a>
            <a class="link-btn" href="{{ route('simulados.result', ['simulado' => $simulado, 'attempt' => $tentativa->id]) }}">Voltar ao resultado</a>
        </header>

        <section class="card">
            <h1 class="title">Detalhe da questao da tentativa #{{ $tentativa->id }}</h1>

            <div class="meta-grid">
                <article class="meta-item">
                    <strong>Questao</strong>
                    <span>#{{ $resposta->questao_id ?? '-' }}</span>
                </article>
                <article class="meta-item">
                    <strong>Resposta marcada</strong>
                    <span>{{ $selecionada !== '' ? $selecionada : '-' }}</span>
                </article>
                <article class="meta-item">
                    <strong>Gabarito</strong>
                    <span>{{ $gabarito !== '' ? $gabarito : '-' }}</span>
                </article>
                <article class="meta-item">
                    <strong>Status</strong>
                    <span>{{ $acertou ? 'Acertou' : 'Errou' }}</span>
                </article>
            </div>

            @if (!$questao)
                <p class="warning">A questao vinculada a esta resposta nao esta mais disponivel.</p>
            @else
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

                <h2 class="title">Enunciado</h2>
                <p class="enunciado">{!! nl2br(e($questao->enunciado)) !!}</p>

                <h2 class="title">Alternativas</h2>
                <div class="alternativas">
                    @foreach ($alternativas as $letra => $texto)
                        @php
                            $isCorreta = $letra === $gabarito;
                            $isSelecionada = $letra === $selecionada;
                            $className = '';

                            if ($isCorreta) {
                                $className = 'alternativa-correta';
                            } elseif ($isSelecionada && !$isCorreta) {
                                $className = 'alternativa-selecionada-incorreta';
                            }
                        @endphp
                        <div class="alternativa {{ $className }}">
                            <span class="letra">{{ $letra }}</span>
                            <div class="texto">
                                <div>{!! nl2br(e($texto)) !!}</div>
                                <div style="margin-top: 6px; display: flex; gap: 8px; flex-wrap: wrap;">
                                    @if ($isSelecionada)
                                        <span class="flag {{ $isCorreta ? 'ok' : 'bad' }}">Selecionada</span>
                                    @endif
                                    @if ($isCorreta)
                                        <span class="flag ok">Correta</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if (!blank($questao->explicacao))
                    <h2 class="title">Comentario da resposta</h2>
                    <p class="comentario">{!! nl2br(e($questao->explicacao)) !!}</p>
                @endif
            @endif
        </section>
    </div>

    @include('partials.feedback-widget')
</body>
</html>

