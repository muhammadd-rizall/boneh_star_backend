<?php

namespace App\Repositories;

use App\Models\Quotation;

class QuotationRepository extends BaseRepository
{
    public function __construct(Quotation $quotation)
    {
        $this->model = $quotation;
    }

    public function getAllQuotation()
    {
        return $this->model->with(['booking', 'user'])->get();
    }

    public function getPaginatedQuotation(int $perPage = 10, ?string $search = null)
    {
        $query = $this->model->with(['booking.customer', 'user']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('booking', function ($bq) use ($search) {
                    $bq->where('booking_code', 'like', "%{$search}%");
                })
                    ->orWhereHas('booking.customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function getQuotationById(string $id)
    {
        return $this->model->with(['booking', 'user'])->findOrFail($id);
    }

    public function getStatistics()
    {
        return [
            'total_quotations' => $this->model->count(),
            'by_status' => [
                'draft' => $this->model->where('status', 'draft')->count(),
                'sent' => $this->model->where('status', 'sent')->count(),
                'accepted' => $this->model->where('status', 'accepted')->count(),
                'revision' => $this->model->where('status', 'revision')->count(),
                'expired' => $this->model->where('status', 'expired')->count(),
                'rejected' => $this->model->where('status', 'rejected')->count(),
            ]
        ];
    }
}
