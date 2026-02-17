@extends('layouts.admin-panel')

@section('title', 'Ticket #' . $ticket->id . ' | Painel ADM')
@section('breadcrumb', 'Painel / Tickets / Detalhes')
@section('page_title', 'Ticket #' . $ticket->id)

@push('styles')
    <style>
        .ticket-layout {
            display: grid;
            gap: 14px;
        }

        .ticket-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            padding: 18px;
            display: grid;
            gap: 14px;
        }

        .ticket-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ticket-title {
            margin: 0;
            font-size: clamp(1.1rem, 2vw, 1.34rem);
            letter-spacing: -0.01em;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 10px;
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

        .meta-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }

        .meta-item {
            border: 1px solid #d8e4f6;
            border-radius: 12px;
            padding: 10px 12px;
            background: #f9fbff;
            display: grid;
            gap: 4px;
        }

        .meta-item strong {
            color: #1f3f67;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .07em;
        }

        .meta-item span {
            color: #2d4f76;
            font-size: 14px;
            line-height: 1.45;
            word-break: break-word;
        }

        .message-box {
            border: 1px solid #d8e4f6;
            border-radius: 12px;
            background: #fff;
            padding: 14px;
            color: #1f3f67;
            line-height: 1.65;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .admin-form {
            border: 1px solid #d8e4f6;
            border-radius: 12px;
            background: #f9fbff;
            padding: 14px;
            display: grid;
            gap: 10px;
        }

        .form-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }

        .form-field {
            display: grid;
            gap: 6px;
        }

        .form-field label {
            color: #1f3f67;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .07em;
            font-weight: 700;
        }

        .form-field select,
        .form-field textarea {
            width: 100%;
            border: 1px solid #c8d9f1;
            border-radius: 10px;
            background: #fff;
            color: #1f3f68;
            font-family: inherit;
            font-size: 14px;
            padding: 10px 12px;
        }

        .form-field textarea {
            min-height: 130px;
            resize: vertical;
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

        .alert-error {
            border: 1px solid #f0cad2;
            background: #fff3f6;
            color: #94263c;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 600;
        }

        .btn {
            min-height: 42px;
            border-radius: 10px;
            border: 1px solid #c8d9f1;
            background: #f5f9ff;
            color: #1f3f68;
            font-weight: 700;
            text-decoration: none;
            padding: 10px 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), #4c83f0);
            box-shadow: 0 10px 20px rgba(31, 95, 224, 0.24);
        }

        .btn[disabled] {
            opacity: 0.75;
            cursor: not-allowed;
        }

        .btn.is-loading {
            position: relative;
            gap: 8px;
        }

        .btn.is-loading::before {
            content: '';
            width: 14px;
            height: 14px;
            border-radius: 999px;
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-top-color: #fff;
            animation: spin .7s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
@endpush

@section('content')
    @php
        $statusClass = match ($ticket->status) {
            'pendente' => 'pendente',
            'processando' => 'processando',
            'concluido', 'fechado' => 'concluido',
            default => 'aberto',
        };
    @endphp
    <section class="ticket-layout">
        <article class="ticket-card">
            <header class="ticket-head">
                <h2 class="ticket-title">Detalhes da mensagem enviada pelo usuario</h2>
                <span class="status-pill {{ $statusClass }}">
                    {{ ucfirst($ticket->status) }}
                </span>
            </header>

            @if (session('status'))
                <div class="alert-ok" role="status">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert-error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form id="ticketUpdateForm" class="admin-form" method="POST" action="{{ route('adm.tickets.update', $ticket) }}">
                @csrf
                @method('PATCH')

                <div class="form-grid">
                    <div class="form-field">
                        <label for="status">Status do ticket</label>
                        <select id="status" name="status" required>
                            @foreach ($allowedStatuses as $allowedStatus)
                                <option value="{{ $allowedStatus }}" @selected(old('status', $ticket->status) === $allowedStatus)>
                                    {{ ucfirst($allowedStatus) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-field">
                    <label for="observacao_admin">Observacao interna (ADM)</label>
                    <textarea
                        id="observacao_admin"
                        name="observacao_admin"
                        maxlength="5000"
                        placeholder="Informe um resumo do atendimento, encaminhamento, retorno ao usuario, etc."
                    >{{ old('observacao_admin', $ticket->observacao_admin) }}</textarea>
                </div>

                <div class="actions">
                    <button id="ticketUpdateSubmit" class="btn btn-primary" type="submit" data-loading-text="Enviando ticket...">Salvar atualizacao</button>
                </div>
            </form>

            <div class="meta-grid">
                <div class="meta-item">
                    <strong>Nome</strong>
                    <span>{{ $ticket->nome ?: ($ticket->user?->name ?: 'Nao informado') }}</span>
                </div>
                <div class="meta-item">
                    <strong>E-mail</strong>
                    <span>{{ $ticket->email ?: ($ticket->user?->email ?: 'Sem e-mail') }}</span>
                </div>
                <div class="meta-item">
                    <strong>Origem da rota</strong>
                    <span>{{ $ticket->origem_rota ?: 'nao identificado' }}</span>
                </div>
                <div class="meta-item">
                    <strong>Enviado em</strong>
                    <span>{{ $ticket->created_at?->format('d/m/Y H:i') }}</span>
                </div>
                <div class="meta-item">
                    <strong>IP</strong>
                    <span>{{ $ticket->ip_address ?: 'nao informado' }}</span>
                </div>
                <div class="meta-item">
                    <strong>Pagina</strong>
                    <span>
                        @if ($ticket->pagina_url)
                            <a href="{{ $ticket->pagina_url }}" target="_blank" rel="noopener noreferrer">{{ $ticket->pagina_url }}</a>
                        @else
                            nao informado
                        @endif
                    </span>
                </div>
            </div>

            <div>
                <h3 class="ticket-title">Mensagem</h3>
                <div class="message-box">{{ $ticket->mensagem }}</div>
            </div>

            <div class="actions">
                <a class="btn" href="{{ route('adm.tickets.index') }}">Voltar para lista</a>
                @if ($ticket->pagina_url)
                    <a class="btn btn-primary" href="{{ $ticket->pagina_url }}" target="_blank" rel="noopener noreferrer">Abrir pagina de origem</a>
                @endif
            </div>
        </article>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            var form = document.getElementById('ticketUpdateForm');
            var submitButton = document.getElementById('ticketUpdateSubmit');

            if (!form || !submitButton) {
                return;
            }

            form.addEventListener('submit', function () {
                if (submitButton.disabled) {
                    return;
                }

                submitButton.dataset.originalText = submitButton.textContent;
                submitButton.textContent = submitButton.dataset.loadingText || 'Enviando...';
                submitButton.disabled = true;
                submitButton.classList.add('is-loading');
            });
        })();
    </script>
@endpush
