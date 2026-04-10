<?php

namespace App\Http\Requests\Bus;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['sometimes', 'string', 'max:50', 'unique:buses,code,' . $this->route('bus')],
            'capacity' => ['sometimes', 'string', 'min:1'],
            'price_per_day' => ['sometimes', 'numeric', 'min:0'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Name must be a string',
            'name.max' => 'Name must not exceed 255 characters',
            'code.string' => 'Code must be a string',
            'code.max' => 'Code must not exceed 50 characters',
            'code.unique' => 'Code has already been taken',
            'capacity.string' => 'Capacity must be a string',
            'capacity.min' => 'Capacity must be at least 1',
            'price_per_day.numeric' => 'Price per day must be a number',
            'price_per_day.min' => 'Price per day must be at least 0',
            'image.string' => 'Image must be a string',
            'image.max' => 'Image must not exceed 255 characters',
            'description.string' => 'Description must be a string',
            'description.max' => 'Description must not exceed 255 characters',
            'is_active.boolean' => 'Is active must be a boolean',
        ];
    }
}
