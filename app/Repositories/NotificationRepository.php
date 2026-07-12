<?php

namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository extends BaseRepository
{
    public function __construct(Notification $notification)
    {
        $this->model = $notification;
    }

    public function getPaginatedNotifications(string $targetType, string $targetId, int $perPage = 15)
    {
        return $this->model->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->latest()
            ->paginate($perPage);
    }

    public function getUnreadCount(string $targetType, string $targetId): int
    {
        return $this->model->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->where('is_read', false)
            ->count();
    }

    public function markAllAsRead(string $targetType, string $targetId): void
    {
        $this->model->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function findNotification(string $id, string $targetType, string $targetId)
    {
        return $this->model
            ->where('id', $id)
            ->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->firstOrFail();
    }
}
