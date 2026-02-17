@extends('layouts.admin-panel')

@section('title', 'Lista de Instituicoes | Painel ADM')
@section('breadcrumb', 'Painel / Instituicao / Lista')
@section('page_title', 'Lista de Instituicoes')

@push('styles')
    <style>
        .panel-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            padding: 18px;
            display: grid;
            gap: 14px;
        }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
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

        .btn {
            min-height: 44px;
            border-radius: 12px;
            border: 0;
            padding: 10px 16px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), #4c83f0);
            box-shadow: 0 10px 20px rgba(31, 95, 224, 0.24);
        }

        .alert-ok {
            border: 1px solid #cfead9;
            background: #f2fbf5;
            color: #14643d;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 600;
        }

        .table-wrap {
            border: 1px solid #d9e3f2;
            border-radius: 12px;
            overflow: auto;
            background: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 620px;
        }

        th, td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #e8eef7;
            font-size: 14px;
        }

        th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #5f728c;
            font-weight: 700;
            background: #f8fbff;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .actions-col {
            width: 120px;
        }

        .row-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .icon-btn {
            width: 34px;
            height: 34px;
            border: 1px solid #d2dfef;
            border-radius: 10px;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #325680;
            transition: background-color .2s ease, border-color .2s ease, color .2s ease;
            text-decoration: none;
        }

        .icon-btn:hover {
            background: #f4f8ff;
            border-color: #bfd2ee;
            color: #1f436f;
        }

        .icon-btn-delete {
            color: #a2273c;
            border-color: #e7ccd2;
        }

        .icon-btn-delete:hover {
            background: #fff3f5;
            border-color: #ddb5bf;
            color: #8d1f34;
        }

        .row-actions form {
            margin: 0;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .empty {
            border: 1px dashed #c9d9ef;
            background: #f8fbff;
            border-radius: 12px;
            padding: 14px;
            color: #34567e;
        }
    </style>
@endpush

@section('content')
    <section class="panel-card" aria-labelledby="titulo-lista-instituicoes">
        <div class="panel-head">
            <div>
                <h2 id="titulo-lista-instituicoes" class="panel-title">Instituicoes cadastradas</h2>
                <p class="panel-subtitle">Gerencie as instituicoes que poderao ser usadas nas questoes e simulados.</p>
            </div>
            <a class="btn" href="{{ route('adm.instituicoes.create') }}">Adicionar Instituicao</a>
        </div>

        @if (session('status'))
            <div class="alert-ok" role="status">{{ session('status') }}</div>
        @endif

        @if ($instituicoes->isEmpty())
            <div class="empty">Nenhuma instituicao cadastrada ate o momento.</div>
        @else
            <div class="table-wrap">
                <table aria-label="Lista de instituicoes">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome da instituicao</th>
                            <th>Slug</th>
                            <th>Criada em</th>
                            <th class="actions-col">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($instituicoes as $instituicao)
                            <tr>
                                <td>{{ $instituicao->id }}</td>
                                <td>{{ $instituicao->name }}</td>
                                <td>{{ $instituicao->slug }}</td>
                                <td>{{ $instituicao->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="row-actions">
                                        <a class="icon-btn" href="{{ route('adm.instituicoes.edit', $instituicao) }}" title="Editar instituicao {{ $instituicao->name }}">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m4 20 4.2-1 9.7-9.7a1.8 1.8 0 0 0 0-2.6l-.7-.7a1.8 1.8 0 0 0-2.6 0L4.9 15.7 4 20Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m12.8 8.1 3.1 3.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                            <span class="sr-only">Editar</span>
                                        </a>
                                        <form method="POST" action="{{ route('adm.instituicoes.destroy', $instituicao) }}" onsubmit="return confirm('Deseja realmente excluir a instituicao {{ addslashes($instituicao->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn icon-btn-delete" title="Excluir instituicao {{ $instituicao->name }}">
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
