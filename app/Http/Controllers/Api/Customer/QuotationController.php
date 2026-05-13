<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Customer\Quotation\RequestRevisionRequest;
use App\Http\Resources\QuotationResource;
use App\Http\Resources\TransactionResource;
use App\Services\BookingService;
use App\Services\QuotationService;
use Illuminate\Http\JsonResponse;

class QuotationController extends BaseController
{
    protected QuotationService $quotationService;

    protected BookingService $bookingService;

    public function __construct(QuotationService $quotationService, BookingService $bookingService)
    {
        $this->quotationService = $quotationService;
        $this->bookingService = $bookingService;
    }

    /**
     * Lihat detail penawaran harga.
     */
    public function show(string $id): JsonResponse
    {
        $customerId = auth()->id();
        $quotation = $this->quotationService->getQuotationById($id);

        // Verify ownership via booking
        if ($quotation->booking->customer_id !== $customerId) {
            return $this->sendError('Unauthorized to view this quotation', [], 403);
        }

        return $this->sendResponse(new QuotationResource($quotation), 'Quotation detail retrieved successfully');
    }

    /**
     * Setuju penawaran harga.
     */
    public function accept(string $id): JsonResponse
    {
        $customerId = auth()->id();
        $quotation = $this->quotationService->getQuotationById($id);

        if ($quotation->booking->customer_id !== $customerId) {
            return $this->sendError('Unauthorized to accept this quotation', [], 403);
        }

        if ($quotation->status !== 'sent') {
            return $this->sendError('Quotation is not in a state that can be accepted', [], 400);
        }

        $transaction = $this->quotationService->acceptQuotation($id);

        return $this->sendResponse(
            new TransactionResource($transaction->load(['booking', 'quotation'])),
            'Quotation accepted and transaction created'
        );
    }

    /**
     * Minta revisi harga.
     */
    public function requestRevision(RequestRevisionRequest $request, string $id): JsonResponse
    {
        $customerId = auth()->id();
        $quotation = $this->quotationService->getQuotationById($id);

        if ($quotation->booking->customer_id !== $customerId) {
            return $this->sendError('Unauthorized to request revision for this quotation', [], 403);
        }

        if (! in_array($quotation->status, ['sent', 'revision'])) {
            return $this->sendError('Quotation revision cannot be requested at this stage', [], 400);
        }

        $updatedQuotation = $this->quotationService->requestRevision($id, $request->revision_note);

        return $this->sendResponse(new QuotationResource($updatedQuotation), 'Quotation revision requested successfully');
    }
}
