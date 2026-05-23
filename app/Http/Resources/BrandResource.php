<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'imgUrl' => $this->img_url,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'vehicles' => VehicleResource::collection(
                $this->whenLoaded('vehicles'),
            ),
        ];
    }
}
