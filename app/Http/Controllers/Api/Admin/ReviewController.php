<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReviewController extends BaseController
{
    use AuthorizesRequests;

    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Review::class);

        $statistics = $this->reviewService->getStatistics();

        $reviews = $this->reviewService->getPaginatedReviews();

        return response()->json([
            'success' => true,
            'statistic' => $statistics,
            'data' => ReviewResource::collection($reviews)->response()->getData(true),
            'message' => 'Reviews retrieved successfully',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view', Review::class);

        $review = $this->reviewService->getReviewById($id);

        return response()->json([
            'success' => true,
            'data' => new ReviewResource($review),
            'message' => 'Review retrieved successfully',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReviewRequest $request)
    {
        $this->authorize('create', Review::class);

        $data = $request->validated();

        $review = $this->reviewService->createReview($data);

        return response()->json([
            'success' => true,
            'data' => new ReviewResource($review->load(['booking', 'customer'])),
            'message' => 'Review created successfully',
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReviewRequest $request, string $id)
    {
        $this->authorize('update', Review::class);

        $data = $request->validated();

        $review = $this->reviewService->updateReview($id, $data);

        return response()->json([
            'success' => true,
            'data' => new ReviewResource($review),
            'message' => 'Review updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', Review::class);

        $this->reviewService->deleteReview($id);

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
        ], 200);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed()
    {
        $this->authorize('viewAny', Review::class);

        $reviews = $this->reviewService->getTrashedReviews();

        return response()->json([
            'success' => true,
            'data' => ReviewResource::collection($reviews),
            'message' => 'Trashed reviews retrieved successfully',
        ], 200);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $this->authorize('restore', Review::class);

        $this->reviewService->restoreReview($id);

        return response()->json([
            'success' => true,
            'message' => 'Review restored successfully',
        ], 200);
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete(string $id)
    {
        $this->authorize('forceDelete', Review::class);

        $this->reviewService->forceDeleteReview($id);

        return response()->json([
            'success' => true,
            'message' => 'Review permanently deleted successfully',
        ], 200);
    }

    /**
     * Toggle the publish status of the specified resource.
     */
    public function togglePublish(string $id)
    {
        $this->authorize('publish', Review::class);

        $review = $this->reviewService->togglePublish($id);

        return response()->json([
            'success' => true,
            'data' => new ReviewResource($review->load(['booking', 'customer'])),
            'message' => 'Review publish status toggled successfully',
        ], 200);
    }
}
