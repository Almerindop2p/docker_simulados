<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulados e Questoes | Filtro Inteligente</title>
    @include('partials.edu-theme-head')
    <style>
        * {
            box-sizing: border-box;
        }

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

        a,
        button,
        select,
        input {
            font-family: inherit;
        }

        a:focus-visible,
        button:focus-visible,
        select:focus-visible,
        input:focus-visible {
            outline: 3px solid rgba(31, 95, 224, 0.34);
            outline-offset: 2px;
        }

        .shell {
            width: min(1200px, 92vw);
            margin: 0 auto;
            padding: 20px 0 32px;
            display: grid;
            gap: 22px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
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
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .nav-link {
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
        }

        .nav-link.primary {
            color: #fff;
            border-color: transparent;
            background: linear-gradient(135deg, var(--brand), #4c83f0);
            box-shadow: 0 10px 22px rgba(31, 95, 224, 0.26);
        }

        .avatar-btn {
            min-height: 44px;
            padding: 6px 8px 6px 6px;
            border-radius: 999px;
            border: 1px solid #d3e0f2;
            background: #fff;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #1f3f67;
            font-weight: 700;
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            background: linear-gradient(135deg, #1f5fe0, #5a8cff);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 13px;
            font-weight: 800;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: inherit;
            display: block;
        }

        .avatar-name {
            display: none;
            font-size: 13px;
        }

        .avatar-menu-wrap {
            position: relative;
        }

        .avatar-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 200px;
            border: 1px solid #d4dfef;
            border-radius: 14px;
            background: #fff;
            box-shadow: var(--shadow-soft);
            padding: 6px;
            animation: fadeDown .16s ease;
            z-index: 22;
        }

        .avatar-menu[hidden] {
            display: none;
        }

        .menu-item,
        .menu-form button {
            width: 100%;
            min-height: 44px;
            border-radius: 10px;
            border: 0;
            background: transparent;
            color: #213f66;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }

        .menu-item:hover,
        .menu-form button:hover {
            background: #f4f8ff;
        }

        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero {
            border: 1px solid #d7e4f5;
            background: linear-gradient(160deg, #ffffff 0%, #f6faff 50%, #edf4ff 100%);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            padding: clamp(18px, 3vw, 30px);
            display: grid;
            gap: 16px;
        }

        .hero-title {
            margin: 0;
            font-size: clamp(1.25rem, 2.6vw, 2rem);
            letter-spacing: -0.02em;
            text-align: center;
        }

        .hero-subtitle {
            margin: 0;
            color: var(--text-soft);
            text-align: center;
            max-width: 760px;
            justify-self: center;
            line-height: 1.6;
        }

        .search-form {
            width: 100%;
            max-width: 980px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            align-items: end;
        }

        .field {
            display: grid;
            gap: 6px;
        }

        .field label {
            font-size: 12px;
            font-weight: 700;
            color: #2e4f77;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .field select {
            width: 100%;
            min-height: 46px;
            border: 1px solid #ccd9eb;
            border-radius: 12px;
            background: #fff;
            color: #213f66;
            padding: 10px 12px;
            font-size: 14px;
        }

        .search-btn {
            min-height: 46px;
            border: 0;
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), #4f86f1);
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 10px 22px rgba(31, 95, 224, 0.26);
            padding: 0 16px;
        }

        .search-btn:hover {
            background: linear-gradient(135deg, #1a56ce, #4078e5);
        }

        .hero-note {
            margin: 0;
            font-size: 13px;
            color: #526985;
            text-align: center;
        }

        .result-card {
            border: 1px solid #d9e4f3;
            border-radius: var(--radius-lg);
            background: #fff;
            box-shadow: var(--shadow-soft);
            padding: 18px;
            display: grid;
            gap: 14px;
        }

        .result-header {
            margin: 0;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            color: #3c5574;
            font-size: 13px;
            font-weight: 700;
        }

        .results-grid {
            display: grid;
            gap: 12px;
        }

        .result-item {
            border: 1px solid #d7e4f5;
            border-radius: 14px;
            padding: 14px;
            background: #f9fbff;
            display: grid;
            gap: 12px;
        }

        .result-item h3 {
            margin: 0;
            font-size: 1rem;
            color: #17365a;
            line-height: 1.5;
        }

        .question-text {
            margin: 0;
            color: #29476e;
            font-size: 0.95rem;
            line-height: 1.7;
            text-align: left;
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

        .question-instituicao {
            margin: 0;
            color: #17365a;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .tag {
            border: 1px solid #d2dff2;
            background: #fff;
            border-radius: 999px;
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 700;
            color: #2a4c75;
        }

        .answer-form {
            display: grid;
            gap: 10px;
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

        .choice:hover {
            border-color: #aac3e9;
        }

        .choice input[type='radio'] {
            margin-top: 3px;
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

        .answer-button {
            justify-self: start;
            min-height: 42px;
            border: 0;
            border-radius: 11px;
            color: #fff;
            background: linear-gradient(135deg, #2264e5, #588dff);
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            padding: 0 14px;
            box-shadow: 0 10px 18px rgba(31, 95, 224, 0.24);
        }

        .answer-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .answer-button:disabled {
            opacity: 0.62;
            cursor: not-allowed;
            box-shadow: none;
        }

        .comment-toggle {
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

        .comment-toggle:hover {
            background: #f3f8ff;
        }

        .answer-comment {
            border: 1px solid #d8e4f3;
            border-radius: 12px;
            background: #fff;
            padding: 11px 12px;
            color: #24476f;
            font-size: 13px;
            line-height: 1.6;
        }

        .answer-comment[hidden] {
            display: none !important;
        }

        .feedback {
            border-radius: 12px;
            padding: 11px 12px;
            font-size: 13px;
            line-height: 1.6;
            display: grid;
            gap: 6px;
        }

        .feedback.success {
            border: 1px solid #badfcb;
            background: #eefbf3;
            color: #21553a;
        }

        .feedback.error {
            border: 1px solid #f0c7cf;
            background: #fff5f7;
            color: #8f2438;
        }

        .feedback.warning {
            border: 1px solid #f2d9be;
            background: #fffbf4;
            color: #7f4e1a;
        }

        .empty {
            margin: 0;
            border: 1px solid #d8e2f0;
            background: #f8fbff;
            color: #49617f;
            border-radius: 12px;
            padding: 12px;
            font-size: 14px;
        }

        .pagination {
            border-top: 1px solid #dbe6f5;
            padding-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pagination-meta {
            margin: 0;
            color: #4d6583;
            font-size: 13px;
            font-weight: 600;
        }

        .pagination-links {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .page-link {
            min-height: 38px;
            border-radius: 10px;
            border: 1px solid #cad8ec;
            background: #fff;
            color: #20446f;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            padding: 8px 12px;
            display: inline-flex;
            align-items: center;
        }

        .page-link.disabled {
            opacity: 0.55;
            pointer-events: none;
        }

        .explain {
            border: 1px solid #d8e4f3;
            border-radius: var(--radius-lg);
            background: #fff;
            box-shadow: var(--shadow-soft);
            padding: 18px;
            display: grid;
            gap: 14px;
        }

        .explain h2 {
            margin: 0;
            font-size: clamp(1.1rem, 2vw, 1.4rem);
            letter-spacing: -0.01em;
        }

        .explain p {
            margin: 0;
            color: var(--text-soft);
            line-height: 1.65;
        }

        .explain-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .explain-item {
            border: 1px solid #d6e2f3;
            background: #f8fbff;
            border-radius: 12px;
            padding: 12px;
            display: grid;
            gap: 6px;
        }

        .explain-item strong {
            font-size: 14px;
            color: #26476f;
        }

        .explain-item span {
            font-size: 13px;
            color: #536b88;
            line-height: 1.5;
        }

        @media (min-width: 780px) {
            .search-form {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .explain-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .avatar-name {
                display: inline;
            }
        }
    </style>
</head>
<body>
    @php
        $loggedUser = auth()->user();
        $isAdm = ($loggedUser->user_type ?? null) === \App\Models\User::TYPE_ADM;
        $isAluno = in_array(($loggedUser->user_type ?? null), [\App\Models\User::TYPE_USER, \App\Models\User::TYPE_USER_ASSINANTE], true);
        $resultadoResposta = session('resultado_resposta');
    @endphp

    <div class="shell">
        <header class="topbar">
            <a class="logo" href="{{ route('home') }}">
                <span class="logo-badge">EN</span>
                <span>Simulados e Questoes</span>
            </a>
            <nav class="top-actions" aria-label="Acesso rapido">
                @auth
                    @if ($isAluno)
                        <a class="nav-link" href="{{ ($loggedUser->user_type ?? null) === \App\Models\User::TYPE_USER_ASSINANTE ? route('area_assinante') : route('area_aluno') }}">Area do Aluno</a>
                    @endif
                    @if ($isAdm)
                        <a class="nav-link" href="{{ route('adm.bancas.index') }}">Painel ADM</a>
                    @endif

                    @include('partials.header-notifications')

                    <div class="avatar-menu-wrap">
                        <button id="avatarButton" class="avatar-btn" type="button" aria-expanded="false" aria-controls="avatarMenu">
                            <span class="avatar">
                                @if ($loggedUser->avatar_url)
                                    <img src="{{ $loggedUser->avatar_url }}" alt="Avatar de {{ $loggedUser->name ?? 'Aluno' }}">
                                @else
                                    {{ strtoupper(substr($loggedUser->name ?? 'A', 0, 1)) }}
                                @endif
                            </span>
                            <span class="avatar-name">{{ $loggedUser->name ?? 'Aluno' }}</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        </button>

                        <div id="avatarMenu" class="avatar-menu" role="menu" hidden>
                            <a class="menu-item" href="{{ route('perfil.show') }}" role="menuitem">Perfil</a>
                            <a class="menu-item" href="{{ route('perfil.show') }}#configuracoes" role="menuitem">Configuracoes</a>
                            <form class="menu-form" method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" role="menuitem">Sair</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Entrar</a>
                    <a class="nav-link primary" href="{{ route('cadastro.create') }}">Criar conta</a>
                @endauth
            </nav>
        </header>

        <section class="hero" aria-labelledby="titulo-filtro">
            <h1 id="titulo-filtro" class="hero-title">Encontre questoes por banca, cargo e materia</h1>
            <p class="hero-subtitle">Use os filtros abaixo para montar sua lista de estudo com foco em ENEM, concursos publicos, simulados e questoes gratuitas.</p>

            <form class="search-form" method="GET" action="{{ route('home') }}">
                <div class="field">
                    <label for="banca_id">Banca</label>
                    <select id="banca_id" name="banca_id">
                        <option value="0">Todas as bancas</option>
                        @foreach ($bancas as $banca)
                            <option value="{{ $banca->id }}" @selected((int) $filtros['banca_id'] === $banca->id)>{{ $banca->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="cargo_id">Cargo</label>
                    <select id="cargo_id" name="cargo_id">
                        <option value="0">Todos os cargos</option>
                        @foreach ($cargos as $cargo)
                            <option value="{{ $cargo->id }}" @selected((int) $filtros['cargo_id'] === $cargo->id)>{{ $cargo->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="materia_id">Materia</label>
                    <select id="materia_id" name="materia_id">
                        <option value="0">Todas as materias</option>
                        @foreach ($materias as $materia)
                            <option value="{{ $materia->id }}" @selected((int) $filtros['materia_id'] === $materia->id)>{{ $materia->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button class="search-btn" type="submit">Pesquisar questoes</button>
            </form>

            <p class="hero-note">Dica: combine mais de um filtro para resultados mais precisos.</p>
        </section>

        @if ($temPesquisa && $questoes)
            <section class="result-card" aria-labelledby="titulo-resultados">
                <p class="result-header">
                    <span id="titulo-resultados">Resultado da pesquisa</span>
                    <span>{{ $totalResultados }} questao(oes) encontrada(s)</span>
                </p>

                @if ($questoes->count() === 0)
                    <p class="empty">Nenhuma questao encontrada com os filtros selecionados.</p>
                @else
                    <div class="results-grid">
                        @foreach ($questoes as $questao)
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

                                $resultadoDaQuestao = is_array($resultadoResposta) && (int) ($resultadoResposta['questao_id'] ?? 0) === $questao->id
                                    ? $resultadoResposta
                                    : null;

                                $respostaEnviada = $resultadoDaQuestao['resposta_enviada'] ?? null;
                                $questaoRespondida = is_array($resultadoDaQuestao) && array_key_exists('acertou', $resultadoDaQuestao);
                                $temComentario = $questaoRespondida && !blank($resultadoDaQuestao['explicacao'] ?? null);
                            @endphp

                            <article class="result-item" id="questao-{{ $questao->id }}">
                                <h3>Questao #{{ $questao->id }}</h3>
                                <p class="question-instituicao">Instituicao: {{ $questao->instituicao?->name ?? '-' }}</p>
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
                                <p class="question-text">{!! nl2br(e($questao->enunciado)) !!}</p>

                                <div class="tags">
                                    <span class="tag">Banca: {{ $questao->banca?->name ?? '-' }}</span>
                                    <span class="tag">Materia: {{ $questao->materia?->name ?? '-' }}</span>
                                    @foreach ($questao->cargos as $cargo)
                                        <span class="tag">Cargo: {{ $cargo->name }}</span>
                                    @endforeach
                                </div>

                                <form class="answer-form" method="POST" action="{{ route('home.answer', $questao) }}">
                                    @csrf
                                    <input type="hidden" name="banca_id" value="{{ (int) $filtros['banca_id'] }}">
                                    <input type="hidden" name="cargo_id" value="{{ (int) $filtros['cargo_id'] }}">
                                    <input type="hidden" name="materia_id" value="{{ (int) $filtros['materia_id'] }}">
                                    <input type="hidden" name="page" value="{{ $questoes->currentPage() }}">

                                    <div class="choices">
                                        @foreach ($alternativas as $letra => $alternativa)
                                            <label class="choice" for="questao-{{ $questao->id }}-{{ strtolower($letra) }}">
                                                <input
                                                    id="questao-{{ $questao->id }}-{{ strtolower($letra) }}"
                                                    type="radio"
                                                    name="resposta"
                                                    value="{{ $letra }}"
                                                    required
                                                    @checked($respostaEnviada === $letra)
                                                >
                                                <span class="choice-letter">{{ $letra }}</span>
                                                <span class="choice-text">{!! nl2br(e($alternativa)) !!}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    <div class="answer-actions">
                                        <button class="answer-button" type="submit" @disabled($questaoRespondida)>
                                            {{ $questaoRespondida ? 'Respondida' : 'Responder' }}
                                        </button>

                                        @if ($temComentario)
                                            <button
                                                class="comment-toggle"
                                                type="button"
                                                data-comment-toggle
                                                data-target="#comentario-questao-{{ $questao->id }}"
                                                aria-expanded="false"
                                                aria-controls="comentario-questao-{{ $questao->id }}"
                                            >
                                                Ver comentario
                                            </button>
                                        @endif
                                    </div>
                                </form>

                                @if ($resultadoDaQuestao)
                                    @if (!empty($resultadoDaQuestao['erro']))
                                        <div class="feedback warning" role="status">{{ $resultadoDaQuestao['erro'] }}</div>
                                    @elseif (($resultadoDaQuestao['acertou'] ?? false) === true)
                                        <div class="feedback success" role="status">
                                            <span><strong>Resposta correta.</strong> Voce marcou {{ $resultadoDaQuestao['resposta_enviada'] }}.</span>
                                        </div>
                                    @else
                                        <div class="feedback error" role="status">
                                            <span><strong>Resposta incorreta.</strong> Voce marcou {{ $resultadoDaQuestao['resposta_enviada'] }} e o gabarito e {{ $resultadoDaQuestao['gabarito'] }}.</span>
                                        </div>
                                    @endif
                                @endif

                                @if ($temComentario)
                                    <div id="comentario-questao-{{ $questao->id }}" class="answer-comment" hidden>
                                        <strong>Comentario da resposta:</strong>
                                        <span>{!! nl2br(e($resultadoDaQuestao['explicacao'])) !!}</span>
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>

                    @if ($questoes->hasPages())
                        <nav class="pagination" aria-label="Paginacao das questoes">
                            <p class="pagination-meta">
                                Exibindo {{ $questoes->firstItem() }}-{{ $questoes->lastItem() }} de {{ $questoes->total() }} questao(oes)
                            </p>
                            <div class="pagination-links">
                                @if ($questoes->onFirstPage())
                                    <span class="page-link disabled">Anterior</span>
                                @else
                                    <a class="page-link" href="{{ $questoes->previousPageUrl() }}">Anterior</a>
                                @endif

                                <span class="page-link disabled">Pagina {{ $questoes->currentPage() }} de {{ $questoes->lastPage() }}</span>

                                @if ($questoes->hasMorePages())
                                    <a class="page-link" href="{{ $questoes->nextPageUrl() }}">Proxima</a>
                                @else
                                    <span class="page-link disabled">Proxima</span>
                                @endif
                            </div>
                        </nav>
                    @endif
                @endif
            </section>
        @endif

        <section class="explain" aria-labelledby="titulo-explicacao">
            <h2 id="titulo-explicacao">Como funciona nossa plataforma de simulados e questoes</h2>
            <p>Nosso sistema foi criado para facilitar sua preparacao com uma experiencia objetiva: voce filtra, pratica e acompanha sua evolucao. A proposta e oferecer um ambiente moderno e intuitivo para quem estuda para ENEM e concursos.</p>

            <div class="explain-grid">
                <article class="explain-item">
                    <strong>1. Filtre com precisao</strong>
                    <span>Encontre questoes por banca, cargo e materia para focar exatamente no que voce precisa estudar.</span>
                </article>
                <article class="explain-item">
                    <strong>2. Pratique com contexto real</strong>
                    <span>Resolva questoes em um fluxo parecido com as provas e melhore sua consistencia em simulados.</span>
                </article>
                <article class="explain-item">
                    <strong>3. Evolua com estrategia</strong>
                    <span>Use os resultados para ajustar sua rotina e concentrar energia nos temas com maior impacto.</span>
                </article>
            </div>
        </section>
    </div>
    @include('partials.feedback-widget')
    <script>
        (function () {
            var avatarButton = document.getElementById('avatarButton');
            var avatarMenu = document.getElementById('avatarMenu');

            if (avatarButton && avatarMenu) {
                function openAvatarMenu() {
                    avatarMenu.hidden = false;
                    avatarButton.setAttribute('aria-expanded', 'true');
                }

                function closeAvatarMenu() {
                    avatarMenu.hidden = true;
                    avatarButton.setAttribute('aria-expanded', 'false');
                }

                avatarButton.addEventListener('click', function (event) {
                    event.stopPropagation();
                    if (avatarMenu.hidden) {
                        openAvatarMenu();
                    } else {
                        closeAvatarMenu();
                    }
                });

                document.addEventListener('click', function (event) {
                    if (!avatarMenu.contains(event.target) && !avatarButton.contains(event.target)) {
                        closeAvatarMenu();
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeAvatarMenu();
                    }
                });
            }

            var commentButtons = document.querySelectorAll('[data-comment-toggle]');

            commentButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var selector = button.getAttribute('data-target');
                    if (!selector) {
                        return;
                    }

                    var target = document.querySelector(selector);
                    if (!target) {
                        return;
                    }

                    var isOpen = !target.hasAttribute('hidden');
                    if (isOpen) {
                        target.setAttribute('hidden', '');
                        button.setAttribute('aria-expanded', 'false');
                        button.textContent = 'Ver comentario';
                        return;
                    }

                    target.removeAttribute('hidden');
                    button.setAttribute('aria-expanded', 'true');
                    button.textContent = 'Ocultar comentario';
                });
            });
        })();
    </script>
</body>
</html>
