<?php

namespace App\Services;

use App\Notifications\QuotationUpdated;
use App\Repositories\BookingRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    protected QuotationRepository $quotationRepository;

    protected TransactionRepository $transactionRepository;

    protected BookingRepository $bookingRepository;

    protected NotificationService $notificationService;

    public function __construct(
        QuotationRepository $quotationRepository,
        TransactionRepository $transactionRepository,
        BookingRepository $bookingRepository,
        NotificationService $notificationService
    ) {
        $this->quotationRepository = $quotationRepository;
        $this->transactionRepository = $transactionRepository;
        $this->bookingRepository = $bookingRepository;
        $this->notificationService = $notificationService;
    }

    /**
     * Get all quotations with eager loaded relations.
     */
    public function getAllQuotation()
    {
        return $this->quotationRepository->getAllQuotation();
    }

    /**
     * Get paginated quotations with eager loaded relations.
     */
    public function getPaginatedQuotation(int $perPage = 10, ?string $search = null)
    {
        return $this->quotationRepository->getPaginatedQuotation($perPage, $search);
    }

    /**
     * Get quotation by ID with relations.
     */
    public function getQuotationById(string $id)
    {
        return $this->quotationRepository->getQuotationById($id);
    }

    /**
     * Create a new quotation.
     */
    public function createQuotation(array $data)
    {
        return DB::transaction(function () use ($data) {
            $booking = $this->bookingRepository->find($data['booking_id']);

            if ($booking->status === 'cancelled') {
                throw new \Exception('Cannot create a quotation for a cancelled booking.');
            }

            $quotation = $this->quotationRepository->create($data);

            // Update booking status to quoted
            $this->bookingRepository->update($quotation->booking_id, ['status' => 'quoted']);

            // Notify Customer
            $booking->customer->notify(new QuotationUpdated($quotation, 'new'));

            return $quotation;
        });
    }

    /**
     * Update an existing quotation.
     */
    public function updateQuotation(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $quotation = $this->quotationRepository->update($id, $data);
            $booking = $quotation->booking;

            // Notify Customer if status is 'sent'
            if ($quotation->status === 'sent') {
                $booking->customer->notify(new QuotationUpdated($quotation, 'updated'));
            }

            return $quotation;
        });
    }

    /**
     * Accept quotation by customer.
     */
    public function acceptQuotation(string $id)
    {
        return DB::transaction(function () use ($id) {
            $quotation = $this->quotationRepository->find($id);

            $this->quotationRepository->update($id, ['status' => 'accepted']);

            // Notify Admin
            $customerName = $quotation->booking->customer->name ?? 'Pelanggan';
            $this->notificationService->notifyAdmin(
                'Penawaran Disetujui',
                "Pelanggan {$customerName} telah menyetujui penawaran harga.",
                'quotation',
                $quotation
            );

            // Create transaction
            return $this->transactionRepository->create([
                'booking_id' => $quotation->booking_id,
                'quotation_id' => $quotation->id,
                'total_amount' => $quotation->proposed_price,
                'dp_paid' => 0,
                'remaining_amount' => $quotation->proposed_price,
                'status' => 'waiting',
            ]);
        });
    }

    /**
     * Request revision by customer.
     */
    public function requestRevision(string $id, string $note)
    {
        $quotation = $this->quotationRepository->update($id, [
            'status' => 'revision',
            'revision_note' => $note,
        ]);

        // Notify Admin
        $customerName = $quotation->booking->customer->name ?? 'Pelanggan';
        $this->notificationService->notifyAdmin(
            'Permintaan Revisi Penawaran',
            "Pelanggan {$customerName} meminta revisi pada penawaran harga.",
            'quotation',
            $quotation
        );

        return $quotation;
    }

    /**
     * Delete a quotation by ID.
     */
    public function deleteQuotation(string $id)
    {
        return $this->quotationRepository->delete($id);
    }

    /**
     * Get all trashed quotations.
     */
    public function getTrashedQuotation()
    {
        return $this->quotationRepository->allTrashed();
    }

    /**
     * Restore a trashed quotation by ID.
     */
    public function restoreQuotation(string $id)
    {
        return $this->quotationRepository->restore($id);
    }

    /**
     * Permanently delete a trashed quotation by ID.
     */
    public function forceDeleteQuotation(string $id)
    {
        return $this->quotationRepository->forceDelete($id);
    }
}
