<?php

namespace App\Repositories;

use App\Models\BusFacilities;

class BusFacilitiesRepository extends BaseRepository
{
    public function __construct(BusFacilities $busFacilities)
    {
        $this->model = $busFacilities;
    }

    public function getAllFacilities()
    {
        return $this->model->all();
    }

    public function getPaginatedFacilities(int $perPage = 10, ?string $search = null)
    {
        $query = $this->model->newQuery();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get statistics for facilities.
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'trashed' => $this->model->onlyTrashed()->count(),
        ];
    }
}
