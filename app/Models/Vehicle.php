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
        'default_color_id',
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
        return $this->belongsToMany(Engine::class, 'engine_vehicles')
            ->using(EngineVehicle::class)
            ->withPivot(['price']);
    }

    public function setups(): BelongsToMany
    {
        return $this->belongsToMany(Setup::class, 'setup_vehicles')
            ->using(SetupVehicle::class)
            ->withPivot(['price']);
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'color_vehicles')
            ->using(ColorVehicle::class)
            ->withPivot([
                'price',
                'front_image_url',
                'back_image_url',
                'side_image_url',
            ]);
    }

    public function colorWithPivot(string $colorId): Color
    {
        $color = $this->colors()
            ->withPivot([
                'price',
                'front_image_url',
                'side_image_url',
                'back_image_url',
            ])
            ->where('colors.id', $colorId)
            ->firstOrFail();

        $color->is_default = $color->id === $this->default_color_id;

        if ($color->is_default) {
            $color->pivot->price = 0;
        }

        return $color;
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(Configuration::class);
    }
}
