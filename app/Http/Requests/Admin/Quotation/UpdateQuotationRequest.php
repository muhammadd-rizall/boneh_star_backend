<?php

namespace App\Http\Requests\Admin\Quotation;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('quotations.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'uuid', 'exists:bookings,id'],
            'user_id' => ['required', 'uuid', 'exists:users,id'],
            'proposed_price' => ['required', 'numeric', 'min:0'],
            'dp_amount' => ['required', 'numeric', 'min:0'],
            'note_to_customer' => ['required', 'string'],
            'status' => ['required', 'in:draft,sent,accepted,revision,expired,rejected'],
            'valid_until' => ['required', 'date'],
            'revision_note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'Booking is required',
            'booking_id.exists' => 'Selected booking does not exist',
            'user_id.required' => 'User is required',
            'user_id.exists' => 'Selected user does not exist',
            'proposed_price.required' => 'Proposed price is required',
            'dp_amount.required' => 'DP amount is required',
            'note_to_customer.required' => 'Note to customer is required',
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
            'valid_until.required' => 'Validity date is required',
        ];
    }
}
