<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\BusCategory\StoreBusCategoryRequest;
use App\Http\Requests\Admin\BusCategory\UpdateBusCategoryRequest;
use App\Http\Resources\BusCategoriesResource;
use App\Models\BusCategory;
use App\Services\BusCategoryService;
use Illuminate\Http\Request;

class BusCategoryController extends BaseController
{
    protected BusCategoryService $busCategoryService;

    public function __construct(BusCategoryService $busCategoryService)
    {
        $this->busCategoryService = $busCategoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', BusCategory::class);

        $statistics = $this->busCategoryService->getStatistics();

        $category = $this->busCategoryService->getPaginatedCategory(10, $request->query('search'));

        return response()->json([
            'success' => true,
            'statistic' => $statistics,
            'data' => BusCategoriesResource::collection($category)->response()->getData(true),
            'message' => 'Category retrieved successfully',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBusCategoryRequest $request)
    {
        $this->authorize('create', BusCategory::class);

        $data = $request->validated();

        $category = $this->busCategoryService->createCategory($data);

        return response()->json([
            'success' => true,
            'data' => new BusCategoriesResource($category),
            'message' => 'Category created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view', BusCategory::class);

        $category = $this->busCategoryService->getCategoryById($id);

        return response()->json([
            'success' => true,
            'data' => new BusCategoriesResource($category),
            'message' => 'Category retrieved successfully',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusCategoryRequest $request, string $id)
    {
        $this->authorize('update', BusCategory::class);

        $data = $request->validated();

        $category = $this->busCategoryService->updateCategory($id, $data);

        return response()->json([
            'success' => true,
            'data' => new BusCategoriesResource($category),
            'message' => 'Category updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', BusCategory::class);

        $this->busCategoryService->deleteCategory($id);

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ], 200);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed()
    {
        $this->authorize('viewAny', BusCategory::class);

        $category = $this->busCategoryService->getTrashedCategory();

        return response()->json([
            'success' => true,
            'data' => BusCategoriesResource::collection($category),
            'message' => 'Trashed category retrieved successfully',
        ], 200);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $this->authorize('restore', BusCategory::class);

        $category = $this->busCategoryService->restoreCategory($id);

        return response()->json([
            'success' => true,
            'data' => new BusCategoriesResource($category),
            'message' => 'Category restored successfully',
        ], 200);
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete(string $id)
    {
        $this->authorize('forceDelete', BusCategory::class);

        $this->busCategoryService->forceDeleteCategory($id);

        return response()->json([
            'success' => true,
            'message' => 'Category permanently deleted successfully',
        ], 200);
    }
}
