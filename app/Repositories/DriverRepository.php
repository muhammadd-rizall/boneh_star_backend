<?php

namespace App\Repositories;

use App\Models\Driver;

class DriverRepository extends BaseRepository
{
    public function __construct(Driver $driver)
    {
        $this->model = $driver;
    }

    public function getAllDriver()
    {
        return $this->all();
    }

    public function getPaginatedDriver(int $perPage = 10)
    {
        return $this->model->latest()->paginate($perPage);
    }
}
