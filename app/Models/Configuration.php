<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

#[
    Fillable([
        'name',
        'total_price',
        'user_id',
        'vehicle_id',
        'engine_vehicle_id',
        'setup_vehicle_id',
        'color_vehicle_id',
    ]),
]
class Configuration extends Model
{
    use HasUuids;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function engine(): HasOneThrough
    {
        return $this->hasOneThrough(Engine::class, EngineVehicle::class);
    }

    public function setup(): HasOneThrough
    {
        return $this->hasOneThrough(Setup::class, SetupVehicle::class);
    }

    public function color(): HasOneThrough
    {
        return $this->hasOneThrough(Color::class, ColorVehicle::class);
    }

    public function optionalSetups(): BelongsToMany
    {
        return $this->belongsToMany(
            OptionalSetup::class,
            'configuration_optionals',
        );
    }

    public function optionals(): BelongsToMany {
        return $this->optionalSetups()->optionals();
    }
}
