<?php

namespace App\Http\Requests\Admin\Transaction;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('transactions.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $params = $this->route() ? $this->route()->parameters() : [];
        $id = !empty($params) ? reset($params) : null;
        $id = is_object($id) ? $id->getKey() : $id;

        $uniqueRule = \Illuminate\Validation\Rule::unique('transactions', 'invoice_number');
        if ($id && is_string($id) && trim($id) !== '') {
            $uniqueRule->ignore($id);
        }

        return [
            'booking_id' => ['required', 'uuid', 'exists:bookings,id'],
            'quotation_id' => ['required', 'uuid', 'exists:quotations,id'],
            'invoice_number' => ['nullable', 'string', 'max:255', $uniqueRule],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'dp_paid' => ['required', 'numeric', 'min:0'],
            'remaining_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:waiting,dp_paid,fully_paid,overdue'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'Booking is required',
            'booking_id.exists' => 'Selected booking does not exist',
            'quotation_id.required' => 'Quotation is required',
            'quotation_id.exists' => 'Selected quotation does not exist',
            'invoice_number.required' => 'Invoice number is required',
            'invoice_number.unique' => 'Invoice number already exists',
            'total_amount.required' => 'Total amount is required',
            'dp_paid.required' => 'DP paid is required',
            'remaining_amount.required' => 'Remaining amount is required',
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
        ];
    }
}
