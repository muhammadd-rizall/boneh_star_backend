<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\Transaction\StoreTransactionRequest;
use App\Http\Requests\Admin\Transaction\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends BaseController
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Transaction::class);

        $transactions = $this->transactionService->getPaginatedTransaction(10, $request->query('search'));
        $statistics = $this->transactionService->getTransactionStatistics();

        return response()->json([
            'success' => true,
            'data' => TransactionResource::collection($transactions)->response()->getData(true),
            'statistics' => $statistics,
            'message' => 'Transactions retrieved successfully',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $this->authorize('create', Transaction::class);

        $data = $request->validated();
        $transaction = $this->transactionService->createTransaction($data);

        return response()->json([
            'success' => true,
            'data' => new TransactionResource($transaction->load(['booking.customer', 'quotation'])),
            'message' => 'Transaction created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize('view', Transaction::class);

        $transaction = $this->transactionService->getTransactionById($id);

        return response()->json([
            'success' => true,
            'data' => new TransactionResource($transaction),
            'message' => 'Transaction retrieved successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, string $id): JsonResponse
    {
        $this->authorize('update', Transaction::class);

        $data = $request->validated();
        $transaction = $this->transactionService->updateTransaction($id, $data);

        return response()->json([
            'success' => true,
            'data' => new TransactionResource($transaction->load(['booking.customer', 'quotation'])),
            'message' => 'Transaction updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize('delete', Transaction::class);

        $this->transactionService->deleteTransaction($id);

        return response()->json([
            'success' => true,
            'message' => 'Transaction deleted successfully',
        ]);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed(): JsonResponse
    {
        $this->authorize('viewAny', Transaction::class);

        $transactions = $this->transactionService->getTrashedTransaction();

        return response()->json([
            'success' => true,
            'data' => TransactionResource::collection($transactions),
            'message' => 'Trashed transactions retrieved successfully',
        ]);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id): JsonResponse
    {
        $transaction = Transaction::withTrashed()->findOrFail($id);
        $this->authorize('restore', $transaction);

        $transaction = $this->transactionService->restoreTransaction($id);

        return response()->json([
            'success' => true,
            'data' => new TransactionResource($transaction),
            'message' => 'Transaction restored successfully',
        ]);
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete(string $id): JsonResponse
    {
        $transaction = Transaction::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $transaction);

        $this->transactionService->forceDeleteTransaction($id);

        return response()->json([
            'success' => true,
            'message' => 'Transaction permanently deleted successfully',
        ]);
    }
}
