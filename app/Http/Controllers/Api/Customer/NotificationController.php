<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\NotificationResource;
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
     * Display a listing of the customer's notifications.
     */
    public function index(Request $request): JsonResponse
    {
        $customerId = auth('customer')->id();
        $notifications = $this->notificationService->getMyNotifications('customer', $customerId, $request->get('per_page', 15));
        $unreadCount = $this->notificationService->getMyUnreadCount('customer', $customerId);

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
        $customerId = auth('customer')->id();
        $notification = $this->notificationService->markAsRead($id, 'customer', $customerId);

        return $this->sendResponse(
            new NotificationResource($notification),
            'Notification marked as read'
        );
    }

    /**
     * Mark all customer's notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $customerId = auth('customer')->id();
        $this->notificationService->markAllAsRead('customer', $customerId);

        return $this->sendResponse(null, 'All notifications marked as read');
    }

    /**
     * Remove the specified notification.
     */
    public function destroy(string $id): JsonResponse
    {
        $customerId = auth('customer')->id();
        $this->notificationService->deleteNotification($id, 'customer', $customerId);

        return $this->sendResponse(null, 'Notification deleted successfully');
    }
}
