<?php

namespace App\Services;

use App\Repositories\ActivityTypeRepository;

class ActivityTypeService
{
    protected ActivityTypeRepository $activityTypeRepository;

    public function __construct(ActivityTypeRepository $activityTypeRepository)
    {
        $this->activityTypeRepository = $activityTypeRepository;
    }

    // get all ActivityTypes
    public function getAllActivityTypes()
    {
        return $this->activityTypeRepository->getAllActivityTypes();
    }

    // get paginated ActivityTypes
    public function getPaginatedActivityTypes(int $perPage = 10)
    {
        return $this->activityTypeRepository->getPaginatedActivityTypes($perPage);
    }

    // get ActivityType by id
    public function getActivityTypeById(string $id)
    {
        return $this->activityTypeRepository->find($id);
    }

    // create ActivityType
    public function createActivityType(array $data)
    {
        return $this->activityTypeRepository->create($data);
    }

    // update ActivityType by id
    public function updateActivityType(string $id, array $data)
    {
        return $this->activityTypeRepository->update($id, $data);
    }

    // delete ActivityType by id
    public function deleteActivityType(string $id)
    {
        return $this->activityTypeRepository->delete($id);
    }

    // get all trashed ActivityTypes
    public function getTrashedActivityTypes()
    {
        return $this->activityTypeRepository->allTrashed();
    }

    // restore ActivityType by id
    public function restoreActivityType(string $id)
    {
        return $this->activityTypeRepository->restore($id);
    }

    // force delete ActivityType by id
    public function forceDeleteActivityType(string $id)
    {
        return $this->activityTypeRepository->forceDelete($id);
    }

    // get activity type statistics
    public function getStatistics(): array
    {
        return $this->activityTypeRepository->getStatistics();
    }
}
