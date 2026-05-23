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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'vehiclePrice' => (float) $this->vehicle_price,
            'enginePrice' => (float) $this->engine_price,
            'setupPrice' => (float) $this->setup_price,
            'colorPrice' => (float) $this->color_price,
            'totalPrice' => (float) $this->total_price,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'engine' => new EngineResource($this->whenLoaded('engine')),
            'setup' => new SetupResource($this->whenLoaded('setup')),
            'color' => new ColorResource($this->whenLoaded('color')),
            'optionals' => OptionalResource::collection(
                $this->whenLoaded('optionals'),
            ),
        ];
    }
}
