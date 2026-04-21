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

    public function getAllPaymentMethods(?string $search = null)
    {
        $query = $this->model->newQuery();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        return $query->latest()->get();
    }

    public function getTrashedPaymentMethods()
    {
        return $this->model->onlyTrashed()->latest()->get();
    }
}
