<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Customer\Booking\StoreBookingRequest;
use App\Http\Requests\Customer\Booking\UploadPaymentRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\PaymentMethodResource;
use App\Http\Resources\PaymentResource;
use App\Services\BookingService;
use App\Services\PaymentMethodService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends BaseController
{
    protected BookingService $bookingService;

    protected PaymentService $paymentService;

    protected PaymentMethodService $paymentMethodService;

    public function __construct(
        BookingService $bookingService,
        PaymentService $paymentService,
        PaymentMethodService $paymentMethodService
    ) {
        $this->bookingService = $bookingService;
        $this->paymentService = $paymentService;
        $this->paymentMethodService = $paymentMethodService;
    }

    /**
     * List all customer bookings.
     */
    public function index(Request $request): JsonResponse
    {
        $customerId = auth()->id();
        $bookings = $this->bookingService->getCustomerBookings($customerId, $request->get('per_page', 10));

        return $this->sendResponse(
            BookingResource::collection($bookings)->response()->getData(true),
            'Bookings retrieved successfully'
        );
    }

    /**
     * Form permintaan pemesanan (store booking).
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['customer_id'] = auth()->id();
        $data['status'] = 'pending';

        $booking = $this->bookingService->createBooking($data);

        return $this->sendResponse(
            new BookingResource($booking->load(['activity', 'routeType'])),
            'Booking request submitted successfully',
            201
        );
    }

    /**
     * Lihat detail pemesanan.
     */
    public function show(string $id): JsonResponse
    {
        $customerId = auth()->id();
        $booking = $this->bookingService->getCustomerBookingById($customerId, $id);

        return $this->sendResponse(new BookingResource($booking), 'Booking details retrieved successfully');
    }

    /**
     * Upload bukti pembayaran (DP/Pelunasan).
     */
    public function uploadPayment(UploadPaymentRequest $request, string $id): JsonResponse
    {
        $customerId = auth()->id();
        $booking = $this->bookingService->getCustomerBookingById($customerId, $id);

        if (! $booking->transaction) {
            return $this->sendError('No active transaction found for this booking. Accept quotation first.', [], 400);
        }

        $data = $request->validated();
        $data['transaction_id'] = $booking->transaction->id;
        $data['status'] = 'pending';

        $payment = $this->paymentService->createPayment($data);

        return $this->sendResponse(
            new PaymentResource($payment->load(['transaction', 'paymentMethod'])),
            'Payment proof uploaded successfully'
        );
    }

    /**
     * Lihat rekening tujuan transfer (active payment methods).
     */
    public function paymentMethods(): JsonResponse
    {
        $methods = $this->paymentMethodService->getActivePaymentMethods();

        return $this->sendResponse(
            PaymentMethodResource::collection($methods),
            'Payment methods retrieved successfully'
        );
    }

    /**
     * Batalkan permintaan pemesanan.
     */
    public function cancel(Request $request, string $id): JsonResponse
    {
        $request->validate(['reason' => ['required', 'string']]);

        $customerId = auth()->id();
        $booking = $this->bookingService->getCustomerBookingById($customerId, $id);

        if (! in_array($booking->status, ['pending', 'quoted'])) {
            return $this->sendError('This booking cannot be cancelled in its current state', [], 400);
        }

        $this->bookingService->updateBooking($id, [
            'status' => 'cancelled',
            'cancelled_by' => 'customer',
            'cancelled_reason' => $request->reason,
        ]);

        return $this->sendResponse(null, 'Booking cancelled successfully');
    }
}
