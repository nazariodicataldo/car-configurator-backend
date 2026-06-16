<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[
    Fillable([
        'price',
        'color_id',
        'vehicle_id',
        'front_image_url',
        'back_image_url',
        'side_image_url',
    ]),
]
class ColorVehicle extends Pivot
{
    use HasUuids;

    protected $table = 'color_vehicles';

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(Configuration::class);
    }
}
