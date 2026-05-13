<?php

namespace App\Services;

use App\Repositories\ReviewRepository;

class ReviewService
{
    protected ReviewRepository $reviewRepository;

    protected NotificationService $notificationService;

    public function __construct(ReviewRepository $reviewRepository, NotificationService $notificationService)
    {
        $this->reviewRepository = $reviewRepository;
        $this->notificationService = $notificationService;
    }

    public function getCustomerReviews(string $customerId, int $perPage = 10)
    {
        return $this->reviewRepository->getCustomerReviews($customerId, $perPage);
    }

    public function createReview(array $data)
    {
        $review = $this->reviewRepository->create($data);

        // Notify Admin
        $customerName = $review->booking->customer->name ?? 'Pelanggan';
        $this->notificationService->notifyAdmin(
            'Ulasan Baru Diterima',
            "Pelanggan {$customerName} telah memberikan ulasan perjalanan.",
            'review',
            $review
        );

        return $review;
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
