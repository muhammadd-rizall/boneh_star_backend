<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\BusFacility\StoreBusFacilitiesRequest;
use App\Http\Requests\Admin\BusFacility\UpdateBusFacilitiesRequest;
use App\Http\Resources\BusFacilitiesResource;
use App\Models\BusFacilities;
use App\Services\BusFacilitiesService;
use Illuminate\Http\Request;

class BusFacilitiesController extends BaseController
{
    protected BusFacilitiesService $busFacilitiesService;

    public function __construct(BusFacilitiesService $busFacilitiesService)
    {
        $this->busFacilitiesService = $busFacilitiesService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', BusFacilities::class);

        $statistics = $this->busFacilitiesService->getStatistics();

        $facilities = $this->busFacilitiesService->getPaginatedFacilities(10, $request->query('search'));

        return response()->json([
            'success' => true,
            'statistic' => $statistics,
            'data' => BusFacilitiesResource::collection($facilities)->response()->getData(true),
            'message' => 'Facilities retrieved successfully',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBusFacilitiesRequest $request)
    {
        $this->authorize('create', BusFacilities::class);

        $data = $request->validated();
        $facility = $this->busFacilitiesService->createFacility($data);

        return response()->json([
            'success' => true,
            'data' => new BusFacilitiesResource($facility),
            'message' => 'Facility created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view', BusFacilities::class);

        $facility = $this->busFacilitiesService->getFacilityById($id);

        return response()->json([
            'success' => true,
            'data' => new BusFacilitiesResource($facility),
            'message' => 'Facility retrieved successfully',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusFacilitiesRequest $request, string $id)
    {
        $this->authorize('update', BusFacilities::class);

        $data = $request->validated();
        $facility = $this->busFacilitiesService->updateFacility($id, $data);

        return response()->json([
            'success' => true,
            'data' => new BusFacilitiesResource($facility),
            'message' => 'Facility updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', BusFacilities::class);

        $this->busFacilitiesService->deleteFacility($id);

        return response()->json([
            'success' => true,
            'message' => 'Facility deleted successfully',
        ], 200);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed()
    {
        $this->authorize('viewAny', BusFacilities::class);

        $facilities = $this->busFacilitiesService->getTrashedFacilities();

        return response()->json([
            'success' => true,
            'data' => BusFacilitiesResource::collection($facilities),
            'message' => 'Trashed facilities retrieved successfully',
        ], 200);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $facility = BusFacilities::withTrashed()->findOrFail($id);
        $this->authorize('restore', $facility);

        $facility = $this->busFacilitiesService->restoreFacility($id);

        return response()->json([
            'success' => true,
            'data' => new BusFacilitiesResource($facility),
            'message' => 'Facility restored successfully',
        ], 200);
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete(string $id)
    {
        $facility = BusFacilities::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $facility);

        $this->busFacilitiesService->forceDeleteFacility($id);

        return response()->json([
            'success' => true,
            'message' => 'Facility permanently deleted successfully',
        ], 200);
    }
}
