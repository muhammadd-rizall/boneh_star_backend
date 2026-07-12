<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\NotificationRepository;
use Illuminate\Database\Eloquent\Model;

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

    public function markAsRead(string $id, string $targetType, string $targetId)
    {
        $notification = $this->notificationRepository->findNotification($id, $targetType, $targetId);

        return $this->notificationRepository->update($notification->id, [
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

    /**
     * Notify a specific customer.
     */
    public function notifyCustomer(string $customerId, string $title, string $message, string $type = 'system', ?Model $reference = null)
    {
        return $this->createNotification([
            'target_type' => 'customer',
            'target_id' => $customerId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
        ]);
    }

    /**
     * Notify all admin users.
     */
    public function notifyAdmin(string $title, string $message, string $type = 'system', ?Model $reference = null)
    {
        $admins = User::all(); // In a real app, you might filter by role

        foreach ($admins as $admin) {
            $this->createNotification([
                'target_type' => 'admin',
                'target_id' => $admin->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
            ]);
        }
    }

    public function deleteNotification(string $id, string $targetType, string $targetId)
    {
        $notification = $this->notificationRepository->findNotification($id, $targetType, $targetId);

        return $this->notificationRepository->delete($notification->id);
    }
}
