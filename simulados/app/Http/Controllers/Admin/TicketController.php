<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\FeedbackTicket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('adm.tickets.index', [
            'tickets' => FeedbackTicket::query()
                ->with('user:id,name,email')
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'abertos' => FeedbackTicket::query()->where('status', FeedbackTicket::STATUS_ABERTO)->count(),
        ]);
    }

    public function show(Request $request, FeedbackTicket $ticket): View
    {
        $this->ensureAdmin($request);

        $ticket->load('user:id,name,email,user_type');
        $this->markTicketNotificationsAsRead($request, $ticket);

        return view('adm.tickets.show', [
            'ticket' => $ticket,
            'allowedStatuses' => FeedbackTicket::ALLOWED_STATUSES,
        ]);
    }

    public function update(Request $request, FeedbackTicket $ticket): RedirectResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate(
            [
                'status' => ['required', Rule::in(FeedbackTicket::ALLOWED_STATUSES)],
                'observacao_admin' => ['nullable', 'string', 'max:5000'],
            ],
            [
                'status.required' => 'Selecione um status para o ticket.',
                'status.in' => 'O status selecionado e invalido.',
                'observacao_admin.max' => 'A observacao deve ter no maximo :max caracteres.',
            ]
        );

        $ticket->update([
            'status' => $data['status'],
            'observacao_admin' => isset($data['observacao_admin'])
                ? trim((string) $data['observacao_admin'])
                : null,
        ]);

        return redirect()
            ->route('adm.tickets.show', $ticket)
            ->with('status', 'Ticket atualizado com sucesso.');
    }

    private function markTicketNotificationsAsRead(Request $request, FeedbackTicket $ticket): void
    {
        if (!Schema::hasTable('admin_notifications')) {
            return;
        }

        AdminNotification::query()
            ->where('user_id', $request->user()->id)
            ->where('category', AdminNotification::CATEGORY_TICKET)
            ->where('reference_key', 'ticket-' . $ticket->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->user_type === User::TYPE_ADM, 403);
    }
}
