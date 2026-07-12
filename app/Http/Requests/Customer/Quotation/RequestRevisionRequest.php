<?php

namespace App\Http\Requests\Customer\Quotation;

use Illuminate\Foundation\Http\FormRequest;

class RequestRevisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'revision_note' => ['required', 'string', 'min:3'],
        ];
    }
}
