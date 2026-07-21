<?php

namespace App\Http\Requests\Customer\Booking;

use App\Rules\BeforeDepartureDate;
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
            'payment_date' => [
                'required',
                'date',
                new BeforeDepartureDate($this->route('id')),
            ],
            'transfer_proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method_id.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method_id.exists' => 'Metode pembayaran tidak valid.',
            'amount.required' => 'Jumlah pembayaran wajib diisi.',
            'amount.numeric' => 'Jumlah pembayaran harus berupa angka.',
            'amount.min' => 'Jumlah pembayaran minimal Rp 1.',
            'payment_type.required' => 'Tipe pembayaran wajib dipilih.',
            'payment_type.in' => 'Tipe pembayaran harus berupa uang muka (dp) atau pelunasan (settlement).',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
            'payment_date.date' => 'Tanggal pembayaran harus berupa tanggal yang valid.',
            'transfer_proof.required' => 'Bukti transfer wajib diunggah.',
            'transfer_proof.image' => 'Bukti transfer harus berupa gambar.',
            'transfer_proof.mimes' => 'Bukti transfer harus berformat: jpeg, png, atau jpg.',
            'transfer_proof.max' => 'Ukuran bukti transfer maksimal 2 MB.',
        ];
    }
}
