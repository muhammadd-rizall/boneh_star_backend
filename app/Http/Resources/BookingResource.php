<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'activity_id' => $this->activity_id,
            'route_type_id' => $this->route_type_id,
            'booking_code' => $this->booking_code,
            'departure_date' => $this->departure_date?->toDateTimeString(),
            'return_date' => $this->return_date?->toDateTimeString(),
            'origin' => $this->origin,
            'full_route' => $this->full_route,
            'passenger_count' => $this->passenger_count,
            'bus_count_requested' => $this->bus_count_requested,
            'special_notes' => $this->special_notes,
            'status' => $this->status,
            'cancelled_by' => $this->cancelled_by,
            'cancelled_reason' => $this->cancelled_reason,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'activity' => new ActivityTypeResource($this->whenLoaded('activity')),
            'route_type' => new RouteTypeResource($this->whenLoaded('routeType')),
            'booking_buses' => BookingBusResource::collection($this->whenLoaded('bookingBuses')),
            'quotation' => $this->whenLoaded('quotation'),
            'transaction' => $this->whenLoaded('transaction'),
            'review' => $this->whenLoaded('review'),
        ];
    }
}
