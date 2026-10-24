<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Notifications\Models\BroadcastNotification;
use App\Domain\Notifications\Models\NotificationLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get User Notifications Feed (Broadcasts + Direct Logs)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $type = $request->query('type', 'all');
        $unreadOnly = $request->boolean('unread_only', false);

        // Fetch published broadcasts
        $query = BroadcastNotification::where('status', 'sent')
            ->where(function ($q) {
                $q->whereNull('scheduled_at')
                  ->orWhere('scheduled_at', '<=', now());
            })
            ->latest('sent_at');

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $perPage = min(max((int) $request->query('per_page', 15), 1), 50);
        $broadcasts = $query->paginate($perPage);

        // Map read state for this user
        $userReadIds = [];
        if ($user) {
            $userReadIds = NotificationLog::where('user_id', $user->id)
                ->where('is_read', true)
                ->pluck('broadcast_id')
                ->all();
        }

        $data = collect($broadcasts->items())->map(function ($item) use ($userReadIds) {
            $isRead = in_array($item->id, $userReadIds);
            return [
                'id' => $item->id,
                'uuid' => $item->uuid,
                'title' => $item->title,
                'body' => $item->body,
                'type' => $item->type,
                'target_audience' => $item->target_audience,
                'action_url' => $item->action_url,
                'is_read' => $isRead,
                'created_at' => $item->sent_at ?? $item->created_at,
            ];
        });

        if ($unreadOnly) {
            $data = $data->filter(fn ($item) => ! $item['is_read'])->values();
        }

        $unreadCount = BroadcastNotification::where('status', 'sent')
            ->whereNotIn('id', $userReadIds)
            ->count();

        return response()->json([
            'status' => 'success',
            'unread_count' => $unreadCount,
            'data' => $data,
            'meta' => [
                'current_page' => $broadcasts->currentPage(),
                'last_page' => $broadcasts->lastPage(),
                'per_page' => $broadcasts->perPage(),
                'total' => $broadcasts->total(),
            ],
        ]);
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            NotificationLog::updateOrCreate([
                'broadcast_id' => $id,
                'user_id' => $user->id,
            ], [
                'channel' => 'in_app',
                'is_read' => true,
                'read_at' => now(),
            ]);

            BroadcastNotification::where('id', $id)->increment('read_count');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Notifikasi ditandai telah dibaca.',
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $broadcastIds = BroadcastNotification::where('status', 'sent')->pluck('id');
            foreach ($broadcastIds as $bId) {
                NotificationLog::updateOrCreate([
                    'broadcast_id' => $bId,
                    'user_id' => $user->id,
                ], [
                    'channel' => 'in_app',
                    'is_read' => true,
                    'read_at' => now(),
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Semua notifikasi ditandai telah dibaca.',
        ]);
    }
}
