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

    /**
     * Get statistics for reviews.
     */
    public function getStatistics(): array
    {
        return [
            'cards' => [
                'total_count' => $this->model->count(),
                'published_count' => $this->model->where('is_publish', true)->count(),
                'unpublished_count' => $this->model->where('is_publish', false)->count(),
                'average_rating' => round((float) $this->model->avg('rating'), 1),
            ],
            'by_rating' => $this->model->select('rating', DB::raw('count(*) as count'))
                ->groupBy('rating')
                ->get()
                ->pluck('count', 'rating')
                ->toArray(),
            'by_publish_status' => [
                'published' => $this->model->where('is_publish', true)->count(),
                'unpublished' => $this->model->where('is_publish', false)->count(),
            ],
        ];
    }
}
