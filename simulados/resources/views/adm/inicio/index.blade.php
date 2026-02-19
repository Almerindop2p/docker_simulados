@extends('layouts.admin-panel')

@section('title', 'Inicio | Painel ADM')
@section('breadcrumb', 'Painel / Inicio')
@section('page_title', 'Inicio do ADM')

@push('styles')
    <style>
        .stats-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 16px;
            display: grid;
            gap: 6px;
        }

        .stat-label {
            margin: 0;
            color: #58708e;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 700;
        }

        .stat-value {
            margin: 0;
            font-size: clamp(1.3rem, 2.5vw, 1.7rem);
            font-weight: 800;
            color: #173b73;
            letter-spacing: -0.01em;
        }

        .stat-note {
            margin: 0;
            color: var(--text-soft);
            font-size: 13px;
            line-height: 1.55;
        }

        .summary-card {
            background: #f8fbff;
            border: 1px solid #d8e3f3;
            border-radius: var(--radius-lg);
            padding: 14px 16px;
            color: #26476f;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
@endpush

@section('content')
    <section id="admInicioMetrics" class="stats-grid" aria-label="Indicadores de metricas" data-url="{{ route('adm.inicio.metrics') }}">
        <article class="stat-card" data-card="total-visualizacoes">
            <p class="stat-label">Total de visualizacoes</p>
            <h2 class="stat-value" data-value>...</h2>
            <p class="stat-note">Soma real de visitas (page_visit_counters), com fallback em route_metrics.</p>
        </article>

        <article class="stat-card" data-card="visualizacoes-hoje">
            <p class="stat-label">Visualizacoes hoje</p>
            <h2 class="stat-value" data-value>...</h2>
            <p class="stat-note">Contagem desde 00:00 baseada em route_metrics.</p>
        </article>

        <article class="stat-card" data-card="visitantes-unicos">
            <p class="stat-label">Visitantes unicos (24h)</p>
            <h2 class="stat-value" data-value>...</h2>
            <p class="stat-note">Estimativa por chave de visitante nas ultimas 24 horas.</p>
        </article>

        <article class="stat-card" data-card="paginas-mapeadas">
            <p class="stat-label">Paginas mapeadas</p>
            <h2 class="stat-value" data-value>...</h2>
            <p class="stat-note">Quantidade de caminhos monitorados.</p>
        </article>

        <article class="stat-card" data-card="top-pagina">
            <p class="stat-label">Pagina mais visitada</p>
            <h2 class="stat-value" data-value>-</h2>
            <p class="stat-note">Visitas: <strong data-visits>...</strong></p>
        </article>

        <article class="stat-card" data-card="top-pais">
            <p class="stat-label">Pais com mais acessos</p>
            <h2 class="stat-value" data-value>-</h2>
            <p class="stat-note">Visitas: <strong data-visits>...</strong></p>
        </article>
    </section>

    <section id="admInicioSummary" class="summary-card" aria-label="Resumo de captura">
        Ultima captura registrada em
        <span data-ultima-captura>-</span>.
        <br>
        Atualizado em <span data-atualizado-em>-</span>.
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var root = document.getElementById('admInicioMetrics');
            if (!root) {
                return;
            }

            var summary = document.getElementById('admInicioSummary');
            var url = root.getAttribute('data-url');
            if (!url) {
                return;
            }

            function formatNumber(value) {
                var numeric = Number(value || 0);
                return new Intl.NumberFormat('pt-BR').format(numeric);
            }

            function formatDate(value) {
                if (!value) {
                    return '-';
                }

                var date = new Date(value);
                if (isNaN(date.getTime())) {
                    return '-';
                }

                return new Intl.DateTimeFormat('pt-BR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                }).format(date);
            }

            function setText(selector, text) {
                var el = root.querySelector(selector);
                if (el) {
                    el.textContent = text;
                }
            }

            function setSummary(selector, text) {
                if (!summary) {
                    return;
                }

                var el = summary.querySelector(selector);
                if (el) {
                    el.textContent = text;
                }
            }

            function refreshMetrics() {
                fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('request_failed');
                        }
                        return response.json();
                    })
                    .then(function (payload) {
                        setText('[data-card="total-visualizacoes"] [data-value]', formatNumber(payload.total_visualizacoes));
                        setText('[data-card="visualizacoes-hoje"] [data-value]', formatNumber(payload.visualizacoes_hoje));
                        setText('[data-card="visitantes-unicos"] [data-value]', formatNumber(payload.visitantes_unicos_24h));
                        setText('[data-card="paginas-mapeadas"] [data-value]', formatNumber(payload.paginas_mapeadas));
                        setText('[data-card="top-pagina"] [data-value]', payload.top_pagina_path || '-');
                        setText('[data-card="top-pagina"] [data-visits]', formatNumber(payload.top_pagina_visitas));
                        setText('[data-card="top-pais"] [data-value]', payload.top_pais_nome || '-');
                        setText('[data-card="top-pais"] [data-visits]', formatNumber(payload.top_pais_visitas));
                        setSummary('[data-ultima-captura]', formatDate(payload.ultima_captura));
                        setSummary('[data-atualizado-em]', formatDate(payload.atualizado_em));
                    })
                    .catch(function () {
                        // Mantem os valores atuais em caso de falha temporaria.
                    });
            }

            window.addEventListener('load', function () {
                setTimeout(function () {
                    refreshMetrics();
                    setInterval(refreshMetrics, 15000);
                }, 300);
            });
        })();
    </script>
@endpush
