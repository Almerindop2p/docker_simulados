@extends('layouts.admin-panel')

@section('title', 'Lista de Questoes | Painel ADM')
@section('breadcrumb', 'Painel / Questoes / Lista')
@section('page_title', 'Lista de Questoes')

@push('styles')
    <style>
        .panel-card { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-lg); box-shadow: var(--shadow-card); padding: 18px; display: grid; gap: 14px; }
        .panel-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
        .panel-title { margin: 0; font-size: clamp(1.15rem, 2vw, 1.4rem); letter-spacing: -0.01em; }
        .panel-subtitle { margin: 0; color: var(--text-soft); line-height: 1.6; }
        .btn { min-height: 44px; border-radius: 12px; border: 0; padding: 10px 16px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; color: #fff; background: linear-gradient(135deg, var(--brand), #4c83f0); box-shadow: 0 10px 20px rgba(31, 95, 224, 0.24); }
        .btn-soft { min-height: 44px; border-radius: 12px; border: 1px solid #cedaeb; padding: 10px 14px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; color: #1d3f6d; background: #f8fbff; cursor: pointer; }
        .alert-ok { border: 1px solid #cfead9; background: #f2fbf5; color: #14643d; border-radius: 12px; padding: 10px 12px; font-size: 13px; font-weight: 600; }
        .filters { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px; align-items: end; }
        .field { display: grid; gap: 6px; }
        .label { margin: 0; font-size: 12px; font-weight: 700; color: #526a88; text-transform: uppercase; letter-spacing: .08em; }
        .input, .select { width: 100%; min-height: 44px; border-radius: 12px; border: 1px solid #cedaeb; background: #fff; padding: 10px 12px; font-size: 14px; color: #1d3352; }
        .table-wrap { border: 1px solid #d9e3f2; border-radius: 12px; overflow: auto; background: #fff; }
        table { width: 100%; border-collapse: collapse; min-width: 980px; }
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid #e8eef7; font-size: 14px; vertical-align: top; }
        th { font-size: 12px; text-transform: uppercase; letter-spacing: .08em; color: #5f728c; font-weight: 700; background: #f8fbff; }
        tr:last-child td { border-bottom: 0; }
        .enunciado { max-width: 360px; line-height: 1.5; color: #223d60; }
        .tag-list { display: flex; flex-wrap: wrap; gap: 6px; }
        .tag { border: 1px solid #d8e3f2; background: #f7faff; color: #325680; border-radius: 999px; padding: 4px 8px; font-size: 12px; font-weight: 600; }
        .actions-col { width: 120px; }
        .row-actions { display: inline-flex; align-items: center; gap: 8px; }
        .icon-btn { width: 34px; height: 34px; border: 1px solid #d2dfef; border-radius: 10px; background: #fff; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: #325680; text-decoration: none; }
        .icon-btn-delete { color: #a2273c; border-color: #e7ccd2; }
        .row-actions form { margin: 0; }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
        .empty { border: 1px dashed #c9d9ef; background: #f8fbff; border-radius: 12px; padding: 14px; color: #34567e; }
    </style>
@endpush

@section('content')
    <section class="panel-card" aria-labelledby="titulo-lista-questoes">
        <div class="panel-head">
            <div>
                <h2 id="titulo-lista-questoes" class="panel-title">Questoes cadastradas</h2>
                <p class="panel-subtitle">Filtre por banca, materia, instituicao e cargo para facilitar a gestao.</p>
            </div>
            <a class="btn" href="{{ route('adm.questoes.create') }}">Adicionar Questao</a>
        </div>

        @if (session('status'))
            <div class="alert-ok" role="status">{{ session('status') }}</div>
        @endif

        <form class="filters" method="GET" action="{{ route('adm.questoes.index') }}">
            <div class="field">
                <label class="label" for="banca_id">Banca</label>
                <select id="banca_id" name="banca_id" class="select">
                    <option value="">Todas</option>
                    @foreach ($bancas as $banca)
                        <option value="{{ $banca->id }}" @selected((int) ($filtros['banca_id'] ?? 0) === $banca->id)>{{ $banca->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label class="label" for="materia_id">Materia</label>
                <select id="materia_id" name="materia_id" class="select">
                    <option value="">Todas</option>
                    @foreach ($materias as $materia)
                        <option value="{{ $materia->id }}" @selected((int) ($filtros['materia_id'] ?? 0) === $materia->id)>{{ $materia->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label class="label" for="instituicao_id">Instituicao</label>
                <select id="instituicao_id" name="instituicao_id" class="select">
                    <option value="">Todas</option>
                    @foreach ($instituicoes as $instituicao)
                        <option value="{{ $instituicao->id }}" @selected((int) ($filtros['instituicao_id'] ?? 0) === $instituicao->id)>{{ $instituicao->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label class="label" for="cargo_id">Cargo</label>
                <select id="cargo_id" name="cargo_id" class="select">
                    <option value="">Todos</option>
                    @foreach ($cargos as $cargo)
                        <option value="{{ $cargo->id }}" @selected((int) ($filtros['cargo_id'] ?? 0) === $cargo->id)>{{ $cargo->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <button type="submit" class="btn-soft">Filtrar</button>
                <a href="{{ route('adm.questoes.index') }}" class="btn-soft">Limpar</a>
            </div>
        </form>

        @if ($questoes->isEmpty())
            <div class="empty">Nenhuma questao encontrada para os filtros selecionados.</div>
        @else
            <div class="table-wrap">
                <table aria-label="Lista de questoes">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Enunciado</th>
                            <th>Banca</th>
                            <th>Materia</th>
                            <th>Instituicao</th>
                            <th>Cargos</th>
                            <th>Gabarito</th>
                            <th class="actions-col">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questoes as $questao)
                            <tr>
                                <td>{{ $questao->id }}</td>
                                <td class="enunciado">{{ \Illuminate\Support\Str::limit($questao->enunciado, 160) }}</td>
                                <td>{{ $questao->banca?->name }}</td>
                                <td>{{ $questao->materia?->name }}</td>
                                <td>{{ $questao->instituicao?->name ?? '-' }}</td>
                                <td>
                                    <div class="tag-list">
                                        @forelse ($questao->cargos as $cargo)
                                            <span class="tag">{{ $cargo->name }}</span>
                                        @empty
                                            <span class="tag">Sem cargo</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>{{ $questao->gabarito }}</td>
                                <td>
                                    <div class="row-actions">
                                        <a class="icon-btn" href="{{ route('adm.questoes.edit', $questao) }}" title="Editar questao {{ $questao->id }}">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m4 20 4.2-1 9.7-9.7a1.8 1.8 0 0 0 0-2.6l-.7-.7a1.8 1.8 0 0 0-2.6 0L4.9 15.7 4 20Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m12.8 8.1 3.1 3.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                            <span class="sr-only">Editar</span>
                                        </a>
                                        <form method="POST" action="{{ route('adm.questoes.destroy', $questao) }}" onsubmit="return confirm('Deseja realmente excluir a questao {{ $questao->id }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn icon-btn-delete" title="Excluir questao {{ $questao->id }}">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 7h14M9 7V5.6c0-.9.7-1.6 1.6-1.6h2.8c.9 0 1.6.7 1.6 1.6V7m-8.3 0 1 11.1c.1 1 .9 1.9 2 1.9h4.6c1 0 1.9-.8 2-1.9L17.3 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                <span class="sr-only">Excluir</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
