<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Buses;
use App\Services\BusAvailabilityReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusAvailabilityReportController extends BaseController
{
    protected BusAvailabilityReportService $reportService;

    public function __construct(BusAvailabilityReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Generate bus availability report.
     */
    public function index(Request $request): JsonResponse
    {
        // Use class string for authorization check
        $this->authorize('viewAny', Buses::class);

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportService->generateReport(
            $request->start_date,
            $request->end_date
        );

        return response()->json([
            'success' => true,
            'period' => $report['period'],
            'data' => $report['items'],
            'message' => 'Bus availability report generated successfully',
        ], 200);
    }
}
