<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends BaseController
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Booking::class);

        $data = $this->dashboardService->getDashboardData();

        return $this->sendResponse($data, 'Dashboard data retrieved successfully');
    }
}
