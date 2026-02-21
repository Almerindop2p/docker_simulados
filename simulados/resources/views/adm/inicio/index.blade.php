@extends('layouts.admin-panel')

@section('title', 'Inicio | Painel ADM')
@section('breadcrumb', 'Painel / Inicio')
@section('page_title', 'Inicio do ADM')

@push('styles')
    <style>
        .dashboard-load {
            display: none;
            align-items: center;
            gap: 10px;
            border: 1px solid #d8e4f5;
            background: #f6faff;
            color: #2a4c74;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 700;
        }

        .dashboard-load.is-active {
            display: inline-flex;
        }

        .dashboard-load-spinner {
            width: 14px;
            height: 14px;
            border-radius: 999px;
            border: 2px solid #aec7eb;
            border-top-color: #1f5fe0;
            animation: adm-dash-spin .7s linear infinite;
            flex: 0 0 auto;
        }

        .loading-fade {
            transition: opacity .2s ease;
        }

        .loading-fade.is-loading {
            opacity: .62;
            pointer-events: none;
        }

        @keyframes adm-dash-spin {
            to { transform: rotate(360deg); }
        }

        .stats-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .stat-card,
        .panel-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 16px;
            display: grid;
            gap: 10px;
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
            font-size: clamp(1.2rem, 2.2vw, 1.6rem);
            font-weight: 800;
            color: #173b73;
            letter-spacing: -0.01em;
            word-break: break-word;
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

        .analytics-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: 1fr;
            margin-top: 4px;
        }

        .panel-title {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            color: #173b73;
            letter-spacing: -0.01em;
        }

        .panel-subtitle {
            margin: 0;
            font-size: 13px;
            color: #516a88;
            line-height: 1.55;
        }

        .pie-wrap {
            display: grid;
            grid-template-columns: 170px 1fr;
            align-items: center;
            gap: 12px;
        }

        .pie-chart {
            width: 160px;
            height: 160px;
            border-radius: 999px;
            background: conic-gradient(#d9e3f4 0 100%);
            position: relative;
            border: 1px solid #d3e0f2;
        }

        .pie-chart::after {
            content: '';
            position: absolute;
            inset: 27px;
            border-radius: inherit;
            background: #fff;
            border: 1px solid #e3ebf8;
        }

        .pie-center {
            position: absolute;
            inset: 0;
            z-index: 1;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 18px;
            color: #173b73;
        }

        .legend {
            display: grid;
            gap: 8px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 13px;
            color: #35557c;
        }

        .legend-main {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-width: 0;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            flex: 0 0 auto;
        }

        .legend-label {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .map-wrap {
            display: grid;
            gap: 10px;
        }

        .map-canvas {
            width: 100%;
            max-width: 520px;
            background: linear-gradient(180deg, #f7fbff 0%, #eef4ff 100%);
            border: 1px solid #d3dff2;
            border-radius: 14px;
            overflow: hidden;
        }

        .map-canvas svg {
            width: 100%;
            height: auto;
            display: block;
        }

        .map-grid-line {
            stroke: #d7e3f5;
            stroke-width: 1;
            opacity: .8;
        }

        .map-point {
            stroke: #fff;
            stroke-width: 1.3;
            fill: #1f5fe0;
            opacity: .92;
        }

        .split-lists {
            display: grid;
            gap: 10px;
            grid-template-columns: 1fr;
        }

        .tiny-list {
            border: 1px solid #d7e2f3;
            border-radius: 12px;
            padding: 10px;
            background: #f9fbff;
            display: grid;
            gap: 8px;
        }

        .tiny-list h4 {
            margin: 0;
            font-size: 13px;
            color: #1f426f;
        }

        .tiny-list ul {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 6px;
        }

        .tiny-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #36567b;
        }

        .table-wrap {
            border: 1px solid #d8e3f3;
            border-radius: 12px;
            overflow: auto;
            background: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
        }

        th,
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e7eef9;
            font-size: 13px;
            color: #2c4d74;
            text-align: left;
            vertical-align: top;
        }

        th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #f7fbff;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #5b7392;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 700;
            background: #edf3ff;
            color: #2b4e79;
            border: 1px solid #d6e2f6;
        }

        @media (min-width: 920px) {
            .analytics-grid {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            }

            .split-lists {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            }
        }

        @media (max-width: 720px) {
            .pie-wrap {
                grid-template-columns: 1fr;
            }

            .pie-chart {
                margin: 0 auto;
            }
        }
    </style>
@endpush

