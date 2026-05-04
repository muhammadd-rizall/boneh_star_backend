<?php

namespace App\Http\Requests\Customer\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'activity_id' => ['required', 'uuid', 'exists:activity_types,id'],
            'route_type_id' => ['required', 'uuid', 'exists:route_types,id'],
            'departure_date' => ['required', 'date', 'after:now'],
            'return_date' => ['required', 'date', 'after:departure_date'],
            'origin' => ['required', 'string'],
            'full_route' => ['required', 'string'],
            'passenger_count' => ['required', 'integer', 'min:1'],
            'bus_count_requested' => ['required', 'integer', 'min:1'],
            'special_notes' => ['nullable', 'string'],
        ];
    }
}
