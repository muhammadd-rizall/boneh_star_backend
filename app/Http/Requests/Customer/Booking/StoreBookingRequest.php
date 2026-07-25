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
            'departure_date' => ['required', 'date'],
            'return_date' => ['nullable', 'date', 'after_or_equal:departure_date'],
            'origin' => ['required', 'string'],
            'full_route' => ['required', 'string'],
            'passenger_count' => ['required', 'integer', 'min:1'],
            'bus_count_requested' => ['required', 'integer', 'min:1'],
            'special_notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'activity_id.required' => 'Jenis aktivitas wajib dipilih.',
            'activity_id.exists' => 'Jenis aktivitas yang dipilih tidak valid.',
            'route_type_id.required' => 'Jenis rute wajib dipilih.',
            'route_type_id.exists' => 'Jenis rute yang dipilih tidak valid.',
            'departure_date.required' => 'Tanggal keberangkatan wajib diisi.',
            'departure_date.date' => 'Tanggal keberangkatan harus berupa tanggal yang valid.',
            'return_date.date' => 'Tanggal kepulangan harus berupa tanggal yang valid.',
            'return_date.after_or_equal' => 'Tanggal kepulangan harus setelah atau sama dengan tanggal keberangkatan.',
            'origin.required' => 'Asal keberangkatan wajib diisi.',
            'full_route.required' => 'Rute lengkap wajib diisi.',
            'passenger_count.required' => 'Jumlah penumpang wajib diisi.',
            'passenger_count.integer' => 'Jumlah penumpang harus berupa angka.',
            'passenger_count.min' => 'Jumlah penumpang minimal 1 orang.',
            'bus_count_requested.required' => 'Jumlah bus wajib diisi.',
            'bus_count_requested.integer' => 'Jumlah bus harus berupa angka.',
            'bus_count_requested.min' => 'Jumlah bus minimal 1 unit.',
        ];
    }
}
