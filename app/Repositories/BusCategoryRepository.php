<?php

namespace App\Repositories;

use App\Models\BusCategory;

class BusCategoryRepository extends BaseRepository
{
    public function __construct(BusCategory $busCategory)
    {
        $this->model = $busCategory;
    }

    public function getAllCategory()
    {
        return $this->model->all();
    }

    public function getPaginatedCategory(int $perPage = 10)
    {
        return $this->model->latest()->paginate($perPage);
    }

    /**
     * Get statistics for buses.
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'trashed' => $this->model->onlyTrashed()->count(),
        ];
    }
}
