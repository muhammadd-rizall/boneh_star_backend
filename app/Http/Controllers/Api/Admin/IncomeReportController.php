<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\IncomeExport;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\IncomeReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string|in:fully_paid,dp,unpaid',
        ]);

        $report = $this->reportService->generateReport(
            $request->month,
            $request->year,
            $request->start_date,
            $request->end_date,
            $request->status
        );

        return response()->json([
            'success' => true,
            'period' => $report['period'],
            'statistics' => $report['statistics'],
            'data' => PaymentResource::collection($report['items']),
            'message' => 'Income report generated successfully',
        ], 200);
    }

    /**
     * Export income report to Excel.
     */
    public function exportExcel(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $request->validate([
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2020',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string|in:fully_paid,dp,unpaid',
        ]);

        $report = $this->reportService->generateReport(
            $request->month,
            $request->year,
            $request->start_date,
            $request->end_date,
            $request->status
        );

        return Excel::download(new IncomeExport($report['items']), 'laporan-pendapatan-'.now()->format('Y-m-d').'.xlsx');
    }

    /**
     * Export income report to PDF.
     */
    public function exportPdf(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $request->validate([
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2020',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string|in:fully_paid,dp,unpaid',
        ]);

        $report = $this->reportService->generateReport(
            $request->month,
            $request->year,
            $request->start_date,
            $request->end_date,
            $request->status
        );

        $profile = \App\Models\CompanyProfile::first();

        $pdf = Pdf::loadView('reports.income-pdf', [
            'items'      => $report['items'],
            'month'      => $request->month,
            'year'       => $request->year ?? date('Y'),
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'profile'    => $profile,
        ]);

        return $pdf->download('laporan-pendapatan-'.now()->format('Y-m-d').'.pdf');
    }
}
