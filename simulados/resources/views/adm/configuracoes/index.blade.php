@extends('layouts.admin-panel')

@section('title', 'Configuracoes | Painel Admin')
@section('breadcrumb', 'Admin / Configuracoes')
@section('page_title', 'Configuracoes do Admin')

@push('styles')
    <style>
        .config-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 18px;
            display: grid;
            gap: 14px;
        }
        .config-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: 1fr;
        }
        .avatar-pane {
            border: 1px solid #d6e2f4;
            background: #f8fbff;
            border-radius: 14px;
            padding: 14px;
            display: grid;
            gap: 10px;
            justify-items: start;
        }
        .avatar-circle {
            width: 96px;
            height: 96px;
            border-radius: 999px;
            border: 2px solid #d0dff6;
            background: #fff;
            display: grid;
            place-items: center;
            overflow: hidden;
            font-weight: 800;
            color: #365d8f;
            font-size: 30px;
        }
        .avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .meta {
            margin: 0;
            color: var(--text-soft);
            font-size: 14px;
        }
        .meta strong {
            color: var(--text-main);
        }
        .form-pane {
            border: 1px solid #d6e2f4;
            border-radius: 14px;
            padding: 14px;
            display: grid;
            gap: 12px;
        }
        .section-title {
            margin: 0;
            font-size: 1.08rem;
            letter-spacing: -0.01em;
        }
        .hint {
            margin: 0;
            color: var(--text-soft);
            font-size: 14px;
        }
        .label {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
            color: #36577f;
        }
        .file-input {
            width: 100%;
            min-height: 42px;
            border: 1px solid #ccdbef;
            border-radius: 10px;
            padding: 8px 10px;
            background: #fff;
            font: inherit;
            color: var(--text-main);
        }
        .code-input {
            width: 100%;
            min-height: 140px;
            border: 1px solid #ccdbef;
            border-radius: 10px;
            padding: 10px;
            background: #fff;
            color: var(--text-main);
            font-family: Consolas, "Courier New", monospace;
            font-size: 13px;
            line-height: 1.5;
            resize: vertical;
        }
        .adsense-fields[hidden] {
            display: none !important;
        }
        .adsense-summary {
            border: 1px solid #d6e2f4;
            border-radius: 12px;
            background: #f8fbff;
            padding: 12px;
            display: grid;
            gap: 8px;
        }
        .adsense-summary p {
            margin: 0;
            color: #36577f;
            font-size: 14px;
        }
        .read-only {
            min-height: 42px;
            border: 1px solid #d6e2f4;
            border-radius: 10px;
            background: #f9fbff;
            color: #36577f;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            font-size: 14px;
        }
        .btn-primary {
            min-height: 44px;
            border: 0;
            border-radius: 12px;
            padding: 10px 14px;
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--brand), #4c83f0);
            box-shadow: 0 10px 20px rgba(31, 95, 224, 0.24);
            font-weight: 700;
            cursor: pointer;
        }
        .btn-primary:disabled {
            opacity: .72;
            cursor: not-allowed;
        }
        .btn-loader {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #fff;
            border-radius: 999px;
            display: none;
            animation: spin .8s linear infinite;
        }
        .btn-primary.is-loading .btn-loader {
            display: inline-block;
        }
        .status {
            margin: 0;
            border: 1px solid #cfe9d8;
            background: #f1fbf4;
            color: #1f6d3c;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
        }
        .errors {
            margin: 0;
            border: 1px solid #f0ccd0;
            background: #fff5f6;
            color: #9b2f37;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @media (min-width: 920px) {
            .config-grid {
                grid-template-columns: 280px minmax(0, 1fr);
            }
        }
    </style>
@endpush

