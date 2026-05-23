<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[
    Fillable([
        'name',
        'user_id',
        'vehicle_id',
        'vehicle_price',
        'engine_id',
        'engine_price',
        'setup_id',
        'setup_price',
        'color_id',
        'color_price',
    ]),
]
class Configuration extends Model
{
    use HasUuids;

    protected $appends = ['total_price'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function engine(): BelongsTo
    {
        return $this->belongsTo(Engine::class);
    }

    public function setup(): BelongsTo
    {
        return $this->belongsTo(Setup::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function optionals(): BelongsToMany
    {
        return $this->belongsToMany(
            Optional::class,
            'configuration_optionals',
        )->withPivot(['optional_price', 'is_included']);
    }

    public function totalPrice(): Attribute
    {
        return Attribute::get(
            fn() => ($this->vehicle_price ?? 0) +
                ($this->engine_price ?? 0) +
                ($this->setup_price ?? 0) +
                ($this->color_price ?? 0) +
                ($this->total_optional_price ?? 0),
        );
    }
}
