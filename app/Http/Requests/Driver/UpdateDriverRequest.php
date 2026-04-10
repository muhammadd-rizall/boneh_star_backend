<?php

namespace App\Http\Requests\Driver;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('drivers.update');
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
            'phone' => ['required', 'string', 'max:20'],
            'position' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama driver wajib diisi',
            'phone.required' => 'Nomor telepon wajib diisi',
            'phone.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter',
            'position.required' => 'Posisi wajib diisi',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status harus active atau inactive',
        ];
    }
}
