<?php

namespace App\Services;

use App\Repositories\TransactionRepository;

class TransactionService
{
    protected TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Get all transactions with eager loaded relations.
     */
    public function getAllTransaction()
    {
        return $this->transactionRepository->getAllTransaction();
    }

    /**
     * Get paginated transactions with eager loaded relations.
     */
    public function getPaginatedTransaction(int $perPage = 10)
    {
        return $this->transactionRepository->getPaginatedTransaction($perPage);
    }

    /**
     * Get transaction by ID with relations.
     */
    public function getTransactionById(string $id)
    {
        return $this->transactionRepository->getTransactionById($id);
    }

    /**
     * Create a new transaction.
     */
    public function createTransaction(array $data)
    {
        return $this->transactionRepository->create($data);
    }

    /**
     * Update an existing transaction.
     */
    public function updateTransaction(string $id, array $data)
    {
        return $this->transactionRepository->update($id, $data);
    }

    /**
     * Delete a transaction by ID.
     */
    public function deleteTransaction(string $id)
    {
        return $this->transactionRepository->delete($id);
    }

    /**
     * Get all trashed transactions.
     */
    public function getTrashedTransaction()
    {
        return $this->transactionRepository->allTrashed(['booking.customer', 'quotation']);
    }

    /**
     * Restore a trashed transaction by ID.
     */
    public function restoreTransaction(string $id)
    {
        return $this->transactionRepository->restore($id);
    }

    /**
     * Permanently delete a trashed transaction by ID.
     */
    public function forceDeleteTransaction(string $id)
    {
        return $this->transactionRepository->forceDelete($id);
    }

    /**
     * Get transaction statistics.
     */
    public function getTransactionStatistics()
    {
        return $this->transactionRepository->getStatistics();
    }
}
