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
                'active' => $this->model->where('is_active', true)->count(),
                'inactive' => $this->model->where('is_active', false)->count(),
                'average_price' => round($this->model->avg('price_per_day') ?? 0, 2),
            ],
            'by_capacity' => $this->model->select('capacity', DB::raw('count(*) as count'))
                ->groupBy('capacity')
                ->get()
                ->pluck('count', 'capacity')
                ->toArray(),
        ];
    }
}
