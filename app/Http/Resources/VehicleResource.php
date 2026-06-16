<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            [
                'id' => $this->id,
                'name' => $this->name,
                'bodyType' => $this->body_type,
                'seats' => (int) $this->seats,
                'basePrice' => (float) $this->base_price,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
                'brand' => new BrandResource($this->whenLoaded('brand')),
                'defaultColor' => new ColorResource(
                    $this->colors->firstWhere('id', $this->default_color_id),
                ),
                'engines' => EngineResource::collection(
                    $this->whenLoaded('engines'),
                ),
                'setups' => SetupResource::collection(
                    $this->whenLoaded('setups'),
                ),
                'colors' => ColorResource::collection(
                    $this->whenLoaded('colors'),
                ),
                'configurations' => ConfigurationResource::collection(
                    $this->whenLoaded('configurations'),
                ),
            ],
            $this->whenPivotLoaded(
                'engine_vehicles',
                fn() => ['price' => (float) $this->pivot->price],
                [],
            ),
            $this->whenPivotLoaded(
                'setup_vehicles',
                fn() => ['price' => (float) $this->pivot->price],
                [],
            ),
            $this->whenPivotLoaded(
                'color_vehicles',
                fn() => ['price' => (float) $this->pivot->price],
                [],
            ),
        );
    }
}
