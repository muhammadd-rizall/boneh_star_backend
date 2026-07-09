<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $images = $this->images ?? [];
        $imageUrls = array_map(function ($image) {
            return str_starts_with($image, 'http') ? $image : asset('storage/' . $image);
        }, $images);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
            'is_publish' => $this->is_publish,
            'images' => $images,
            'image_urls' => $imageUrls,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
