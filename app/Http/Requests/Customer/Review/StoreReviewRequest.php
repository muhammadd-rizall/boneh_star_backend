<?php

namespace App\Http\Requests\Customer\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'uuid', 'exists:bookings,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'ID pemesanan wajib diisi.',
            'booking_id.exists' => 'ID pemesanan tidak ditemukan.',
            'rating.required' => 'Penilaian wajib diisi.',
            'rating.integer' => 'Penilaian harus berupa angka bulat.',
            'rating.min' => 'Penilaian minimal 1 bintang.',
            'rating.max' => 'Penilaian maksimal 5 bintang.',
            'comment.required' => 'Ulasan wajib diisi.',
            'comment.min' => 'Ulasan minimal 1 karakter.',
        ];
    }
}
