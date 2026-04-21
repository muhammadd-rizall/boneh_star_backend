<?php

namespace App\Repositories;

use App\Models\ActivityType;

class ActivityTypeRepository extends BaseRepository
{
    public function __construct(ActivityType $activityType)
    {
        $this->model = $activityType;
    }

    public function getAllActivityTypes()
    {
        return $this->model->all();
    }

    public function getPaginatedActivityTypes(int $perPage = 10, ?string $search = null)
    {
        $query = $this->model->newQuery();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get statistics for activity types.
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'trashed' => $this->model->onlyTrashed()->count(),
        ];
    }
}
