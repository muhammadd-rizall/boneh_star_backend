<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
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
            'user_id' => $this->user_id,
            'proposed_price' => $this->proposed_price,
            'initial_price' => $this->initial_price ?? $this->proposed_price,
            'dp_amount' => $this->dp_amount,
            'note_to_customer' => $this->note_to_customer,
            'status' => $this->status,
            'valid_until' => $this->valid_until?->toDateTimeString(),
            'revision_note' => $this->revision_note,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'booking' => new BookingResource($this->whenLoaded('booking')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
