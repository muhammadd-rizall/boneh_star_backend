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
        $params = $this->route() ? $this->route()->parameters() : [];
        $id = !empty($params) ? reset($params) : null;
        $id = is_object($id) ? $id->getKey() : $id;

        $uniqueRule = \Illuminate\Validation\Rule::unique('bus_facilities', 'name');
        if ($id && is_string($id) && trim($id) !== '') {
            $uniqueRule->ignore($id);
        }

        return [
            'name' => ['required', 'string', 'max:255', $uniqueRule],
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
