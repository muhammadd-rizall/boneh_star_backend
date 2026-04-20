<?php

namespace App\Services;

use App\Repositories\NotificationRepository;

class NotificationService
{
    protected NotificationRepository $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function getMyNotifications(string $targetType, string $targetId, int $perPage = 15)
    {
        return $this->notificationRepository->getPaginatedNotifications($targetType, $targetId, $perPage);
    }

    public function getMyUnreadCount(string $targetType, string $targetId): int
    {
        return $this->notificationRepository->getUnreadCount($targetType, $targetId);
    }

    public function markAsRead(string $id)
    {
        return $this->notificationRepository->update($id, [
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAllAsRead(string $targetType, string $targetId)
    {
        $this->notificationRepository->markAllAsRead($targetType, $targetId);
    }

    public function createNotification(array $data)
    {
        return $this->notificationRepository->create($data);
    }

    public function deleteNotification(string $id)
    {
        return $this->notificationRepository->delete($id);
    }
}
