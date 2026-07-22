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
        return $this->model->with(['category', 'facilities', 'images'])->get();
    }

    public function getPaginatedBuses(int $perPage = 10, ?string $search = null)
    {
        $query = $this->model->newQuery()->with(['category', 'facilities', 'images']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('plat_number', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
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

    /**
     * Get bus availability for a specific period.
     */
    public function getBusAvailability(string $startDate, string $endDate)
    {
        return $this->model->query()
            ->where('status', '!=', 'inactive')
            ->with(['category', 'images', 'bookingBuses.booking' => function ($query) use ($startDate, $endDate) {
                $query->where('status', '!=', 'cancelled')
                    ->where(function ($q) use ($startDate, $endDate) {
                        $q->where('departure_date', '<=', $endDate.' 23:59:59')
                            ->where(function ($sq) use ($startDate) {
                                $sq->where('return_date', '>=', $startDate.' 00:00:00')
                                    ->orWhere(function ($ssq) use ($startDate) {
                                        $ssq->whereNull('return_date')
                                            ->where('departure_date', '>=', $startDate.' 00:00:00');
                                    });
                            });
                    });
            }])
            ->orderBy('id')
            ->get();
    }
}
