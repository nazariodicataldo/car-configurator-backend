<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfigurationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /* 'color' => $this->when($this->color_id, function () {
    if ($this->relationLoaded('vehicle') && $this->vehicle->relationLoaded('colors')) {
        $color = $this->vehicle->colors->firstWhere('id', $this->color_id);
    } else {
        $color = $this->vehicle->colorWithPivot($this->color_id);
    }

    if (!$color) return null;

    $color->is_default = $color->id === $this->vehicle->default_color_id;
    if ($color->is_default) $color->pivot->price = 0;

    return new ColorResource($color);
}), */

        $color = $this->vehicle?->relationLoaded('colors')
            ? $this->vehicle->colors->firstWhere('id', $this->color_id)
            : $this->vehicle
                ?->colors()
                ->where('colors.id', $this->color_id)
                ->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'vehiclePrice' => (float) $this->vehicle_price,
            'enginePrice' => (float) $this->engine_price,
            'setupPrice' => (float) $this->setup_price,
            'colorPrice' => (float) $this->color_price,
            'totalOptionalPrice' => (float) $this->total_optional_price,
            'totalPrice' => (float) $this->total_price,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'vehicle' => new VehicleResource($this->vehicle),
            'engine' => new EngineResource($this->engine),
            'setup' => new SetupResource($this->setup),
            'color' => new ColorResource($color),
            'optionals' => OptionalResource::collection($this->optionals),
        ];
    }
}
