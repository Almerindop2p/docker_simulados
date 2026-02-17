<?php

namespace App\Http\Controllers;

use App\Support\HeaderNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationFeedController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'ok' => false,
                'notifications' => [
                    'title' => 'Notificacoes',
                    'items' => [],
                    'count' => 0,
                ],
            ], 401);
        }

        return response()->json([
            'ok' => true,
            'notifications' => HeaderNotifications::buildFor($user),
            'server_time' => now()->toIso8601String(),
        ]);
    }
}

