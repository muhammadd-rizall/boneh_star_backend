<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository extends BaseRepository
{
    public function __construct(Transaction $transaction)
    {
        $this->model = $transaction;
    }

    public function getAllTransaction()
    {
        return $this->model->with(['booking.customer', 'quotation'])->get();
    }

    public function getPaginatedTransaction(int $perPage = 10, ?string $search = null)
    {
        $query = $this->model->with(['booking.customer', 'quotation']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                    ->orWhereHas('booking.customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function getTransactionById(string $id)
    {
        return $this->model->with(['booking.customer', 'quotation'])->findOrFail($id);
    }

    public function getStatistics()
    {
        return [
            'total_transactions' => $this->model->count(),
            'total_revenue' => $this->model->where('status', 'fully_paid')->sum('total_amount'),
            'total_dp_collected' => $this->model->sum('dp_paid'),
            'total_outstanding' => $this->model->sum('remaining_amount'),
            'by_status' => [
                'waiting' => $this->model->where('status', 'waiting')->count(),
                'dp_paid' => $this->model->where('status', 'dp_paid')->count(),
                'fully_paid' => $this->model->where('status', 'fully_paid')->count(),
                'overdue' => $this->model->where('status', 'overdue')->count(),
            ],
        ];
    }
}
