<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil | Simulados e Questoes</title>
    @include('partials.edu-theme-head')
    <style>
        * {
            box-sizing: border-box;
        }

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

        a, button {
            font-family: inherit;
        }

        a:focus-visible,
        button:focus-visible,
        input:focus-visible {
            outline: 3px solid rgba(31, 95, 224, 0.35);
            outline-offset: 2px;
        }

        .layout {
            display: grid;
            grid-template-columns: 1fr;
            min-height: 100vh;
        }

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

        .sidebar.is-open {
            transform: translateX(0);
        }

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

        .nav-link svg {
            width: 18px;
            height: 18px;
            color: #47678f;
        }

        .nav-toggle {
            width: 100%;
            background: transparent;
            text-align: left;
            cursor: pointer;
            grid-template-columns: 22px 1fr 16px;
        }

        .nav-chevron {
            width: 14px;
            height: 14px;
            transition: transform .2s ease;
        }

        .nav-toggle[aria-expanded="true"] .nav-chevron {
            transform: rotate(180deg);
        }

        .nav-submenu {
            margin: 6px 0 0;
            padding: 0 0 0 34px;
            list-style: none;
            display: none;
            gap: 6px;
        }

        .nav-submenu.is-open {
            display: grid;
        }

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

        .nav-sublink:hover {
            background: #f3f7ff;
            border-color: #d7e4fa;
            color: #1d3f6d;
        }

        .nav-sublink.is-active {
            color: #163b76;
            background: #eaf2ff;
            border-color: #c9dcfb;
        }

        .nav-link:hover {
            background: #f3f7ff;
            border-color: #d7e4fa;
            color: #1d3f6d;
        }

        .nav-link.is-active {
            color: #163b76;
            background: #eaf2ff;
            border-color: #c9dcfb;
        }

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

        .main {
            min-width: 0;
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

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

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

        .title-wrap {
            min-width: 0;
        }

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

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
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

        .content {
            padding: 16px;
            display: grid;
            gap: 16px;
        }

        .profile-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            padding: 18px;
            display: grid;
            gap: 14px;
        }

        .status {
            margin: 0;
            border: 1px solid var(--ok-line);
            background: var(--ok-bg);
            color: var(--ok-text);
            border-radius: 12px;
            padding: 11px;
            font-size: 13px;
            line-height: 1.5;
        }

        .errors {
            margin: 0;
            border: 1px solid #efd2d7;
            background: #fff7f8;
            color: #9e1f36;
            border-radius: 12px;
            padding: 11px;
            font-size: 13px;
            line-height: 1.5;
        }

        .profile-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: 1fr;
        }

        .avatar-pane {
            border: 1px solid #d8e4f4;
            border-radius: var(--radius-md);
            background: #f8fbff;
            padding: 16px;
            display: grid;
            gap: 12px;
            justify-items: center;
            text-align: center;
        }

        .avatar-circle {
            width: 120px;
            height: 120px;
            border-radius: 999px;
            border: 2px solid #d2e1f6;
            background: linear-gradient(135deg, #1f5fe0, #5a8cff);
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 42px;
            overflow: hidden;
        }

        .avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .meta {
            margin: 0;
            color: #4d6381;
            font-size: 14px;
        }

        .form-pane {
            border: 1px solid #d8e4f4;
            border-radius: var(--radius-md);
            background: #fff;
            padding: 16px;
            display: grid;
            gap: 12px;
        }

        .label {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
            color: #28486f;
        }

        .file-input {
            width: 100%;
            border: 1px dashed #c4d7f1;
            border-radius: 12px;
            padding: 10px;
            background: #f9fbff;
            min-height: 44px;
        }

        .hint {
            margin: 0;
            font-size: 12px;
            color: #5f7490;
        }

        .btn-primary {
            min-height: 44px;
            border: 0;
            border-radius: 12px;
            padding: 10px 14px;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), #4c83f0);
            box-shadow: 0 10px 20px rgba(31, 95, 224, 0.24);
            font-weight: 700;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1a56ce, #3f77e4);
        }

        .btn-primary[disabled] {
            opacity: .7;
            cursor: not-allowed;
            box-shadow: none;
        }

        .btn-loader {
            width: 15px;
            height: 15px;
            border: 2px solid rgba(255,255,255,.45);
            border-top-color: #fff;
            border-radius: 999px;
            display: none;
            animation: spin .8s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        .btn-primary.is-loading .btn-loader {
            display: inline-block;
        }

        .read-only {
            border: 1px solid #d4e1f2;
            border-radius: 12px;
            background: #f7faff;
            padding: 10px 12px;
            font-size: 14px;
            color: #335073;
        }

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(16, 36, 63, 0.34);
            opacity: 0;
            visibility: hidden;
            transition: opacity .2s ease, visibility .2s ease;
            z-index: 40;
        }

        .overlay.is-active {
            opacity: 1;
            visibility: visible;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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

        @media (min-width: 768px) {
            .content {
                padding: 20px;
            }

            .avatar-name {
                display: inline;
            }

            .profile-grid {
                grid-template-columns: 280px minmax(0, 1fr);
                align-items: start;
            }
        }

        @media (min-width: 1100px) {
            .layout {
                grid-template-columns: 280px minmax(0, 1fr);
            }

            .sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
                transform: translateX(0);
                box-shadow: none;
                z-index: 20;
            }

            .overlay,
            .menu-btn {
                display: none;
            }

            .content {
                padding: 24px;
                gap: 18px;
            }
        }
    </style>
