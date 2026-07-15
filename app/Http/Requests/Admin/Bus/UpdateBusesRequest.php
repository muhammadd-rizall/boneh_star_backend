<?php

namespace App\Http\Requests\Admin\Bus;

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
        $params = $this->route() ? $this->route()->parameters() : [];
        $id = !empty($params) ? reset($params) : null;
        $id = is_object($id) ? $id->getKey() : $id;

        $uniqueRule = \Illuminate\Validation\Rule::unique('buses', 'plat_number');
        if ($id && is_string($id) && trim($id) !== '') {
            $uniqueRule->ignore($id);
        }

        return [
            'bus_category_id' => ['sometimes', 'uuid', 'exists:bus_categories,id'],
            'plat_number' => ['sometimes', 'string', 'max:255', $uniqueRule],
            'name' => ['sometimes', 'string', 'max:255'],
            'seat_count' => ['sometimes', 'integer', 'min:1'],
            'brand' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'in:available,booked,maintenance,inactive'],
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
            'bus_category_id.uuid' => 'Invalid bus category format',
            'bus_category_id.exists' => 'Bus category does not exist',
            'plat_number.unique' => 'Plat number has already been taken',
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
