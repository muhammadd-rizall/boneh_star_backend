<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\ActivityType\StoreActivityTypeRequest;
use App\Http\Requests\ActivityType\UpdateActivityTypeRequest;
use App\Http\Resources\ActivityTypeResource;
use App\Models\ActivityType;
use App\Services\ActivityTypeService;
use Illuminate\Http\Request;

class ActivityTypeController extends BaseController
{
    protected ActivityTypeService $activityTypeService;

    public function __construct(ActivityTypeService $activityTypeService)
    {
        $this->activityTypeService = $activityTypeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ActivityType::class);

        $statistics = $this->activityTypeService->getStatistics();

        $activity = $this->activityTypeService->getPaginatedActivityTypes(10, $request->query('search'));

        return response()->json([
            'success' => true,
            'statistic' => $statistics,
            'data' => ActivityTypeResource::collection($activity)->response()->getData(true),
            'message' => 'Activity retrieved successfully',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityTypeRequest $request)
    {
        $this->authorize('create', ActivityType::class);

        $data = $request->validated();
        $activityType = $this->activityTypeService->createActivityType($data);

        return response()->json([
            'success' => true,
            'data' => new ActivityTypeResource($activityType),
            'message' => 'Activity type created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view', ActivityType::class);

        $activityType = $this->activityTypeService->getActivityTypeById($id);

        return response()->json([
            'success' => true,
            'data' => new ActivityTypeResource($activityType),
            'message' => 'Activity type retrieved successfully',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityTypeRequest $request, string $id)
    {
        $this->authorize('update', ActivityType::class);

        $data = $request->validated();
        $activityType = $this->activityTypeService->updateActivityType($id, $data);

        return response()->json([
            'success' => true,
            'data' => new ActivityTypeResource($activityType),
            'message' => 'Activity type updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', ActivityType::class);

        $this->activityTypeService->deleteActivityType($id);

        return response()->json([
            'success' => true,
            'message' => 'Activity type deleted successfully',
        ], 200);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed()
    {
        $this->authorize('viewAny', ActivityType::class);

        $activityTypes = $this->activityTypeService->getTrashedActivityTypes();

        return response()->json([
            'success' => true,
            'data' => ActivityTypeResource::collection($activityTypes),
            'message' => 'Trashed activity types retrieved successfully',
        ], 200);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $this->authorize('restore', ActivityType::class);

        $activityType = $this->activityTypeService->restoreActivityType($id);

        return response()->json([
            'success' => true,
            'data' => new ActivityTypeResource($activityType),
            'message' => 'Activity type restored successfully',
        ], 200);
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete(string $id)
    {
        $this->authorize('forceDelete', ActivityType::class);

        $this->activityTypeService->forceDeleteActivityType($id);

        return response()->json([
            'success' => true,
            'message' => 'Activity type permanently deleted successfully',
        ], 200);

    }
}