@section('content')
    @php
        $feedbackFeedEnabled = old('feedback_feed_enabled', (int) (($siteConfig->feedback_feed_enabled ?? true) ? 1 : 0));
        $recaptchaEnabled = old('recaptcha_enabled', (int) (($siteConfig->recaptcha_enabled ?? false) ? 1 : 0));
        $recaptchaSiteKey = old('recaptcha_site_key', (string) ($siteConfig->recaptcha_site_key ?? ''));
        $recaptchaSecretKey = old('recaptcha_secret_key', (string) ($siteConfig->recaptcha_secret_key ?? ''));
        $adsenseEnabled = old('adsense_enabled', (int) (($siteConfig->adsense_enabled ?? false) ? 1 : 0));
        $adsenseScript = old('adsense_head_script', (string) ($siteConfig->adsense_head_script ?? ''));
        $customHtmlCode = old('custom_html_code', (string) ($siteConfig->custom_html_code ?? ''));
    @endphp

    @if (session('status'))
        <p class="status" role="status">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <p class="errors" role="alert">{{ $errors->first() }}</p>
    @endif

    <section class="config-card">
        <div class="config-grid">
            <aside class="avatar-pane">
                <div id="avatarPreview" class="avatar-circle" aria-hidden="true">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="Avatar atual de {{ $user->name }}">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <p class="meta"><strong>{{ $user->name }}</strong></p>
                <p class="meta">{{ $user->email }}</p>
            </aside>

            <div class="form-pane">
                <h2 class="section-title">Configurar avatar</h2>
                <p class="hint">Envie uma imagem JPG, PNG ou WEBP com ate 2MB.</p>

                <form id="avatarForm" method="POST" action="{{ route('perfil.avatar.update') }}" enctype="multipart/form-data">
                    @csrf
                    <label class="label" for="avatar">Imagem do avatar</label>
                    <input id="avatar" class="file-input" type="file" name="avatar" accept="image/png,image/jpeg,image/webp" required>

                    <div>
                        <button id="submitAvatar" class="btn-primary" type="submit">
                            <span class="btn-loader" aria-hidden="true"></span>
                            <span class="btn-text">Salvar avatar</span>
                        </button>
                    </div>
                </form>

                <h3 class="section-title" style="font-size: 1rem;">Dados da conta</h3>
                <label class="label" for="profile_name">Nome</label>
                <div id="profile_name" class="read-only">{{ $user->name }}</div>

                <label class="label" for="profile_email">E-mail</label>
                <div id="profile_email" class="read-only">{{ $user->email }}</div>
            </div>
        </div>
    </section>

    <section class="config-card">
        <div class="form-pane">
            <h2 class="section-title">Configuracao do feed de feedback</h2>
            <p class="hint">Defina se o feed de feedback (icone flutuante) ficara ativo no site.</p>

            <form method="POST" action="{{ route('adm.configuracoes.feed.update') }}">
                @csrf
                @method('PATCH')

                <label class="label" for="feedback_feed_enabled">Feed de feedback</label>
                <select id="feedback_feed_enabled" name="feedback_feed_enabled" class="file-input" required>
                    <option value="1" @selected((string) $feedbackFeedEnabled === '1')>Ativo</option>
                    <option value="0" @selected((string) $feedbackFeedEnabled === '0')>Inativo</option>
                </select>

                <div style="margin-top: 12px;">
                    <button class="btn-primary" type="submit">Salvar feed</button>
                </div>
            </form>
        </div>
    </section>

    <section class="config-card">
        <div class="form-pane">
            <h2 class="section-title">Configuracoes do reCAPTCHA do Google</h2>
            <p class="hint">Ative para validar humano no cadastro. As chaves sao salvas de forma criptografada na base de dados.</p>

            <form method="POST" action="{{ route('adm.configuracoes.recaptcha.update') }}">
                @csrf
                @method('PATCH')

                <label class="label" for="recaptcha_enabled">reCAPTCHA</label>
                <select id="recaptcha_enabled" name="recaptcha_enabled" class="file-input" required>
                    <option value="1" @selected((string) $recaptchaEnabled === '1')>Ativo</option>
                    <option value="0" @selected((string) $recaptchaEnabled === '0')>Inativo</option>
                </select>

                <div id="recaptchaFields" class="adsense-fields" @if((string) $recaptchaEnabled !== '1') hidden @endif>
                    <label class="label" for="recaptcha_site_key" style="margin-top: 12px;">Site Key (publica)</label>
                    <input
                        id="recaptcha_site_key"
                        name="recaptcha_site_key"
                        type="text"
                        class="file-input"
                        value="{{ $recaptchaSiteKey }}"
                        autocomplete="off"
                    >

                    <label class="label" for="recaptcha_secret_key" style="margin-top: 12px;">Secret Key (privada)</label>
                    <input
                        id="recaptcha_secret_key"
                        name="recaptcha_secret_key"
                        type="password"
                        class="file-input"
                        value="{{ $recaptchaSecretKey }}"
                        autocomplete="new-password"
                    >
                </div>

                <div style="margin-top: 12px;">
                    <button class="btn-primary" type="submit">Salvar reCAPTCHA</button>
                </div>
            </form>
        </div>
    </section>

    <section class="config-card">
        <div class="form-pane">
            <h2 class="section-title">Configuracao de Adsense</h2>
            <p class="hint">Defina se o Adsense fica ativo. Quando ativo, o campo de script e liberado e os anuncios ativos sao exibidos no site.</p>

            <form method="POST" action="{{ route('adm.configuracoes.adsense.update') }}">
                @csrf
                @method('PATCH')

                <label class="label" for="adsense_enabled">Anuncio Adsense</label>
                <select id="adsense_enabled" name="adsense_enabled" class="file-input" required>
                    <option value="1" @selected((string) $adsenseEnabled === '1')>Ativo</option>
                    <option value="0" @selected((string) $adsenseEnabled === '0')>Inativo</option>
                </select>

                <div id="adsenseFields" class="adsense-fields" @if((string) $adsenseEnabled !== '1') hidden @endif>
                    <label class="label" for="adsense_head_script" style="margin-top: 12px;">Script do Adsense (head)</label>
                    <textarea
                        id="adsense_head_script"
                        name="adsense_head_script"
                        class="code-input"
                        placeholder="<script async src='https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=...'></script>"
                    >{{ $adsenseScript }}</textarea>
                </div>

                <div style="margin-top: 12px;">
                    <button class="btn-primary" type="submit">Salvar configuracoes</button>
                </div>
            </form>

            <div class="adsense-summary">
                <p>Total de anuncios cadastrados: <strong>{{ (int) ($totalAds ?? 0) }}</strong></p>
                <p>Anuncios ativos: <strong>{{ (int) ($activeAds ?? 0) }}</strong></p>
                <div>
                    <a class="btn-primary" href="{{ route('adm.anuncios.index') }}">Gerenciar formatos de anuncios</a>
                </div>
            </div>
        </div>
    </section>

    <section class="config-card">
        <div class="form-pane">
            <h2 class="section-title">Codigo HTML personalizado</h2>
            <p class="hint">Cole abaixo um codigo HTML longo para manter salvo na base de dados.</p>

            <form method="POST" action="{{ route('adm.configuracoes.custom-html.update') }}">
                @csrf
                @method('PATCH')

                <label class="label" for="custom_html_code">Codigo HTML</label>
                <textarea
                    id="custom_html_code"
                    name="custom_html_code"
                    class="code-input"
                    placeholder="<div>Seu codigo HTML personalizado</div>"
                >{{ $customHtmlCode }}</textarea>

                <div style="margin-top: 12px;">
                    <button class="btn-primary" type="submit">Salvar codigo HTML</button>
                </div>
            </form>
        </div>
    </section>

    <script>
        (function () {
            var form = document.getElementById('avatarForm');
            var fileInput = document.getElementById('avatar');
            var preview = document.getElementById('avatarPreview');
            var submit = document.getElementById('submitAvatar');
            var recaptchaEnabledSelect = document.getElementById('recaptcha_enabled');
            var recaptchaFields = document.getElementById('recaptchaFields');
            var adsenseEnabledSelect = document.getElementById('adsense_enabled');
            var adsenseFields = document.getElementById('adsenseFields');

            if (!form || !fileInput || !preview || !submit) {
                return;
            }

            fileInput.addEventListener('change', function () {
                var file = fileInput.files && fileInput.files[0];
                if (!file) {
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (event) {
                    if (!event.target || !event.target.result) {
                        return;
                    }
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

            if (adsenseEnabledSelect && adsenseFields) {
                adsenseEnabledSelect.addEventListener('change', function () {
                    adsenseFields.hidden = adsenseEnabledSelect.value !== '1';
                });
            }

            if (recaptchaEnabledSelect && recaptchaFields) {
                recaptchaEnabledSelect.addEventListener('change', function () {
                    recaptchaFields.hidden = recaptchaEnabledSelect.value !== '1';
                });
            }
        })();
    </script>
@endsection