</head>
<body>
    @php
        $loggedUser = auth()->user();
        $profileUser = $user ?? $loggedUser;
        $isAdm = ($loggedUser->user_type ?? null) === \App\Models\User::TYPE_ADM;
        $isBancaRoute = request()->routeIs('adm.bancas.*');
        $isSimuladoRoute = request()->routeIs('adm.simulados.*');
        $isMateriaRoute = request()->routeIs('adm.materias.*');
        $isCargoRoute = request()->routeIs('adm.cargos.*');
        $isQuestaoRoute = request()->routeIs('adm.questoes.*');
        $isTicketRoute = request()->routeIs('adm.tickets.*');
        $isProgressoRoute = $isAdm
            ? request()->routeIs('adm.progresso.*')
            : request()->routeIs('progresso.*');
        $isMeusSimuladosRoute = request()->routeIs('meus-simulados.*');
        $isStudent = in_array(($loggedUser->user_type ?? null), [
            \App\Models\User::TYPE_USER,
            \App\Models\User::TYPE_USER_ASSINANTE,
        ], true);
        $homeRoute = ($loggedUser->user_type ?? null) === \App\Models\User::TYPE_USER_ASSINANTE
            ? route('area_assinante')
            : route('area_aluno');
    @endphp

    <div class="layout">
        <aside id="sidebar" class="sidebar" aria-label="Menu principal">
            <a class="brand" href="{{ $homeRoute }}">
                <span class="brand-badge">EN</span>
                <span>Area do Aluno</span>
            </a>

            <p class="sidebar-title">Menu</p>
            <nav>
                <ul class="nav">
                    <li>
                        <a class="nav-link {{ request()->routeIs('area_aluno') || request()->routeIs('area_assinante') ? 'is-active' : '' }}" href="{{ $homeRoute }}" @if(request()->routeIs('area_aluno') || request()->routeIs('area_assinante')) aria-current="page" @endif>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 11.8 12 4l9 7.8V21a1 1 0 0 1-1 1h-5.8v-6h-4.4v6H4a1 1 0 0 1-1-1v-9.2Z" stroke="currentColor" stroke-width="1.8"/></svg>
                            <span>Inicio</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="#">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/><path d="M12 8v4l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>Atividades</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ $isProgressoRoute ? 'is-active' : '' }}" href="{{ $isAdm ? route('adm.progresso.index') : route('progresso.index') }}" @if($isProgressoRoute) aria-current="page" @endif>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 18h16M7 15V9m5 6V6m5 9v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            <span>Progresso</span>
                        </a>
                    </li>
                    @if ($isStudent)
                        <li>
                            <a class="nav-link {{ $isMeusSimuladosRoute ? 'is-active' : '' }}" href="{{ route('meus-simulados.index') }}" @if($isMeusSimuladosRoute) aria-current="page" @endif>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4.5 6.5A2.5 2.5 0 0 1 7 4h10a2.5 2.5 0 0 1 2.5 2.5v11A2.5 2.5 0 0 1 17 20H7a2.5 2.5 0 0 1-2.5-2.5v-11Z" stroke="currentColor" stroke-width="1.8"/><path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                <span>Meus Simulados</span>
                            </a>
                        </li>
                    @endif
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
                                id="simuladoToggle"
                                class="nav-link nav-toggle {{ $isSimuladoRoute ? 'is-active' : '' }}"
                                type="button"
                                aria-expanded="{{ $isSimuladoRoute ? 'true' : 'false' }}"
                                aria-controls="simuladoSubmenu"
                            >
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4.5 6.5A2.5 2.5 0 0 1 7 4h10a2.5 2.5 0 0 1 2.5 2.5v11A2.5 2.5 0 0 1 17 20H7a2.5 2.5 0 0 1-2.5-2.5v-11Z" stroke="currentColor" stroke-width="1.8"/><path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                <span>Simulados</span>
                                <svg class="nav-chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <ul id="simuladoSubmenu" class="nav-submenu {{ $isSimuladoRoute ? 'is-open' : '' }}">
                                <li><a class="nav-sublink {{ request()->routeIs('adm.simulados.create') ? 'is-active' : '' }}" href="{{ route('adm.simulados.create') }}">Adicionar Simulado</a></li>
                                <li><a class="nav-sublink {{ request()->routeIs('adm.simulados.index') ? 'is-active' : '' }}" href="{{ route('adm.simulados.index') }}">Lista de Simulados</a></li>
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
                        <li>
                            <a class="nav-link {{ $isTicketRoute ? 'is-active' : '' }}" href="{{ route('adm.tickets.index') }}">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 5.5A2.5 2.5 0 0 1 7.5 3h9A2.5 2.5 0 0 1 19 5.5v13a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 5 18.5v-13Z" stroke="currentColor" stroke-width="1.8"/><path d="M8.5 8.5h7M8.5 12h7M8.5 15.5h4.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                <span>Tickets</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>

            <div class="sidebar-footer">
                Continue seu plano com simulados e questoes gratuitas para manter sua evolucao.
            </div>
        </aside>

        <div class="main">
            <header class="topbar">
                <div class="topbar-left">
                    <button id="openSidebar" class="menu-btn" type="button" aria-controls="sidebar" aria-label="Abrir menu lateral">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                    <div class="title-wrap">
                        <p class="breadcrumb">Inicio / Perfil</p>
                        <h1 class="title">Perfil do usuario</h1>
                    </div>
                </div>

                <div class="topbar-right">
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
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>

                        <div id="avatarMenu" class="avatar-menu" role="menu" hidden>
                            <a class="menu-item" href="{{ route('perfil.show') }}" role="menuitem">Perfil</a>
                            <a class="menu-item" href="{{ $isAdm ? route('adm.configuracoes.index') : route('perfil.show') . '#configuracoes' }}" role="menuitem">Configuracoes</a>
                            <form class="menu-form" method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" role="menuitem">Sair</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="content">
                @if (session('status'))
                    <p class="status" role="status">{{ session('status') }}</p>
                @endif

                @if ($errors->any())
                    <div class="errors" role="alert">{{ $errors->first() }}</div>
                @endif

                <section class="profile-card" id="configuracoes">
                    <div class="profile-grid">
                        <aside class="avatar-pane">
                            <div id="avatarPreview" class="avatar-circle" aria-hidden="true">
                                @if ($profileUser->avatar_url)
                                    <img src="{{ $profileUser->avatar_url }}" alt="Avatar atual de {{ $profileUser->name }}">
                                @else
                                    {{ strtoupper(substr($profileUser->name, 0, 1)) }}
                                @endif
                            </div>
                            <p class="meta"><strong>{{ $profileUser->name }}</strong></p>
                            <p class="meta">{{ $profileUser->email }}</p>
                        </aside>

                        <div class="form-pane">
                            <h2 style="margin:0; font-size:1.1rem;">Alterar avatar</h2>
                            <p class="hint">Envie uma imagem JPG, PNG ou WEBP com ate 2MB.</p>

                            <form id="avatarForm" method="POST" action="{{ route('perfil.avatar.update') }}" enctype="multipart/form-data">
                                @csrf
                                <label class="label" for="avatar">Imagem do avatar</label>
                                <input id="avatar" class="file-input" type="file" name="avatar" accept="image/png,image/jpeg,image/webp" required>

                                <div style="margin-top: 12px;">
                                    <button id="submitAvatar" class="btn-primary" type="submit">
                                        <span class="btn-loader" aria-hidden="true"></span>
                                        <span class="btn-text">Salvar avatar</span>
                                    </button>
                                </div>
                            </form>

                            <h3 style="margin:10px 0 0; font-size:1rem;">Dados da conta</h3>
                            <label class="label" for="profile_name">Nome</label>
                            <div id="profile_name" class="read-only">{{ $profileUser->name }}</div>

                            <label class="label" for="profile_email">E-mail</label>
                            <div id="profile_email" class="read-only">{{ $profileUser->email }}</div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <div id="mobileOverlay" class="overlay" aria-hidden="true"></div>

    @include('partials.feedback-widget')
    @include('partials.adsense-placements')

    <script>
        (function () {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('mobileOverlay');
            var openSidebar = document.getElementById('openSidebar');
            var avatarButton = document.getElementById('avatarButton');
            var avatarMenu = document.getElementById('avatarMenu');
            var menuToggles = [
                {button: document.getElementById('bancaToggle'), submenu: document.getElementById('bancaSubmenu')},
                {button: document.getElementById('simuladoToggle'), submenu: document.getElementById('simuladoSubmenu')},
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
                if (isDesktop()) {
                    return;
                }

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
                if (!button || !submenu) {
                    return;
                }

                var isOpen = submenu.classList.contains('is-open');
                submenu.classList.toggle('is-open', !isOpen);
                button.setAttribute('aria-expanded', String(!isOpen));
            }

            openSidebar.addEventListener('click', function () {
                if (sidebar.classList.contains('is-open')) {
                    closeSidebarDrawer();
                } else {
                    openSidebarDrawer();
                }
            });

            overlay.addEventListener('click', closeSidebarDrawer);

            avatarButton.addEventListener('click', function (event) {
                event.stopPropagation();
                if (avatarMenu.hidden) {
                    openAvatarMenu();
                } else {
                    closeAvatarMenu();
                }
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
                if (isDesktop()) {
                    closeSidebarDrawer();
                }
            });
        })();

        (function () {
            var form = document.getElementById('avatarForm');
            var fileInput = document.getElementById('avatar');
            var preview = document.getElementById('avatarPreview');
            var submit = document.getElementById('submitAvatar');

            if (!form || !fileInput || !preview || !submit) {
                return;
            }

            fileInput.addEventListener('change', function () {
                var file = fileInput.files && fileInput.files[0];
                if (!file) return;

                var reader = new FileReader();
                reader.onload = function (event) {
                    if (!event.target || !event.target.result) return;
                    preview.innerHTML = '<img src="' + event.target.result + '" alt="Preview do novo avatar">';
                };
                reader.readAsDataURL(file);
            });

            form.addEventListener('submit', function () {
                submit.classList.add('is-loading');
                submit.setAttribute('disabled', 'disabled');
                var text = submit.querySelector('.btn-text');
                if (text) {
                    text.textContent = 'Salvando...';
                }
            });
        })();
    </script>
</body>
</html>
