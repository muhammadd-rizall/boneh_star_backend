<?php

namespace App\Http\Requests\CompanyProfile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|array|min:1',
            'phone.*' => 'required|string|max:20',
            'whatsapp' => 'required|array|min:1',
            'whatsapp.*' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'At least one phone number is required',
            'phone.array' => 'Phone numbers must be an array',
            'whatsapp.required' => 'At least one WhatsApp number is required',
            'whatsapp.array' => 'WhatsApp numbers must be an array',
        ];
    }
}
