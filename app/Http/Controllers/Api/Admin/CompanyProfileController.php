<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\CompanyProfile\UpdateCompanyProfileRequest;
use App\Http\Resources\CompanyProfileResource;
use App\Models\CompanyProfile;
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
        $this->authorize('viewAny', CompanyProfile::class);

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
        $this->authorize('update', $this->service->getProfile() ?? CompanyProfile::class);

        $profile = $this->service->updateOrCreateProfile($request->validated());

        return response()->json([
            'success' => true,
            'data' => new CompanyProfileResource($profile),
            'message' => 'Company profile updated successfully',
        ], 200);
    }

    /**
     * Delete the company profile.
     */
    public function destroy(): JsonResponse
    {
        $profile = $this->service->getProfile();

        if (! $profile) {
            return response()->json([
                'success' => false,
                'message' => 'Company profile not found',
            ], 404);
        }

        $this->authorize('delete', $profile);

        $this->service->deleteProfile();

        return response()->json([
            'success' => true,
            'message' => 'Company profile deleted successfully',
        ], 200);
    }
}
