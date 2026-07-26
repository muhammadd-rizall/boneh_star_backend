<?php

namespace App\Http\Requests\Admin\BusCategory;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBusCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('categories.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'min_capacity' => ['required', 'integer', 'min:1'],
            'max_capacity' => ['required', 'integer', 'min:1', 'gte:min_capacity'],
            'price_inside_min' => ['nullable', 'integer', 'min:0'],
            'price_inside_max' => ['nullable', 'integer', 'min:0', 'gte:price_inside_min'],
            'price_outside' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori bus harus diisi.',
            'min_capacity.required' => 'Kapasitas minimal harus diisi.',
            'min_capacity.integer' => 'Kapasitas minimal harus berupa angka.',
            'max_capacity.required' => 'Kapasitas maksimal harus diisi.',
            'max_capacity.integer' => 'Kapasitas maksimal harus berupa angka.',
            'max_capacity.gte' => 'Kapasitas maksimal tidak boleh kurang dari kapasitas minimal.',
        ];
    }
}
