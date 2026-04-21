<?php

namespace App\Services;

use App\Repositories\PaymentRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    protected PaymentRepository $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function getPaginatedPayments(int $perPage = 10, ?string $search = null)
    {
        return $this->paymentRepository->getPaginatedPayments($perPage, $search);
    }

    public function getPaymentById(string $id)
    {
        return $this->paymentRepository->find($id, ['transaction', 'paymentMethod', 'verifiedBy']);
    }

    public function createPayment(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['transfer_proof']) && $data['transfer_proof'] instanceof UploadedFile) {
                $data['transfer_proof'] = $data['transfer_proof']->store('payments', 'public');
            }

            $payment = $this->paymentRepository->create($data);

            return $payment->load(['transaction', 'paymentMethod']);
        });
    }

    public function updatePayment(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $payment = $this->paymentRepository->find($id);

            if (isset($data['transfer_proof']) && $data['transfer_proof'] instanceof UploadedFile) {
                if ($payment->transfer_proof) {
                    Storage::disk('public')->delete($payment->transfer_proof);
                }
                $data['transfer_proof'] = $data['transfer_proof']->store('payments', 'public');
            }

            $updatedPayment = $this->paymentRepository->update($id, $data);

            return $updatedPayment->load(['transaction', 'paymentMethod', 'verifiedBy']);
        });
    }

    public function verifyPayment(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $updateData = [
                'status' => $data['status'],
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ];

            if ($data['status'] === 'rejected') {
                $updateData['rejection_reason'] = $data['rejection_reason'] ?? null;
            }

            $payment = $this->paymentRepository->update($id, $updateData);

            return $payment->load(['transaction', 'paymentMethod', 'verifiedBy']);
        });
    }

    public function deletePayment(string $id)
    {
        return $this->paymentRepository->delete($id);
    }

    public function getTrashedPayments()
    {
        return $this->paymentRepository->getTrashedPayments();
    }

    public function restorePayment(string $id)
    {
        return $this->paymentRepository->restore($id);
    }

    public function forceDeletePayment(string $id)
    {
        $payment = $this->paymentRepository->findTrashed($id);

        if ($payment->transfer_proof) {
            Storage::disk('public')->delete($payment->transfer_proof);
        }

        return $this->paymentRepository->forceDelete($id);
    }

    /**
     * Get statistics for payments.
     */
    public function getStatistics(): array
    {
        return $this->paymentRepository->getStatistics();
    }
}
