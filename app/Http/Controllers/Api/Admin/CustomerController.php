<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;

class CustomerController extends BaseController
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Customer::class);

        $statistics = $this->customerService->getStatistics();

        $customers = $this->customerService->getPaginatedCustomers();

        return response()->json([
            'success' => true,
            'statistic' => $statistics,
            'data' => CustomerResource::collection($customers)->response()->getData(true),
            'message' => 'Customers retrieved successfully',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $this->authorize('create', Customer::class);

        $data = $request->validated();
        $customer = $this->customerService->createCustomer($data);

        return response()->json([
            'success' => true,
            'data' => new CustomerResource($customer),
            'message' => 'Customer created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view', Customer::class);
        $customer = $this->customerService->getCustomerById($id);

        return response()->json([
            'success' => true,
            'data' => new CustomerResource($customer),
            'message' => 'Customer retrieved successfully',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, string $id)
    {
        $this->authorize('update', Customer::class);

        $data = $request->validated();
        $customer = $this->customerService->updateCustomer($id, $data);

        return response()->json([
            'success' => true,
            'data' => new CustomerResource($customer),
            'message' => 'Customer updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', Customer::class);

        $this->customerService->deleteCustomer($id);

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully',
        ], 200);
    }

    /**
     * Display a listing of the trashed resource.
     */
    public function trashed()
    {
        $this->authorize('viewAny', Customer::class);
        $customers = $this->customerService->getTrashedCustomers();

        return response()->json([
            'success' => true,
            'data' => CustomerResource::collection($customers),
            'message' => 'Trashed customers retrieved successfully',
        ], 200);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $this->authorize('restore', Customer::class);
        $this->customerService->restoreCustomer($id);

        return response()->json([
            'success' => true,
            'message' => 'Customer restored successfully',
        ], 200);
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete(string $id)
    {
        $this->authorize('forceDelete', Customer::class);
        $this->customerService->forceDeleteCustomer($id);

        return response()->json([
            'success' => true,
            'message' => 'Customer permanently deleted successfully',
        ], 200);
    }
}
