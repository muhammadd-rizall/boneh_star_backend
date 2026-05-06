<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ActivityTypeResource;
use App\Http\Resources\BusesResource;
use App\Http\Resources\BusFacilitiesResource;
use App\Http\Resources\CompanyProfileResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\RouteTypeResource;
use App\Services\ActivityTypeService;
use App\Services\BusesService;
use App\Services\BusFacilitiesService;
use App\Services\CompanyProfileService;
use App\Services\ReviewService;
use App\Services\RouteTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicController extends BaseController
{
    public function __construct(
        protected BusesService $busesService,
        protected BusFacilitiesService $busFacilitiesService,
        protected ReviewService $reviewService,
        protected CompanyProfileService $companyProfileService,
        protected ActivityTypeService $activityTypeService,
        protected RouteTypeService $routeTypeService,
        protected \App\Services\BusCategoryService $busCategoryService
    ) {}

    /**
     * Tampilkan profil perusahaan.
     */
    public function companyProfile(): JsonResponse
    {
        $profile = $this->companyProfileService->getProfile();

        return $this->sendResponse(new CompanyProfileResource($profile), 'Company profile retrieved successfully');
    }

    /**
     * Daftar armada bus.
     */
    public function buses(Request $request): JsonResponse
    {
        $buses = $this->busesService->getPaginatedBuses($request->get('per_page', 12), $request->get('search'));

        return $this->sendResponse(
            BusesResource::collection($buses)->response()->getData(true),
            'Buses retrieved successfully'
        );
    }

    /**
     * Detail armada bus.
     */
    public function busDetail(string $id): JsonResponse
    {
        $bus = $this->busesService->getBusById($id);

        return $this->sendResponse(new BusesResource($bus), 'Bus detail retrieved successfully');
    }

    /**
     * Daftar fasilitas bus.
     */
    public function facilities(): JsonResponse
    {
        $facilities = $this->busFacilitiesService->getAllFacilities();

        return $this->sendResponse(BusFacilitiesResource::collection($facilities), 'Facilities retrieved successfully');
    }

    /**
     * Daftar ulasan yang dipublish.
     */
    public function reviews(Request $request): JsonResponse
    {
        $reviews = $this->reviewService->getPublishedReviews($request->get('per_page', 10));

        return $this->sendResponse(
            ReviewResource::collection($reviews)->response()->getData(true),
            'Public reviews retrieved successfully'
        );
    }

    /**
     * Daftar jenis kegiatan.
     */
    public function activityTypes(): JsonResponse
    {
        $types = $this->activityTypeService->getAllActivityTypes();

        return $this->sendResponse(ActivityTypeResource::collection($types), 'Activity types retrieved successfully');
    }

    /**
     * Daftar jenis rute.
     */
    public function routeTypes(): JsonResponse
    {
        $types = $this->routeTypeService->getAllRouteTypes();

        return $this->sendResponse(RouteTypeResource::collection($types), 'Route types retrieved successfully');
    }

    /**
     * Daftar kategori bus.
     */
    public function busCategories(): JsonResponse
    {
        $categories = $this->busCategoryService->getAllCategory();

        return $this->sendResponse(\App\Http\Resources\BusCategoriesResource::collection($categories), 'Bus categories retrieved successfully');
    }
}
