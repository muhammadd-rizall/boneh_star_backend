<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $params = $this->route() ? $this->route()->parameters() : [];
        $id = !empty($params) ? reset($params) : null;
        $id = is_object($id) ? $id->getKey() : $id;

        $uniqueRule = Rule::unique('users', 'email');
        if ($id && is_string($id) && trim($id) !== '') {
            $uniqueRule->ignore($id);
        }

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', $uniqueRule],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'role_ids' => ['sometimes', 'array', 'min:1'],
            'role_ids.*' => ['exists:roles,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Name must be a string',
            'name.max' => 'Name must not exceed 255 characters',
            'email.string' => 'Email must be a string',
            'email.email' => 'Email must be a valid email address',
            'email.max' => 'Email must not exceed 255 characters',
            'email.unique' => 'Email has already been taken',
            'password.string' => 'Password must be a string',
            'password.min' => 'Password must be at least 8 characters',
            'role_ids.array' => 'Roles must be an array',
            'role_ids.*.exists' => 'One or more selected roles do not exist',
        ];
    }
}
