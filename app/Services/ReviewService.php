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

    public function getCustomerReviews(string $customerId, int $perPage = 10)
    {
        return $this->reviewRepository->getCustomerReviews($customerId, $perPage);
    }

    public function createReview(array $data)
    {
        return $this->reviewRepository->create($data);
    }

    public function updateReview(string $id, array $data)
    {
        return $this->reviewRepository->update($id, $data);
    }

    public function getReviewById(string $id)
    {
        return $this->reviewRepository->find($id);
    }

    public function getPublishedReviews(int $perPage = 10)
    {
        return $this->reviewRepository->getLatestReviewsForReport($perPage);
    }
}
