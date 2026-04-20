<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingReportController extends BaseController
{
    protected BookingReportService $reportService;

    public function __construct(BookingReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Generate booking report.
     */
    public function index(Request $request): JsonResponse
    {
        // Use class string for authorization check
        $this->authorize('viewAny', Booking::class);

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|string',
            'scope' => 'nullable|string|in:internal,external',
        ]);

        $report = $this->reportService->generateReport(
            $request->start_date,
            $request->end_date,
            $request->status,
            $request->scope
        );

        return response()->json([
            'success' => true,
            'period' => $report['period'],
            'filters' => $report['filters'],
            'statistics' => $report['statistics'],
            'data' => BookingResource::collection($report['items']),
            'message' => 'Booking report generated successfully',
        ], 200);
    }
}
