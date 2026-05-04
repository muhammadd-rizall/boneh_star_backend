<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\Booking\StoreBookingRequest;
use App\Http\Requests\Admin\Booking\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;

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
    public function index(Request $request)
    {
        $this->authorize('viewAny', Booking::class);

        $bookings = $this->bookingService->getPaginatedBooking(10, $request->query('search'));
        $statistics = $this->bookingService->getBookingStatistics();

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings)->response()->getData(true),
            'statistics' => $statistics,
            'message' => 'Bookings retrieved successfully',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $this->authorize('create', Booking::class);

        $data = $request->validated();
        $booking = $this->bookingService->createBooking($data);

        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking->load(['customer', 'activity', 'routeType', 'bookingBuses.bus', 'bookingBuses.driver'])),
            'message' => 'Booking created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
    public function update(UpdateBookingRequest $request, string $id)
    {
        $this->authorize('update', Booking::class);

        $data = $request->validated();
        $booking = $this->bookingService->updateBooking($id, $data);

        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking->load(['customer', 'activity', 'routeType', 'bookingBuses.bus', 'bookingBuses.driver'])),
            'message' => 'Booking updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', Booking::class);

        $this->bookingService->deleteBooking($id);

        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully',
        ]);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed()
    {
        $this->authorize('viewAny', Booking::class);

        $bookings = $this->bookingService->getTrashedBooking();

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings),
            'message' => 'Trashed bookings retrieved successfully',
        ]);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $this->authorize('restore', Booking::class);

        $booking = $this->bookingService->restoreBooking($id);

        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking),
            'message' => 'Booking restored successfully',
        ]);
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete(string $id)
    {
        $this->authorize('forceDelete', Booking::class);

        $this->bookingService->forceDeleteBooking($id);

        return response()->json([
            'success' => true,
            'message' => 'Booking permanently deleted successfully',
        ]);
    }
}
