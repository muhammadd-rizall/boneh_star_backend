<?php

namespace App\Http\Requests\Customer\Review;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['sometimes', 'required', 'integer', 'min:1', 'max:5'],
            'comment' => ['sometimes', 'required', 'string', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Penilaian wajib diisi.',
            'rating.integer' => 'Penilaian harus berupa angka bulat.',
            'rating.min' => 'Penilaian minimal 1 bintang.',
            'rating.max' => 'Penilaian maksimal 5 bintang.',
            'comment.required' => 'Ulasan wajib diisi.',
            'comment.min' => 'Ulasan minimal 1 karakter.',
        ];
    }
}
