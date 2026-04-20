<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(config('Permission.payments.update'));
    }

    public function rules(): array
    {
        return [
            'transaction_id' => 'sometimes|exists:transactions,id',
            'payment_method_id' => 'sometimes|exists:payment_methods,id',
            'amount' => 'sometimes|numeric|min:0',
            'payment_type' => 'sometimes|in:dp,settlement',
            'payment_date' => 'sometimes|date',
            'transfer_proof' => 'sometimes|image|max:2048',
            'status' => 'sometimes|in:pending,verified,rejected',
            'rejection_reason' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_id.exists' => 'Transaction does not exist',
            'payment_method_id.exists' => 'Payment method does not exist',
            'amount.numeric' => 'Amount must be a number',
            'amount.min' => 'Amount must be at least 0',
            'payment_type.in' => 'Invalid payment type selected (dp or settlement)',
            'payment_date.date' => 'Invalid payment date format',
            'transfer_proof.image' => 'Transfer proof must be an image',
            'transfer_proof.max' => 'Transfer proof size must not exceed 2048KB',
            'status.in' => 'Invalid status selected',
        ];
    }
}
