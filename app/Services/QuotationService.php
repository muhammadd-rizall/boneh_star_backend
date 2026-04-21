<?php

namespace App\Services;

use App\Repositories\QuotationRepository;

class QuotationService
{
    protected QuotationRepository $quotationRepository;

    public function __construct(QuotationRepository $quotationRepository)
    {
        $this->quotationRepository = $quotationRepository;
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
        return $this->quotationRepository->create($data);
    }

    /**
     * Update an existing quotation.
     */
    public function updateQuotation(string $id, array $data)
    {
        return $this->quotationRepository->update($id, $data);
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
