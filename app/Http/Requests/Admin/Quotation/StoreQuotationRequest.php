<?php

namespace App\Http\Requests\Admin\Quotation;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('quotations.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $booking = $this->booking_id ? \App\Models\Booking::find($this->booking_id) : null;
        $validUntilRules = ['required', 'date', 'after_or_equal:today'];
        if ($booking && $booking->departure_date) {
            $validUntilRules[] = 'before_or_equal:' . \Carbon\Carbon::parse($booking->departure_date)->format('Y-m-d');
        }

        return [
            'booking_id' => ['required', 'uuid', 'exists:bookings,id'],
            'user_id' => ['required', 'uuid', 'exists:users,id'],
            'proposed_price' => ['required', 'numeric', 'min:0'],
            'dp_amount' => ['required', 'numeric', 'min:0'],
            'note_to_customer' => ['required', 'string'],
            'status' => ['required', 'in:draft,sent,accepted,revision,expired,rejected'],
            'valid_until' => $validUntilRules,
            'revision_note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'Pemesanan wajib dipilih.',
            'booking_id.exists' => 'Pemesanan yang dipilih tidak terdaftar.',
            'user_id.required' => 'Pengguna wajib dipilih.',
            'user_id.exists' => 'Pengguna yang dipilih tidak terdaftar.',
            'proposed_price.required' => 'Harga penawaran wajib diisi.',
            'dp_amount.required' => 'Jumlah uang muka wajib diisi.',
            'note_to_customer.required' => 'Catatan untuk pelanggan wajib diisi.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status yang dipilih tidak valid.',
            'valid_until.required' => 'Tanggal berlaku wajib diisi.',
            'valid_until.after_or_equal' => 'Tanggal berlaku tidak boleh di masa lalu.',
            'valid_until.before_or_equal' => 'Masa berlaku penawaran tidak boleh melebihi tanggal keberangkatan pesanan.',
        ];
    }
}
