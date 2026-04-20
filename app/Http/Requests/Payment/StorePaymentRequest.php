<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(config('payments.create'));
    }

    public function rules(): array
    {
        return [
            'transaction_id' => 'required|exists:transactions,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:dp,settlement',
            'payment_date' => 'required|date',
            'transfer_proof' => 'required|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_id.required' => 'Transaction ID is required',
            'transaction_id.exists' => 'Transaction does not exist',
            'payment_method_id.required' => 'Payment method is required',
            'payment_method_id.exists' => 'Payment method does not exist',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'amount.min' => 'Amount must be at least 0',
            'payment_type.required' => 'Payment type is required',
            'payment_type.in' => 'Invalid payment type selected (dp or settlement)',
            'payment_date.required' => 'Payment date is required',
            'payment_date.date' => 'Invalid payment date format',
            'transfer_proof.required' => 'Transfer proof is required',
            'transfer_proof.image' => 'Transfer proof must be an image',
            'transfer_proof.max' => 'Transfer proof size must not exceed 2048KB',
        ];
    }
}
