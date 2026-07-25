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

        if ($booking->status === 'cancelled') {
            return $this->sendError('Tidak dapat mengunggah bukti pembayaran untuk pemesanan yang telah dibatalkan.', [], 400);
        }

        if (! $booking->transaction) {
            return $this->sendError('No active transaction found for this booking. Accept quotation first.', [], 400);
        }

        if ($booking->transaction->remaining_amount <= 0 || $booking->transaction->status === 'fully_paid') {
            return $this->sendError('Tagihan untuk pemesanan ini sudah lunas.', [], 400);
        }

        $pendingPaymentsSum = $booking->transaction->payments()->where('status', 'pending')->sum('amount');
        $effectiveRemaining = max(0, (float) $booking->transaction->remaining_amount - $pendingPaymentsSum);

        if ($effectiveRemaining <= 0) {
            return $this->sendError('Bukti pembayaran Anda sebelumnya sedang dalam proses verifikasi oleh admin.', [], 400);
        }

        if ((float) $request->amount > $effectiveRemaining) {
            return $this->sendError('Nominal pembayaran melebihi sisa tagihan yang belum terverifikasi.', [], 422);
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

    /**
     * Selesaikan pemesanan perjalanan.
     */
    public function complete(string $id): JsonResponse
    {
        $customerId = auth()->id();
        $booking = $this->bookingService->getCustomerBookingById($customerId, $id);

        if (! in_array($booking->status, ['confirmed', 'ongoing'])) {
            return $this->sendError('Pemesanan tidak dapat diselesaikan pada status saat ini.', [], 400);
        }

        $today = now()->startOfDay();
        $returnDate = $booking->return_date->startOfDay();
        if ($today->lt($returnDate)) {
            return $this->sendError('Perjalanan belum berakhir. Anda baru dapat menyelesaikan pemesanan pada tanggal kepulangan.', [], 400);
        }

        if (! $booking->transaction || $booking->transaction->status !== 'fully_paid') {
            return $this->sendError('Pembayaran belum lunas. Silakan lakukan pelunasan pembayaran terlebih dahulu.', [], 400);
        }

        $this->bookingService->updateBooking($id, [
            'status' => 'completed',
        ]);

        return $this->sendResponse(null, 'Pemesanan perjalanan berhasil diselesaikan.');
    }
}
