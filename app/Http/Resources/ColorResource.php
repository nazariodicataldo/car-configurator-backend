<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'hexCode' => $this->hex_code,
            'imgUrl' => $this->img_url,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'price' => $this->whenPivotLoaded(
                'color_vehicles',
                fn () => (float) $this->pivot->price,
            ),
            'vehicles' => VehicleResource::collection(
                $this->whenLoaded('vehicles'),
            ),
        ];
    }
}
