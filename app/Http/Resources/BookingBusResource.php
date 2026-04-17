<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingBusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'bus_id' => $this->bus_id,
            'driver_id' => $this->driver_id,
            'seat_count' => $this->seat_count,
            'assignment_notes' => $this->assignment_notes,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'bus' => new BusesResource($this->whenLoaded('bus')),
            'driver' => new DriverResource($this->whenLoaded('driver')),
        ];
    }
}
