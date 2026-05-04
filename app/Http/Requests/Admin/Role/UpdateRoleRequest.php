<?php

namespace App\Http\Requests\Admin\Role;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('role'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:roles,slug,'.$this->route('role')->id, 'alpha_dash'],
            'is_active' => ['sometimes', 'boolean'],
            'permissions_ids' => ['sometimes', 'array'],
            'permissions_ids.*' => ['exists:permissions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Name must be a string',
            'name.max' => 'Name must not exceed 255 characters',
            'slug.string' => 'Slug must be a string',
            'slug.max' => 'Slug must not exceed 255 characters',
            'slug.unique' => 'Slug has already been taken',
            'slug.alpha_dash' => 'Slug may only contain letters, numbers, dashes and underscores',
            'is_active.boolean' => 'Is active must be a boolean',
            'permissions_ids.array' => 'Permissions ids must be an array',
            'permissions_ids.*.exists' => 'Permission does not exist',
        ];
    }
}
