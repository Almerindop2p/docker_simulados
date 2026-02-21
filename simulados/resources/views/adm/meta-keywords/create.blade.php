@extends('layouts.admin-panel')

@section('title', 'Adicionar Meta Keyword | Painel ADM')
@section('breadcrumb', 'Painel / SEO / Adicionar')
@section('page_title', 'Adicionar Meta Keyword')

@push('styles')
    <style>
        .panel-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            padding: 18px;
            display: grid;
            gap: 16px;
        }

        .panel-title {
            margin: 0;
            font-size: clamp(1.15rem, 2vw, 1.4rem);
            letter-spacing: -0.01em;
        }

        .panel-subtitle {
            margin: 0;
            color: var(--text-soft);
            line-height: 1.6;
        }

        .form-grid {
            display: grid;
            gap: 14px;
        }

        .field {
            display: grid;
            gap: 6px;
        }

        .label {
            font-size: 14px;
            font-weight: 700;
            color: #27466d;
        }

        .input {
            width: 100%;
            min-height: 44px;
            border-radius: 12px;
            border: 1px solid #cedaeb;
            background: #ffffff;
            padding: 10px 12px;
            font-size: 15px;
            color: #1d3352;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .input:hover { border-color: #b5c9e7; }
        .input:focus { border-color: #2a67e8; box-shadow: 0 0 0 4px rgba(42, 103, 232, 0.16); outline: none; }

        .help {
            margin: 0;
            font-size: 12px;
            color: #5a708d;
        }

        .status {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            min-height: 20px;
        }

        .status.ok { color: #16663f; }
        .status.error { color: #b7233e; }

        .field-error {
            margin: 0;
            color: #b4233c;
            font-size: 12px;
            font-weight: 600;
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            min-height: 44px;
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 10px 16px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .btn-primary {
            color: #fff;
            border: 0;
            background: linear-gradient(135deg, var(--brand), #4c83f0);
            box-shadow: 0 10px 20px rgba(31, 95, 224, 0.24);
        }

        .btn-primary:disabled {
            opacity: .7;
            cursor: not-allowed;
        }

        .btn-soft {
            color: #1d3f6d;
            border-color: #cedaeb;
            background: #f8fbff;
        }

        .alert {
            border: 1px solid #efd4d9;
            background: #fff7f8;
            color: #a3213a;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 13px;
            line-height: 1.5;
        }
    </style>
@endpush

@section('content')
    <section class="panel-card" aria-labelledby="titulo-form-meta-keyword">
        <div>
            <h2 id="titulo-form-meta-keyword" class="panel-title">Cadastrar palavra-chave</h2>
            <p class="panel-subtitle">Esse valor passara a compor o meta keywords de todas as paginas.</p>
        </div>

        @if ($errors->any())
            <div class="alert" role="alert">
                Revise os campos do formulario antes de continuar.
            </div>
        @endif

        <form id="metaKeywordForm" class="form-grid" method="POST" action="{{ route('adm.meta-keywords.store') }}" novalidate>
            @csrf

            <div class="field">
                <label class="label" for="keyword">Palavra-chave</label>
                <input
                    id="keyword"
                    name="keyword"
                    class="input"
                    type="text"
                    maxlength="160"
                    value="{{ old('keyword') }}"
                    autocomplete="off"
                    required
                    aria-describedby="keyword-help keyword-status"
                >
                <p id="keyword-help" class="help">Exemplo: concurso publico, simulados online, enem.</p>
                <p id="keyword-status" class="status" aria-live="polite"></p>
                @error('keyword')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="actions">
                <button id="submitButton" class="btn btn-primary" type="submit">Cadastrar palavra-chave</button>
                <a class="btn btn-soft" href="{{ route('adm.meta-keywords.index') }}">Cancelar</a>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var keywordInput = document.getElementById('keyword');
            var keywordStatusEl = document.getElementById('keyword-status');
            var form = document.getElementById('metaKeywordForm');
            var submitButton = document.getElementById('submitButton');

            if (!keywordInput || !keywordStatusEl || !form || !submitButton) {
                return;
            }

            var defaultSubmitText = submitButton.textContent;
            var invalidKeyword = false;
            var pendingKeyword = false;
            var keywordCheckId = 0;
            var timerKeyword = null;
            var forceSubmitting = false;

            function setStatus(type, text) {
                keywordStatusEl.className = 'status' + (type ? ' ' + type : '');
                keywordStatusEl.textContent = text || '';
            }

            function updateSubmitState() {
                if (forceSubmitting) {
                    return;
                }

                submitButton.disabled = invalidKeyword || pendingKeyword;
            }

            function checkKeyword(value) {
                var trimmed = value.trim();
                var requestId;

                if (trimmed.length < 2) {
                    setStatus('', '');
                    invalidKeyword = false;
                    pendingKeyword = false;
                    updateSubmitState();
                    return Promise.resolve();
                }

                keywordCheckId += 1;
                requestId = keywordCheckId;
                pendingKeyword = true;
                setStatus('', 'Validando...');
                updateSubmitState();

                return fetch('{{ route('adm.meta-keywords.check-field') }}?value=' + encodeURIComponent(trimmed), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if (requestId !== keywordCheckId) {
                            return;
                        }

                        if (data.exists) {
                            setStatus('error', data.message || 'Esse valor ja esta em uso.');
                            invalidKeyword = true;
                            return;
                        }

                        setStatus('ok', data.message || 'Palavra-chave disponivel.');
                        invalidKeyword = false;
                    })
                    .catch(function () {
                        if (requestId !== keywordCheckId) {
                            return;
                        }

                        setStatus('error', 'Nao foi possivel validar agora.');
                        invalidKeyword = true;
                    })
                    .finally(function () {
                        if (requestId !== keywordCheckId) {
                            return;
                        }

                        pendingKeyword = false;
                        updateSubmitState();
                    });
            }

            keywordInput.addEventListener('input', function () {
                clearTimeout(timerKeyword);
                timerKeyword = setTimeout(function () {
                    checkKeyword(keywordInput.value);
                }, 420);
            });

            keywordInput.addEventListener('blur', function () {
                checkKeyword(keywordInput.value);
            });

            form.addEventListener('submit', function (event) {
                if (forceSubmitting) return;
                event.preventDefault();

                clearTimeout(timerKeyword);
                submitButton.disabled = true;
                submitButton.textContent = 'Validando...';

                checkKeyword(keywordInput.value).then(function () {
                    if (invalidKeyword || pendingKeyword) {
                        submitButton.textContent = defaultSubmitText;
                        updateSubmitState();
                        if (invalidKeyword) {
                            keywordInput.focus();
                        }
                        return;
                    }

                    forceSubmitting = true;
                    submitButton.disabled = true;
                    submitButton.textContent = 'Salvando...';
                    form.submit();
                });
            });
        })();
    </script>
@endpush

