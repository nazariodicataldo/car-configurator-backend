<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompatibilityRuleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'optionalA' => new OptionalResource($this->whenLoaded('optionalA')),
            'optionalB' => new OptionalResource($this->whenLoaded('optionalB')),
        ];
    }
}
