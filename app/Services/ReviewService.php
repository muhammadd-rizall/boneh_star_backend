<?php

namespace App\Services;

use App\Repositories\ReviewRepository;

class ReviewService
{
    protected ReviewRepository $reviewRepository;

    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function getPaginatedReviews(int $perPage = 10)
    {
        return $this->reviewRepository->getPaginatedReviews($perPage);
    }

    public function getReviewById(string $id)
    {
        return $this->reviewRepository->find($id, ['booking', 'customer']);
    }

    public function createReview(array $data)
    {
        return $this->reviewRepository->create($data);
    }

    public function updateReview(string $id, array $data)
    {
        $review = $this->reviewRepository->update($id, $data);

        return $review->load(['booking', 'customer']);
    }

    public function deleteReview(string $id)
    {
        return $this->reviewRepository->delete($id);
    }

    public function getTrashedReviews()
    {
        return $this->reviewRepository->getTrashedReviews();
    }

    public function restoreReview(string $id)
    {
        return $this->reviewRepository->restore($id);
    }

    public function forceDeleteReview(string $id)
    {
        return $this->reviewRepository->forceDelete($id);
    }

    public function togglePublish(string $id)
    {
        $review = $this->reviewRepository->find($id);

        return $this->reviewRepository->update($id, [
            'is_publish' => ! $review->is_publish,
        ]);
    }

    /**
     * Get statistics for reviews.
     */
    public function getStatistics(): array
    {
        return $this->reviewRepository->getStatistics();
    }
}
