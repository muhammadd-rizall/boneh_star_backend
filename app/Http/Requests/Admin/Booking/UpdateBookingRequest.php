<?php

namespace App\Http\Requests\Admin\Booking;

use App\Rules\BusAvailabilityRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('bookings.update');
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $params = $this->route() ? $this->route()->parameters() : [];
        $id = !empty($params) ? reset($params) : null;
        $id = is_object($id) ? $id->getKey() : $id;

        $this->merge([
            'id' => ($id && is_string($id) && trim($id) !== '') ? $id : null,
        ]);
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

        $uniqueRule = \Illuminate\Validation\Rule::unique('bookings', 'booking_code');
        if ($id && is_string($id) && trim($id) !== '') {
            $uniqueRule->ignore($id);
        }

        return [
            'id' => ['required', 'uuid', 'exists:bookings,id'],
            'customer_id' => ['required', 'uuid', 'exists:customers,id'],
            'activity_id' => ['required', 'uuid', 'exists:activity_types,id'],
            'route_type_id' => ['required', 'uuid', 'exists:route_types,id'],
            'booking_code' => ['nullable', 'string', 'max:255', $uniqueRule],
            'departure_date' => ['required', 'date'],
            'return_date' => ['nullable', 'date', 'after_or_equal:departure_date'],
            'origin' => ['required', 'string'],
            'full_route' => ['nullable', 'string'],
            'passenger_count' => ['required', 'integer', 'min:1'],
            'bus_count_requested' => ['required', 'integer', 'min:1'],
            'special_notes' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,quoted,confirmed,ongoing,completed,cancelled'],
            'cancelled_by' => ['nullable', 'in:customer,admin'],
            'cancelled_reason' => ['nullable', 'string'],
            'buses' => ['nullable', 'array'],
            'buses.*.bus_id' => ['required', 'uuid', 'exists:buses,id', new BusAvailabilityRule],
            'buses.*.driver_id' => ['required', 'uuid', 'exists:drivers,id'],
            'buses.*.seat_count' => ['required', 'integer', 'min:1'],
            'buses.*.assignment_notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Pelanggan wajib dipilih.',
            'customer_id.uuid' => 'ID Pelanggan harus berupa UUID yang valid.',
            'customer_id.exists' => 'Pelanggan yang dipilih tidak terdaftar.',
            'activity_id.required' => 'Jenis aktivitas wajib dipilih.',
            'activity_id.uuid' => 'ID Aktivitas harus berupa UUID yang valid.',
            'activity_id.exists' => 'Jenis aktivitas tidak terdaftar.',
            'route_type_id.required' => 'Jenis rute wajib dipilih.',
            'route_type_id.uuid' => 'ID Jenis rute harus berupa UUID yang valid.',
            'route_type_id.exists' => 'Jenis rute tidak terdaftar.',
            'booking_code.required' => 'Kode booking wajib diisi.',
            'booking_code.unique' => 'Kode booking sudah digunakan.',
            'departure_date.required' => 'Tanggal keberangkatan wajib diisi.',
            'departure_date.date' => 'Tanggal keberangkatan harus berupa tanggal yang valid.',
            'return_date.date' => 'Tanggal kepulangan harus berupa tanggal yang valid.',
            'return_date.after_or_equal' => 'Tanggal kepulangan harus setelah atau sama dengan tanggal keberangkatan.',
            'origin.required' => 'Asal keberangkatan wajib diisi.',
            'passenger_count.required' => 'Jumlah penumpang wajib diisi.',
            'passenger_count.integer' => 'Jumlah penumpang harus berupa angka.',
            'passenger_count.min' => 'Jumlah penumpang minimal 1 orang.',
            'bus_count_requested.required' => 'Jumlah bus wajib diisi.',
            'bus_count_requested.integer' => 'Jumlah bus harus berupa angka.',
            'bus_count_requested.min' => 'Jumlah bus minimal 1 unit.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status yang dipilih tidak valid.',
            'cancelled_by.in' => 'Pembatal oleh tidak valid.',
            'buses.*.bus_id.required' => 'Bus wajib dipilih untuk alokasi.',
            'buses.*.bus_id.exists' => 'Bus yang dipilih tidak ditemukan.',
            'buses.*.driver_id.required' => 'Supir wajib dipilih untuk alokasi.',
            'buses.*.driver_id.exists' => 'Supir yang dipilih tidak ditemukan.',
            'buses.*.seat_count.required' => 'Jumlah kursi wajib diisi.',
        ];
    }
}
