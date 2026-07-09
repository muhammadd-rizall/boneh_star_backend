<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryResource;
use App\Services\GalleryService;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function __construct(protected GalleryService $galleryService)
    {}

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 12);
        
        $galleries = $this->galleryService->getPublishedPaginated($perPage);

        return GalleryResource::collection($galleries)->additional([
            'status' => 'success',
            'message' => 'Published galleries retrieved successfully'
        ]);
    }
}
