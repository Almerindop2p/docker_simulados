<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Painel Administrativo')</title>
    @include('partials.edu-theme-head')
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Plus Jakarta Sans", "Manrope", "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 10% 12%, #ffffff 0%, rgba(255, 255, 255, 0) 45%),
                radial-gradient(circle at 90% 92%, #e9f1ff 0%, rgba(233, 241, 255, 0) 42%),
                linear-gradient(180deg, var(--bg-main), var(--bg-soft));
            color: var(--text-main);
            min-height: 100vh;
        }

        a, button, input { font-family: inherit; }
        a:focus-visible, button:focus-visible, input:focus-visible {
            outline: 3px solid rgba(31, 95, 224, 0.35);
            outline-offset: 2px;
        }

        .layout { display: grid; grid-template-columns: 1fr; min-height: 100vh; }
        .main { min-width: 0; }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 280px;
            padding: 20px 16px;
            background: #ffffff;
            border-right: 1px solid var(--line);
            box-shadow: 0 10px 40px rgba(16, 36, 63, 0.16);
            transform: translateX(-104%);
            transition: transform .25s ease;
            z-index: 45;
            overflow-y: auto;
        }

        .sidebar.is-open { transform: translateX(0); }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 22px;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 800;
        }

        .brand-badge {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 800;
            background: linear-gradient(135deg, #1f5fe0, #5a8cff);
            box-shadow: 0 8px 18px rgba(31, 95, 224, .34);
        }

        .sidebar-title {
            margin: 0 0 10px;
            color: #5f728c;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .09em;
            padding-left: 8px;
        }

        .nav {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 6px;
        }

        .nav-link {
            display: grid;
            grid-template-columns: 22px 1fr;
            align-items: center;
            gap: 10px;
            min-height: 44px;
            padding: 10px 12px;
            border-radius: 12px;
            text-decoration: none;
            color: #294567;
            font-weight: 600;
            border: 1px solid transparent;
            transition: background-color .2s ease, border-color .2s ease, color .2s ease;
        }

        .nav-link svg { width: 18px; height: 18px; color: #47678f; }
        .nav-link:hover { background: #f3f7ff; border-color: #d7e4fa; color: #1d3f6d; }
        .nav-link.is-active { color: #163b76; background: #eaf2ff; border-color: #c9dcfb; }

        .nav-toggle {
            width: 100%;
            background: transparent;
            text-align: left;
            cursor: pointer;
            grid-template-columns: 22px 1fr 16px;
        }

        .nav-chevron { width: 14px; height: 14px; transition: transform .2s ease; }
        .nav-toggle[aria-expanded="true"] .nav-chevron { transform: rotate(180deg); }

        .nav-submenu {
            margin: 6px 0 0;
            padding: 0 0 0 34px;
            list-style: none;
            display: none;
            gap: 6px;
        }

        .nav-submenu.is-open { display: grid; }

        .nav-sublink {
            min-height: 40px;
            border-radius: 10px;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            padding: 8px 10px;
            text-decoration: none;
            color: #33557d;
            font-weight: 600;
            font-size: 14px;
            transition: background-color .2s ease, border-color .2s ease, color .2s ease;
        }

        .nav-sublink:hover { background: #f3f7ff; border-color: #d7e4fa; color: #1d3f6d; }
        .nav-sublink.is-active { color: #163b76; background: #eaf2ff; border-color: #c9dcfb; }

        .sidebar-footer {
            margin-top: 22px;
            border: 1px solid #d9e3f1;
            background: #f8fbff;
            border-radius: 14px;
            padding: 12px;
            font-size: 12px;
            color: #506683;
            line-height: 1.55;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 30;
            min-height: 74px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            background: rgba(246, 248, 252, 0.92);
            border-bottom: 1px solid rgba(220, 228, 238, 0.9);
            backdrop-filter: blur(8px);
        }

        .topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }

        .menu-btn {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: 1px solid #d4dfef;
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #1f406a;
        }

        .title-wrap { min-width: 0; }
        .breadcrumb {
            margin: 0;
            color: #5e7390;
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .title {
            margin: 2px 0 0;
            font-size: clamp(1.1rem, 2.2vw, 1.42rem);
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .topbar-right { display: flex; align-items: center; gap: 10px; }

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
        }

        .avatar-menu[hidden] { display: none; }

        .menu-item, .menu-form button {
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

        .menu-item:hover, .menu-form button:hover { background: #f4f8ff; }

        .content { padding: 16px; display: grid; gap: 16px; }

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(16, 36, 63, 0.34);
            opacity: 0;
            visibility: hidden;
            transition: opacity .2s ease, visibility .2s ease;
            z-index: 40;
        }

        .overlay.is-active { opacity: 1; visibility: visible; }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (min-width: 768px) {
            .content { padding: 20px; }
            .avatar-name { display: inline; }
        }

        @media (min-width: 1100px) {
            .layout { grid-template-columns: 280px minmax(0, 1fr); }
            .sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
                transform: translateX(0);
                box-shadow: none;
                z-index: 20;
            }
            .overlay, .menu-btn { display: none; }
            .content { padding: 24px; gap: 18px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $loggedUser = auth()->user();
        $isAdm = ($loggedUser->user_type ?? null) === \App\Models\User::TYPE_ADM;
        $isBancaRoute = request()->routeIs('adm.bancas.*');
        $isMateriaRoute = request()->routeIs('adm.materias.*');
        $isCargoRoute = request()->routeIs('adm.cargos.*');
        $isQuestaoRoute = request()->routeIs('adm.questoes.*');
    @endphp

    <div class="layout">
        <aside id="sidebar" class="sidebar" aria-label="Menu principal">
            <a class="brand" href="{{ route('area_aluno') }}">
                <span class="brand-badge">EN</span>
                <span>Area do Aluno</span>
            </a>

            <p class="sidebar-title">Menu</p>
            <nav>
                <ul class="nav">
                    <li>
                        <a class="nav-link {{ request()->routeIs('area_aluno') ? 'is-active' : '' }}" href="{{ route('area_aluno') }}">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 11.8 12 4l9 7.8V21a1 1 0 0 1-1 1h-5.8v-6h-4.4v6H4a1 1 0 0 1-1-1v-9.2Z" stroke="currentColor" stroke-width="1.8"/></svg>
                            <span>Inicio</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="#">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v11a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 17.5v-11Z" stroke="currentColor" stroke-width="1.8"/><path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            <span>Meus Cursos</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="#">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/><path d="M12 8v4l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>Atividades</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="#">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 18h16M7 15V9m5 6V6m5 9v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            <span>Progresso</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="#">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 4 4 8v8l8 4 8-4V8l-8-4Z" stroke="currentColor" stroke-width="1.8"/><path d="m8.5 12 2.2 2.2 4.8-4.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>Certificados</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="#">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 20a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z" stroke="currentColor" stroke-width="1.8"/><path d="M9.2 9.5a3 3 0 1 1 5.5 2l-.8.8c-.4.4-.7.9-.7 1.5v.4M12 17.6h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            <span>Suporte</span>
                        </a>
                    </li>

                    @if ($isAdm)
                        <li>
                            <button
                                id="bancaToggle"
                                class="nav-link nav-toggle {{ $isBancaRoute ? 'is-active' : '' }}"
                                type="button"
                                aria-expanded="{{ $isBancaRoute ? 'true' : 'false' }}"
                                aria-controls="bancaSubmenu"
                            >
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v11a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 17.5v-11Z" stroke="currentColor" stroke-width="1.8"/><path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                <span>Banca</span>
                                <svg class="nav-chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <ul id="bancaSubmenu" class="nav-submenu {{ $isBancaRoute ? 'is-open' : '' }}">
                                <li><a class="nav-sublink {{ request()->routeIs('adm.bancas.create') ? 'is-active' : '' }}" href="{{ route('adm.bancas.create') }}">Adicionar Banca</a></li>
                                <li><a class="nav-sublink {{ request()->routeIs('adm.bancas.index') ? 'is-active' : '' }}" href="{{ route('adm.bancas.index') }}">Lista de Bancas</a></li>
                            </ul>
                        </li>
                        <li>
                            <button
                                id="materiaToggle"
                                class="nav-link nav-toggle {{ $isMateriaRoute ? 'is-active' : '' }}"
                                type="button"
                                aria-expanded="{{ $isMateriaRoute ? 'true' : 'false' }}"
                                aria-controls="materiaSubmenu"
                            >
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4.5 6.5A2.5 2.5 0 0 1 7 4h10a2.5 2.5 0 0 1 2.5 2.5v11A2.5 2.5 0 0 1 17 20H7a2.5 2.5 0 0 1-2.5-2.5v-11Z" stroke="currentColor" stroke-width="1.8"/><path d="M8 8h8M8 12h8M8 16h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                <span>Materias</span>
                                <svg class="nav-chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <ul id="materiaSubmenu" class="nav-submenu {{ $isMateriaRoute ? 'is-open' : '' }}">
                                <li><a class="nav-sublink {{ request()->routeIs('adm.materias.create') ? 'is-active' : '' }}" href="{{ route('adm.materias.create') }}">Adicionar Materia</a></li>
                                <li><a class="nav-sublink {{ request()->routeIs('adm.materias.index') ? 'is-active' : '' }}" href="{{ route('adm.materias.index') }}">Lista de Materias</a></li>
                            </ul>
                        </li>
                        <li>
                            <button
                                id="cargoToggle"
                                class="nav-link nav-toggle {{ $isCargoRoute ? 'is-active' : '' }}"
                                type="button"
                                aria-expanded="{{ $isCargoRoute ? 'true' : 'false' }}"
                                aria-controls="cargoSubmenu"
                            >
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5a4 4 0 1 1 0 8 4 4 0 0 1 0-8Zm-7 14a7 7 0 1 1 14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                <span>Cargos</span>
                                <svg class="nav-chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <ul id="cargoSubmenu" class="nav-submenu {{ $isCargoRoute ? 'is-open' : '' }}">
                                <li><a class="nav-sublink {{ request()->routeIs('adm.cargos.create') ? 'is-active' : '' }}" href="{{ route('adm.cargos.create') }}">Adicionar Cargo</a></li>
                                <li><a class="nav-sublink {{ request()->routeIs('adm.cargos.index') ? 'is-active' : '' }}" href="{{ route('adm.cargos.index') }}">Lista de Cargos</a></li>
                            </ul>
                        </li>
                        <li>
                            <button
                                id="questaoToggle"
                                class="nav-link nav-toggle {{ $isQuestaoRoute ? 'is-active' : '' }}"
                                type="button"
                                aria-expanded="{{ $isQuestaoRoute ? 'true' : 'false' }}"
                                aria-controls="questaoSubmenu"
                            >
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4.5 6.5A2.5 2.5 0 0 1 7 4h10a2.5 2.5 0 0 1 2.5 2.5v11A2.5 2.5 0 0 1 17 20H7a2.5 2.5 0 0 1-2.5-2.5v-11Z" stroke="currentColor" stroke-width="1.8"/><path d="M9 9h6M9 13h6M9 17h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                <span>Questoes</span>
                                <svg class="nav-chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <ul id="questaoSubmenu" class="nav-submenu {{ $isQuestaoRoute ? 'is-open' : '' }}">
                                <li><a class="nav-sublink {{ request()->routeIs('adm.questoes.create') ? 'is-active' : '' }}" href="{{ route('adm.questoes.create') }}">Adicionar Questao</a></li>
                                <li><a class="nav-sublink {{ request()->routeIs('adm.questoes.index') ? 'is-active' : '' }}" href="{{ route('adm.questoes.index') }}">Lista de Questoes</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </nav>

            <div class="sidebar-footer">
                Continue seu plano com simulados e questoes gratuitas para ENEM e concursos publicos.
            </div>
        </aside>

        <div class="main">
            <header class="topbar">
                <div class="topbar-left">
                    <button id="openSidebar" class="menu-btn" type="button" aria-controls="sidebar" aria-label="Abrir menu lateral">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                    <div class="title-wrap">
                        <p class="breadcrumb">@yield('breadcrumb', 'Painel / Administracao')</p>
                        <h1 class="title">@yield('page_title', 'Painel Administrativo')</h1>
                    </div>
                </div>

                <div class="topbar-right">
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
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
                </div>
            </header>

            <main class="content">
                @yield('content')
            </main>
        </div>
    </div>

    <div id="mobileOverlay" class="overlay" aria-hidden="true"></div>

    <script>
        (function () {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('mobileOverlay');
            var openSidebar = document.getElementById('openSidebar');
            var avatarButton = document.getElementById('avatarButton');
            var avatarMenu = document.getElementById('avatarMenu');
            var menuToggles = [
                {button: document.getElementById('bancaToggle'), submenu: document.getElementById('bancaSubmenu')},
                {button: document.getElementById('materiaToggle'), submenu: document.getElementById('materiaSubmenu')},
                {button: document.getElementById('cargoToggle'), submenu: document.getElementById('cargoSubmenu')},
                {button: document.getElementById('questaoToggle'), submenu: document.getElementById('questaoSubmenu')}
            ];

            if (!sidebar || !overlay || !openSidebar || !avatarButton || !avatarMenu) {
                return;
            }

            function isDesktop() {
                return window.matchMedia('(min-width: 1100px)').matches;
            }

            function openSidebarDrawer() {
                if (isDesktop()) return;
                sidebar.classList.add('is-open');
                overlay.classList.add('is-active');
                openSidebar.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebarDrawer() {
                sidebar.classList.remove('is-open');
                overlay.classList.remove('is-active');
                openSidebar.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            function openAvatarMenu() {
                avatarMenu.hidden = false;
                avatarButton.setAttribute('aria-expanded', 'true');
            }

            function closeAvatarMenu() {
                avatarMenu.hidden = true;
                avatarButton.setAttribute('aria-expanded', 'false');
            }

            function toggleSubmenu(button, submenu) {
                if (!button || !submenu) return;
                var isOpen = submenu.classList.contains('is-open');
                submenu.classList.toggle('is-open', !isOpen);
                button.setAttribute('aria-expanded', String(!isOpen));
            }

            openSidebar.addEventListener('click', function () {
                if (sidebar.classList.contains('is-open')) closeSidebarDrawer();
                else openSidebarDrawer();
            });

            overlay.addEventListener('click', closeSidebarDrawer);

            avatarButton.addEventListener('click', function (event) {
                event.stopPropagation();
                if (avatarMenu.hidden) openAvatarMenu();
                else closeAvatarMenu();
            });

            menuToggles.forEach(function (item) {
                if (item.button && item.submenu) {
                    item.button.addEventListener('click', function () {
                        toggleSubmenu(item.button, item.submenu);
                    });
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
                    closeSidebarDrawer();
                }
            });

            window.addEventListener('resize', function () {
                if (isDesktop()) closeSidebarDrawer();
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
