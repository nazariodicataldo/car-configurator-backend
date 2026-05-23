<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EngineResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'transmission' => $this->transmission,
            'consumption' => (float) $this->consumption,
            'emissions' => (float) $this->emissions,
            'power' => (float) $this->power,
            'fuel' => $this->fuel,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'price' => $this->whenPivotLoaded(
                'engine_vehicles',
                fn () => (float) $this->pivot->price,
            ),
            'vehicles' => VehicleResource::collection(
                $this->whenLoaded('vehicles'),
            ),
        ];
    }
}
