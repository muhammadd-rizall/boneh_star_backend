<?php

namespace App\Http\Requests\Admin\Booking;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('bookings.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'uuid', 'exists:customers,id'],
            'activity_id' => ['required', 'uuid', 'exists:activity_types,id'],
            'route_type_id' => ['required', 'uuid', 'exists:route_types,id'],
            'booking_code' => ['nullable', 'string', 'max:255', 'unique:bookings,booking_code'],
            'departure_date' => ['required', 'date'],
            'return_date' => ['nullable', 'date', 'after_or_equal:departure_date'],
            'origin' => ['required', 'string'],
            'full_route' => ['nullable', 'string'],
            'passenger_count' => ['required', 'integer', 'min:1'],
            'bus_count_requested' => ['required', 'integer', 'min:1'],
            'special_notes' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,quoted,confirmed,ongoing,completed,cancelled'],
            'buses' => ['nullable', 'array'],
            'buses.*.bus_id' => ['required', 'uuid', 'exists:buses,id'],
            'buses.*.driver_id' => ['required', 'uuid', 'exists:drivers,id'],
            'buses.*.seat_count' => ['required', 'integer', 'min:1'],
            'buses.*.assignment_notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer is required',
            'customer_id.uuid' => 'Customer ID must be a valid UUID',
            'customer_id.exists' => 'Customer does not exist',
            'activity_id.required' => 'Activity type is required',
            'activity_id.uuid' => 'Activity ID must be a valid UUID',
            'activity_id.exists' => 'Activity type does not exist',
            'route_type_id.required' => 'Route type is required',
            'route_type_id.uuid' => 'Route type ID must be a valid UUID',
            'route_type_id.exists' => 'Route type does not exist',
            'booking_code.required' => 'Booking code is required',
            'booking_code.unique' => 'Booking code has already been taken',
            'departure_date.required' => 'Departure date is required',
            'departure_date.date' => 'Departure date must be a valid date',
            'return_date.date' => 'Return date must be a valid date',
            'return_date.after_or_equal' => 'Return date must be after or equal to departure date',
            'origin.required' => 'Origin is required',
            'passenger_count.required' => 'Passenger count is required',
            'passenger_count.integer' => 'Passenger count must be an integer',
            'passenger_count.min' => 'Passenger count must be at least 1',
            'bus_count_requested.required' => 'Bus count requested is required',
            'bus_count_requested.integer' => 'Bus count requested must be an integer',
            'bus_count_requested.min' => 'Bus count requested must be at least 1',
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
            'buses.*.bus_id.required' => 'Bus is required for assignment',
            'buses.*.bus_id.exists' => 'Selected bus does not exist',
            'buses.*.driver_id.required' => 'Driver is required for assignment',
            'buses.*.driver_id.exists' => 'Selected driver does not exist',
            'buses.*.seat_count.required' => 'Seat count is required for assignment',
        ];
    }
}
