<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\BookingResource;
use App\Http\Resources\PaymentResource;
use App\Services\BookingService;
use App\Services\PaymentMethodService;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HistoryController extends BaseController
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
     * Daftar semua riwayat pemesanan.
     */
    public function index(Request $request): JsonResponse
    {
        $customerId = auth('customer')->id();
        $bookings = $this->bookingService->getCustomerBookings($customerId, $request->get('per_page', 10));

        return $this->sendResponse(
            BookingResource::collection($bookings)->response()->getData(true),
            'Booking history retrieved successfully'
        );
    }

    /**
     * Detail pemesanan lengkap + timeline.
     */
    public function show(string $id): JsonResponse
    {
        $customerId = auth('customer')->id();
        $booking = $this->bookingService->getCustomerBookingById($customerId, $id);

        $timeline = $this->generateTimeline($booking);

        return $this->sendResponse([
            'booking' => new BookingResource($booking),
            'timeline' => $timeline,
        ], 'Booking detail and timeline retrieved successfully');
    }

    /**
     * Cek status pembayaran secara real-time.
     */
    public function paymentStatus(string $id): JsonResponse
    {
        $customerId = auth('customer')->id();
        $booking = $this->bookingService->getCustomerBookingById($customerId, $id);

        if (! $booking->transaction) {
            return $this->sendError('No transaction found for this booking', [], 404);
        }

        $payments = $booking->transaction->payments()->latest()->get();

        return $this->sendResponse([
            'transaction_status' => $booking->transaction->status,
            'total_amount' => $booking->transaction->total_amount,
            'dp_paid' => $booking->transaction->dp_paid,
            'remaining_amount' => $booking->transaction->remaining_amount,
            'payments' => PaymentResource::collection($payments),
        ], 'Payment status retrieved successfully');
    }

    /**
     * Download invoice PDF.
     */
    public function downloadInvoice(string $id)
    {
        $customerId = auth('customer')->id();
        $booking = $this->bookingService->getCustomerBookingById($customerId, $id);

        if (! $booking->transaction) {
            return $this->sendError('No transaction/invoice found for this booking', [], 404);
        }

        $data = [
            'booking' => $booking,
            'transaction' => $booking->transaction,
            'customer' => $booking->customer,
            'payment_methods' => $this->paymentMethodService->getActivePaymentMethods(),
        ];

        $pdf = Pdf::loadView('pdf.invoice', $data);

        return $pdf->download("Invoice-{$booking->transaction->invoice_number}.pdf");
    }

    /**
     * Generate timeline events for the booking.
     */
    private function generateTimeline($booking): array
    {
        $timeline = [];

        // 1. Booking Created
        $timeline[] = [
            'event' => 'Pemesanan Dibuat',
            'status' => 'pending',
            'time' => $booking->created_at->toDateTimeString(),
            'description' => "Permintaan pemesanan dengan kode {$booking->booking_code} telah diterima.",
        ];

        // 2. Quotation Sent
        if ($booking->quotation) {
            $timeline[] = [
                'event' => 'Penawaran Harga Diterima',
                'status' => $booking->quotation->status,
                'time' => $booking->quotation->created_at->toDateTimeString(),
                'description' => 'Admin telah mengirimkan penawaran harga sebesar Rp '.number_format($booking->quotation->proposed_price, 0, ',', '.'),
            ];

            // 3. Quotation Accepted/Revision
            if ($booking->quotation->status === 'accepted') {
                $timeline[] = [
                    'event' => 'Penawaran Harga Disetujui',
                    'status' => 'accepted',
                    'time' => $booking->quotation->updated_at->toDateTimeString(),
                    'description' => 'Anda telah menyetujui penawaran harga.',
                ];
            } elseif ($booking->quotation->status === 'revision') {
                $timeline[] = [
                    'event' => 'Revisi Harga Diminta',
                    'status' => 'revision',
                    'time' => $booking->quotation->updated_at->toDateTimeString(),
                    'description' => "Anda meminta revisi harga dengan catatan: {$booking->quotation->revision_note}",
                ];
            }
        }

        // 4. Payments
        if ($booking->transaction) {
            foreach ($booking->transaction->payments as $payment) {
                $type = $payment->payment_type === 'dp' ? 'DP' : 'Pelunasan';
                $timeline[] = [
                    'event' => "Bukti Pembayaran {$type} Diunggah",
                    'status' => $payment->status,
                    'time' => $payment->created_at->toDateTimeString(),
                    'description' => 'Pembayaran sebesar Rp '.number_format($payment->amount, 0, ',', '.').' menunggu verifikasi.',
                ];

                if ($payment->status === 'verified') {
                    $timeline[] = [
                        'event' => "Pembayaran {$type} Diverifikasi",
                        'status' => 'verified',
                        'time' => $payment->verified_at->toDateTimeString(),
                        'description' => "Pembayaran {$type} telah diverifikasi oleh admin.",
                    ];
                } elseif ($payment->status === 'rejected') {
                    $timeline[] = [
                        'event' => "Pembayaran {$type} Ditolak",
                        'status' => 'rejected',
                        'time' => $payment->updated_at->toDateTimeString(),
                        'description' => "Pembayaran ditolak dengan alasan: {$payment->rejection_reason}",
                    ];
                }
            }
        }

        // 5. Booking Status Final States
        if (in_array($booking->status, ['ongoing', 'completed', 'cancelled'])) {
            $statusLabel = [
                'ongoing' => 'Perjalanan Berlangsung',
                'completed' => 'Pemesanan Selesai',
                'cancelled' => 'Pemesanan Dibatalkan',
            ];

            $timeline[] = [
                'event' => $statusLabel[$booking->status],
                'status' => $booking->status,
                'time' => $booking->updated_at->toDateTimeString(),
                'description' => $booking->status === 'cancelled' ? "Dibatalkan oleh: {$booking->cancelled_by}. Alasan: {$booking->cancelled_reason}" : "Status pemesanan telah diperbarui menjadi {$booking->status}.",
            ];
        }

        // Sort by time
        usort($timeline, fn ($a, $b) => strtotime($a['time']) <=> strtotime($b['time']));

        return $timeline;
    }
}
