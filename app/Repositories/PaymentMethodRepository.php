<?php

namespace App\Repositories;

use App\Models\PaymentMethod;

class PaymentMethodRepository extends BaseRepository
{
    public function __construct(PaymentMethod $paymentMethod)
    {
        $this->model = $paymentMethod;
    }

    public function getActivePaymentMethods()
    {
        return $this->model->where('is_active', true)->get();
    }

    public function getTrashedPaymentMethods()
    {
        return $this->model->onlyTrashed()->latest()->get();
    }
}
