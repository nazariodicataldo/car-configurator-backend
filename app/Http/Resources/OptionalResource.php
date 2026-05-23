<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptionalResource extends JsonResource
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
                'category' => $this->category,
                'description' => $this->description,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
                'setups' => SetupResource::collection($this->whenLoaded('setups')),
                'configurations' => ConfigurationResource::collection(
                    $this->whenLoaded('configurations'),
                ),
                'compatibilyRules' => OptionalResource::collection(
                    $this->whenLoaded('compatibilyRules'),
                ),
                'compatibleWithMe' => OptionalResource::collection(
                    $this->whenLoaded('compatibleWithMe'),
                ),
            ],
            $this->whenPivotLoaded(
                'optional_setups',
                fn () => [
                    'price' => (float) $this->pivot->price,
                    'isIncluded' => (bool) $this->pivot->is_included,
                ],
                [],
            ),
            $this->whenPivotLoaded(
                'configuration_optionals',
                fn () => [
                    'optionalPrice' => (float) $this->pivot->optional_price,
                    'isIncluded' => (bool) $this->pivot->is_included,
                ],
                [],
            ),
        );
    }
}
