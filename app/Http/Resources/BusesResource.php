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
        return [
            'id' => $this->id,
            'bus_category_id' => $this->bus_category_id,
            'plat_number' => $this->plat_number,
            'name' => $this->name,
            'seat_count' => $this->seat_count,
            'production_year' => $this->production_year,
            'brand' => $this->brand,
            'status' => $this->status,
            'notes' => $this->notes,
            'category' => new BusCategoriesResource($this->whenLoaded('category')),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image_path' => $image->image_path,
                        'url' => asset('storage/'.$image->image_path),
                        'is_primary' => $image->is_primary,
                    ];
                });
            }),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
