<?php

namespace App\Http\Requests\Admin\BusFacility;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBusFacilitiesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('facilities.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:bus_facilities,name,'.$this->route('id')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama fasilitas harus diisi.',
            'name.unique' => 'Nama fasilitas sudah ada.',
        ];
    }
}
