<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class SyncPermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermissions('permissions.manage');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'permissions_ids' => ['required', 'array'],
            'permissions_ids.*' => ['exists:permissions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'permissions_ids.required' => 'Permissions ids are required',
            'permissions_ids.array' => 'Permissions ids must be an array',
            'permissions_ids.*.exists' => 'Permission does not exist',
        ];
    }
}
