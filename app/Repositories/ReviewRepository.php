<?php

namespace App\Repositories;

use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ReviewRepository extends BaseRepository
{
    public function __construct(Review $review)
    {
        $this->model = $review;
    }

    public function getPaginatedReviews(int $perPage = 10)
    {
        return $this->model->with(['booking', 'customer'])->latest()->paginate($perPage);
    }

    public function getTrashedReviews()
    {
        return $this->model->onlyTrashed()->with(['booking', 'customer'])->latest()->get();
    }

    public function getCustomerReviews(string $customerId, int $perPage = 10)
    {
        return $this->model->where('customer_id', $customerId)
            ->with(['booking.activity'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get statistics for reviews.
     */
    public function getStatistics(): array
    {
        $ratingDistribution = $this->model->where('is_publish', true)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->get()
            ->pluck('count', 'rating')
            ->toArray();

        // Ensure all ratings 1-5 are present
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $ratingDistribution[$i] ?? 0;
        }

        return [
            'total_count' => $this->model->count(),
            'published_count' => $this->model->where('is_publish', true)->count(),
            'unpublished_count' => $this->model->where('is_publish', false)->count(),
            'average_rating' => round((float) $this->model->where('is_publish', true)->avg('rating'), 2),
            'rating_distribution' => $distribution,
        ];
    }

    /**
     * Get monthly rating average.
     */
    public function getMonthlyRatingAverage(?string $startDate = null, ?string $endDate = null): array
    {
        $query = $this->model->where('is_publish', true)
            ->select(
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('count(*) as count'),
                DB::raw('ROUND(AVG(rating), 2) as avg_rating')
            );

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    /**
     * Get rating average by activity type.
     */
    public function getActivityTypeRatingAverage(): array
    {
        return DB::table('reviews')
            ->join('bookings', 'bookings.id', '=', 'reviews.booking_id')
            ->join('activity_types', 'activity_types.id', '=', 'bookings.activity_id')
            ->where('reviews.is_publish', true)
            ->whereNull('reviews.deleted_at')
            ->select(
                'activity_types.name as activity_type',
                DB::raw('count(reviews.id) as count'),
                DB::raw('ROUND(AVG(reviews.rating), 2) as avg_rating')
            )
            ->groupBy('activity_types.id', 'activity_types.name')
            ->get()
            ->toArray();
    }

    /**
     * Get rating average by bus unit.
     */
    public function getBusRatingAverage(): array
    {
        return DB::table('reviews')
            ->join('bookings', 'bookings.id', '=', 'reviews.booking_id')
            ->join('booking_buses', 'booking_buses.booking_id', '=', 'bookings.id')
            ->join('buses', 'buses.id', '=', 'booking_buses.bus_id')
            ->where('reviews.is_publish', true)
            ->whereNull('reviews.deleted_at')
            ->select(
                'buses.name as bus_name',
                'buses.plat_number',
                DB::raw('count(reviews.id) as count'),
                DB::raw('ROUND(AVG(reviews.rating), 2) as avg_rating')
            )
            ->groupBy('buses.id', 'buses.name', 'buses.plat_number')
            ->get()
            ->toArray();
    }

    /**
     * Get latest reviews with related data for reporting/management with pagination.
     */
    public function getLatestReviewsForReport(int $perPage = 15, ?int $rating = null, bool $onlyPublished = true)
    {
        $query = $this->model->with(['customer', 'booking.activity'])->latest();

        if ($onlyPublished) {
            $query->where('is_publish', true);
        }

        if ($rating) {
            $query->where('rating', $rating);
        }

        return $query->paginate($perPage);
    }
}
