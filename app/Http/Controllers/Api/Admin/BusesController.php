<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Bus\StoreBusesRequest;
use App\Http\Requests\Bus\UpdateBusesRequest;
use App\Http\Resources\BusesResource;
use App\Models\Buses;
use App\Services\BusesService;

class BusesController extends BaseController
{
    protected BusesService $busesService;

    public function __construct(BusesService $busesService)
    {
        $this->busesService = $busesService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Buses::class);

        $statistics = $this->busesService->getStatistics();

        $buses = $this->busesService->getPaginatedBuses();

        return response()->json([
            'success' => true,
            'statistic' => $statistics,
            'data' => BusesResource::collection($buses)->response()->getData(true),
            'message' => 'Buses retrieved successfully',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view', Buses::class);

        $bus = $this->busesService->getBusById($id);

        return response()->json([
            'success' => true,
            'data' => new BusesResource($bus),
            'message' => 'Bus retrieved successfully',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBusesRequest $request)
    {
        $this->authorize('create', Buses::class);

        $data = $request->validated();

        $bus = $this->busesService->createBus($data);

        return response()->json([
            'success' => true,
            'data' => new BusesResource($bus),
            'message' => 'Bus created successfully',
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusesRequest $request, string $id)
    {
        $this->authorize('update', Buses::class);

        $data = $request->validated();

        $bus = $this->busesService->updateBus($id, $data);

        return response()->json([
            'success' => true,
            'data' => new BusesResource($bus),
            'message' => 'Bus updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', Buses::class);

        $this->busesService->deleteBus($id);

        return response()->json([
            'success' => true,
            'message' => 'Bus deleted successfully',
        ], 200);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed()
    {
        $this->authorize('viewAny', Buses::class);

        $buses = $this->busesService->getTrashedBuses();

        return response()->json([
            'success' => true,
            'data' => BusesResource::collection($buses),
            'message' => 'Trashed buses retrieved successfully',
        ], 200);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $this->authorize('restore', Buses::class);

        $this->busesService->restoreBus($id);

        return response()->json([
            'success' => true,
            'message' => 'Bus restored successfully',
        ], 200);
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete(string $id)
    {
        $this->authorize('forceDelete', Buses::class);

        $this->busesService->forceDeleteBus($id);

        return response()->json([
            'success' => true,
            'message' => 'Bus permanently deleted successfully',
        ], 200);
    }
}
