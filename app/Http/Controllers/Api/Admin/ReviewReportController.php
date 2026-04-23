<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\ReviewReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewReportController extends BaseController
{
    protected ReviewReportService $reportService;

    public function __construct(ReviewReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Generate review report.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Review::class);

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'rating' => 'nullable|integer|min:1|max:5',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $report = $this->reportService->generateReport(
            $request->start_date,
            $request->end_date,
            $request->rating,
            $request->per_page ?? 15
        );

        return response()->json([
            'success' => true,
            'message' => 'Review report generated successfully',
            'data' => [
                'period' => $report['period'],
                'filters' => $report['filters'],
                'statistics' => $report['statistics'],
                'monthly_averages' => $report['monthly_averages'],
                'activity_type_averages' => $report['activity_type_averages'],
                'bus_averages' => $report['bus_averages'],
                'reviews' => ReviewResource::collection($report['reviews'])->response()->getData(true),
            ],
        ], 200);
    }

    /**
     * Toggle the publish status of the specified resource.
     */
    public function togglePublish(string $id)
    {
        $this->authorize('publish', Review::class);

        $review = $this->reportService->togglePublish($id);

        return response()->json([
            'success' => true,
            'data' => new ReviewResource($review->load(['booking', 'customer'])),
            'message' => 'Review publish status toggled successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', Review::class);

        $this->reportService->deleteReview($id);

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
        ], 200);
    }
}
