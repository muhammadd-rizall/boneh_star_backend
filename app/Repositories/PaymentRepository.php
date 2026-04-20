<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentRepository extends BaseRepository
{
    public function __construct(Payment $payment)
    {
        $this->model = $payment;
    }

    public function getPaginatedPayments(int $perPage = 10)
    {
        return $this->model->with(['transaction', 'paymentMethod', 'verifiedBy'])->latest()->paginate($perPage);
    }

    public function getTrashedPayments()
    {
        return $this->model->onlyTrashed()->with(['transaction', 'paymentMethod', 'verifiedBy'])->latest()->get();
    }

    /**
     * Get statistics for payments.
     */
    public function getStatistics(): array
    {
        return [
            'cards' => [
                'total_amount' => (float) $this->model->where('status', 'verified')->sum('amount'),
                'total_count' => $this->model->count(),
                'pending_count' => $this->model->where('status', 'pending')->count(),
                'verified_count' => $this->model->where('status', 'verified')->count(),
            ],
            'by_status' => $this->model->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray(),
            'by_type' => $this->model->select('payment_type', DB::raw('count(*) as count'))
                ->groupBy('payment_type')
                ->get()
                ->pluck('count', 'payment_type')
                ->toArray(),
        ];
    }
}
