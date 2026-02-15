@extends('layouts.admin-panel')

@section('title', 'Adicionar Materia | Painel ADM')
@section('breadcrumb', 'Painel / Materias / Adicionar')
@section('page_title', 'Adicionar Materia')

@push('styles')
    <style>
        .panel-card { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-lg); box-shadow: var(--shadow-card); padding: 18px; display: grid; gap: 16px; }
        .panel-title { margin: 0; font-size: clamp(1.15rem, 2vw, 1.4rem); letter-spacing: -0.01em; }
        .panel-subtitle { margin: 0; color: var(--text-soft); line-height: 1.6; }
        .form-grid { display: grid; gap: 14px; }
        .field { display: grid; gap: 6px; }
        .label { font-size: 14px; font-weight: 700; color: #27466d; }
        .input { width: 100%; min-height: 44px; border-radius: 12px; border: 1px solid #cedaeb; background: #ffffff; padding: 10px 12px; font-size: 15px; color: #1d3352; transition: border-color .2s ease, box-shadow .2s ease; }
        .input:hover { border-color: #b5c9e7; }
        .input:focus { border-color: #2a67e8; box-shadow: 0 0 0 4px rgba(42, 103, 232, 0.16); outline: none; }
        .help { margin: 0; font-size: 12px; color: #5a708d; }
        .status { margin: 0; font-size: 13px; font-weight: 600; min-height: 20px; }
        .status.ok { color: #16663f; }
        .status.error { color: #b7233e; }
        .field-error { margin: 0; color: #b4233c; font-size: 12px; font-weight: 600; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn { min-height: 44px; border: 1px solid transparent; border-radius: 12px; padding: 10px 16px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; }
        .btn-primary { color: #fff; border: 0; background: linear-gradient(135deg, var(--brand), #4c83f0); box-shadow: 0 10px 20px rgba(31, 95, 224, 0.24); }
        .btn-primary:disabled { opacity: .7; cursor: not-allowed; }
        .btn-soft { color: #1d3f6d; border-color: #cedaeb; background: #f8fbff; }
        .alert { border: 1px solid #efd4d9; background: #fff7f8; color: #a3213a; border-radius: 12px; padding: 10px 12px; font-size: 13px; line-height: 1.5; }
    </style>
@endpush

@section('content')
    <section class="panel-card" aria-labelledby="titulo-form-materia">
        <div>
            <h2 id="titulo-form-materia" class="panel-title">Cadastrar nova materia</h2>
            <p class="panel-subtitle">Preencha os dados para adicionar uma nova materia ao sistema.</p>
        </div>

        @if ($errors->any())
            <div class="alert" role="alert">Revise os campos do formulario antes de continuar.</div>
        @endif

        <form id="materiaForm" class="form-grid" method="POST" action="{{ route('adm.materias.store') }}" novalidate>
            @csrf
            <div class="field">
                <label class="label" for="name">Nome da materia</label>
                <input id="name" name="name" class="input" type="text" maxlength="120" value="{{ old('name') }}" autocomplete="off" required>
                <p class="status" id="nameStatus" aria-live="polite"></p>
                @error('name')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="field">
                <label class="label" for="slug">Slug</label>
                <input id="slug" name="slug" class="input" type="text" maxlength="140" value="{{ old('slug') }}" autocomplete="off" required>
                <p class="help">Use letras minusculas, numeros e hifen.</p>
                <p class="status" id="slugStatus" aria-live="polite"></p>
                @error('slug')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="actions">
                <button id="submitButton" class="btn btn-primary" type="submit">Cadastrar materia</button>
                <a class="btn btn-soft" href="{{ route('adm.materias.index') }}">Cancelar</a>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var form = document.getElementById('materiaForm');
            var nameInput = document.getElementById('name');
            var slugInput = document.getElementById('slug');
            var nameStatus = document.getElementById('nameStatus');
            var slugStatus = document.getElementById('slugStatus');
            var submitButton = document.getElementById('submitButton');

            if (!form || !nameInput || !slugInput || !nameStatus || !slugStatus || !submitButton) return;

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
                return value.toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
            }

            function setStatus(el, type, message) {
                el.className = 'status' + (type ? ' ' + type : '');
                el.textContent = message || '';
            }

            function updateSubmitState() {
                if (forceSubmitting) return;
                submitButton.disabled = invalidName || invalidSlug || pendingName || pendingSlug;
            }

            function setPending(field, pending) {
                if (field === 'name') pendingName = pending;
                if (field === 'slug') pendingSlug = pending;
                updateSubmitState();
            }

            function setInvalid(field, invalid) {
                if (field === 'name') invalidName = invalid;
                if (field === 'slug') invalidSlug = invalid;
                updateSubmitState();
            }

            function checkField(field, value, statusEl) {
                var trimmed = value.trim();
                var isName = field === 'name';
                var requestId;

                if (!trimmed || trimmed.length < 3) {
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

                return fetch('{{ route('adm.materias.check-field') }}?field=' + encodeURIComponent(field) + '&value=' + encodeURIComponent(trimmed), {
                    headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                    credentials: 'same-origin'
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if ((isName && requestId !== nameCheckId) || (!isName && requestId !== slugCheckId)) {
                            return;
                        }

                        if (data.exists) {
                            setStatus(statusEl, 'error', data.message || 'Valor em uso.');
                            setInvalid(field, true);
                        } else {
                            setStatus(statusEl, 'ok', data.message || 'Disponivel.');
                            setInvalid(field, false);
                        }
                    })
                    .catch(function () {
                        if ((isName && requestId !== nameCheckId) || (!isName && requestId !== slugCheckId)) {
                            return;
                        }
                        setStatus(statusEl, 'error', 'Falha ao validar campo.');
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
                if (!slugTouched) slugInput.value = toSlug(nameInput.value);
                clearTimeout(timerName);
                timerName = setTimeout(function () {
                    checkField('name', nameInput.value.trim(), nameStatus);
                }, 350);

                clearTimeout(timerSlug);
                timerSlug = setTimeout(function () {
                    checkField('slug', slugInput.value.trim(), slugStatus);
                }, 350);
            });

            slugInput.addEventListener('input', function () {
                slugInput.value = toSlug(slugInput.value);
                slugTouched = true;
                clearTimeout(timerSlug);
                timerSlug = setTimeout(function () {
                    checkField('slug', slugInput.value.trim(), slugStatus);
                }, 350);
            });

            form.addEventListener('submit', function (event) {
                if (forceSubmitting) return;
                event.preventDefault();

                clearTimeout(timerName);
                clearTimeout(timerSlug);
                submitButton.disabled = true;
                submitButton.textContent = 'Validando...';

                Promise.all([
                    checkField('name', nameInput.value, nameStatus),
                    checkField('slug', slugInput.value, slugStatus)
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
        })();
    </script>
@endpush
