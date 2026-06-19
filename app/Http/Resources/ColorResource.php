<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'imgUrl' => $this->img_url ? Storage::url($this->img_url) : null,
            'isDefault' => (bool) $this->is_default,
            'frontImageUrl' => $this->whenPivotLoaded(
                'color_vehicles',
                fn() => $this->pivot->front_image_url
                    ? Storage::url($this->pivot->front_image_url)
                    : $this->pivot->front_image_url,
            ),
            'sideImageUrl' => $this->whenPivotLoaded(
                'color_vehicles',
                fn() => $this->pivot->side_image_url
                    ? Storage::url($this->pivot->side_image_url)
                    : null,
            ),
            'backImageUrl' => $this->whenPivotLoaded(
                'color_vehicles',
                fn() => $this->pivot->back_image_url
                    ? Storage::url($this->pivot->back_image_url)
                    : null,
            ),
            'price' => $this->whenPivotLoaded(
                'color_vehicles',
                fn() => (float) $this->pivot->price,
            ),
            'vehicles' => VehicleResource::collection(
                $this->whenLoaded('vehicles'),
            ),
        ];
    }
}
