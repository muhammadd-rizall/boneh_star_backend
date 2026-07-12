<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\Gallery\StoreGalleryRequest;
use App\Http\Requests\Admin\Gallery\UpdateGalleryRequest;
use App\Http\Resources\GalleryResource;
use App\Services\GalleryService;
use Illuminate\Http\Request;

class GalleryController extends BaseController
{
    public function __construct(protected GalleryService $galleryService)
    {}

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search');

        $galleries = $this->galleryService->getAllPaginated($perPage, $search);

        return GalleryResource::collection($galleries)->additional([
            'status' => 'success',
            'message' => 'Galleries retrieved successfully'
        ]);
    }

    public function store(StoreGalleryRequest $request)
    {
        $gallery = $this->galleryService->create($request->validated());

        return $this->sendResponse(
            new GalleryResource($gallery),
            'Gallery created successfully',
            201
        );
    }

    public function show(string $id)
    {
        $gallery = $this->galleryService->find($id);

        return $this->sendResponse(
            new GalleryResource($gallery),
            'Gallery retrieved successfully'
        );
    }

    public function update(UpdateGalleryRequest $request, string $id)
    {
        $gallery = $this->galleryService->update($id, $request->validated());

        return $this->sendResponse(
            new GalleryResource($gallery),
            'Gallery updated successfully'
        );
    }

    public function destroy(string $id)
    {
        $this->galleryService->delete($id);

        return $this->sendResponse(
            null,
            'Gallery deleted successfully'
        );
    }
}
