<?php

namespace App\Repositories;

use App\Models\Buses;

class BusesRepository extends BaseRepository
{
    public function __construct(Buses $buses)
    {
        $this->model = $buses;
    }

    public function getAllBuses()
    {
        return $this->model->all();
    }

    public function getPaginatedBuses(int $perPage = 10)
    {
        return $this->model->latest()->paginate($perPage);
    }

}
