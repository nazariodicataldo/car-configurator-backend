<?php

namespace App\Models;

use App\Models\Engine as ModelsEngine;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable(['price', 'engine_id', 'vehicle_id'])]
class EngineVehicle extends Pivot
{
    use HasUuids;

    protected $table = 'engine_vehicles';

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function engine(): BelongsTo
    {
        return $this->belongsTo(ModelsEngine::class);
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(Configuration::class);
    }
}
