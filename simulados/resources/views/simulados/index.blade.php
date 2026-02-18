<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulados | Plataforma</title>
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

        a, button, input { font-family: inherit; }

        a:focus-visible,
        button:focus-visible,
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

        .top-main-nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex: 1 1 auto;
            min-width: 220px;
        }

        .main-nav-link {
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

        .main-nav-link.is-active {
            border-color: #b9cdf0;
            background: #f2f7ff;
            color: #163a63;
        }

        .main-nav-link.is-disabled {
            color: #7d90aa;
            background: #f6f8fc;
            border-style: dashed;
            cursor: not-allowed;
            pointer-events: none;
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

        .avatar-name { display: none; font-size: 13px; }
        .avatar-menu-wrap { position: relative; }

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

        .avatar-menu[hidden] { display: none; }

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
        .menu-form button:hover { background: #f4f8ff; }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
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
            max-width: 760px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            align-items: end;
        }

        .field { display: grid; gap: 6px; }

        .field label {
            font-size: 12px;
            font-weight: 700;
            color: #2e4f77;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .field input {
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

        .search-btn:hover { background: linear-gradient(135deg, #1a56ce, #4078e5); }

        .list-card {
            border: 1px solid #d9e4f3;
            border-radius: var(--radius-lg);
            background: #fff;
            box-shadow: var(--shadow-soft);
            padding: 18px;
            display: grid;
            gap: 14px;
        }

        .list-header {
            margin: 0;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            color: #3c5574;
            font-size: 13px;
            font-weight: 700;
        }

        .simulado-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        .simulado-item {
            border: 1px solid #d7e4f5;
            border-radius: 14px;
            padding: 14px;
            background: #f9fbff;
            display: grid;
            gap: 9px;
        }

        .simulado-item h3 {
            margin: 0;
            font-size: 1rem;
            color: #17365a;
            line-height: 1.45;
        }

        .simulado-meta {
            margin: 0;
            color: #31547d;
            font-size: 13px;
            line-height: 1.6;
        }

        .chip {
            width: fit-content;
            border: 1px solid #d2dff2;
            background: #fff;
            border-radius: 999px;
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 700;
            color: #2a4c75;
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

        @media (min-width: 780px) {
            .search-form {
                grid-template-columns: 1fr auto;
            }

            .avatar-name {
                display: inline;
            }
        }

        @media (max-width: 779px) {
            .top-main-nav {
                order: 3;
                flex-basis: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    @php
        $loggedUser = auth()->user();
        $isAdm = ($loggedUser->user_type ?? null) === \App\Models\User::TYPE_ADM;
        $isAluno = in_array(($loggedUser->user_type ?? null), [\App\Models\User::TYPE_USER, \App\Models\User::TYPE_USER_ASSINANTE], true);
    @endphp

    <div class="shell">
        <header class="topbar">
            <a class="logo" href="{{ route('home') }}">
                <span class="logo-badge">EN</span>
                <span>Simulados e Questoes</span>
            </a>

            <nav class="top-main-nav" aria-label="Menu principal">
                <a class="main-nav-link" href="{{ route('home') }}">Inicio</a>
                <span class="main-nav-link is-disabled" aria-disabled="true" title="Em breve">Redacao</span>
                <a class="main-nav-link is-active" href="{{ route('simulados.public') }}">Simulados</a>
            </nav>

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

        <section class="hero" aria-labelledby="titulo-simulados">
            <h1 id="titulo-simulados" class="hero-title">Catalogo de Simulados</h1>
            <p class="hero-subtitle">Pesquise simulados por nome ou slug. Esta tela segue o mesmo padrao visual da pagina principal.</p>

            <form class="search-form" method="GET" action="{{ route('simulados.public') }}">
                <div class="field">
                    <label for="q">Pesquisar simulado</label>
                    <input
                        id="q"
                        type="text"
                        name="q"
                        value="{{ $searchTerm }}"
                        placeholder="Ex.: enem-2026, matematica-basico..."
                    >
                </div>
                <button class="search-btn" type="submit">Pesquisar</button>
            </form>
        </section>

        <section class="list-card" aria-labelledby="titulo-lista">
            <p class="list-header">
                <span id="titulo-lista">Resultado da pesquisa</span>
                <span>{{ $simulados->total() }} simulado(s) encontrado(s)</span>
            </p>

            @if ($simulados->isEmpty())
                <p class="empty">Nenhum simulado encontrado com esse termo.</p>
            @else
                <div class="simulado-grid">
                    @foreach ($simulados as $simulado)
                        <article class="simulado-item">
                            <h3>{{ $simulado->name }}</h3>
                            <span class="chip">{{ \App\Models\Simulado::visibilidadeLabel($simulado->visibilidade) }}</span>
                            <p class="simulado-meta"><strong>Slug:</strong> {{ $simulado->slug }}</p>
                            <p class="simulado-meta"><strong>Questoes vinculadas:</strong> {{ $simulado->questoes_count }}</p>
                        </article>
                    @endforeach
                </div>

                @if ($simulados->hasPages())
                    <nav class="pagination" aria-label="Paginacao dos simulados">
                        <p class="pagination-meta">
                            Exibindo {{ $simulados->firstItem() }}-{{ $simulados->lastItem() }} de {{ $simulados->total() }} simulado(s)
                        </p>
                        <div class="pagination-links">
                            @if ($simulados->onFirstPage())
                                <span class="page-link disabled">Anterior</span>
                            @else
                                <a class="page-link" href="{{ $simulados->previousPageUrl() }}">Anterior</a>
                            @endif

                            <span class="page-link disabled">Pagina {{ $simulados->currentPage() }} de {{ $simulados->lastPage() }}</span>

                            @if ($simulados->hasMorePages())
                                <a class="page-link" href="{{ $simulados->nextPageUrl() }}">Proxima</a>
                            @else
                                <span class="page-link disabled">Proxima</span>
                            @endif
                        </div>
                    </nav>
                @endif
            @endif
        </section>
    </div>

    @include('partials.feedback-widget')

    <script>
        (function () {
            var avatarButton = document.getElementById('avatarButton');
            var avatarMenu = document.getElementById('avatarMenu');

            if (!avatarButton || !avatarMenu) {
                return;
            }

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
        })();
    </script>
</body>
</html>

