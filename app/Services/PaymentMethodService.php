<?php

namespace App\Services;

use App\Repositories\PaymentMethodRepository;

class PaymentMethodService
{
    protected PaymentMethodRepository $paymentMethodRepository;

    public function __construct(PaymentMethodRepository $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function getAllPaymentMethods()
    {
        return $this->paymentMethodRepository->all();
    }

    public function getActivePaymentMethods()
    {
        return $this->paymentMethodRepository->getActivePaymentMethods();
    }

    public function createPaymentMethod(array $data)
    {
        return $this->paymentMethodRepository->create($data);
    }

    public function updatePaymentMethod(string $id, array $data)
    {
        return $this->paymentMethodRepository->update($id, $data);
    }

    public function deletePaymentMethod(string $id)
    {
        return $this->paymentMethodRepository->delete($id);
    }

    public function getPaymentMethodById(string $id)
    {
        return $this->paymentMethodRepository->find($id);
    }

    public function getTrashedPaymentMethods()
    {
        return $this->paymentMethodRepository->getTrashedPaymentMethods();
    }

    public function restorePaymentMethod(string $id)
    {
        return $this->paymentMethodRepository->restore($id);
    }

    public function forceDeletePaymentMethod(string $id)
    {
        return $this->paymentMethodRepository->forceDelete($id);
    }
}
