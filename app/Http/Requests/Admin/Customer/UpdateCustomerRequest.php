<?php

namespace App\Http\Requests\Admin\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('customers.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $params = $this->route() ? $this->route()->parameters() : [];
        $customerId = !empty($params) ? reset($params) : null;
        $customerId = is_object($customerId) ? $customerId->getKey() : $customerId;

        $uniqueRule = Rule::unique('customers', 'email');
        if ($customerId && is_string($customerId) && trim($customerId) !== '') {
            $uniqueRule->ignore($customerId);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                $uniqueRule,
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'profile_picture' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'Email has already been taken.',
            'password.min' => 'Password must be at least 8 characters.',
        ];
    }
}
