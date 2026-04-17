<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'quotation_id' => $this->quotation_id,
            'invoice_number' => $this->invoice_number,
            'total_amount' => $this->total_amount,
            'dp_paid' => $this->dp_paid,
            'remaining_amount' => $this->remaining_amount,
            'status' => $this->status,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'booking' => new BookingResource($this->whenLoaded('booking')),
            'quotation' => new QuotationResource($this->whenLoaded('quotation')),
        ];
    }
}
