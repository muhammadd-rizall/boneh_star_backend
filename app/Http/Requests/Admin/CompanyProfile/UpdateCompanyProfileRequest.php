<?php

namespace App\Http\Requests\Admin\CompanyProfile;

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
            'company_name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string',
            'whatsapp' => 'nullable|array',
            'whatsapp.*' => 'required|string|max:20',
            'social_media' => 'nullable|array',
            'social_media.*.platform' => 'required|string|max:50',
            'social_media.*.url' => 'required|url|max:255',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',

            // Flags for clearing data
            'remove_logo' => 'nullable|boolean',
            'clear_whatsapp' => 'nullable|boolean',
            'clear_social_media' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'whatsapp.array' => 'WhatsApp numbers must be an array',
        ];
    }
}
