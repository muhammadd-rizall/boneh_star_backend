<?php

namespace App\Http\Requests\Bus;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBusesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('buses.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bus_category_id' => ['sometimes', 'exists:bus_categories,id'],
            'plat_number' => ['sometimes', 'string', 'max:255', 'unique:buses,plat_number,'.$this->route('bus')],
            'name' => ['sometimes', 'string', 'max:255'],
            'seat_count' => ['sometimes', 'integer', 'min:1'],
            'production_year' => ['sometimes', 'integer', 'min:1900', 'max:'.date('Y')],
            'brand' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'in:available,booked,maintenance,inactive'],
            'notes' => ['nullable', 'string'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['exists:bus_facilities,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'bus_category_id.exists' => 'Bus category does not exist',
            'plat_number.unique' => 'Plat number has already been taken',
            'status.in' => 'Invalid status selected',
            'images.array' => 'Images must be an array',
            'images.*.image' => 'File must be an image',
            'images.*.mimes' => 'Image must be a type of: jpeg, png, jpg, gif, svg',
            'images.*.max' => 'Image size must not exceed 2048KB',
        ];
    }
}
