<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil | Simulados e Questoes</title>
    @include('partials.edu-theme-head')
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Plus Jakarta Sans", "Manrope", "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at 8% 10%, #ffffff 0%, rgba(255, 255, 255, 0) 42%),
                radial-gradient(circle at 92% 90%, #e9f1ff 0%, rgba(233, 241, 255, 0) 40%),
                linear-gradient(180deg, var(--bg-main), var(--bg-soft));
            color: var(--text-main);
            min-height: 100vh;
            padding: 20px 14px 28px;
        }

        .shell {
            max-width: 980px;
            margin: 0 auto;
            display: grid;
            gap: 16px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .back-link {
            text-decoration: none;
            color: #204b87;
            font-weight: 700;
            font-size: 14px;
            border: 1px solid #d4e2f6;
            background: #f8fbff;
            border-radius: 10px;
            min-height: 40px;
            padding: 9px 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-link:hover {
            background: #edf4ff;
        }

        .title {
            margin: 0;
            font-size: clamp(1.25rem, 2.4vw, 1.7rem);
            letter-spacing: -0.01em;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            padding: 18px;
        }

        .status {
            margin: 0 0 12px;
            border: 1px solid var(--ok-line);
            background: var(--ok-bg);
            color: var(--ok-text);
            border-radius: 12px;
            padding: 11px;
            font-size: 13px;
            line-height: 1.5;
        }

        .errors {
            margin: 0 0 12px;
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

        .btn {
            border: 0;
            border-radius: 12px;
            min-height: 44px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), #4d83f0);
            box-shadow: 0 10px 20px rgba(31, 95, 224, 0.24);
            cursor: pointer;
        }

        .btn:hover {
            background: linear-gradient(135deg, #1a56ce, #3f77e4);
        }

        .btn[disabled] {
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

        .btn.is-loading .btn-loader {
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

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (min-width: 860px) {
            body { padding: 28px; }
            .card { padding: 22px; }
            .profile-grid {
                grid-template-columns: 280px minmax(0, 1fr);
                align-items: start;
            }
        }
    </style>
</head>
<body>
    @php
        $homeRoute = ($user->user_type ?? null) === \App\Models\User::TYPE_ADM ? route('adm.bancas.index') : route('area_aluno');
    @endphp

    <div class="shell">
        <div class="topbar">
            <a class="back-link" href="{{ $homeRoute }}">&larr; Voltar</a>
            <h1 class="title">Perfil do usuario</h1>
        </div>

        <section class="card">
            @if (session('status'))
                <p class="status" role="status">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <div class="errors" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="profile-grid">
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

                <div class="form-pane" id="configuracoes">
                    <h2 style="margin:0; font-size:1.1rem;">Alterar avatar</h2>
                    <p class="hint">Envie uma imagem JPG, PNG ou WEBP com ate 2MB.</p>

                    <form id="avatarForm" method="POST" action="{{ route('perfil.avatar.update') }}" enctype="multipart/form-data">
                        @csrf
                        <label class="label" for="avatar">Imagem do avatar</label>
                        <input id="avatar" class="file-input" type="file" name="avatar" accept="image/png,image/jpeg,image/webp" required>

                        <div style="margin-top: 12px;">
                            <button id="submitAvatar" class="btn" type="submit">
                                <span class="btn-loader" aria-hidden="true"></span>
                                <span class="btn-text">Salvar avatar</span>
                            </button>
                        </div>
                    </form>

                    <h3 style="margin:10px 0 0; font-size:1rem;">Dados da conta</h3>
                    <label class="label" for="profile_name">Nome</label>
                    <div id="profile_name" class="read-only">{{ $user->name }}</div>

                    <label class="label" for="profile_email">E-mail</label>
                    <div id="profile_email" class="read-only">{{ $user->email }}</div>
                </div>
            </div>
        </section>
    </div>

    <script>
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
