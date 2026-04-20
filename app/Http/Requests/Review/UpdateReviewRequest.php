<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(config('reviews.update'));
    }

    public function rules(): array
    {
        return [
            'booking_id' => 'sometimes|exists:bookings,id',
            'customer_id' => 'sometimes|exists:customers,id',
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_publish' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.exists' => 'Booking does not exist',
            'customer_id.exists' => 'Customer does not exist',
            'rating.integer' => 'Rating must be an integer',
            'rating.min' => 'Rating must be at least 1',
            'rating.max' => 'Rating must not exceed 5',
            'comment.string' => 'Comment must be a string',
            'is_publish.boolean' => 'Publish status must be a boolean',
        ];
    }
}
