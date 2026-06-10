<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable(['price', 'setup_id', 'vehicle_id'])]
class SetupVehicle extends Pivot
{
    use HasUuids;

    protected $table = 'setup_vehicles';

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function setup(): BelongsTo
    {
        return $this->belongsTo(Setup::class);
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(Configuration::class);
    }

    public function optionals(): BelongsToMany
    {
        return $this->belongsToMany(
            Optional::class,
            'optional_setups',
            'setup_vehicle_id',
            'optional_id',
        )
            ->using(OptionalSetup::class)
            ->withPivot(['price', 'is_included']);
    }
}
