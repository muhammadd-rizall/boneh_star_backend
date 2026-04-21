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

    public function getPaginatedDrivers(int $perPage = 10, ?string $search = null)
    {
        $query = $this->model->newQuery();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
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
