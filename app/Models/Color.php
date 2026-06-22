<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'hex_code', 'img_url'])]
class Color extends Model
{
    use HasUuids;

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class)
            ->using(ColorVehicle::class)
            ->withPivot([
                'price',
                'front_image_url',
                'back_image_url',
                'side_image_url',
            ]);
    }
}
