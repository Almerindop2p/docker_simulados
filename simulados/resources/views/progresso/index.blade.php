@extends('layouts.admin-panel')

@section('title', 'Progresso | Area do Aluno')
@section('breadcrumb', 'Inicio / Progresso')
@section('page_title', 'Meu Progresso')

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
        .period-grid { display: grid; gap: 12px; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
        .period-card {
            border: 1px solid #d6e3f6;
            border-radius: 14px;
            background: #f8fbff;
            padding: 12px;
            display: grid;
            gap: 10px;
        }
        .period-title { margin: 0; font-size: 14px; color: #1f416c; font-weight: 800; }
        .pie-wrap { display: flex; align-items: center; gap: 12px; }
        .pie {
            --acertos: 0;
            width: 74px;
            height: 74px;
            border-radius: 999px;
            background: conic-gradient(
                #1f5fe0 0 calc(var(--acertos) * 1%),
                #e14b4b calc(var(--acertos) * 1%) 100%
            );
            position: relative;
            flex: 0 0 auto;
        }
        .pie.pie-empty {
            background: conic-gradient(#e1e9f7 0 100%);
        }
        .pie::after {
            content: '';
            position: absolute;
            inset: 10px;
            background: #fff;
            border-radius: inherit;
            border: 1px solid #e4ecf8;
        }
        .pie-value {
            position: absolute;
            inset: 0;
            z-index: 1;
            display: grid;
            place-items: center;
            font-weight: 800;
            color: #23496f;
            font-size: 12px;
        }
        .legend { display: grid; gap: 4px; font-size: 12px; color: #446383; }
        .legend strong { color: #213f66; }
        .tables-grid { display: grid; gap: 14px; grid-template-columns: 1fr; }
        .table-wrap { border: 1px solid #d8e3f2; border-radius: 12px; background: #fff; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 620px; }
        th, td { padding: 11px 12px; border-bottom: 1px solid #e7eef8; text-align: left; font-size: 14px; vertical-align: top; }
        th { font-size: 12px; text-transform: uppercase; letter-spacing: .08em; color: #60738d; background: #f7faff; }
        tr:last-child td { border-bottom: 0; }
        .preview { max-width: 380px; line-height: 1.5; color: #27496f; }
        .meta { color: #5b7290; font-size: 13px; white-space: nowrap; }
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
            cursor: pointer;
        }
        .actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .empty { margin: 0; border: 1px dashed #ccd9ee; background: #f9fbff; color: #446282; border-radius: 10px; padding: 10px; font-size: 14px; }
        @media (min-width: 1024px) {
            .tables-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
@endpush

@section('content')
    @php
        $periodLabels = [
            'hoje' => 'Hoje',
            'ultimos_7' => 'Ultimos 7 dias',
            'ultimos_30' => 'Ultimos 30 dias',
            'ultimos_90' => 'Ultimos 90 dias',
        ];
    @endphp

    <section class="card">
        <h2 class="title">Visao geral por periodo</h2>
        <p class="muted">Graficos de pizza com a distribuicao de acertos e erros conforme o historico salvo em <code>questao_respostas</code>.</p>

        <div class="period-grid">
            @foreach ($periodLabels as $periodKey => $periodLabel)
                @php
                    $stats = $periodStats[$periodKey] ?? ['total' => 0, 'acertos' => 0, 'erros' => 0, 'acertos_percentual' => 0, 'erros_percentual' => 0];
                @endphp
                <article class="period-card">
                    <h3 class="period-title">{{ $periodLabel }}</h3>
                    <div class="pie-wrap">
                        <div class="pie {{ $stats['total'] > 0 ? '' : 'pie-empty' }}" style="--acertos: {{ $stats['acertos_percentual'] }};">
                            <div class="pie-value">{{ (int) round($stats['acertos_percentual']) }}%</div>
                        </div>
                        <div class="legend">
                            <span><strong>Total:</strong> {{ $stats['total'] }}</span>
                            <span><strong>Acertos:</strong> {{ $stats['acertos'] }} ({{ $stats['acertos_percentual'] }}%)</span>
                            <span><strong>Erros:</strong> {{ $stats['erros'] }} ({{ $stats['erros_percentual'] }}%)</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="tables-grid">
        <article class="card" id="erros-table">
            <h2 class="title">Ultimos erros</h2>
            @if ($erros->isEmpty())
                <p class="empty">Nenhum erro registrado ate o momento.</p>
            @else
                <div class="table-wrap">
                    <table aria-label="Ultimos erros">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Questao</th>
                                <th>Data</th>
                                <th>Acao</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($erros as $resposta)
                                <tr>
                                    <td>{{ $resposta->id }}</td>
                                    <td class="preview">
                                        {{ \Illuminate\Support\Str::limit($resposta->questao?->enunciado ?? 'Questao indisponivel (removida).', 120) }}
                                    </td>
                                    <td class="meta">{{ optional($resposta->respondida_em)->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>
                                        <a class="btn-soft" href="{{ route('progresso.show', $resposta) }}">Ver</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($erros->hasMorePages())
                    <div class="actions">
                        <a class="btn-soft" href="{{ $erros->nextPageUrl() }}#erros-table">Carregar mais</a>
                    </div>
                @endif
            @endif
        </article>

        <article class="card" id="acertos-table">
            <h2 class="title">Ultimos acertos</h2>
            @if ($acertos->isEmpty())
                <p class="empty">Nenhum acerto registrado ate o momento.</p>
            @else
                <div class="table-wrap">
                    <table aria-label="Ultimos acertos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Questao</th>
                                <th>Data</th>
                                <th>Acao</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($acertos as $resposta)
                                <tr>
                                    <td>{{ $resposta->id }}</td>
                                    <td class="preview">
                                        {{ \Illuminate\Support\Str::limit($resposta->questao?->enunciado ?? 'Questao indisponivel (removida).', 120) }}
                                    </td>
                                    <td class="meta">{{ optional($resposta->respondida_em)->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>
                                        <a class="btn-soft" href="{{ route('progresso.show', $resposta) }}">Ver</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($acertos->hasMorePages())
                    <div class="actions">
                        <a class="btn-soft" href="{{ $acertos->nextPageUrl() }}#acertos-table">Carregar mais</a>
                    </div>
                @endif
            @endif
        </article>
    </section>
@endsection
