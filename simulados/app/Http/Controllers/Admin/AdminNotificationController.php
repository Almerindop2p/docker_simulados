<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function read(Request $request, AdminNotification $notification): JsonResponse
    {
        $this->ensureCanManageNotification($request, $notification);

        $notification->markAsRead();

        $unreadCount = AdminNotification::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'ok' => true,
            'unread_count' => $unreadCount,
            'read_at' => $notification->read_at?->toIso8601String(),
        ]);
    }

    public function open(Request $request, AdminNotification $notification): RedirectResponse
    {
        $this->ensureCanManageNotification($request, $notification);

        $notification->markAsRead();

        if ($notification->category === AdminNotification::CATEGORY_TICKET) {
            $ticketId = (int) data_get($notification->data, 'ticket_id', 0);

            if ($ticketId > 0) {
                return redirect()->route('adm.tickets.show', $ticketId);
            }

            return redirect()
                ->route('adm.tickets.index')
                ->with('status', 'O ticket relacionado a esta notificacao nao foi encontrado.');
        }

        return redirect()
            ->route('adm.bancas.index')
            ->with('status', 'Notificacao visualizada.');
    }

    private function ensureCanManageNotification(Request $request, AdminNotification $notification): void
    {
        abort_unless($request->user()?->user_type === User::TYPE_ADM, 403);
        abort_unless((int) $notification->user_id === (int) $request->user()->id, 404);
    }
}

