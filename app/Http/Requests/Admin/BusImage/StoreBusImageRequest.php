<?php

namespace App\Http\Requests\Admin\BusImage;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBusImageRequest extends FormRequest
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
            'bus_id' => ['required', 'exists:buses,id'],
            'images' => ['required', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:5120'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'bus_id.required' => 'Bus ID wajib diisi.',
            'bus_id.exists' => 'Bus tidak ditemukan.',
            'images.required' => 'Foto armada wajib diunggah.',
            'images.*.image' => 'File harus berupa gambar.',
            'images.*.mimes' => 'Format gambar harus berupa jpeg, png, jpg, gif, svg, atau webp.',
            'images.*.max' => 'Ukuran gambar tidak boleh melebihi 5MB.',
        ];
    }
}
