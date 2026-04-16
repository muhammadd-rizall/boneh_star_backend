<?php

namespace App\Repositories;

use App\Models\Driver;

class DriverRepository extends BaseRepository
{
    public function __construct(Driver $driver)
    {
        $this->model = $driver;
    }

    public function getAllDrivers()
    {
        return $this->model->all();
    }

    public function getPaginatedDrivers(int $perPage = 10)
    {
        return $this->model->latest()->paginate($perPage);
    }

    /**
     * Get statistics for drivers.
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'available' => $this->model->where('status', 'available')->count(),
            'on_duty' => $this->model->where('status', 'on_duty')->count(),
            'inactive' => $this->model->where('status', 'inactive')->count(),
            'trashed' => $this->model->onlyTrashed()->count(),
        ];
    }
}
