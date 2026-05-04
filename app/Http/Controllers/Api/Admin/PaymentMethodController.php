<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\PaymentMethod\StorePaymentMethodRequest;
use App\Http\Requests\Admin\PaymentMethod\UpdatePaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use App\Services\PaymentMethodService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends BaseController
{
    use AuthorizesRequests;

    protected PaymentMethodService $paymentMethodService;

    public function __construct(PaymentMethodService $paymentMethodService)
    {
        $this->paymentMethodService = $paymentMethodService;
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', PaymentMethod::class);
        $paymentMethods = $this->paymentMethodService->getAllPaymentMethods($request->query('search'));

        return $this->sendResponse(
            PaymentMethodResource::collection($paymentMethods),
            'Payment methods retrieved successfully'
        );
    }

    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        $this->authorize('create', PaymentMethod::class);
        $paymentMethod = $this->paymentMethodService->createPaymentMethod($request->validated());

        return $this->sendResponse(
            new PaymentMethodResource($paymentMethod),
            'Payment method created successfully',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $paymentMethod = $this->paymentMethodService->getPaymentMethodById($id);
        $this->authorize('view', $paymentMethod);

        return $this->sendResponse(
            new PaymentMethodResource($paymentMethod),
            'Payment method retrieved successfully'
        );
    }

    public function update(UpdatePaymentMethodRequest $request, string $id): JsonResponse
    {
        $paymentMethod = $this->paymentMethodService->getPaymentMethodById($id);
        $this->authorize('update', $paymentMethod);
        $paymentMethod = $this->paymentMethodService->updatePaymentMethod($id, $request->validated());

        return $this->sendResponse(
            new PaymentMethodResource($paymentMethod),
            'Payment method updated successfully'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $paymentMethod = $this->paymentMethodService->getPaymentMethodById($id);
        $this->authorize('delete', $paymentMethod);
        $this->paymentMethodService->deletePaymentMethod($id);

        return $this->sendResponse(null, 'Payment method deleted successfully');
    }

    public function active(): JsonResponse
    {
        $paymentMethods = $this->paymentMethodService->getActivePaymentMethods();

        return $this->sendResponse(
            PaymentMethodResource::collection($paymentMethods),
            'Active payment methods retrieved successfully'
        );
    }

    public function trashed(): JsonResponse
    {
        $this->authorize('viewAny', PaymentMethod::class);
        $paymentMethods = $this->paymentMethodService->getTrashedPaymentMethods();

        return $this->sendResponse(
            PaymentMethodResource::collection($paymentMethods),
            'Trashed payment methods retrieved successfully'
        );
    }

    public function restore(string $id): JsonResponse
    {
        $this->authorize('restore', PaymentMethod::class);
        $paymentMethod = $this->paymentMethodService->restorePaymentMethod($id);

        return $this->sendResponse(
            new PaymentMethodResource($paymentMethod),
            'Payment method restored successfully'
        );
    }

    public function forceDelete(string $id): JsonResponse
    {
        $this->authorize('forceDelete', PaymentMethod::class);
        $this->paymentMethodService->forceDeletePaymentMethod($id);

        return $this->sendResponse(null, 'Payment method permanently deleted successfully');
    }
}
