<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\Payment\StorePaymentRequest;
use App\Http\Requests\Admin\Payment\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends BaseController
{
    use AuthorizesRequests;

    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Payment::class);
        $payments = $this->paymentService->getPaginatedPayments(10, $request->query('search'));
        $statistics = $this->paymentService->getStatistics();

        return response()->json([
            'success' => true,
            'message' => 'Payments retrieved successfully',
            'data' => PaymentResource::collection($payments),
            'statistics' => $statistics,
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ],
        ], 200);
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $this->authorize('create', Payment::class);
        $payment = $this->paymentService->createPayment($request->validated());

        return $this->sendResponse(
            new PaymentResource($payment),
            'Payment created successfully',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $payment = $this->paymentService->getPaymentById($id);
        $this->authorize('view', $payment);

        return $this->sendResponse(
            new PaymentResource($payment),
            'Payment retrieved successfully'
        );
    }

    public function update(UpdatePaymentRequest $request, string $id): JsonResponse
    {
        $payment = $this->paymentService->getPaymentById($id);
        $this->authorize('update', $payment);
        $updatedPayment = $this->paymentService->updatePayment($id, $request->validated());

        return $this->sendResponse(
            new PaymentResource($updatedPayment),
            'Payment updated successfully'
        );
    }

    public function verify(Request $request, string $id): JsonResponse
    {
        $this->authorize('verify', Payment::class);

        $request->validate([
            'status' => 'required|in:verified,rejected',
            'rejection_reason' => 'required_if:status,rejected|string|nullable',
        ]);

        $payment = $this->paymentService->verifyPayment($id, $request->all());

        return $this->sendResponse(
            new PaymentResource($payment),
            'Payment verified successfully'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $payment = $this->paymentService->getPaymentById($id);
        $this->authorize('delete', $payment);
        $this->paymentService->deletePayment($id);

        return $this->sendResponse(null, 'Payment deleted successfully');
    }

    public function trashed(): JsonResponse
    {
        $this->authorize('viewAny', Payment::class);
        $payments = $this->paymentService->getTrashedPayments();

        return $this->sendResponse(
            PaymentResource::collection($payments),
            'Trashed payments retrieved successfully'
        );
    }

    public function restore(string $id): JsonResponse
    {
        $payment = Payment::withTrashed()->findOrFail($id);
        $this->authorize('restore', $payment);
        $payment = $this->paymentService->restorePayment($id);

        return $this->sendResponse(new PaymentResource($payment), 'Payment restored successfully');
    }

    public function forceDelete(string $id): JsonResponse
    {
        $payment = Payment::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $payment);
        $this->paymentService->forceDeletePayment($id);

        return $this->sendResponse(null, 'Payment permanently deleted successfully');
    }
}
