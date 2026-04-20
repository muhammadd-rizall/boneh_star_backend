<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\IncomeReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncomeReportController extends BaseController
{
    protected IncomeReportService $reportService;

    public function __construct(IncomeReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Generate income report.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Payment::class);

        $request->validate([
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2020',
        ]);

        $report = $this->reportService->generateReport(
            $request->month,
            $request->year
        );

        return response()->json([
            'success' => true,
            'period' => $report['period'],
            'statistics' => $report['statistics'],
            'data' => PaymentResource::collection($report['items']),
            'message' => 'Income report generated successfully',
        ], 200);
    }
}
