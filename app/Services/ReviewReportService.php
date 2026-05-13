<?php

namespace App\Services;

use App\Repositories\ReviewRepository;

class ReviewReportService
{
    protected ReviewRepository $reviewRepository;

    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * Generate review report.
     */
    public function generateReport(?string $startDate = null, ?string $endDate = null, ?int $rating = null, int $perPage = 15): array
    {
        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'filters' => [
                'rating' => $rating,
            ],
            'statistics' => $this->reviewRepository->getStatistics(),
            'monthly_averages' => $this->reviewRepository->getMonthlyRatingAverage($startDate, $endDate),
            'activity_type_averages' => $this->reviewRepository->getActivityTypeRatingAverage(),
            'bus_averages' => $this->reviewRepository->getBusRatingAverage(),
            'reviews' => $this->reviewRepository->getLatestReviewsForReport($perPage, $rating, false),
        ];
    }

    public function togglePublish(string $id)
    {
        $review = $this->reviewRepository->find($id);

        return $this->reviewRepository->update($id, [
            'is_publish' => ! $review->is_publish,
        ]);
    }

    public function deleteReview(string $id)
    {
        return $this->reviewRepository->delete($id);
    }
}
