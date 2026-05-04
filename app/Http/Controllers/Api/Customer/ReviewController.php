<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Customer\Review\StoreReviewRequest;
use App\Http\Requests\Customer\Review\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\BookingService;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends BaseController
{
    protected ReviewService $reviewService;

    protected BookingService $bookingService;

    public function __construct(ReviewService $reviewService, BookingService $bookingService)
    {
        $this->reviewService = $reviewService;
        $this->bookingService = $bookingService;
    }

    /**
     * Tampilkan ulasan saya.
     */
    public function index(Request $request): JsonResponse
    {
        $customerId = auth('customer')->id();
        $reviews = $this->reviewService->getCustomerReviews($customerId, $request->get('per_page', 10));

        return $this->sendResponse(
            ReviewResource::collection($reviews)->response()->getData(true),
            'My reviews retrieved successfully'
        );
    }

    /**
     * Tulis ulasan baru.
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $customerId = auth('customer')->id();
        $booking = $this->bookingService->getCustomerBookingById($customerId, $request->booking_id);

        if ($booking->status !== 'completed') {
            return $this->sendError('You can only review completed bookings', [], 400);
        }

        // Check if user already reviewed this booking
        $existingReview = Review::where('booking_id', $request->booking_id)
            ->where('customer_id', $customerId)
            ->first();

        if ($existingReview) {
            return $this->sendError('You have already submitted a review for this booking', [], 400);
        }

        $data = $request->validated();
        $data['customer_id'] = $customerId;
        $data['is_publish'] = true; // Default publish but admin can toggle

        $review = $this->reviewService->createReview($data);

        return $this->sendResponse(
            new ReviewResource($review->load(['booking.activity'])),
            'Review submitted successfully',
            201
        );
    }

    /**
     * Edit ulasan.
     */
    public function update(UpdateReviewRequest $request, string $id): JsonResponse
    {
        $customerId = auth('customer')->id();
        $review = $this->reviewService->getReviewById($id);

        if ($review->customer_id !== $customerId) {
            return $this->sendError('Unauthorized to update this review', [], 403);
        }

        $updatedReview = $this->reviewService->updateReview($id, $request->validated());

        return $this->sendResponse(
            new ReviewResource($updatedReview->load(['booking.activity'])),
            'Review updated successfully'
        );
    }

    /**
     * Lihat detail satu ulasan.
     */
    public function show(string $id): JsonResponse
    {
        $customerId = auth('customer')->id();
        $review = $this->reviewService->getReviewById($id);

        if ($review->customer_id !== $customerId) {
            return $this->sendError('Unauthorized to view this review', [], 403);
        }

        return $this->sendResponse(
            new ReviewResource($review->load(['booking.activity'])),
            'Review detail retrieved successfully'
        );
    }
}
