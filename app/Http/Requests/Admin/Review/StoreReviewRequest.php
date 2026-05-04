<?php

namespace App\Http\Requests\Admin\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(config('reviews.create'));
    }

    public function rules(): array
    {
        return [
            'booking_id' => 'required|exists:bookings,id',
            'customer_id' => 'required|exists:customers,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_publish' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'Booking ID is required',
            'booking_id.exists' => 'Booking does not exist',
            'customer_id.required' => 'Customer ID is required',
            'customer_id.exists' => 'Customer does not exist',
            'rating.required' => 'Rating is required',
            'rating.integer' => 'Rating must be an integer',
            'rating.min' => 'Rating must be at least 1',
            'rating.max' => 'Rating must not exceed 5',
            'comment.string' => 'Comment must be a string',
            'is_publish.boolean' => 'Publish status must be a boolean',
        ];
    }
}
