<?php

namespace App\Http\Requests\Admin\Role;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Role::class);
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
            'slug' => ['required', 'string', 'max:255', 'unique:roles,slug', 'alpha_dash'],
            'is_active' => ['required', 'boolean'],
            'permissions_ids' => ['sometimes', 'array'],
            'permissions_ids.*' => ['exists:permissions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.max' => 'Name must not exceed 255 characters',
            'slug.required' => 'Slug is required',
            'slug.string' => 'Slug must be a string',
            'slug.max' => 'Slug must not exceed 255 characters',
            'slug.unique' => 'Slug has already been taken',
            'slug.alpha_dash' => 'Slug may only contain letters, numbers, dashes and underscores',
            'is_active.required' => 'Is active is required',
            'is_active.boolean' => 'Is active must be a boolean',
            'permissions_ids.array' => 'Permissions ids must be an array',
            'permissions_ids.*.exists' => 'Permission does not exist',
        ];
    }
}
