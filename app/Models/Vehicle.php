<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[
    Fillable([
        'name',
        'body_type',
        'seats',
        'base_price',
        'base_img_url',
        'brand_id',
    ]),
]
class Vehicle extends Model
{
    use HasUuids;

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function engines(): BelongsToMany
    {
        return $this->belongsToMany(Engine::class)->using(EngineVehicle::class)->withPivot(['price']);
    }

    public function setups(): BelongsToMany
    {
        return $this->belongsToMany(Setup::class)->using(SetupVehicle::class)->withPivot(['price']);
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class)->using(ColorVehicle::class)->withPivot(['price']);
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(Configuration::class);
    }
}
