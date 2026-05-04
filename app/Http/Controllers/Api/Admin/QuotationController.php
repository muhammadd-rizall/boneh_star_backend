<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\Quotation\StoreQuotationRequest;
use App\Http\Requests\Admin\Quotation\UpdateQuotationRequest;
use App\Http\Resources\QuotationResource;
use App\Models\Quotation;
use App\Services\QuotationService;
use Illuminate\Http\Request;

class QuotationController extends BaseController
{
    protected QuotationService $quotationService;

    public function __construct(QuotationService $quotationService)
    {
        $this->quotationService = $quotationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Quotation::class);

        $quotations = $this->quotationService->getPaginatedQuotation(10, $request->query('search'));

        return response()->json([
            'success' => true,
            'data' => QuotationResource::collection($quotations)->response()->getData(true),
            'message' => 'Quotations retrieved successfully',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuotationRequest $request)
    {
        $this->authorize('create', Quotation::class);

        $data = $request->validated();
        $quotation = $this->quotationService->createQuotation($data);

        return response()->json([
            'success' => true,
            'data' => new QuotationResource($quotation->load(['booking', 'user'])),
            'message' => 'Quotation created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view', Quotation::class);

        $quotation = $this->quotationService->getQuotationById($id);

        return response()->json([
            'success' => true,
            'data' => new QuotationResource($quotation),
            'message' => 'Quotation retrieved successfully',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuotationRequest $request, string $id)
    {
        $this->authorize('update', Quotation::class);

        $data = $request->validated();
        $quotation = $this->quotationService->updateQuotation($id, $data);

        return response()->json([
            'success' => true,
            'data' => new QuotationResource($quotation->load(['booking', 'user'])),
            'message' => 'Quotation updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', Quotation::class);

        $this->quotationService->deleteQuotation($id);

        return response()->json([
            'success' => true,
            'message' => 'Quotation deleted successfully',
        ]);
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trashed()
    {
        $this->authorize('viewAny', Quotation::class);

        $quotations = $this->quotationService->getTrashedQuotation();

        return response()->json([
            'success' => true,
            'data' => QuotationResource::collection($quotations),
            'message' => 'Trashed quotations retrieved successfully',
        ]);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $this->authorize('restore', Quotation::class);

        $quotation = $this->quotationService->restoreQuotation($id);

        return response()->json([
            'success' => true,
            'data' => new QuotationResource($quotation),
            'message' => 'Quotation restored successfully',
        ]);
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete(string $id)
    {
        $this->authorize('forceDelete', Quotation::class);

        $this->quotationService->forceDeleteQuotation($id);

        return response()->json([
            'success' => true,
            'message' => 'Quotation permanently deleted successfully',
        ]);
    }
}
