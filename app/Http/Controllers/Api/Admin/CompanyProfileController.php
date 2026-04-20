<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\CompanyProfile\UpdateCompanyProfileRequest;
use App\Http\Resources\CompanyProfileResource;
use App\Services\CompanyProfileService;
use Illuminate\Http\JsonResponse;

class CompanyProfileController extends BaseController
{
    protected CompanyProfileService $service;

    public function __construct(CompanyProfileService $service)
    {
        $this->service = $service;
    }

    /**
     * Display the company profile.
     */
    public function index(): JsonResponse
    {
        $profile = $this->service->getProfile();

        return response()->json([
            'success' => true,
            'data' => $profile ? new CompanyProfileResource($profile) : null,
            'message' => 'Company profile retrieved successfully',
        ], 200);
    }

    /**
     * Update or create the company profile.
     */
    public function store(UpdateCompanyProfileRequest $request): JsonResponse
    {
        $profile = $this->service->updateOrCreateProfile($request->validated());

        return response()->json([
            'success' => true,
            'data' => new CompanyProfileResource($profile),
            'message' => 'Company profile updated successfully',
        ], 200);
    }
}
