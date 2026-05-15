<?php

namespace App\Notifications\Channels;

use App\Services\NotificationService;
use Illuminate\Notifications\Notification;

class SystemChannel
{
    public function __construct(protected NotificationService $notificationService)
    {
    }

    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSystem')) {
            return;
        }

        $data = $notification->toSystem($notifiable);

        $targetType = method_exists($notifiable, 'getMorphClass') ? $notifiable->getMorphClass() : null;
        
        // Map class names to 'customer' or 'admin' as per current project convention
        if ($targetType === 'App\Models\Customer') {
            $targetType = 'customer';
        } elseif ($targetType === 'App\Models\User') {
            $targetType = 'admin';
        }

        $this->notificationService->createNotification([
            'target_type' => $targetType,
            'target_id' => $notifiable->id,
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type'] ?? 'system',
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
        ]);
    }
}
