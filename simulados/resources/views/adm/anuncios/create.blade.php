@extends('layouts.admin-panel')

@section('title', 'Adicionar Anuncio | Painel ADM')
@section('breadcrumb', 'Painel / Anuncios / Adicionar')
@section('page_title', 'Adicionar Anuncio')

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
        .input,
        .select {
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
        .input:hover,
        .select:hover { border-color: #b5c9e7; }
        .input:focus,
        .select:focus { border-color: #2a67e8; box-shadow: 0 0 0 4px rgba(42, 103, 232, 0.16); outline: none; }
        .code-input {
            width: 100%;
            min-height: 160px;
            border-radius: 12px;
            border: 1px solid #cedaeb;
            background: #ffffff;
            padding: 10px 12px;
            font-size: 13px;
            color: #1d3352;
            font-family: Consolas, "Courier New", monospace;
            line-height: 1.5;
            resize: vertical;
        }
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
        .adsense-fieldset[hidden] {
            display: none !important;
        }
    </style>
@endpush

@section('content')
    @php
        $formatOptions = \App\Models\AdPost::formatOptions();
        $isActive = old('is_active', '1') === '1';
    @endphp

    <section class="panel-card" aria-labelledby="titulo-form-anuncio">
        <div>
            <h2 id="titulo-form-anuncio" class="panel-title">Cadastrar novo anuncio</h2>
            <p class="panel-subtitle">Adicione cada formato de anuncio do site e mantenha ativo apenas o que deve aparecer em producao.</p>
        </div>

        @if ($errors->any())
            <div class="alert" role="alert">
                Revise os campos do formulario antes de continuar.
            </div>
        @endif

        <form id="anuncioForm" class="form-grid" method="POST" action="{{ route('adm.anuncios.store') }}" novalidate>
            @csrf

            <div class="field">
                <label class="label" for="title">Titulo do anuncio</label>
                <input id="title" name="title" class="input" type="text" maxlength="120" value="{{ old('title') }}" autocomplete="off" required aria-describedby="title-status">
                <p id="title-status" class="status" aria-live="polite"></p>
                @error('title')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label class="label" for="slug">Slug</label>
                <input id="slug" name="slug" class="input" type="text" maxlength="140" value="{{ old('slug') }}" autocomplete="off" required aria-describedby="slug-status">
                <p class="help">Use letras minusculas, numeros e hifen. Exemplo: home-horizontal-topo.</p>
                <p id="slug-status" class="status" aria-live="polite"></p>
                @error('slug')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label class="label" for="format">Formato</label>
                <select id="format" name="format" class="select" required>
                    <option value="">Selecione</option>
                    @foreach ($formatOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('format') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="help">Formatos atualmente mapeados no site: horizontal e vertical.</p>
                @error('format')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label class="label" for="is_active">Status</label>
                <select id="is_active" name="is_active" class="select" required>
                    <option value="1" @selected($isActive)>Ativo</option>
                    <option value="0" @selected(!$isActive)>Inativo</option>
                </select>
                <p class="help">Somente anuncios ativos podem ser exibidos quando o Adsense global estiver ativo.</p>
                @error('is_active')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <fieldset id="adsenseFieldset" class="adsense-fieldset" @if (!$isActive) hidden @endif>
                <div class="field">
                    <label class="label" for="embed_code">Codigo Adsense</label>
                    <textarea id="embed_code" name="embed_code" class="code-input" placeholder="<ins class='adsbygoogle' ...></ins>">{{ old('embed_code') }}</textarea>
                    <p class="help">Este campo e exibido apenas quando o anuncio esta ativo.</p>
                    @error('embed_code')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
            </fieldset>

            <div class="actions">
                <button id="submitButton" class="btn btn-primary" type="submit">Cadastrar anuncio</button>
                <a class="btn btn-soft" href="{{ route('adm.anuncios.index') }}">Cancelar</a>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var titleInput = document.getElementById('title');
            var slugInput = document.getElementById('slug');
            var titleStatusEl = document.getElementById('title-status');
            var slugStatusEl = document.getElementById('slug-status');
            var statusSelect = document.getElementById('is_active');
            var adsenseFieldset = document.getElementById('adsenseFieldset');
            var form = document.getElementById('anuncioForm');
            var submitButton = document.getElementById('submitButton');

            if (!titleInput || !slugInput || !titleStatusEl || !slugStatusEl || !statusSelect || !adsenseFieldset || !form || !submitButton) {
                return;
            }

            var defaultSubmitText = submitButton.textContent;
            var slugTouched = slugInput.value.trim() !== '';
            var invalidTitle = false;
            var invalidSlug = false;
            var pendingTitle = false;
            var pendingSlug = false;
            var titleCheckId = 0;
            var slugCheckId = 0;
            var timerTitle = null;
            var timerSlug = null;
            var forceSubmitting = false;

            function toSlug(value) {
                return value
                    .toString()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
            }

            function updateAdsenseVisibility() {
                adsenseFieldset.hidden = statusSelect.value !== '1';
            }

            function setStatus(element, type, text) {
                element.className = 'status' + (type ? ' ' + type : '');
                element.textContent = text || '';
            }

            function updateSubmitState() {
                if (forceSubmitting) {
                    return;
                }
                submitButton.disabled = invalidTitle || invalidSlug || pendingTitle || pendingSlug;
            }

            function setPending(field, pending) {
                if (field === 'title') {
                    pendingTitle = pending;
                } else {
                    pendingSlug = pending;
                }
                updateSubmitState();
            }

            function setInvalid(field, invalid) {
                if (field === 'title') {
                    invalidTitle = invalid;
                } else {
                    invalidSlug = invalid;
                }
                updateSubmitState();
            }

            function checkField(field, value, statusEl) {
                var isTitle = field === 'title';
                var trimmed = value.trim();
                var requestId;

                if (trimmed.length < 3) {
                    setStatus(statusEl, '', '');
                    setInvalid(field, false);
                    setPending(field, false);
                    return Promise.resolve();
                }

                if (isTitle) {
                    titleCheckId += 1;
                    requestId = titleCheckId;
                } else {
                    slugCheckId += 1;
                    requestId = slugCheckId;
                }

                setPending(field, true);
                setStatus(statusEl, '', 'Validando...');

                return fetch('{{ route('adm.anuncios.check-field') }}?field=' + encodeURIComponent(field) + '&value=' + encodeURIComponent(trimmed), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if ((isTitle && requestId !== titleCheckId) || (!isTitle && requestId !== slugCheckId)) {
                            return;
                        }

                        if (data.exists) {
                            setStatus(statusEl, 'error', data.message || 'Esse valor ja esta em uso.');
                            setInvalid(field, true);
                            return;
                        }

                        setStatus(statusEl, 'ok', data.message || 'Valor disponivel.');
                        setInvalid(field, false);
                    })
                    .catch(function () {
                        if ((isTitle && requestId !== titleCheckId) || (!isTitle && requestId !== slugCheckId)) {
                            return;
                        }

                        setStatus(statusEl, 'error', 'Nao foi possivel validar agora.');
                        setInvalid(field, true);
                    })
                    .finally(function () {
                        if ((isTitle && requestId !== titleCheckId) || (!isTitle && requestId !== slugCheckId)) {
                            return;
                        }

                        setPending(field, false);
                    });
            }

            statusSelect.addEventListener('change', updateAdsenseVisibility);

            titleInput.addEventListener('input', function () {
                if (!slugTouched) {
                    slugInput.value = toSlug(titleInput.value);
                }

                clearTimeout(timerTitle);
                timerTitle = setTimeout(function () {
                    checkField('title', titleInput.value, titleStatusEl);
                }, 420);

                clearTimeout(timerSlug);
                timerSlug = setTimeout(function () {
                    checkField('slug', slugInput.value, slugStatusEl);
                }, 420);
            });

            slugInput.addEventListener('input', function () {
                slugInput.value = toSlug(slugInput.value);
                slugTouched = true;

                clearTimeout(timerSlug);
                timerSlug = setTimeout(function () {
                    checkField('slug', slugInput.value, slugStatusEl);
                }, 420);
            });

            form.addEventListener('submit', function (event) {
                if (forceSubmitting) return;
                event.preventDefault();

                clearTimeout(timerTitle);
                clearTimeout(timerSlug);
                submitButton.disabled = true;
                submitButton.textContent = 'Validando...';

                Promise.all([
                    checkField('title', titleInput.value, titleStatusEl),
                    checkField('slug', slugInput.value, slugStatusEl)
                ]).then(function () {
                    if (invalidTitle || invalidSlug || pendingTitle || pendingSlug) {
                        submitButton.textContent = defaultSubmitText;
                        updateSubmitState();
                        if (invalidTitle) {
                            titleInput.focus();
                        } else if (invalidSlug) {
                            slugInput.focus();
                        }
                        return;
                    }

                    forceSubmitting = true;
                    submitButton.disabled = true;
                    submitButton.textContent = 'Salvando...';
                    form.submit();
                });
            });

            updateAdsenseVisibility();
        })();
    </script>
@endpush
