<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_name' => $this->company_name,
            'address' => $this->address,
            'whatsapp' => $this->whatsapp,
            'social_media' => $this->social_media,
            'email' => $this->email,
            'description' => $this->description,
            'logo' => $this->logo ? (strpos($this->logo, 'http') === 0 ? $this->logo : asset('storage/'.$this->logo)) : null,
            'updated_at' => $this->updated_at,
        ];
    }
}
