<?php

namespace App\Http\Requests\Payment_method;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(config('payment_methods.create'));
    }

    public function rules(): array
    {
        return [
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'bank_name.required' => 'Bank name is required',
            'bank_name.string' => 'Bank name must be a string',
            'bank_name.max' => 'Bank name must not exceed 255 characters',
            'account_number.required' => 'Account number is required',
            'account_number.string' => 'Account number must be a string',
            'account_number.max' => 'Account number must not exceed 255 characters',
            'account_name.required' => 'Account name is required',
            'account_name.string' => 'Account name must be a string',
            'account_name.max' => 'Account name must not exceed 255 characters',
            'is_active.boolean' => 'Active status must be a boolean',
        ];
    }
}
