<?php

namespace App\Services;

use App\Notifications\PaymentStatusUpdated;
use App\Repositories\PaymentRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    protected PaymentRepository $paymentRepository;

    protected NotificationService $notificationService;

    public function __construct(PaymentRepository $paymentRepository, NotificationService $notificationService)
    {
        $this->paymentRepository = $paymentRepository;
        $this->notificationService = $notificationService;
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

            // Notify Admin
            $booking = $payment->transaction->booking;
            $customerName = $booking->customer->name ?? 'Pelanggan';
            $paymentType = strtoupper($payment->payment_type);
            $this->notificationService->notifyAdmin(
                "Bukti Pembayaran {$paymentType} Diterima",
                "Pelanggan {$customerName} telah mengunggah bukti pembayaran.",
                'payment',
                $payment
            );

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
            } else {
                unset($data['transfer_proof']);
            }

            $updatedPayment = $this->paymentRepository->update($id, $data);

            return $updatedPayment->load(['transaction', 'paymentMethod', 'verifiedBy']);
        });
    }

    public function verifyPayment(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $payment = $this->paymentRepository->find($id, ['transaction.booking']);
            $previousStatus = $payment->status;
            $newStatus = $data['status'];

            $updateData = [
                'status' => $newStatus,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ];

            if ($newStatus === 'rejected') {
                $updateData['rejection_reason'] = $data['rejection_reason'] ?? null;
            }

            $payment = $this->paymentRepository->update($id, $updateData);

            if ($newStatus === 'verified' && $previousStatus !== 'verified') {
                $transaction = $payment->transaction;

                if ($payment->payment_type === 'dp') {
                    $transaction->dp_paid += $payment->amount;
                }

                $transaction->remaining_amount = max(0, $transaction->remaining_amount - $payment->amount);

                // Update transaction status
                if ($transaction->remaining_amount <= 0) {
                    $transaction->status = 'fully_paid';
                } elseif ($transaction->dp_paid > 0) {
                    $transaction->status = 'dp_paid';
                }

                $transaction->save();

                // Update booking status to confirmed if it was a DP or full payment
                if (in_array($payment->payment_type, ['dp', 'settlement'])) {
                    $booking = $transaction->booking;
                    if ($booking && $booking->status === 'quoted') {
                        $booking->status = 'confirmed';
                        $booking->save();
                    }
                }
            } elseif ($newStatus === 'rejected' && $previousStatus === 'verified') {
                $transaction = $payment->transaction;

                if ($payment->payment_type === 'dp') {
                    $transaction->dp_paid = max(0, $transaction->dp_paid - $payment->amount);
                }

                $transaction->remaining_amount = min($transaction->total_amount, $transaction->remaining_amount + $payment->amount);

                if ($transaction->remaining_amount >= $transaction->total_amount) {
                    $transaction->status = 'waiting';
                } elseif ($transaction->dp_paid > 0) {
                    $transaction->status = 'dp_paid';
                } else {
                    $transaction->status = 'waiting';
                }

                $transaction->save();
            }

            // Notify Customer
            $payment->transaction->booking->customer->notify(new PaymentStatusUpdated($payment));

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
