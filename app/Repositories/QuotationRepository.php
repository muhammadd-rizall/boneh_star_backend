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
}
