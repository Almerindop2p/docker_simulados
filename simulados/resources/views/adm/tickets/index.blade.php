@extends('layouts.admin-panel')

@section('title', 'Tickets | Painel ADM')
@section('breadcrumb', 'Painel / Tickets / Lista')
@section('page_title', 'Tickets de Feedback')

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

        .stats-chip {
            border: 1px solid #f1d7ad;
            background: #fffaf1;
            color: #885f14;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 700;
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
            min-width: 760px;
        }

        th, td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #e8eef7;
            font-size: 14px;
            vertical-align: top;
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

        .status-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 9px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #d8e4f6;
            background: #f7faff;
            color: #32557b;
        }

        .status-pill.aberto {
            border-color: #f1d7ad;
            background: #fffaf1;
            color: #845b12;
        }

        .status-pill.fechado {
            border-color: #cbe4d4;
            background: #f1fbf5;
            color: #1d5e39;
        }

        .status-pill.pendente {
            border-color: #f4dfb4;
            background: #fffaf1;
            color: #845b12;
        }

        .status-pill.processando {
            border-color: #bfd8f7;
            background: #f1f7ff;
            color: #1e4f8f;
        }

        .status-pill.concluido {
            border-color: #cbe4d4;
            background: #f1fbf5;
            color: #1d5e39;
        }

        .message-preview {
            color: #2a476d;
            line-height: 1.45;
        }

        .view-link {
            display: inline-flex;
            min-height: 34px;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 9px;
            border: 1px solid #c8d9f1;
            background: #f5f9ff;
            color: #1f3f68;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
        }

        .pagination-wrap {
            overflow-x: auto;
            padding-bottom: 4px;
        }

        .pagination-wrap nav {
            min-width: max-content;
        }
    </style>
@endpush

@section('content')
    <section class="panel-card">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Tickets enviados pelos usuarios</h2>
                <p class="panel-subtitle">Acompanhe mensagens do chat de feedback e abra cada ticket para ver todos os detalhes.</p>
            </div>
            <div class="stats-chip">{{ $abertos }} ticket(s) em aberto</div>
        </div>

        @if (session('status'))
            <div class="alert-ok" role="status">{{ session('status') }}</div>
        @endif

        @if ($tickets->isEmpty())
            <p class="panel-subtitle">Nenhum ticket registrado ate o momento.</p>
        @else
            <div class="table-wrap">
                <table aria-label="Lista de tickets">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mensagem</th>
                            <th>Contato</th>
                            <th>Status</th>
                            <th>Origem</th>
                            <th>Enviado em</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $ticket)
                            @php
                                $statusClass = match ($ticket->status) {
                                    'pendente' => 'pendente',
                                    'processando' => 'processando',
                                    'concluido', 'fechado' => 'concluido',
                                    default => 'aberto',
                                };
                            @endphp
                            <tr>
                                <td>{{ $ticket->id }}</td>
                                <td class="message-preview">{{ \Illuminate\Support\Str::limit($ticket->mensagem, 120) }}</td>
                                <td>
                                    <strong>{{ $ticket->nome ?: ($ticket->user?->name ?: 'Nao informado') }}</strong><br>
                                    <small>{{ $ticket->email ?: ($ticket->user?->email ?: 'Sem e-mail') }}</small>
                                </td>
                                <td>
                                    <span class="status-pill {{ $statusClass }}">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </td>
                                <td>{{ $ticket->origem_rota ?: 'nao identificado' }}</td>
                                <td>{{ $ticket->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a class="view-link" href="{{ route('adm.tickets.show', $ticket) }}">Visualizar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                {{ $tickets->links() }}
            </div>
        @endif
    </section>
@endsection
