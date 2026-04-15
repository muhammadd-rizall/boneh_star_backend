<?php

namespace App\Repositories;

use App\Models\Buses;
use Illuminate\Support\Facades\DB;

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

    /**
     * Get statistics for buses.
     */
    public function getStatistics(): array
    {
        return [
            'cards' => [
                'total' => $this->model->count(),
                'available' => $this->model->where('status', 'available')->count(),
                'booked' => $this->model->where('status', 'booked')->count(),
                'maintenance' => $this->model->where('status', 'maintenance')->count(),
            ],
            'by_status' => $this->model->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray(),
        ];
    }
}
