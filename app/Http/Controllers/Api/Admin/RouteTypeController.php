<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\RouteType\StoreRouteTypeRequest;
use App\Http\Requests\Admin\RouteType\UpdateRouteTypeRequest;
use App\Http\Resources\RouteTypeResource;
use App\Models\RouteType;
use App\Services\RouteTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RouteTypeController extends BaseController
{
    protected RouteTypeService $routeTypeService;

    public function __construct(RouteTypeService $routeTypeService)
    {
        $this->routeTypeService = $routeTypeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', RouteType::class);

        $statistics = $this->routeTypeService->getStatistics();

        $route = $this->routeTypeService->getPaginatedRouteTypes(10, $request->query('search'));

        return response()->json([
            'success' => true,
            'statistic' => $statistics,
            'data' => RouteTypeResource::collection($route)->response()->getData(true),
            'message' => 'Route retrieved successfully',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRouteTypeRequest $request): JsonResponse
    {
        $this->authorize('create', RouteType::class);

        $data = $request->validated();
        $routeType = $this->routeTypeService->createRouteType($data);

        return response()->json([
            'success' => true,
            'data' => new RouteTypeResource($routeType),
            'message' => 'Route type created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize('view', RouteType::class);

        $routeType = $this->routeTypeService->getRouteTypeById($id);

        return response()->json([
            'success' => true,
            'data' => new RouteTypeResource($routeType),
            'message' => 'Route type retrieved successfully',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRouteTypeRequest $request, string $id): JsonResponse
    {
        $this->authorize('update', RouteType::class);

        $data = $request->validated();
        $routeType = $this->routeTypeService->updateRouteType($id, $data);

        return response()->json([
            'success' => true,
            'data' => new RouteTypeResource($routeType),
            'message' => 'Route type updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize('delete', RouteType::class);

        $this->routeTypeService->deleteRouteType($id);

        return response()->json([
            'success' => true,
            'message' => 'Route type deleted successfully',
        ], 200);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed(): JsonResponse
    {
        $this->authorize('viewAny', RouteType::class);

        $routeTypes = $this->routeTypeService->getTrashedRouteTypes();

        return response()->json([
            'success' => true,
            'data' => RouteTypeResource::collection($routeTypes),
            'message' => 'Trashed route types retrieved successfully',
        ], 200);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id): JsonResponse
    {
        $this->authorize('restore', RouteType::class);

        $routeType = $this->routeTypeService->restoreRouteType($id);

        return response()->json([
            'success' => true,
            'data' => new RouteTypeResource($routeType),
            'message' => 'Route type restored successfully',
        ], 200);
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete(string $id): JsonResponse
    {
        $this->authorize('forceDelete', RouteType::class);

        $this->routeTypeService->forceDeleteRouteType($id);

        return response()->json([
            'success' => true,
            'message' => 'Route type permanently deleted successfully',
        ], 200);
    }
}
