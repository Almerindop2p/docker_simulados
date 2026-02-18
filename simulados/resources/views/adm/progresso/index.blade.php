@extends('layouts.admin-panel')

@section('title', 'Progresso Geral | Painel Admin')
@section('breadcrumb', 'Admin / Progresso')
@section('page_title', 'Progresso Geral')

@push('styles')
    <style>
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
        .period-grid { display: grid; gap: 12px; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); }
        .period-card {
            border: 1px solid #d6e3f6;
            border-radius: 14px;
            background: #f8fbff;
            padding: 12px;
            display: grid;
            gap: 10px;
        }
        .period-title { margin: 0; font-size: 14px; color: #1f416c; font-weight: 800; }
        .pie-wrap { display: flex; align-items: center; gap: 16px; }
        .pie {
            --acertos: 0;
            width: clamp(132px, 18vw, 180px);
            height: clamp(132px, 18vw, 180px);
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
            inset: clamp(18px, 2.8vw, 26px);
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
            font-size: clamp(18px, 2.6vw, 24px);
        }
        .legend { display: grid; gap: 5px; font-size: 13px; color: #446383; }
        .legend strong { color: #213f66; }
        @media (max-width: 640px) {
            .pie-wrap { flex-direction: column; align-items: flex-start; }
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
        <h2 class="title">Visao geral do sistema por periodo</h2>
        <p class="muted">Graficos de pizza agregados com base em todas as respostas salvas em <code>questao_respostas</code>.</p>

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
@endsection
