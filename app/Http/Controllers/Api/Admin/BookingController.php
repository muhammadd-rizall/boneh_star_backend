<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Requests\Booking\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;

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
    public function index()
    {
        $this->authorize('viewAny', Booking::class);

        $booking = $this->bookingService->getAllBooking();

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($booking),
            'message' => 'Booking retrieved successfully',
        ], 200);
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
            'data' => new BookingResource($booking),
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
        ], 200);
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
            'data' => new BookingResource($booking),
            'message' => 'Booking updated successfully',
        ], 200);
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
        ], 200);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed()
    {
        $this->authorize('viewAny', Booking::class);

        $booking = $this->bookingService->getTrashedBooking();

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($booking),
            'message' => 'Trashed booking retrieved successfully',
        ], 200);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $this->authorize('restore', Booking::class);

        $this->bookingService->restoreBooking($id);

        return response()->json([
            'success' => true,
            'message' => 'Booking restored successfully',
        ], 200);
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
        ], 200);
    }
}
