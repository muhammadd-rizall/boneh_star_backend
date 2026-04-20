<?php

namespace App\Http\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Usually created by system, but if via API check permission
    }

    public function rules(): array
    {
        return [
            'target_type' => 'required|in:admin,customer',
            'target_id' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|max:50',
            'reference_type' => 'nullable|string|max:255',
            'reference_id' => 'nullable|string',
        ];
    }
}
