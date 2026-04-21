<?php

namespace App\Repositories;

use App\Models\RouteType;

class RouteTypeRepository extends BaseRepository
{
    public function __construct(RouteType $routeType)
    {
        $this->model = $routeType;
    }

    public function getAllRouteTypes()
    {
        return $this->model->all();
    }

    public function getPaginatedRouteTypes(int $perPage = 10, ?string $search = null)
    {
        $query = $this->model->newQuery();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get statistics for route types.
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'trashed' => $this->model->onlyTrashed()->count(),
        ];
    }
}
