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

    public function getPaginatedQuotation(int $perPage = 10)
    {
        return $this->model->with(['booking', 'user'])->latest()->paginate($perPage);
    }

    public function getQuotationById(string $id)
    {
        return $this->model->with(['booking', 'user'])->findOrFail($id);
    }
}
