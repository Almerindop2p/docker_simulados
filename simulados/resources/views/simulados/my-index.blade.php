@extends('layouts.admin-panel')

@section('title', 'Meus Simulados | Area do Aluno')
@section('breadcrumb', 'Inicio / Meus Simulados')
@section('page_title', 'Meus Simulados')

@push('styles')
    <style>
        .grid { display: grid; gap: 14px; }
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 16px;
            display: grid;
            gap: 12px;
        }
        .title { margin: 0; font-size: 1.12rem; letter-spacing: -0.01em; }
        .muted { margin: 0; color: var(--text-soft); font-size: 14px; line-height: 1.6; }
        .table-wrap {
            border: 1px solid #d8e3f2;
            border-radius: 12px;
            background: #fff;
            overflow-x: auto;
        }
        table { width: 100%; border-collapse: collapse; min-width: 700px; }
        th, td {
            padding: 11px 12px;
            border-bottom: 1px solid #e7eef8;
            text-align: left;
            font-size: 14px;
            vertical-align: middle;
        }
        th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #60738d;
            background: #f7faff;
        }
        tr:last-child td { border-bottom: 0; }
        .status {
            display: inline-flex;
            align-items: center;
            border: 1px solid #d4e0f1;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 700;
            color: #2a4a73;
            background: #fff;
            white-space: nowrap;
        }
        .status.aberto {
            border-color: #ebddb5;
            background: #fff9eb;
            color: #875e13;
        }
        .status.concluido {
            border-color: #c9ddf9;
            background: #eef4ff;
            color: #1f4b86;
        }
        .btn-soft {
            min-height: 38px;
            border-radius: 10px;
            border: 1px solid #cddbef;
            background: #fff;
            color: #23496f;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            cursor: pointer;
            white-space: nowrap;
        }
        .btn-soft[aria-disabled="true"] {
            opacity: .65;
            pointer-events: none;
            cursor: not-allowed;
        }
        .empty {
            margin: 0;
            border: 1px dashed #ccd9ee;
            background: #f9fbff;
            color: #446282;
            border-radius: 10px;
            padding: 12px;
            font-size: 14px;
        }
        .pagination-wrap {
            display: flex;
            justify-content: flex-end;
        }
    </style>
@endpush

@section('content')
    <section class="grid">
        <article class="card">
            <h2 class="title">Lista de simulados em andamento e finalizados</h2>
            <p class="muted">Acompanhe seu historico de tentativas e abra o resultado correspondente de cada simulado.</p>

            @if ($tentativas->isEmpty())
                <p class="empty">Voce ainda nao iniciou nenhum simulado.</p>
            @else
                <div class="table-wrap">
                    <table aria-label="Lista de meus simulados">
                        <thead>
                            <tr>
                                <th>ID Tentativa</th>
                                <th>ID Simulado</th>
                                <th>Simulado</th>
                                <th>Status</th>
                                <th>Atualizado em</th>
                                <th>Acao</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tentativas as $tentativa)
                                @php
                                    $simulado = $tentativa->simulado;
                                    $status = strtolower((string) $tentativa->status);
                                    $statusLabel = $status === \App\Models\SimuladoTentativa::STATUS_ABERTO ? 'Em andamento' : 'Finalizado';
                                @endphp
                                <tr>
                                    <td>{{ $tentativa->id }}</td>
                                    <td>{{ $simulado?->id ?? '-' }}</td>
                                    <td>{{ $simulado?->name ?? 'Simulado removido' }}</td>
                                    <td>
                                        <span class="status {{ $status }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td>{{ optional($tentativa->updated_at)->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>
                                        @if ($simulado)
                                            <a
                                                class="btn-soft"
                                                href="{{ route('simulados.result', ['simulado' => $simulado, 'attempt' => $tentativa->id]) }}"
                                            >
                                                Ver resultado
                                            </a>
                                        @else
                                            <span class="btn-soft" aria-disabled="true">Indisponivel</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($tentativas->hasPages())
                    <div class="pagination-wrap">
                        {{ $tentativas->onEachSide(1)->links() }}
                    </div>
                @endif
            @endif
        </article>
    </section>
@endsection
