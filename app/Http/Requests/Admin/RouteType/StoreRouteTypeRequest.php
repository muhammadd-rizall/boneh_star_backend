<?php

namespace App\Http\Requests\Admin\RouteType;

use Illuminate\Foundation\Http\FormRequest;

class StoreRouteTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('routes.create');
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
            'scope' => ['required', 'in:inside,outside'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'scope.required' => 'Scope is required',
            'scope.in' => 'Invalid scope selected',
            'description.required' => 'Description is nullable',
        ];
    }
}
