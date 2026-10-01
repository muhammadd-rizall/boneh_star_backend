<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\Driver\StoreDriverRequest;
use App\Http\Requests\Admin\Driver\UpdateDriverRequest;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use App\Services\DriverService;
use Illuminate\Http\Request;

class DriverController extends BaseController
{
    protected DriverService $driverService;

    public function __construct(DriverService $driverService)
    {
        $this->driverService = $driverService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Driver::class);

        $statistics = $this->driverService->getStatistics();

        if ($request->query('all') === 'true') {
            $drivers = $this->driverService->getAllDrivers();
            return response()->json([
                'success' => true,
                'statistic' => $statistics,
                'data' => DriverResource::collection($drivers),
                'message' => 'Drivers retrieved successfully',
            ], 200);
        }

        $drivers = $this->driverService->getPaginatedDrivers(10, $request->query('search'));

        return response()->json([
            'success' => true,
            'statistic' => $statistics,
            'data' => DriverResource::collection($drivers)->response()->getData(true),
            'message' => 'Drivers retrieved successfully',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDriverRequest $request)
    {
        $this->authorize('create', Driver::class);

        $data = $request->validated();
        $driver = $this->driverService->createDriver($data);

        return response()->json([
            'success' => true,
            'data' => new DriverResource($driver),
            'message' => 'Driver created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view', Driver::class);

        $driver = $this->driverService->getDriverById($id);

        return response()->json([
            'success' => true,
            'data' => new DriverResource($driver),
            'message' => 'Driver retrieved successfully',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDriverRequest $request, string $id)
    {
        $this->authorize('update', Driver::class);

        $data = $request->validated();
        $driver = $this->driverService->updateDriver($id, $data);

        return response()->json([
            'success' => true,
            'data' => new DriverResource($driver),
            'message' => 'Driver updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', Driver::class);

        $this->driverService->deleteDriver($id);

        return response()->json([
            'success' => true,
            'message' => 'Driver deleted successfully',
        ], 200);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed()
    {
        $this->authorize('viewAny', Driver::class);

        $drivers = $this->driverService->getTrashedDrivers();

        return response()->json([
            'success' => true,
            'data' => DriverResource::collection($drivers),
            'message' => 'Trashed drivers retrieved successfully',
        ], 200);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $driver = Driver::withTrashed()->findOrFail($id);
        $this->authorize('restore', $driver);

        $driver = $this->driverService->restoreDriver($id);

        return response()->json([
            'success' => true,
            'data' => new DriverResource($driver),
            'message' => 'Driver restored successfully',
        ], 200);
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete(string $id)
    {
        $driver = Driver::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $driver);

        $this->driverService->forceDeleteDriver($id);

        return response()->json([
            'success' => true,
            'message' => 'Driver permanently deleted successfully',
        ], 200);
    }
}
