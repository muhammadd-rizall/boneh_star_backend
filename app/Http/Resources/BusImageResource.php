<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isUrl = str_starts_with($this->image_path, 'http');

        return [
            'id' => $this->id,
            'bus_id' => $this->bus_id,
            'image_path' => $this->image_path,
            'url' => $isUrl ? $this->image_path : asset('storage/'.$this->image_path),
            'caption' => $this->caption,
            'is_primary' => $this->is_primary,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
