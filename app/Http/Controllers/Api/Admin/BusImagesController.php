<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\BusImage\StoreBusImageRequest;
use App\Http\Requests\Admin\BusImage\UpdateBusImageRequest;
use App\Http\Resources\BusImageResource;
use App\Services\BusImageService;
use Illuminate\Http\JsonResponse;

class BusImagesController extends BaseController
{
    protected BusImageService $busImageService;

    public function __construct(BusImageService $busImageService)
    {
        $this->busImageService = $busImageService;
    }

    /**
     * Store newly created resources in storage.
     */
    public function store(StoreBusImageRequest $request): JsonResponse
    {
        $data = $request->validated();

        $images = $this->busImageService->uploadImages($data['bus_id'], $request->file('images'));

        return response()->json([
            'success' => true,
            'data' => BusImageResource::collection($images),
            'message' => 'Images uploaded successfully',
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusImageRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();

        $image = $this->busImageService->updateImage($id, $data);

        if (isset($data['is_primary']) && $data['is_primary']) {
            $image = $this->busImageService->setPrimary($id);
        }

        return response()->json([
            'success' => true,
            'data' => new BusImageResource($image),
            'message' => 'Image updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->busImageService->deleteImage($id);

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully',
        ], 200);
    }

    /**
     * Set image as primary.
     */
    public function setPrimary(string $id): JsonResponse
    {
        $image = $this->busImageService->setPrimary($id);

        return response()->json([
            'success' => true,
            'data' => new BusImageResource($image),
            'message' => 'Image set as primary successfully',
        ], 200);
    }
}
