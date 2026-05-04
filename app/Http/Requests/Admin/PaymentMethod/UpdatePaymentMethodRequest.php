<?php

namespace App\Http\Requests\Admin\PaymentMethod;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(config('Permission.payment_methods.update'));
    }

    public function rules(): array
    {
        return [
            'bank_name' => 'sometimes|string|max:255',
            'account_number' => 'sometimes|string|max:255',
            'account_name' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'bank_name.string' => 'Bank name must be a string',
            'bank_name.max' => 'Bank name must not exceed 255 characters',
            'account_number.string' => 'Account number must be a string',
            'account_number.max' => 'Account number must not exceed 255 characters',
            'account_name.string' => 'Account name must be a string',
            'account_name.max' => 'Account name must not exceed 255 characters',
            'is_active.boolean' => 'Active status must be a boolean',
        ];
    }
}
