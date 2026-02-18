@extends('layouts.admin-panel')

@section('title', 'Detalhe da Resposta | Progresso')
@section('breadcrumb', 'Inicio / Progresso / Detalhe')
@section('page_title', 'Detalhe da Resposta')

@push('styles')
    <style>
        .grid { display: grid; gap: 14px; }
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 16px;
            display: grid;
            gap: 12px;
        }
        .title { margin: 0; font-size: 1.12rem; letter-spacing: -0.01em; }
        .muted { margin: 0; color: var(--text-soft); font-size: 14px; line-height: 1.6; }
        .alert {
            border-radius: 12px;
            padding: 11px 12px;
            font-size: 14px;
            line-height: 1.6;
            font-weight: 600;
        }
        .alert.success { border: 1px solid #cbe7d5; background: #f0fbf4; color: #1f5d3e; }
        .alert.error { border: 1px solid #efcfd6; background: #fff6f8; color: #92233b; }
        .alert.warn { border: 1px solid #eadfc4; background: #fffbf4; color: #7d571d; }
        .meta-grid { display: grid; gap: 10px; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
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
        .enunciado {
            margin: 0;
            border: 1px solid #d7e3f4;
            border-radius: 12px;
            background: #fff;
            padding: 12px;
            color: #27496f;
            line-height: 1.7;
        }
        .comentario {
            margin: 0;
            border: 1px solid #d8e8d6;
            border-radius: 12px;
            background: #f7fcf7;
            padding: 12px;
            color: #244b2f;
            line-height: 1.7;
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
        .flags { display: flex; gap: 8px; flex-wrap: wrap; }
        .flag {
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #d3dff1;
            background: #fff;
            color: #2a4b73;
        }
        .flag.ok { border-color: #b8dfc8; background: #eefaf3; color: #1e5b3c; }
        .flag.bad { border-color: #efc8d1; background: #fff5f7; color: #97253d; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-soft {
            min-height: 40px;
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
        }
    </style>
@endpush

@section('content')
    @php
        $selecionada = strtoupper((string) $resposta->resposta_marcada);
        $gabarito = strtoupper((string) $resposta->gabarito);
        $acertou = (bool) $resposta->acertou;
    @endphp

    <section class="grid">
        <article class="card">
            <h2 class="title">Resultado da tentativa #{{ $resposta->id }}</h2>
            @if ($acertou)
                <p class="alert success">Voce acertou! Excelente desempenho nesta questao. Continue nesse ritmo.</p>
            @else
                <p class="alert error">Voce errou. Nao desanime: revisar os detalhes da resposta ajuda a evoluir mais rapido.</p>
            @endif

            <div class="meta-grid">
                <div class="meta-item">
                    <strong>Data da resposta</strong>
                    <span>{{ optional($resposta->respondida_em)->format('d/m/Y H:i') ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <strong>Banca</strong>
                    <span>{{ $resposta->banca?->name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <strong>Materia</strong>
                    <span>{{ $resposta->materia?->name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <strong>Questao ID</strong>
                    <span>{{ $resposta->questao_id ?? '-' }}</span>
                </div>
            </div>

            <div class="flags">
                <span class="flag {{ $acertou ? 'ok' : 'bad' }}">Selecionada: {{ $selecionada }}</span>
                <span class="flag ok">Correta: {{ $gabarito }}</span>
            </div>
        </article>

        @if (!$questao)
            <article class="card">
                <p class="alert warn">A questao desta tentativa nao esta mais disponivel (pode ter sido removida). Os dados da resposta foram preservados no historico.</p>
            </article>
        @else
            <article class="card">
                <h3 class="title">Enunciado</h3>
                <p class="enunciado">{!! nl2br(e($questao->enunciado)) !!}</p>
            </article>

            <article class="card">
                <h3 class="title">Alternativas</h3>
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
                                <div class="flags" style="margin-top: 6px;">
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
            </article>

            @if (!blank($questao->explicacao))
                <article class="card">
                    <h3 class="title">Comentario da resposta</h3>
                    <p class="comentario">{!! nl2br(e($questao->explicacao)) !!}</p>
                </article>
            @endif
        @endif

        <div class="actions">
            <a class="btn-soft" href="{{ route('progresso.index') }}">Voltar para progresso</a>
        </div>
    </section>
@endsection
