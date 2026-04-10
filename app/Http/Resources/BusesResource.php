<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'capacity' => $this->capacity,
            'price_per_day' => $this->price_per_day,
            'image' => $this->image,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
