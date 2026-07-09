<?php

namespace App\Services;

use App\Repositories\ActivityTypeRepository;
use Illuminate\Support\Facades\Storage;

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
    public function getPaginatedActivityTypes(int $perPage = 10, ?string $search = null)
    {
        return $this->activityTypeRepository->getPaginatedActivityTypes($perPage, $search);
    }

    // get ActivityType by id
    public function getActivityTypeById(string $id)
    {
        return $this->activityTypeRepository->find($id);
    }

    // create ActivityType
    public function createActivityType(array $data)
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image'] = $data['image']->store('activity-types', 'public');
        }
        return $this->activityTypeRepository->create($data);
    }

    // update ActivityType by id
    public function updateActivityType(string $id, array $data)
    {
        $activityType = $this->activityTypeRepository->find($id);

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($activityType->image) {
                Storage::disk('public')->delete($activityType->image);
            }
            $data['image'] = $data['image']->store('activity-types', 'public');
        } elseif (array_key_exists('image', $data) && empty($data['image'])) {
            if ($activityType->image) {
                Storage::disk('public')->delete($activityType->image);
            }
            $data['image'] = null;
        }

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
        $activityType = $this->activityTypeRepository->withTrashed()->find($id);
        if ($activityType && $activityType->image) {
            Storage::disk('public')->delete($activityType->image);
        }
        return $this->activityTypeRepository->forceDelete($id);
    }

    // get activity type statistics
    public function getStatistics(): array
    {
        return $this->activityTypeRepository->getStatistics();
    }
}
