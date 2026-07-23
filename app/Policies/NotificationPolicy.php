<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

class NotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->roles()->where('is_active', true)->exists();
    }

    public function view(User $user, Notification $notification): bool
    {
        // Admin can see admin target notifications
        return $notification->target_type === 'admin' && $notification->target_id === $user->id;
    }

    public function delete(User $user, Notification $notification): bool
    {
        return $notification->target_id === $user->id;
    }
}
