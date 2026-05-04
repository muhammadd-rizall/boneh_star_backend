<?php

namespace App\Http\Requests\Admin\Bus;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBusesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('buses.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bus_category_id' => ['required', 'uuid', 'exists:bus_categories,id'],
            'plat_number' => ['required', 'string', 'max:255', 'unique:buses,plat_number'],
            'name' => ['required', 'string', 'max:255'],
            'seat_count' => ['required', 'integer', 'min:1'],
            'production_year' => ['required', 'integer', 'min:1900', 'max:'.date('Y')],
            'brand' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:available,booked,maintenance,inactive'],
            'notes' => ['nullable', 'string'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['uuid', 'exists:bus_facilities,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'bus_category_id.required' => 'Bus category is required',
            'bus_category_id.uuid' => 'Invalid bus category format',
            'bus_category_id.exists' => 'Bus category does not exist',
            'plat_number.required' => 'Plat number is required',
            'plat_number.unique' => 'Plat number has already been taken',
            'name.required' => 'Name is required',
            'seat_count.required' => 'Seat count is required',
            'production_year.required' => 'Production year is required',
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
            'facilities.*.uuid' => 'Invalid facility format',
            'facilities.*.exists' => 'Facility does not exist',
            'images.array' => 'Images must be an array',
            'images.*.image' => 'File must be an image',
            'images.*.mimes' => 'Image must be a type of: jpeg, png, jpg, gif, svg',
            'images.*.max' => 'Image size must not exceed 2048KB',
        ];
    }
}
