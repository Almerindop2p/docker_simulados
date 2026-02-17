<?php

namespace App\Http\Controllers;

use App\Models\UserNotificationRead;
use App\Support\HeaderNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class NotificationReadController extends Controller
{
    public function read(Request $request, string $notificationKey): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'Usuario nao autenticado.',
            ], 401);
        }

        $normalizedKey = $this->normalizeNotificationKey($notificationKey);

        if ($normalizedKey === '') {
            return response()->json([
                'ok' => false,
                'message' => 'Notificacao invalida.',
            ], 422);
        }

        if ($this->readsTableExists()) {
            UserNotificationRead::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notification_key' => $normalizedKey,
                ],
                [
                    'read_at' => now(),
                ]
            );
        }

        $unreadCount = (int) data_get(HeaderNotifications::buildFor($user), 'count', 0);

        return response()->json([
            'ok' => true,
            'notification_key' => $normalizedKey,
            'unread_count' => $unreadCount,
        ]);
    }

    private function normalizeNotificationKey(string $notificationKey): string
    {
        $normalized = strtolower($notificationKey);
        $normalized = preg_replace('/[^a-z0-9\-]/', '', $normalized) ?? '';
        $normalized = trim($normalized, '-');

        return substr($normalized, 0, 180);
    }

    private function readsTableExists(): bool
    {
        try {
            return Schema::hasTable('user_notification_reads');
        } catch (\Throwable) {
            return false;
        }
    }
}

