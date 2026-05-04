<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Customer\Profile\UpdateProfileRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends BaseController
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * View profile.
     */
    public function show(): JsonResponse
    {
        $customerId = auth('customer')->id();
        $customer = $this->customerService->getCustomerById($customerId);

        return $this->sendResponse(new CustomerResource($customer), 'Profile retrieved successfully');
    }

    /**
     * Edit profile.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $customerId = auth('customer')->id();
        $customer = $this->customerService->updateCustomer($customerId, $request->validated());

        return $this->sendResponse(new CustomerResource($customer), 'Profile updated successfully');
    }

    /**
     * Upload/change profile picture.
     */
    public function uploadProfilePicture(Request $request): JsonResponse
    {
        $request->validate([
            'profile_picture' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $customerId = auth('customer')->id();
        $customer = $this->customerService->getCustomerById($customerId);

        if ($customer->profile_picture) {
            Storage::disk('public')->delete($customer->profile_picture);
        }

        $path = $request->file('profile_picture')->store('customers/profiles', 'public');

        $customer = $this->customerService->updateCustomer($customerId, [
            'profile_picture' => $path,
        ]);

        return $this->sendResponse(new CustomerResource($customer), 'Profile picture updated successfully');
    }

    /**
     * Get booking statistics.
     */
    public function statistics(): JsonResponse
    {
        $customerId = auth('customer')->id();
        $statistics = $this->customerService->getCustomerBookingStatistics($customerId);

        return $this->sendResponse($statistics, 'Booking statistics retrieved successfully');
    }
}
