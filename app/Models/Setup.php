<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
//use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class Setup extends Model
{
    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class)
            ->using(SetupVehicle::class)
            ->withPivot(['price']);
    }

    public function optionals(): BelongsToMany
    {
        return $this->belongsToMany(Optional::class)
            ->using(SetupVehicle::class)
            ->withPivot(['price', 'is_included']);
    }

    /* public function includedOptionals(): HasMany {
        
    } */
}
