<?php

namespace App\Http\Requests\Customer\Booking;

use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method_id' => ['required', 'uuid', 'exists:payment_methods,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_type' => ['required', 'string', 'in:dp,settlement'],
            'payment_date' => ['required', 'date'],
            'transfer_proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}
