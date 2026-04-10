<?php

namespace App\Http\Requests\Booking;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('bookings.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_code' => ['required', 'string', 'max:50', 'unique:bookings,booking_code'],
            'customer_id' => ['required', 'string', 'exists:customers,id'],
            'trip_type' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'total_price' => ['required', 'numeric', 'min:0'],
            'payment_status' => ['required', 'in:unpaid,dp_paid,paid'],
            'status' => ['required', 'in:request,waiting,approved,completed,cancelled'],
            'approved_by' => ['nullable', 'string', 'exists:users,id'],
            'approved_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_code.required' => 'Booking code wajib diisi',
            'booking_code.unique' => 'Booking code sudah digunakan',
            'customer_id.required' => 'Customer wajib dipilih',
            'customer_id.exists' => 'Customer tidak ditemukan',
            'trip_type.required' => 'Tipe perjalanan wajib diisi',
            'destination.required' => 'Destinasi wajib diisi',
            'pickup_location.required' => 'Lokasi jemput wajib diisi',
            'total_price.required' => 'Total harga wajib diisi',
            'total_price.numeric' => 'Total harga harus berupa angka',
            'payment_status.required' => 'Status pembayaran wajib diisi',
            'payment_status.in' => 'Status pembayaran tidak valid',
            'status.required' => 'Status booking wajib diisi',
            'status.in' => 'Status booking tidak valid',
            'approved_by.exists' => 'User penyetuju tidak ditemukan',
            'approved_at.date' => 'Format tanggal persetujuan tidak valid',
        ];
    }
}
