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
        if (! auth()->check() || ! auth()->user()->is_active) {
            return $this->sendError('Unauthorized access.', [], 403);
        }

        $data = $this->dashboardService->getDashboardData();

        return $this->sendResponse($data, 'Dashboard data retrieved successfully');
    }
}
