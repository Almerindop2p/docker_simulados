@extends('layouts.admin-panel')

@section('title', 'Adicionar Questao | Painel ADM')
@section('breadcrumb', 'Painel / Questoes / Adicionar')
@section('page_title', 'Adicionar Questao')

@push('styles')
    <style>
        .panel-card { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-lg); box-shadow: var(--shadow-card); padding: 18px; display: grid; gap: 16px; }
        .panel-title { margin: 0; font-size: clamp(1.15rem, 2vw, 1.4rem); letter-spacing: -0.01em; }
        .panel-subtitle { margin: 0; color: var(--text-soft); line-height: 1.6; }
        .form-grid { display: grid; gap: 14px; }
        .grid-2 { display: grid; gap: 12px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .field { display: grid; gap: 6px; }
        .label { font-size: 14px; font-weight: 700; color: #27466d; }
        .input, .textarea, .select { width: 100%; border-radius: 12px; border: 1px solid #cedaeb; background: #fff; padding: 10px 12px; font-size: 15px; color: #1d3352; }
        .input, .select { min-height: 44px; }
        .textarea { min-height: 110px; resize: vertical; }
        .help { margin: 0; font-size: 12px; color: #5a708d; }
        .field-error { margin: 0; color: #b4233c; font-size: 12px; font-weight: 600; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn { min-height: 44px; border: 1px solid transparent; border-radius: 12px; padding: 10px 16px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; }
        .btn-primary { color: #fff; border: 0; background: linear-gradient(135deg, var(--brand), #4c83f0); box-shadow: 0 10px 20px rgba(31, 95, 224, 0.24); }
        .btn-primary:disabled { opacity: .7; cursor: not-allowed; }
        .btn-soft { color: #1d3f6d; border-color: #cedaeb; background: #f8fbff; }
        .alert { border: 1px solid #efd4d9; background: #fff7f8; color: #a3213a; border-radius: 12px; padding: 10px 12px; font-size: 13px; line-height: 1.5; }
        .image-preview-wrap { display: none; border: 1px dashed #c9d9ef; border-radius: 12px; padding: 10px; background: #f8fbff; }
        .image-preview-wrap.is-visible { display: block; }
        .image-preview { width: 100%; max-width: min(100%, 640px); max-height: 360px; object-fit: contain; border-radius: 10px; background: #fff; display: block; margin-inline: auto; }
    </style>
@endpush

@section('content')
    @php
        $selectedCargoIds = collect(old('cargo_ids', []))->map(fn ($id) => (int) $id)->all();
        $hasCargos = $cargos->isNotEmpty();
    @endphp

    <section class="panel-card" aria-labelledby="titulo-form-questao">
        <div>
            <h2 id="titulo-form-questao" class="panel-title">Cadastrar nova questao</h2>
            <p class="panel-subtitle">Informe o enunciado, alternativas e vinculos para filtros de banca, materia e cargo.</p>
        </div>

        @if ($errors->any())
            <div class="alert" role="alert">Revise os campos do formulario antes de continuar.</div>
        @endif

        <form id="questaoForm" class="form-grid" method="POST" action="{{ route('adm.questoes.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid-2">
                <div class="field">
                    <label class="label" for="banca_id">Banca</label>
                    <select id="banca_id" name="banca_id" class="select" required>
                        <option value="">Selecione</option>
                        @foreach ($bancas as $banca)
                            <option value="{{ $banca->id }}" @selected((int) old('banca_id') === $banca->id)>{{ $banca->name }}</option>
                        @endforeach
                    </select>
                    @error('banca_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div class="field">
                    <label class="label" for="materia_id">Materia</label>
                    <select id="materia_id" name="materia_id" class="select" required>
                        <option value="">Selecione</option>
                        @foreach ($materias as $materia)
                            <option value="{{ $materia->id }}" @selected((int) old('materia_id') === $materia->id)>{{ $materia->name }}</option>
                        @endforeach
                    </select>
                    @error('materia_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div class="field">
                    <label class="label" for="instituicao_id">Instituicao</label>
                    <select id="instituicao_id" name="instituicao_id" class="select" required>
                        <option value="">Selecione</option>
                        @foreach ($instituicoes as $instituicao)
                            <option value="{{ $instituicao->id }}" @selected((int) old('instituicao_id') === $instituicao->id)>{{ $instituicao->name }}</option>
                        @endforeach
                    </select>
                    @error('instituicao_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="field">
                <label class="label" for="cargo_ids">Cargos cadastrados ({{ $cargos->count() }})</label>
                <select id="cargo_ids" name="cargo_ids[]" class="select" multiple size="6" @if($hasCargos) required @else disabled @endif>
                    @forelse ($cargos as $cargo)
                        <option value="{{ $cargo->id }}" @selected(in_array($cargo->id, $selectedCargoIds, true))>{{ $cargo->name }}</option>
                    @empty
                        <option value="">Nenhum cargo cadastrado</option>
                    @endforelse
                </select>
                @if ($hasCargos)
                    <p class="help">Segure CTRL (ou CMD) para selecionar mais de um cargo.</p>
                @else
                    <p class="help">Cadastre ao menos um cargo em <a href="{{ route('adm.cargos.create') }}">/adm/cargos/adicionar</a> para liberar o cadastro da questao.</p>
                @endif
                @error('cargo_ids')<p class="field-error">{{ $message }}</p>@enderror
                @error('cargo_ids.*')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="field">
                <label class="label" for="imagem">Imagem da questao (opcional)</label>
                <input id="imagem" name="imagem" class="input" type="file" accept=".jpg,.jpeg,.png,.webp,.gif,image/*">
                <p class="help">A imagem sera salva em armazenamento publico. Formatos: JPG, JPEG, PNG, WEBP ou GIF (maximo 5MB).</p>
                <div id="imagePreviewWrap" class="image-preview-wrap" aria-live="polite">
                    <img id="imagePreview" class="image-preview" alt="Preview da imagem selecionada">
                </div>
                @error('imagem')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="field">
                <label class="label" for="enunciado">Enunciado</label>
                <textarea id="enunciado" name="enunciado" class="textarea" required>{{ old('enunciado') }}</textarea>
                @error('enunciado')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="grid-2">
                <div class="field">
                    <label class="label" for="alternativa_a">Alternativa A</label>
                    <textarea id="alternativa_a" name="alternativa_a" class="textarea" required>{{ old('alternativa_a') }}</textarea>
                    @error('alternativa_a')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label class="label" for="alternativa_b">Alternativa B</label>
                    <textarea id="alternativa_b" name="alternativa_b" class="textarea" required>{{ old('alternativa_b') }}</textarea>
                    @error('alternativa_b')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label class="label" for="alternativa_c">Alternativa C</label>
                    <textarea id="alternativa_c" name="alternativa_c" class="textarea" required>{{ old('alternativa_c') }}</textarea>
                    @error('alternativa_c')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label class="label" for="alternativa_d">Alternativa D</label>
                    <textarea id="alternativa_d" name="alternativa_d" class="textarea" required>{{ old('alternativa_d') }}</textarea>
                    @error('alternativa_d')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label class="label" for="alternativa_e">Alternativa E (opcional)</label>
                    <textarea id="alternativa_e" name="alternativa_e" class="textarea">{{ old('alternativa_e') }}</textarea>
                    @error('alternativa_e')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label class="label" for="gabarito">Gabarito</label>
                    <select id="gabarito" name="gabarito" class="select" required>
                        <option value="">Selecione</option>
                        @foreach (['A', 'B', 'C', 'D', 'E'] as $alternativa)
                            <option value="{{ $alternativa }}" @selected(old('gabarito') === $alternativa)>{{ $alternativa }}</option>
                        @endforeach
                    </select>
                    @error('gabarito')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="field">
                <label class="label" for="explicacao">Explicacao (opcional)</label>
                <textarea id="explicacao" name="explicacao" class="textarea">{{ old('explicacao') }}</textarea>
                @error('explicacao')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="actions">
                <button id="submitButton" class="btn btn-primary" type="submit" @disabled(!$hasCargos)>Cadastrar questao</button>
                <a class="btn btn-soft" href="{{ route('adm.questoes.index') }}">Cancelar</a>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var form = document.getElementById('questaoForm');
            var submitButton = document.getElementById('submitButton');
            var imageInput = document.getElementById('imagem');
            var imagePreviewWrap = document.getElementById('imagePreviewWrap');
            var imagePreview = document.getElementById('imagePreview');

            if (!form || !submitButton) return;

            if (imageInput && imagePreviewWrap && imagePreview) {
                imageInput.addEventListener('change', function () {
                    var file = imageInput.files && imageInput.files[0];

                    if (!file) {
                        imagePreview.removeAttribute('src');
                        imagePreviewWrap.classList.remove('is-visible');
                        return;
                    }

                    if (!file.type || file.type.indexOf('image/') !== 0) {
                        imagePreview.removeAttribute('src');
                        imagePreviewWrap.classList.remove('is-visible');
                        return;
                    }

                    var reader = new FileReader();
                    reader.onload = function (event) {
                        imagePreview.src = String(event.target && event.target.result ? event.target.result : '');
                        imagePreviewWrap.classList.add('is-visible');
                    };
                    reader.readAsDataURL(file);
                });
            }

            form.addEventListener('submit', function () {
                submitButton.disabled = true;
                submitButton.textContent = 'Salvando...';
            });
        })();
    </script>
@endpush
