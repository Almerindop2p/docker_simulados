@extends('layouts.admin-panel')

@section('title', 'Adicionar Simulado | Painel ADM')
@section('breadcrumb', 'Painel / Simulados / Adicionar')
@section('page_title', 'Adicionar Simulado')

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
        .select,
        .textarea,
        .file-input {
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
        .select:hover,
        .textarea:hover,
        .file-input:hover { border-color: #b5c9e7; }
        .input:focus,
        .select:focus,
        .textarea:focus,
        .file-input:focus { border-color: #2a67e8; box-shadow: 0 0 0 4px rgba(42, 103, 232, 0.16); outline: none; }

        .textarea {
            min-height: 110px;
            resize: vertical;
        }

        .file-input {
            padding: 9px 10px;
            line-height: 1.4;
        }

        .image-preview-wrap {
            margin-top: 6px;
            border: 1px solid #d5e1f2;
            border-radius: 12px;
            padding: 10px;
            background: #f8fbff;
            display: none;
            justify-content: center;
        }

        .image-preview-wrap.is-visible {
            display: flex;
        }

        .image-preview {
            display: block;
            max-width: min(100%, 520px);
            width: auto;
            height: auto;
            max-height: 260px;
            border-radius: 10px;
            border: 1px solid #d1ddf0;
            background: #fff;
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
    </style>
@endpush

@section('content')
    @php
        $visibilidadeOptions = [
            \App\Models\Simulado::VISIBILIDADE_PUBLICO => 'Publico',
            \App\Models\Simulado::VISIBILIDADE_PRIVADO => 'Privado',
            \App\Models\Simulado::VISIBILIDADE_ASSINANTES => 'Usuarios assinantes',
            \App\Models\Simulado::VISIBILIDADE_NAO_LISTADO => 'Nao listado',
        ];
    @endphp

    <section class="panel-card" aria-labelledby="titulo-form-simulado">
        <div>
            <h2 id="titulo-form-simulado" class="panel-title">Cadastrar novo simulado</h2>
            <p class="panel-subtitle">Preencha os dados para adicionar um novo simulado ao sistema.</p>
        </div>

        @if ($errors->any())
            <div class="alert" role="alert">
                Revise os campos do formulario antes de continuar.
            </div>
        @endif

        <form id="simuladoForm" class="form-grid" method="POST" action="{{ route('adm.simulados.store') }}" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="field">
                <label class="label" for="name">Nome do simulado</label>
                <input
                    id="name"
                    name="name"
                    class="input"
                    type="text"
                    maxlength="120"
                    value="{{ old('name') }}"
                    autocomplete="off"
                    required
                    aria-describedby="name-help name-status"
                >
                <p id="name-help" class="help">Exemplo: Simulado ENEM 2026.</p>
                <p id="name-status" class="status" aria-live="polite"></p>
                @error('name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label class="label" for="slug">Slug</label>
                <input
                    id="slug"
                    name="slug"
                    class="input"
                    type="text"
                    maxlength="140"
                    value="{{ old('slug') }}"
                    autocomplete="off"
                    required
                    aria-describedby="slug-status"
                >
                <p class="help">Use letras minusculas, numeros e hifen. Exemplo: simulado-enem-2026.</p>
                <p id="slug-status" class="status" aria-live="polite"></p>
                @error('slug')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label class="label" for="descricao">Descricao</label>
                <textarea
                    id="descricao"
                    name="descricao"
                    class="textarea"
                    maxlength="3000"
                    aria-describedby="descricao-help"
                >{{ old('descricao') }}</textarea>
                <p id="descricao-help" class="help">Resumo opcional do simulado para exibicao em listagens e detalhes.</p>
                @error('descricao')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label class="label" for="imagem_destaque">Imagem destaque</label>
                <input
                    id="imagem_destaque"
                    name="imagem_destaque"
                    class="file-input"
                    type="file"
                    accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif"
                    aria-describedby="imagem-help"
                >
                <p id="imagem-help" class="help">Upload opcional. Formatos: jpg, jpeg, png, webp ou gif (maximo 5MB).</p>
                <div id="imagemPreviewWrap" class="image-preview-wrap" aria-live="polite">
                    <img id="imagemPreview" class="image-preview" alt="Preview da imagem destaque do simulado">
                </div>
                @error('imagem_destaque')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label class="label" for="visibilidade">Visibilidade</label>
                <select id="visibilidade" name="visibilidade" class="select" required>
                    <option value="">Selecione</option>
                    @foreach ($visibilidadeOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('visibilidade') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('visibilidade')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="actions">
                <button id="submitButton" class="btn btn-primary" type="submit">Cadastrar simulado</button>
                <a class="btn btn-soft" href="{{ route('adm.simulados.index') }}">Cancelar</a>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var nameInput = document.getElementById('name');
            var slugInput = document.getElementById('slug');
            var nameStatusEl = document.getElementById('name-status');
            var slugStatusEl = document.getElementById('slug-status');
            var form = document.getElementById('simuladoForm');
            var submitButton = document.getElementById('submitButton');
            var imageInput = document.getElementById('imagem_destaque');
            var imagePreviewWrap = document.getElementById('imagemPreviewWrap');
            var imagePreview = document.getElementById('imagemPreview');

            if (!nameInput || !slugInput || !nameStatusEl || !slugStatusEl || !form || !submitButton) {
                return;
            }

            var defaultSubmitText = submitButton.textContent;
            var slugTouched = slugInput.value.trim() !== '';
            var invalidName = false;
            var invalidSlug = false;
            var pendingName = false;
            var pendingSlug = false;
            var nameCheckId = 0;
            var slugCheckId = 0;
            var timerName = null;
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

            function setStatus(element, type, text) {
                element.className = 'status' + (type ? ' ' + type : '');
                element.textContent = text || '';
            }

            function updateSubmitState() {
                if (forceSubmitting) {
                    return;
                }

                submitButton.disabled = invalidName || invalidSlug || pendingName || pendingSlug;
            }

            function setPending(field, pending) {
                if (field === 'name') {
                    pendingName = pending;
                } else {
                    pendingSlug = pending;
                }

                updateSubmitState();
            }

            function setInvalid(field, invalid) {
                if (field === 'name') {
                    invalidName = invalid;
                } else {
                    invalidSlug = invalid;
                }

                updateSubmitState();
            }

            function checkField(field, value, statusEl) {
                var isName = field === 'name';
                var trimmed = value.trim();
                var requestId;

                if (trimmed.length < 3) {
                    setStatus(statusEl, '', '');
                    setInvalid(field, false);
                    setPending(field, false);
                    return Promise.resolve();
                }

                if (isName) {
                    nameCheckId += 1;
                    requestId = nameCheckId;
                } else {
                    slugCheckId += 1;
                    requestId = slugCheckId;
                }

                setPending(field, true);
                setStatus(statusEl, '', 'Validando...');

                return fetch('{{ route('adm.simulados.check-field') }}?field=' + encodeURIComponent(field) + '&value=' + encodeURIComponent(trimmed), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if ((isName && requestId !== nameCheckId) || (!isName && requestId !== slugCheckId)) {
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
                        if ((isName && requestId !== nameCheckId) || (!isName && requestId !== slugCheckId)) {
                            return;
                        }

                        setStatus(statusEl, 'error', 'Nao foi possivel validar agora.');
                        setInvalid(field, true);
                    })
                    .finally(function () {
                        if ((isName && requestId !== nameCheckId) || (!isName && requestId !== slugCheckId)) {
                            return;
                        }

                        setPending(field, false);
                    });
            }

            nameInput.addEventListener('input', function () {
                if (!slugTouched) {
                    slugInput.value = toSlug(nameInput.value);
                }

                clearTimeout(timerName);
                timerName = setTimeout(function () {
                    checkField('name', nameInput.value, nameStatusEl);
                }, 420);

                clearTimeout(timerSlug);
                timerSlug = setTimeout(function () {
                    checkField('slug', slugInput.value, slugStatusEl);
                }, 420);
            });

            nameInput.addEventListener('blur', function () {
                checkField('name', nameInput.value, nameStatusEl);
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

                clearTimeout(timerName);
                clearTimeout(timerSlug);
                submitButton.disabled = true;
                submitButton.textContent = 'Validando...';

                Promise.all([
                    checkField('name', nameInput.value, nameStatusEl),
                    checkField('slug', slugInput.value, slugStatusEl)
                ]).then(function () {
                    if (invalidName || invalidSlug || pendingName || pendingSlug) {
                        submitButton.textContent = defaultSubmitText;
                        updateSubmitState();

                        if (invalidName) {
                            nameInput.focus();
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

            if (imageInput && imagePreviewWrap && imagePreview) {
                imageInput.addEventListener('change', function () {
                    var file = imageInput.files && imageInput.files[0] ? imageInput.files[0] : null;
                    if (!file) {
                        imagePreviewWrap.classList.remove('is-visible');
                        imagePreview.removeAttribute('src');
                        return;
                    }

                    var objectUrl = URL.createObjectURL(file);
                    imagePreview.src = objectUrl;
                    imagePreviewWrap.classList.add('is-visible');
                    imagePreview.onload = function () {
                        URL.revokeObjectURL(objectUrl);
                    };
                });
            }
        })();
    </script>
@endpush