@section('content')
    <div id="dashboardLoadState" class="dashboard-load is-active" role="status" aria-live="polite">
        <span class="dashboard-load-spinner" aria-hidden="true"></span>
        <span data-load-message>Carregando metricas...</span>
    </div>

    <section id="admInicioMetrics"
        class="stats-grid loading-fade is-loading"
        aria-label="Indicadores de metricas"
        data-summary-url="{{ route('adm.inicio.metrics') }}"
        data-details-url="{{ route('adm.inicio.metrics.details') }}"
    >
        <article class="stat-card" data-card="total-visualizacoes">
            <p class="stat-label">Total de visualizacoes</p>
            <h2 class="stat-value" data-value>...</h2>
            <p class="stat-note">Soma de visitas da tabela de contadores.</p>
        </article>

        <article class="stat-card" data-card="visualizacoes-hoje">
            <p class="stat-label">Visualizacoes hoje</p>
            <h2 class="stat-value" data-value>...</h2>
            <p class="stat-note">Contagem de entrada no dia atual.</p>
        </article>

        <article class="stat-card" data-card="visitantes-unicos">
            <p class="stat-label">Visitantes unicos (24h)</p>
            <h2 class="stat-value" data-value>...</h2>
            <p class="stat-note">Estimativa por chave de visitante.</p>
        </article>

        <article class="stat-card" data-card="paginas-mapeadas">
            <p class="stat-label">Paginas mapeadas</p>
            <h2 class="stat-value" data-value>...</h2>
            <p class="stat-note">Quantidade de paginas monitoradas.</p>
        </article>

        <article class="stat-card" data-card="consentimentos-ativos">
            <p class="stat-label">Consentimentos ativos (7d)</p>
            <h2 class="stat-value" data-value>...</h2>
            <p class="stat-note">Usuarios com consentimento LGPD valido em user_metric_consents.</p>
        </article>
    </section>

    <section id="admInicioSummary" class="summary-card loading-fade is-loading" aria-label="Resumo de captura">
        Ultima captura registrada em
        <span data-ultima-captura>-</span>.
        <br>
        Atualizado em <span data-atualizado-em>-</span>.
    </section>

    <section id="admInicioAnalytics" class="analytics-grid loading-fade is-loading" aria-label="Graficos e mapa de metricas">
        <article class="panel-card">
            <h3 class="panel-title">Uso por navegador</h3>
            <p class="panel-subtitle">Distribuicao de uso com agrupamento em Chrome, Firefox, Opera, Edge e demais.</p>
            <div class="pie-wrap">
                <div class="pie-chart" id="browserPieChart">
                    <div class="pie-center" id="browserPieCenter">0%</div>
                </div>
                <div class="legend" id="browserLegend">
                    <div class="legend-item">Carregando...</div>
                </div>
            </div>
        </article>

        <article class="panel-card">
            <h3 class="panel-title">Top estados por visualizacao</h3>
            <p class="panel-subtitle">Estados com maior volume de visualizacoes registradas.</p>
            <div class="pie-wrap">
                <div class="pie-chart" id="topStatesPieChart">
                    <div class="pie-center" id="topStatesPieCenter">0</div>
                </div>
                <div class="legend" id="topStatesLegend">
                    <div class="legend-item">Carregando...</div>
                </div>
            </div>
        </article>

        <article class="panel-card">
            <h3 class="panel-title">Top paises por visualizacao</h3>
            <p class="panel-subtitle">Paises com maior volume de visualizacoes registradas.</p>
            <div class="pie-wrap">
                <div class="pie-chart" id="topCountriesPieChart">
                    <div class="pie-center" id="topCountriesPieCenter">0</div>
                </div>
                <div class="legend" id="topCountriesLegend">
                    <div class="legend-item">Carregando...</div>
                </div>
            </div>
        </article>

        <article class="panel-card">
            <h3 class="panel-title">Mapa 2D de paises de acesso</h3>
            <p class="panel-subtitle">Visao geografica simplificada por coordenadas e agrupamento regional.</p>

            <div class="map-wrap">
                <div class="map-canvas">
                    <svg viewBox="0 0 520 260" aria-label="Mapa 2D simplificado">
                        <line class="map-grid-line" x1="0" y1="65" x2="520" y2="65"></line>
                        <line class="map-grid-line" x1="0" y1="130" x2="520" y2="130"></line>
                        <line class="map-grid-line" x1="0" y1="195" x2="520" y2="195"></line>
                        <line class="map-grid-line" x1="130" y1="0" x2="130" y2="260"></line>
                        <line class="map-grid-line" x1="260" y1="0" x2="260" y2="260"></line>
                        <line class="map-grid-line" x1="390" y1="0" x2="390" y2="260"></line>
                        <g id="countryMapPoints"></g>
                    </svg>
                </div>

                <div class="split-lists">
                    <div class="tiny-list">
                        <h4>Top paises</h4>
                        <ul id="countriesList">
                            <li>Carregando...</li>
                        </ul>
                    </div>
                    <div class="tiny-list">
                        <h4>Agrupamento por regiao</h4>
                        <ul id="regionsList">
                            <li>Carregando...</li>
                        </ul>
                    </div>
                </div>
            </div>
        </article>
    </section>

    <section id="admInicioRecent" class="panel-card loading-fade is-loading" aria-label="Acessos recentes">
        <h3 class="panel-title">Acessos recentes e detalhes</h3>
        <p class="panel-subtitle">Ultimos registros de metricas com IP, usuario, rota, pagina e contexto tecnico.</p>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data/Hora</th>
                        <th>IP</th>
                        <th>Usuario</th>
                        <th>Tipo</th>
                        <th>Rota</th>
                        <th>Pagina</th>
                        <th>Pais/Estado/Cidade</th>
                        <th>Navegador</th>
                        <th>Dispositivo/SO</th>
                        <th>Modo</th>
                    </tr>
                </thead>
                <tbody id="recentAccessTableBody">
                    <tr>
                        <td colspan="11">Carregando dados...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var root = document.getElementById('admInicioMetrics');
            if (!root) {
                return;
            }

            var summaryUrl = root.getAttribute('data-summary-url');
            var detailsUrl = root.getAttribute('data-details-url');
            var summary = document.getElementById('admInicioSummary');
            var browserPieChart = document.getElementById('browserPieChart');
            var browserPieCenter = document.getElementById('browserPieCenter');
            var browserLegend = document.getElementById('browserLegend');
            var topStatesPieChart = document.getElementById('topStatesPieChart');
            var topStatesPieCenter = document.getElementById('topStatesPieCenter');
            var topStatesLegend = document.getElementById('topStatesLegend');
            var topCountriesPieChart = document.getElementById('topCountriesPieChart');
            var topCountriesPieCenter = document.getElementById('topCountriesPieCenter');
            var topCountriesLegend = document.getElementById('topCountriesLegend');
            var countryMapPoints = document.getElementById('countryMapPoints');
            var countriesList = document.getElementById('countriesList');
            var regionsList = document.getElementById('regionsList');
            var recentAccessTableBody = document.getElementById('recentAccessTableBody');
            var dashboardLoadState = document.getElementById('dashboardLoadState');
            var analyticsSection = document.getElementById('admInicioAnalytics');
            var recentSection = document.getElementById('admInicioRecent');
            var loadingTargets = [root, summary, analyticsSection, recentSection];
            var loadingCount = 0;

            if (!summaryUrl || !detailsUrl || !summary) {
                return;
            }

            function escapeHtml(value) {
                return String(value || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function formatNumber(value) {
                return new Intl.NumberFormat('pt-BR').format(Number(value || 0));
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

            function setText(selector, value) {
                var el = root.querySelector(selector);
                if (el) {
                    el.textContent = value;
                }
            }

            function setSummary(selector, value) {
                var el = summary.querySelector(selector);
                if (el) {
                    el.textContent = value;
                }
            }

            function fetchJson(url) {
                return fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                }).then(function (response) {
                    if (!response.ok) {
                        throw new Error('request_failed');
                    }
                    return response.json();
                });
            }

            function setTargetsLoading(isLoading) {
                loadingTargets.forEach(function (target) {
                    if (!target) {
                        return;
                    }
                    target.classList.toggle('is-loading', isLoading);
                });
            }

            function setDashboardLoading(isActive, message) {
                if (!dashboardLoadState) {
                    return;
                }

                dashboardLoadState.classList.toggle('is-active', isActive);
                var messageEl = dashboardLoadState.querySelector('[data-load-message]');
                if (messageEl && message) {
                    messageEl.textContent = message;
                }
            }

            function beginLoading(isInitial, message) {
                loadingCount += 1;
                if (isInitial) {
                    setTargetsLoading(true);
                }
                setDashboardLoading(true, message || 'Carregando metricas...');
            }

            function endLoading(isInitial) {
                loadingCount = Math.max(0, loadingCount - 1);

                if (loadingCount === 0) {
                    setDashboardLoading(false);
                    setTargetsLoading(false);
                    return;
                }

                if (!isInitial) {
                    setTargetsLoading(false);
                }
            }

            function refreshSummary(isInitial) {
                beginLoading(!!isInitial, isInitial ? 'Carregando dados de resumo...' : 'Atualizando resumo...');
                fetchJson(summaryUrl)
                    .then(function (payload) {
                        setText('[data-card="total-visualizacoes"] [data-value]', formatNumber(payload.total_visualizacoes));
                        setText('[data-card="visualizacoes-hoje"] [data-value]', formatNumber(payload.visualizacoes_hoje));
                        setText('[data-card="visitantes-unicos"] [data-value]', formatNumber(payload.visitantes_unicos_24h));
                        setText('[data-card="paginas-mapeadas"] [data-value]', formatNumber(payload.paginas_mapeadas));
                        setText('[data-card="consentimentos-ativos"] [data-value]', formatNumber(payload.consentimentos_ativos));
                        setSummary('[data-ultima-captura]', formatDate(payload.ultima_captura));
                        setSummary('[data-atualizado-em]', formatDate(payload.atualizado_em));
                    })
                    .catch(function () {
                        // Mantem valores atuais em caso de falha temporaria.
                    })
                    .finally(function () {
                        endLoading(!!isInitial);
                    });
            }

            function renderDistributionPie(chart, center, legend, items, emptyMessage, centerTextResolver) {
                if (!chart || !center || !legend) {
                    return;
                }

                if (!Array.isArray(items) || items.length === 0) {
                    chart.style.background = 'conic-gradient(#d9e3f4 0 100%)';
                    center.textContent = '0';
                    legend.innerHTML = '<div class="legend-item">' + escapeHtml(emptyMessage || 'Sem dados.') + '</div>';
                    return;
                }

                var total = items.reduce(function (acc, item) {
                    return acc + Number(item.count || 0);
                }, 0);

                var start = 0;
                var slices = [];
                var legendHtml = [];

                items.forEach(function (item) {
                    var percent = Number(item.percent || 0);
                    var color = item.color || '#8aa1bf';
                    var end = Math.min(100, start + percent);
                    slices.push(color + ' ' + start + '% ' + end + '%');

                    legendHtml.push(
                        '<div class="legend-item">' +
                            '<span class="legend-main">' +
                                '<span class="legend-dot" style="background:' + escapeHtml(color) + '"></span>' +
                                '<span class="legend-label">' + escapeHtml(item.label || 'Demais') + '</span>' +
                            '</span>' +
                            '<strong>' + formatNumber(item.count || 0) + ' (' + percent.toFixed(1).replace('.', ',') + '%)</strong>' +
                        '</div>'
                    );

                    start = end;
                });

                if (slices.length === 0 || total === 0) {
                    slices = ['#d9e3f4 0 100%'];
                }

                chart.style.background = 'conic-gradient(' + slices.join(',') + ')';
                center.textContent = typeof centerTextResolver === 'function'
                    ? centerTextResolver(total)
                    : (total > 0 ? '100%' : '0%');
                legend.innerHTML = legendHtml.join('');
            }

            function renderBrowserPie(items) {
                renderDistributionPie(
                    browserPieChart,
                    browserPieCenter,
                    browserLegend,
                    items,
                    'Sem dados de navegador.',
                    function (total) {
                        return total > 0 ? '100%' : '0%';
                    }
                );
            }

            function renderTopStatesPie(items) {
                renderDistributionPie(
                    topStatesPieChart,
                    topStatesPieCenter,
                    topStatesLegend,
                    items,
                    'Sem dados de estado.',
                    function (total) {
                        return formatNumber(total);
                    }
                );
            }

            function renderTopCountriesPie(items) {
                renderDistributionPie(
                    topCountriesPieChart,
                    topCountriesPieCenter,
                    topCountriesLegend,
                    items,
                    'Sem dados de pais.',
                    function (total) {
                        return formatNumber(total);
                    }
                );
            }

            function projectPoint(lat, lon, width, height) {
                var x = ((lon + 180) / 360) * width;
                var y = ((90 - lat) / 180) * height;

                return { x: x, y: y };
            }

            function renderMapPoints(points) {
                if (!countryMapPoints) {
                    return;
                }

                if (!Array.isArray(points) || points.length === 0) {
                    countryMapPoints.innerHTML = '';
                    return;
                }

                var maxVisits = points.reduce(function (acc, item) {
                    var visits = Number(item.visits || 0);
                    return visits > acc ? visits : acc;
                }, 0);

                var width = 520;
                var height = 260;
                var circles = '';

                points.forEach(function (item) {
                    var lat = Number(item.lat);
                    var lon = Number(item.lon);
                    if (isNaN(lat) || isNaN(lon)) {
                        return;
                    }

                    var projected = projectPoint(lat, lon, width, height);
                    var visits = Number(item.visits || 0);
                    var radius = maxVisits > 0 ? (4 + ((visits / maxVisits) * 8)) : 5;
                    var title = escapeHtml((item.country || '-') + ' - ' + formatNumber(visits) + ' visitas');

                    circles += '<circle class="map-point" cx="' + projected.x.toFixed(2) + '" cy="' + projected.y.toFixed(2) + '" r="' + radius.toFixed(2) + '">' +
                        '<title>' + title + '</title>' +
                    '</circle>';
                });

                countryMapPoints.innerHTML = circles;
            }

            function renderCountriesList(items) {
                if (!countriesList) {
                    return;
                }

                if (!Array.isArray(items) || items.length === 0) {
                    countriesList.innerHTML = '<li>Sem dados de pais.</li>';
                    return;
                }

                var html = items.map(function (item) {
                    return '<li>' +
                        '<span>' + escapeHtml(item.country || '-') + ' <small>(' + escapeHtml(item.region || 'Demais') + ')</small></span>' +
                        '<strong>' + formatNumber(item.visits || 0) + ' (' + Number(item.percent || 0).toFixed(1).replace('.', ',') + '%)</strong>' +
                    '</li>';
                }).join('');

                countriesList.innerHTML = html;
            }

            function renderRegionsList(items) {
                if (!regionsList) {
                    return;
                }

                if (!Array.isArray(items) || items.length === 0) {
                    regionsList.innerHTML = '<li>Sem dados de regiao.</li>';
                    return;
                }

                var html = items.map(function (item) {
                    return '<li>' +
                        '<span>' + escapeHtml(item.region || 'Demais') + '</span>' +
                        '<strong>' + formatNumber(item.visits || 0) + ' (' + Number(item.percent || 0).toFixed(1).replace('.', ',') + '%)</strong>' +
                    '</li>';
                }).join('');

                regionsList.innerHTML = html;
            }

            function renderRecentAccessTable(items) {
                if (!recentAccessTableBody) {
                    return;
                }

                if (!Array.isArray(items) || items.length === 0) {
                    recentAccessTableBody.innerHTML = '<tr><td colspan="11">Nenhum acesso registrado.</td></tr>';
                    return;
                }

                var html = items.map(function (item) {
                    return '<tr>' +
                        '<td>' + escapeHtml(item.id) + '</td>' +
                        '<td>' + escapeHtml(formatDate(item.captured_at)) + '</td>' +
                        '<td>' + escapeHtml(item.ip) + '</td>' +
                        '<td>' + escapeHtml(item.user_name) + '<br><small>' + escapeHtml(item.user_email) + '</small></td>' +
                        '<td><span class="pill">' + escapeHtml(item.user_type) + '</span></td>' +
                        '<td>' + escapeHtml(item.route_name) + '</td>' +
                        '<td><strong>' + escapeHtml(item.path) + '</strong><br><small>' + escapeHtml(item.page_url) + '</small></td>' +
                        '<td>' + escapeHtml(item.country) + '<br><small>' + escapeHtml(item.state + ' / ' + item.city) + '</small></td>' +
                        '<td>' + escapeHtml(item.browser) + '</td>' +
                        '<td>' + escapeHtml(item.device_type + ' / ' + item.operating_system) + '</td>' +
                        '<td><span class="pill">' + escapeHtml(item.consent_mode) + '</span></td>' +
                    '</tr>';
                }).join('');

                recentAccessTableBody.innerHTML = html;
            }

            function refreshDetails(isInitial) {
                beginLoading(!!isInitial, isInitial ? 'Carregando graficos e lista de acessos...' : 'Atualizando detalhes...');
                fetchJson(detailsUrl)
                    .then(function (payload) {
                        renderBrowserPie(payload.browsers || []);
                        renderTopStatesPie(payload.top_states_pie || []);
                        renderTopCountriesPie(payload.top_countries_pie || []);
                        renderMapPoints(payload.map_points || []);
                        renderCountriesList(payload.countries || []);
                        renderRegionsList(payload.regions || []);
                        renderRecentAccessTable(payload.recent_accesses || []);
                        setSummary('[data-atualizado-em]', formatDate(payload.atualizado_em));
                    })
                    .catch(function () {
                        // Mantem dados atuais para evitar piscadas.
                    })
                    .finally(function () {
                        endLoading(!!isInitial);
                    });
            }

            window.addEventListener('load', function () {
                setTimeout(function () {
                    refreshSummary(true);
                    refreshDetails(true);
                }, 350);

                setInterval(function () { refreshSummary(false); }, 15000);
                setInterval(function () { refreshDetails(false); }, 30000);
            });
        })();
    </script>
@endpush
