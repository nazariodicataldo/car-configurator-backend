<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
//use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class Setup extends Model
{
    use HasUuids;

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'setup_vehicles')
            ->using(SetupVehicle::class)
            ->withPivot(['price']);
    }
}
