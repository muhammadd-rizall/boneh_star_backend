<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Requests\Booking\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;

class BookingController extends BaseController
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Booking::class);

        $bookings = $this->bookingService->getAllBooking();
        $statistics = $this->bookingService->getBookingStatistics();

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings),
            'statistics' => $statistics,
            'message' => 'Bookings retrieved successfully',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $this->authorize('create', Booking::class);

        $data = $request->validated();
        $booking = $this->bookingService->createBooking($data);

        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking->load(['customer', 'activity', 'routeType'])),
            'message' => 'Booking created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize('view', Booking::class);

        $booking = $this->bookingService->getBookingById($id);

        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking),
            'message' => 'Booking retrieved successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, string $id): JsonResponse
    {
        $this->authorize('update', Booking::class);

        $data = $request->validated();
        $booking = $this->bookingService->updateBooking($id, $data);

        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking->load(['customer', 'activity', 'routeType'])),
            'message' => 'Booking updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize('delete', Booking::class);

        $this->bookingService->deleteBooking($id);

        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully',
        ]);
    }
}
