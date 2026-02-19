<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Simulado | {{ $simulado->name }}</title>
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
            padding: 20px;
            display: grid;
            gap: 16px;
        }

        .result-inline-vertical-ad {
            width: min(960px, 100%);
            min-height: 96px;
            margin: 0 auto;
            border: 1px dashed #b6bfcb;
            border-radius: 12px;
            background: #e7eaef;
            box-shadow: 0 8px 24px rgba(16, 36, 63, 0.08);
            padding: 10px;
            overflow: hidden;
        }

        .result-inline-vertical-ad-placeholder {
            min-height: 76px;
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

        .title {
            margin: 0;
            font-size: clamp(1.25rem, 2.5vw, 1.8rem);
            color: #17365a;
            text-align: center;
        }

        .subtitle {
            margin: 0;
            color: #4b6380;
            line-height: 1.6;
            text-align: center;
        }

        .summary {
            display: grid;
            gap: 18px;
            justify-items: center;
        }

        .pie {
            --acertos: 0;
            width: min(360px, 70vw);
            aspect-ratio: 1 / 1;
            border-radius: 999px;
            background: conic-gradient(
                #1f5fe0 0 calc(var(--acertos) * 1%),
                #e14b4b calc(var(--acertos) * 1%) 100%
            );
            position: relative;
        }

        .pie::after {
            content: '';
            position: absolute;
            inset: 54px;
            border-radius: inherit;
            background: #fff;
            border: 1px solid #e4ecf8;
        }

        .pie-center {
            position: absolute;
            inset: 0;
            z-index: 1;
            display: grid;
            place-items: center;
            text-align: center;
            gap: 2px;
        }

        .pie-center strong {
            font-size: clamp(1.2rem, 3vw, 1.8rem);
            color: #1c3f67;
        }

        .pie-center span {
            font-size: 12px;
            color: #5a6f8a;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .legend {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .chip {
            border: 1px solid #d2dff2;
            background: #fff;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            color: #2a4c75;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            display: inline-block;
            margin-right: 6px;
        }

        .dot.acerto { background: #1f5fe0; }
        .dot.erro { background: #e14b4b; }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .tables-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: 1fr;
            margin-top: 10px;
        }

        .table-card {
            border: 1px solid #d8e3f2;
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
            display: grid;
            gap: 0;
        }

        .table-head {
            margin: 0;
            padding: 12px;
            border-bottom: 1px solid #e6eef8;
            font-size: 14px;
            color: #20466f;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th, td {
            padding: 11px 12px;
            border-bottom: 1px solid #e7eef8;
            text-align: left;
            font-size: 14px;
            vertical-align: top;
        }

        th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #60738d;
            background: #f7faff;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .preview {
            color: #27496f;
            line-height: 1.55;
            max-width: 460px;
        }

        .chip-table {
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #d3dff1;
            background: #fff;
            color: #2a4b73;
            display: inline-block;
        }

        .chip-table.ok {
            border-color: #b8dfc8;
            background: #eefaf3;
            color: #1e5b3c;
        }

        .chip-table.bad {
            border-color: #efc8d1;
            background: #fff5f7;
            color: #97253d;
        }

        .btn-soft {
            min-height: 36px;
            border-radius: 10px;
            border: 1px solid #cddbef;
            background: #fff;
            color: #23496f;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            font-size: 13px;
            margin-top: 4px;
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
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn.primary {
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, #2264e5, #588dff);
            box-shadow: 0 10px 18px rgba(31, 95, 224, 0.24);
        }

    </style>
</head>
<body>
    @php
        $totalSeguro = max(1, (int) $total);
        $acertosPercent = round(((int) $acertos / $totalSeguro) * 100, 1);
        $errosPercent = round(((int) $erros / $totalSeguro) * 100, 1);
        $horas = intdiv((int) $totalElapsedSeconds, 3600);
        $minutos = intdiv(((int) $totalElapsedSeconds % 3600), 60);
        $segundos = (int) $totalElapsedSeconds % 60;
        $tempoFmt = $horas > 0
            ? sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos)
            : sprintf('%02d:%02d', $minutos, $segundos);
    @endphp

    <div class="shell">
        <header class="topbar">
            <a class="logo" href="{{ route('home') }}">
                <span class="logo-badge">EN</span>
                <span>{{ $simulado->name }}</span>
            </a>
            <a class="link-btn" href="{{ route('simulados.public') }}">Voltar para simulados</a>
        </header>

        @include('partials.ad-slot', [
            'format' => 'horizontal',
            'tag' => 'section',
            'slotClass' => 'result-inline-vertical-ad',
            'placeholderClass' => 'result-inline-vertical-ad-placeholder',
            'placeholder' => 'Espaco anuncio horizontal',
            'ariaLabel' => 'Publicidade horizontal do resultado',
        ])

        <section class="card">
            <h1 class="title">Resultado final do simulado</h1>
            <p class="subtitle">Tentativa {{ $isGuest ? 'anonima' : 'registrada' }} finalizada. Tempo total: <strong>{{ $tempoFmt }}</strong>.</p>

            <div class="summary">
                <div class="pie" style="--acertos: {{ $acertosPercent }};">
                    <div class="pie-center">
                        <strong>{{ $acertos }} / {{ $total }}</strong>
                        <span>Acertos</span>
                    </div>
                </div>

                <div class="legend">
                    <span class="chip"><span class="dot acerto"></span>Acertos: {{ $acertos }} ({{ $acertosPercent }}%)</span>
                    <span class="chip"><span class="dot erro"></span>Erros: {{ $erros }} ({{ $errosPercent }}%)</span>
                </div>
            </div>

            <div class="actions">
                <form method="POST" action="{{ route('simulados.start', $simulado) }}">
                    @csrf
                    <button class="btn primary" type="submit">Realizar novamente</button>
                </form>
                <a class="btn" href="{{ route('simulados.public') }}">Voltar ao catalogo</a>
                @if ($isGuest && $resumeUrl)
                    <a class="btn" href="{{ $resumeUrl }}">Retomar estado anonimo</a>
                @endif
            </div>

            @if (!$isGuest)
                <p class="subtitle">Tentativa #{{ $attemptId }} salva para o usuario logado.</p>

                <section class="tables-grid">
                    <article class="table-card">
                        <h2 class="table-head">Questoes com erro ({{ $errosRows->count() }})</h2>
                        @if ($errosRows->isEmpty())
                            <p class="subtitle" style="padding: 12px;">Nenhum erro registrado nesta tentativa.</p>
                        @else
                            <div class="table-wrap">
                                <table aria-label="Tabela de questoes com erro">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Previa</th>
                                            <th>Status</th>
                                            <th>Acao</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($errosRows as $row)
                                            <tr>
                                                <td>{{ $row->questao_id ?? '-' }}</td>
                                                <td class="preview">
                                                    {{ \Illuminate\Support\Str::limit($row->questao?->enunciado ?? 'Questao indisponivel (removida).', 110) }}
                                                </td>
                                                <td>
                                                    <span class="chip-table bad">
                                                        {{ blank($row->resposta_marcada) ? 'Nao respondida' : 'Errou' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a
                                                        class="btn-soft"
                                                        href="{{ route('simulados.result.question', ['simulado' => $simulado, 'tentativa' => $attemptId, 'resposta' => $row->id]) }}"
                                                    >
                                                        Ver resposta
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </article>

                    <article class="table-card">
                        <h2 class="table-head">Questoes com acerto ({{ $acertosRows->count() }})</h2>
                        @if ($acertosRows->isEmpty())
                            <p class="subtitle" style="padding: 12px;">Nenhum acerto registrado nesta tentativa.</p>
                        @else
                            <div class="table-wrap">
                                <table aria-label="Tabela de questoes com acerto">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Previa</th>
                                            <th>Status</th>
                                            <th>Acao</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($acertosRows as $row)
                                            <tr>
                                                <td>{{ $row->questao_id ?? '-' }}</td>
                                                <td class="preview">
                                                    {{ \Illuminate\Support\Str::limit($row->questao?->enunciado ?? 'Questao indisponivel (removida).', 110) }}
                                                </td>
                                                <td><span class="chip-table ok">Acertou</span></td>
                                                <td>
                                                    <a
                                                        class="btn-soft"
                                                        href="{{ route('simulados.result.question', ['simulado' => $simulado, 'tentativa' => $attemptId, 'resposta' => $row->id]) }}"
                                                    >
                                                        Ver resposta
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </article>
                </section>
            @endif
        </section>
    </div>

    @include('partials.feedback-widget')
    @include('partials.adsense-placements')
</body>
</html>
