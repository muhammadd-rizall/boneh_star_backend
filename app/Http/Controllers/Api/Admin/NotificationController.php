<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the admin's notifications.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Notification::class);

        $notifications = $this->notificationService->getMyNotifications('admin', auth()->id());
        $unreadCount = $this->notificationService->getMyUnreadCount('admin', auth()->id());

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'data' => NotificationResource::collection($notifications)->response()->getData(true),
            'message' => 'Notifications retrieved successfully',
        ], 200);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(string $id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $this->authorize('view', $notification);

        $updated = $this->notificationService->markAsRead($id, 'admin', auth()->id());

        return $this->sendResponse(
            new NotificationResource($updated),
            'Notification marked as read'
        );
    }

    /**
     * Mark all admin's notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $this->authorize('viewAny', Notification::class);

        $this->notificationService->markAllAsRead('admin', auth()->id());

        return $this->sendResponse(null, 'All notifications marked as read');
    }

    /**
     * Remove the specified notification.
     */
    public function destroy(string $id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $this->authorize('delete', $notification);

        $this->notificationService->deleteNotification($id, 'admin', auth()->id());

        return $this->sendResponse(null, 'Notification deleted successfully');
    }
}
